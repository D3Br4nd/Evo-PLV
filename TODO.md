# Cose da sistemare

- [ ] Completare funzionalità di check-in per le riunioni

---

# 📧 Broadcast Email Implementation Plan

## Obiettivo
Implementare l'invio massivo di email quando viene creato un Broadcast Notification, raggiungendo tutti i soci attivi (~400 membri).

---

## 🎯 Requisiti

1. **Inviare email** a tutti i soci attivi quando viene creato un broadcast
2. **Evitare blocchi** da parte del provider email (Aruba)
3. **Tracciare** gli invii e gestire eventuali errori
4. **Performance** - non bloccare l'interfaccia admin
5. **GDPR compliant** - permettere opt-out

---

## 🏗️ Architettura Consigliata

### **Opzione 1: Queue + Batch (Consigliata)** ⭐

Laravel ha un sistema di **queue** e **batch** perfetto per questo caso d'uso.

#### Vantaggi:
- ✅ **Rate limiting** - limita a X email al minuto
- ✅ **Retry automatico** - se un'email fallisce, riprova
- ✅ **Monitoraggio** - vedi quante email sono state inviate/fallite
- ✅ **Non blocca l'UI** - tutto in background
- ✅ **Scalabile** - funziona anche con 10.000 soci

#### Implementazione:

```php
// app/Jobs/SendBroadcastEmail.php
<?php

namespace App\Jobs;

use App\Mail\BroadcastMail;
use App\Models\BroadcastNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBroadcastEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // Retry dopo 1min, 5min, 15min
    public $timeout = 120;

    public function __construct(
        public User $member,
        public BroadcastNotification $broadcast
    ) {}

    public function middleware()
    {
        return [
            new RateLimited('emails'), // Max 100/hour
        ];
    }

    public function handle()
    {
        // Verifica che il socio non abbia disattivato le email broadcast
        if ($this->member->email_notifications_disabled ?? false) {
            return;
        }

        Mail::to($this->member->email)
            ->send(new BroadcastMail($this->broadcast));

        // Log dell'invio
        \Log::info('Broadcast email sent', [
            'broadcast_id' => $this->broadcast->id,
            'member_id' => $this->member->id,
            'email' => $this->member->email,
        ]);
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Broadcast email failed', [
            'broadcast_id' => $this->broadcast->id,
            'member_id' => $this->member->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

```php
// app/Mail/BroadcastMail.php
<?php

namespace App\Mail;

use App\Models\BroadcastNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BroadcastNotification $broadcast
    ) {}

    public function build()
    {
        return $this->subject($this->broadcast->title)
            ->view('emails.broadcast')
            ->with([
                'title' => $this->broadcast->title,
                'content' => $this->broadcast->content,
                'featuredImage' => $this->broadcast->featured_image_path,
                'attachment' => $this->broadcast->attachment_path,
            ]);
    }
}
```

```php
// Nel controller quando crei il broadcast
use Illuminate\Support\Facades\Bus;

