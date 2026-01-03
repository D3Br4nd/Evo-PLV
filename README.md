# Pro Loco Venticanese Evolution

PWA per la gestione di eventi e tesseramenti della Pro Loco Venticanese.

## 🚀 Stack Tecnologico

### Backend
- **PHP**: 8.4
- **Framework**: Laravel 12.44
- **Server**: FrankenPHP (Caddy + PHP)
- **Database**: PostgreSQL 18.1
- **Cache/Queue**: Redis 8.4.0
- **WebSockets**: Laravel Reverb

### Frontend
- **Framework**: Svelte 5 (Runes)
- **Router**: Inertia.js 2.0
- **Styling**: Tailwind CSS 4.1 (CSS-first)
- **Theme**: Shadcn Zinc (Monochrome)
- **Build**: Vite 5
- **PWA**: vite-plugin-pwa

## 🆕 Fresh Install (Docker)

Segui questi passaggi per avviare il progetto da zero su una nuova macchina.

### 1. Preparazione

Clona il repository ed entra nella cartella:

```bash
git clone <repository-url> plv_saas
cd plv_saas
```

Crea il file `.env` (esempio minimo):

```bash
cat > .env <<'EOF'
APP_NAME="Pro Loco Venticanese"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FORCE_HTTPS=false

APP_KEY=

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=plv_saas
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PORT=6379

REVERB_APP_ID=plv
REVERB_APP_KEY=plv-key
REVERB_APP_SECRET=plv-secret
REVERB_HOST=reverb
REVERB_PORT=8080
REVERB_SCHEME=http

APP_PORT=8000
DB_PORT_FORWARD=5432
REDIS_PORT_FORWARD=6379
REVERB_PORT_FORWARD=8080
VITE_PORT_FORWARD=5173
EOF
```

### 2. Installazione Dipendenze

Installa le dipendenze di PHP e Node.js usando Docker (senza bisogno di avere PHP/Node installati localmente):

```bash
# Dipendenze PHP (Vendor)
docker run --rm -v $(pwd):/app -w /app composer:2 install --ignore-platform-reqs

# Dipendenze Node.js (Node Modules)
docker run --rm -v $(pwd):/app -w /app node:24-alpine npm install
```

### 3. Build & Avvio

Compila gli asset e avvia i container:

```bash
# Rete Docker condivisa (Nginx Proxy Manager, ecc.)
docker network create plv_network || true

# Build degli asset frontend
docker run --rm -v $(pwd):/app -w /app node:24-alpine npm run build

# Avvia lo stack
docker compose up -d --build
```

### 4. Database Setup (Migrazioni e Seeders)

Una volta che i container sono attivi (verifica con `docker compose ps`), esegui le migrazioni e il seeding del database:

**IMPORTANTE**: Questo comando resetta il database e inserisce i dati di esempio.

```bash
docker compose exec app php artisan migrate:fresh --seed
```

### 5. Accesso

- **Web App**: [http://localhost:8000](http://localhost:8000)
- **Admin Login**: `admin@prolocoventicanese.it` / `password`
- **Tessera socio (PWA)**: dopo login come socio → `/me/card`

---

## 🔧 Comandi Utili per lo Sviluppo

```bash
# Logs in tempo reale
docker compose logs -f

# Accesso alla shell del container app
docker compose exec app sh

# Eseguire comandi Artisan
docker compose exec app php artisan [command]

# Riavviare i container
docker compose restart
```

## 📁 Struttura Progetto

```
plv_saas/
├── app/
│   ├── Models/         # User, Event, Membership, Project (UUIDv7)
├── database/
│   ├── migrations/     # 2025_01_01_000000_create_plv_schema.php (Squashed)
├── resources/
│   ├── js/Pages/       # Svelte Components (Admin/Members, Admin/Events, Admin/Projects)
├── docker/             # Configurazioni e volumi persistenti
├── Dockerfile          # FrankenPHP image definition
├── docker-compose.yml  # Servizi: app, db, redis, reverb, vite
```

## 👥 Team

- **Tech Lead**: Massimiliano
- **Stack**: Bleeding Edge PHP Ecosystem
