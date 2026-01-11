#!/bin/bash

# Script per monitorare le registrazioni push in tempo reale
# Usage: ./monitor-push-subscriptions.sh

echo "=== Monitoring Push Subscriptions ==="
echo "Press Ctrl+C to stop"
echo ""

# Mostra lo stato attuale
echo "Current subscriptions in database:"
docker compose exec app php artisan tinker --execute="
    \$subs = DB::table('push_subscriptions')
        ->join('users', 'push_subscriptions.subscribable_id', '=', 'users.id')
        ->select('users.name', 'users.id as user_id', 'push_subscriptions.created_at')
        ->orderBy('push_subscriptions.created_at', 'desc')
        ->get();
    
    foreach(\$subs as \$sub) {
        echo \$sub->name . ' (' . substr(\$sub->user_id, 0, 8) . '...) - ' . \$sub->created_at . PHP_EOL;
    }
    
    echo PHP_EOL . 'Total: ' . \$subs->count() . ' subscriptions' . PHP_EOL;
"

echo ""
echo "Watching logs for new push subscription attempts..."
echo ""

# Monitora i log in tempo reale
docker compose logs -f app | grep --line-buffered "Push subscription"
