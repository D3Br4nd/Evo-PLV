#!/bin/bash

# Script per testare le notifiche dei progetti
# Monitora i log in tempo reale filtrando per "project"

echo "=== Monitoring Project Notifications ==="
echo "Watching for project update notifications..."
echo "Press Ctrl+C to stop"
echo ""

docker compose logs -f app | grep --line-buffered -i "project"
