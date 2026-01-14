# 🇮🇹 Pro Loco Venticanese Evolution

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20.svg)
![Svelte](https://img.shields.io/badge/Svelte-5.0-FF3E00.svg)
![Tailwind](https://img.shields.io/badge/Tailwind-4.0-38B2AC.svg)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18.0-336791.svg)

**Pro Loco Venticanese Evolution** è la piattaforma digitale per la gestione moderna della Pro Loco di Venticano. Un'unica applicazione che unisce un portale pubblico, una PWA per i soci e una dashboard amministrativa avanzata.

---

## ✨ Funzionalità Principali

### 📱 Per i Soci (PWA)
- **Tessera Digitale**: QR Code personale sempre disponibile.
- **Profilo Utente**: Gestione dati, avatar e preferenze.
- **Eventi & Biglietti**: Accesso rapido agli eventi della Pro Loco.
- **Notifiche**: Aggiornamenti in tempo reale su riunioni e attività.
- **Comitati**: Spazio dedicato per i gruppi di lavoro.

### 🛠️ Per l'Amministrazione
- **Dashboard Completa**: Panoramica delle attività e statistiche.
- **Gestione Soci**: Database centralizzato con export/import CSV.
- **Broadcast System**: Invio notifiche e email massive.
- **CMS**: Gestione pagine pubbliche e contenuti.
- **Check-in Eventi**: Sistema di verifica presenze.

---

## 🚀 Installazione (Docker)

Il progetto è completamente containerizzato per garantire un avvio rapido e consistente.

### Prerequisiti
- [Docker Engine](https://docs.docker.com/engine/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Quick Start

1. **Clona il repository**
   ```bash
   git clone https://github.com/pro-loco-venticanese/plv-evo.git
   cd plv_evo
   ```

2. **Configura l'ambiente**
   Copia il file di esempio e configuralo (se necessario):
   ```bash
   cp .env.example .env
   ```

3. **Avvia con Docker**
   ```bash
   # Avvia i container in background
   docker compose up -d --build
   ```

4. **Inizializza il Database**
   Esegui migrazioni e seed (resetta il DB!):
   ```bash
   docker compose exec app php artisan migrate:fresh --seed
   ```

5. **Accedi**
   - **Frontend**: [http://localhost:8000](http://localhost:8000)
   - **Admin CP**: Accedi con le credenziali configurate nel seeder o crea un super admin:
     ```bash
     docker compose exec app php artisan plv:make-superadmin
     ```

---

## 🏗️ Stack Tecnologico

Il progetto utilizza uno stack "Bleeding Edge" per massimizzare performance e manutenibilità:

- **Backend**: Laravel 12 (PHP 8.4) con Laravel Reverb (WebSockets).
- **Frontend**: Svelte 5 (Runes Syntax) + Inertia.js 2.0.
- **Style**: Tailwind CSS 4 (Configurazione CSS-first).
- **Database**: PostgreSQL 18 con Primary Keys UUIDv7.
- **Server**: FrankenPHP (Caddy Server integrato).

---

## 📏 Best Practices & Standard

Per mantenere alta la qualità del codice, seguiamo queste regole rigorose:

1. **Svelte 5 Runes**: Utilizzare sempre la nuova sintassi (`$state`, `$props`). NO `export let`.
2. **UUIDv7**: Tutti i modelli Eloquent devono usare il trait `HasUuids` con UUIDv7.
3. **Tailwind 4**: Configurazione via CSS variables in `app.css`. NO `tailwind.config.js`.
4. **Theme**: Utilizzare il tema standard Shadcn Zinc.
5. **Migrations**: Single-file schema (`schema/pgsql-schema.sql`) + future migrations schiacciate.

---

## 🗺️ Roadmap & Next Steps

Visualizza il file [TODO.md](./TODO.md) per la lista aggiornata delle attività, tra cui:
- [ ] Implementazione invio email broadcast.
- [ ] Completamento check-in riunioni.

---

## 📄 Licenza

Questo progetto è rilasciato sotto licenza **MIT**. Contribuisci liberamente!