public function store(Request $request)
{
    // ... validazione e creazione del broadcast ...
    
    $broadcast = BroadcastNotification::create([...]);
    
    // Se l'utente ha scelto di inviare anche le email
    if ($request->boolean('send_email')) {
        $activeMembers = User::whereHas('memberships', function($q) {
            $q->where('year', now()->year)
              ->where('status', 'active');
        })->get();

        $jobs = $activeMembers->map(function($member) use ($broadcast) {
            return new SendBroadcastEmail($member, $broadcast);
        });

        $batch = Bus::batch($jobs)
            ->name('Broadcast Email: ' . $broadcast->title)
            ->allowFailures()
            ->onQueue('emails')
            ->dispatch();

        // Salva il batch ID per monitoraggio
        $broadcast->update(['email_batch_id' => $batch->id]);
    }
    
    // ... resto del codice ...
}
```

---

### **Opzione 2: Chunking Semplice**

Se non vuoi usare le queue, puoi fare chunking manuale:

```php
public function store(Request $request)
{
    $broadcast = BroadcastNotification::create([...]);
    
    if ($request->boolean('send_email')) {
        // Dispatch a job che fa il chunking
        dispatch(function() use ($broadcast) {
            User::whereHas('memberships', function($q) {
                $q->where('year', now()->year)
                  ->where('status', 'active');
            })
            ->chunk(50, function($members) use ($broadcast) {
                foreach($members as $member) {
                    try {
                        Mail::to($member->email)->send(new BroadcastMail($broadcast));
                        usleep(100000); // 100ms di pausa tra ogni email
                    } catch (\Exception $e) {
                        \Log::error('Email failed', [
                            'member_id' => $member->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                sleep(5); // 5 secondi tra ogni chunk
            });
        })->onQueue('emails');
    }
}
```

**Vantaggi:**
- ✅ Più semplice da implementare
- ✅ Non richiede configurazione complessa

**Svantaggi:**
- ❌ Meno controllo sul rate limiting
- ❌ Nessun retry automatico
- ❌ Difficile monitorare i progressi

---

## ⚙️ Configurazione

### 1. Environment Variables

```env
# .env
QUEUE_CONNECTION=database  # o redis per performance migliori

# Email Configuration (Aruba)
MAIL_MAILER=smtp
MAIL_HOST=smtp.aruba.it
MAIL_PORT=587
MAIL_USERNAME=noreply@prolocoventicano.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@prolocoventicano.com
MAIL_FROM_NAME="Pro Loco Venticanese"
```

### 2. Rate Limiting

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot()
{
    RateLimiter::for('emails', function ($job) {
        return Limit::perHour(100)->by($job->member->id);
    });
}
```

### 3. Database Migration per Queue

```bash
php artisan queue:table
php artisan queue:batches-table
php artisan migrate
```

### 4. Queue Worker

```bash
# Avvia il worker (in produzione usa supervisor)
php artisan queue:work --queue=emails --tries=3
```

---

## 📊 Limiti Email Aruba

Aruba tipicamente ha questi limiti:
- **~100-200 email/ora** per account condivisi
- **~500-1000 email/ora** per account dedicati
- **~50 MB** dimensione massima allegati

**Configurazione consigliata:**
- Rate limit: **80-100 email/ora** (per stare sicuri)
- Chunk size: **50 email** per batch
- Tempo stimato per 400 email: **4-5 ore**

---

## 🎨 UI Changes

### Admin Broadcast Create/Edit Form

```svelte
<!-- resources/js/Pages/Admin/Broadcasts/Create.svelte -->

<script>
    let sendEmail = $state(false);
    let activeMembersCount = $props().activeMembersCount || 0;
    
    let estimatedTime = $derived.by(() => {
        if (!sendEmail) return 0;
        // Assumendo 100 email/ora
        return Math.ceil(activeMembersCount / 100);
    });
</script>

<Card.Root>
    <Card.Header>
        <Card.Title>Opzioni di Invio</Card.Title>
    </Card.Header>
    <Card.Content class="space-y-4">
        <div class="flex items-center space-x-2">
            <Switch id="send-email" bind:checked={sendEmail} />
            <Label for="send-email">
                Invia anche email a tutti i soci attivi
            </Label>
        </div>
        
        {#if sendEmail}
            <Alert>
                <AlertCircle class="h-4 w-4" />
                <AlertTitle>Invio Email Massivo</AlertTitle>
                <AlertDescription>
                    Verranno inviate circa <strong>{activeMembersCount}</strong> email.
                    L'invio avverrà in background e potrebbe richiedere 
                    fino a <strong>{estimatedTime} ore</strong>.
                    <br><br>
                    Le notifiche push verranno inviate immediatamente.
                </AlertDescription>
            </Alert>
        {/if}
    </Card.Content>
</Card.Root>
```

### Monitoring Dashboard

```svelte
<!-- Nella pagina show del broadcast -->

{#if broadcast.email_batch_id}
    <Card.Root>
        <Card.Header>
            <Card.Title>Stato Invio Email</Card.Title>
        </Card.Header>
        <Card.Content>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Email inviate:</span>
                    <span class="font-medium">{batch.processedJobs} / {batch.totalJobs}</span>
                </div>
                <Progress value={(batch.processedJobs / batch.totalJobs) * 100} />
                
                {#if batch.failedJobs > 0}
                    <div class="text-sm text-destructive">
                        {batch.failedJobs} email fallite
                    </div>
                {/if}
                
                <div class="text-xs text-muted-foreground">
                    {#if batch.finished_at}
                        Completato: {new Date(batch.finished_at).toLocaleString()}
                    {:else}
                        In corso...
                    {/if}
                </div>
            </div>
        </Card.Content>
    </Card.Root>
{/if}
```

---

## 📝 Email Template

```blade
<!-- resources/views/emails/broadcast.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #18181b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>
    
    <div class="content">
        @if($featuredImage)
            <img src="{{ asset('storage/' . $featuredImage) }}" alt="{{ $title }}">
        @endif
        
        <div style="margin-top: 20px;">
            {!! $content !!}
        </div>
        
        @if($attachment)
            <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 8px;">
                <strong>📎 Allegato:</strong>
                <a href="{{ asset('storage/' . $attachment) }}">Scarica file</a>
            </div>
        @endif
    </div>
    
    <div class="footer">
        <p>
            Hai ricevuto questa email perché sei un socio attivo della Pro Loco Venticanese.
            <br>
            <a href="{{ url('/me/settings') }}">Gestisci le tue preferenze email</a>
        </p>
    </div>
</body>
</html>
```

---

## 🔐 GDPR Compliance

### 1. Aggiungi campo al database

```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->boolean('email_notifications_enabled')->default(true);
});
```

### 2. Aggiungi toggle nelle impostazioni utente

```svelte
<!-- resources/js/Pages/Member/Settings.svelte -->
<Switch bind:checked={emailNotificationsEnabled}>
    Ricevi email per le comunicazioni broadcast
</Switch>
```

### 3. Filtra gli utenti nel job

```php
$activeMembers = User::whereHas('memberships', function($q) {
    $q->where('year', now()->year)->where('status', 'active');
})
->where('email_notifications_enabled', true)
->get();
```

---

## 🚀 Deployment Checklist

- [ ] Configurare credenziali SMTP Aruba in `.env`
- [ ] Testare invio email singola
- [ ] Creare migration per `jobs` e `job_batches` tables
- [ ] Implementare `SendBroadcastEmail` job
- [ ] Implementare `BroadcastMail` mailable
- [ ] Creare template email
- [ ] Aggiungere rate limiting
- [ ] Aggiungere UI per toggle email
- [ ] Aggiungere monitoring dashboard
- [ ] Implementare opt-out GDPR
- [ ] Configurare supervisor per queue worker in produzione
- [ ] Testare con piccolo gruppo (5-10 email)
- [ ] Testare con gruppo medio (50 email)
- [ ] Deploy in produzione

---

## 📈 Monitoring & Logging

### Comandi utili:

```bash
# Vedere lo stato delle queue
php artisan queue:monitor

# Vedere i batch in corso
php artisan queue:batches

# Vedere i job falliti
php artisan queue:failed

# Retry job falliti
php artisan queue:retry all

# Pulire job vecchi
php artisan queue:prune-batches --hours=48
```

### Log da monitorare:

```bash
# Vedere gli invii email
tail -f storage/logs/laravel.log | grep "Broadcast email"

# Vedere gli errori
tail -f storage/logs/laravel.log | grep "ERROR"
```

---

## 💡 Best Practices

1. **Testa sempre** con un piccolo gruppo prima di inviare a tutti
2. **Monitora i bounce** - Aruba potrebbe segnalare email invalide
3. **Usa template responsive** - molti soci leggeranno da mobile
4. **Includi unsubscribe link** - obbligatorio per GDPR
5. **Logga tutto** - utile per debugging e compliance
6. **Backup delle email** - salva una copia nel database
7. **Considera alternative** - SendGrid, Mailgun, SES per volumi alti

---

## 🔮 Future Improvements

1. **Email scheduling** - programma invio per orario specifico
2. **A/B testing** - testa subject lines diversi
3. **Analytics** - traccia aperture e click
4. **Segmentazione** - invia solo a specifici comitati
5. **Template builder** - editor visuale per email
6. **Preview** - anteprima email prima dell'invio
7. **Throttling dinamico** - adatta rate in base a risposte server

---

## 📚 Risorse

- [Laravel Queue Documentation](https://laravel.com/docs/11.x/queues)
- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Laravel Batch Jobs](https://laravel.com/docs/11.x/queues#job-batching)
- [Aruba Email Limits](https://guide.aruba.it/hosting/linux/gestione-email.aspx)
- [GDPR Email Marketing](https://gdpr.eu/email-marketing/)

---

**Ultimo aggiornamento:** 2026-01-11  
**Stato:** 📋 Pianificato (non implementato)
