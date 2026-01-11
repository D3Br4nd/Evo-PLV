# 🔔 Fix: Project Status Notifications from PWA

## Problema Risolto
Quando un socio aggiornava lo stato di un progetto dalla PWA, le notifiche **non venivano inviate** agli altri membri del progetto e agli admin.

## Data Fix
**2026-01-11**

---

## 🐛 Causa del Problema

Il `MemberProjectController` (usato dalla PWA) non aveva la logica di invio notifiche, mentre l'`AdminProjectController` sì.

### Codice Originale (MemberProjectController.php)
```php
public function update(Request $request, Project $project)
{
    // ... validazione ...
    
    $project->update([
        'status' => $validated['status'],
    ]);

    // Note: AdminProjectController usually handles notifications on status change.
    // If we want members to notify each other or admins, we'd add it here.
    // For now, keeping it simple as requested: "solo quello però" (status change only).

    return back()->with('success', 'Stato aggiornato.');
}
```

---

## ✅ Soluzione Implementata

Aggiunta la stessa logica di notifica dell'`AdminProjectController` al `MemberProjectController`.

### Codice Aggiornato (MemberProjectController.php)
```php
public function update(Request $request, Project $project)
{
    // Ensure user is member of the project
    if (!$project->members()->where('users.id', auth()->id())->exists()) {
        abort(403);
    }

    $oldStatus = $project->status;

    $validated = $request->validate([
        'status' => 'required|in:todo,in_progress,done',
    ]);

    $project->update([
        'status' => $validated['status'],
    ]);

    // Notify on status change (same logic as AdminProjectController)
    if ($oldStatus !== $project->status) {
        $notification = new \App\Notifications\ProjectUpdateNotification($project, $oldStatus, false);
        
        $admins = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)
            ->orWhere('role', \App\Enums\UserRole::SuperAdmin->value)
            ->get();
        
        $membersToNotify = $project->members;
        
        // Exclude the current user from notifications (they already know they changed it)
        $usersToNotify = $admins->merge($membersToNotify)
            ->unique('id')
            ->reject(fn($user) => $user->id === auth()->id());
        
        \Log::info('Sending project update notification (from member)', [
            'project_id' => $project->id,
            'project_title' => $project->title,
            'old_status' => $oldStatus,
            'new_status' => $project->status,
            'updated_by' => auth()->user()->name,
            'users_to_notify_count' => $usersToNotify->count(),
            'user_ids' => $usersToNotify->pluck('id')->toArray(),
        ]);
        
        foreach ($usersToNotify as $user) {
            $user->notify($notification);
        }
    }

    return back()->with('success', 'Stato aggiornato e notifiche inviate.');
}
```

---

## 🎯 Comportamento Attuale

### Quando un socio cambia lo stato di un progetto dalla PWA:

1. ✅ **Notifiche inviate a:**
   - Tutti gli **admin** e **super admin**
   - Tutti i **membri del progetto**
   - **ESCLUSO** l'utente che ha fatto la modifica (non serve notificare se stesso)

2. ✅ **Notifiche Push:**
   - Inviate immediatamente a tutti i dispositivi registrati

3. ✅ **Notifiche In-App:**
   - Visibili nella sezione "Notifiche" della PWA

4. ✅ **Logging:**
   - Ogni invio viene loggato con dettagli completi
   - Log include: project_id, old_status, new_status, updated_by, users_to_notify

---

## 📊 Differenze tra Admin e Member Update

| Aspetto | AdminProjectController | MemberProjectController |
|---------|----------------------|------------------------|
| **Chi può aggiornare** | Solo admin/super_admin | Solo membri del progetto |
| **Campi modificabili** | Tutti (title, status, priority, description, deadline, members) | Solo status |
| **Notifiche inviate** | ✅ Sì | ✅ Sì (dopo fix) |
| **Esclude autore** | ❌ No | ✅ Sì |
| **Log message** | "Sending project update notification" | "Sending project update notification (from member)" |

---

## 🧪 Come Testare

### 1. Preparazione
```bash
# Monitora i log in tempo reale
./monitor-project-notifications.sh
```

### 2. Test dalla PWA
1. Accedi come **Socio A** (membro di un progetto)
2. Vai su **Progetti** → Seleziona un progetto
3. Cambia lo **stato** del progetto (es. da "To Do" a "In Progress")
4. Verifica che appaia il messaggio "Stato aggiornato e notifiche inviate"

### 3. Verifica Notifiche
1. Accedi come **Socio B** (altro membro dello stesso progetto)
2. Vai su **Notifiche**
3. Dovresti vedere una notifica del tipo:
   ```
   Progetto aggiornato
   [Nome Progetto] è passato da "To Do" a "In Progress"
   ```

### 4. Verifica Log
Nel terminale con `monitor-project-notifications.sh` dovresti vedere:
```
production.INFO: Sending project update notification (from member) 
{
  "project_id": "...",
  "project_title": "...",
  "old_status": "todo",
  "new_status": "in_progress",
  "updated_by": "Socio A",
  "users_to_notify_count": 2,
  "user_ids": ["...", "..."]
}
```

---

## 🔍 Debugging

### Verificare chi riceve le notifiche
```bash
docker compose exec app php artisan tinker

# Trova un progetto
$project = App\Models\Project::first();

# Vedi i membri
$project->members;

# Vedi gli admin
$admins = App\Models\User::where('role', 'admin')
    ->orWhere('role', 'super_admin')
    ->get();

# Simula chi riceverebbe la notifica
$usersToNotify = $admins->merge($project->members)
    ->unique('id')
    ->reject(fn($user) => $user->id === auth()->id());
    
echo "Users to notify: " . $usersToNotify->count();
```

### Verificare le notifiche inviate
```bash
docker compose exec app php artisan tinker

# Notifiche per un utente
$user = App\Models\User::find('user-uuid');
$user->notifications()->latest()->take(5)->get();
```

---

## 📝 File Modificati

- **`app/Http/Controllers/MemberProjectController.php`**
  - Metodo `update()` - Aggiunta logica notifiche

---

## 🚀 Script Utili

### Monitor Project Notifications
```bash
./monitor-project-notifications.sh
```

### Monitor Push Subscriptions
```bash
./monitor-push-subscriptions.sh
```

---

## 💡 Note Tecniche

1. **Esclusione dell'autore**: A differenza dell'`AdminProjectController`, il `MemberProjectController` **esclude** l'utente che ha fatto la modifica dalle notifiche. Questo è intenzionale perché:
   - L'utente sa già di aver cambiato lo stato
   - Evita notifiche ridondanti
   - Migliora l'UX

2. **Logging differenziato**: Il log include "(from member)" per distinguere le modifiche fatte dai membri da quelle fatte dagli admin.

3. **Validazione permessi**: Il controller verifica sempre che l'utente sia effettivamente membro del progetto prima di permettere modifiche.

---

## 🔮 Future Improvements

1. **Notifiche Email**: Aggiungere invio email oltre alle push notifications
2. **Notifiche Personalizzate**: Permettere ai membri di scegliere per quali progetti ricevere notifiche
3. **Digest Giornaliero**: Opzione per ricevere un riassunto giornaliero invece di notifiche immediate
4. **Menzioni**: Permettere di menzionare specifici utenti nei commenti
5. **Webhook**: Integrare con Slack/Discord per notifiche esterne

---

**Ultimo aggiornamento:** 2026-01-11  
**Stato:** ✅ Implementato e Testato
