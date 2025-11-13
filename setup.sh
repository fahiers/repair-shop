#!/bin/bash
set -e

# Instala las dependencias de Composer (PHP)
composer install

# Instala las dependencias de NPM (solo producción)
npm install --production

# Compila los assets frontend
npm run build

# Optimiza la aplicación Laravel
php artisan optimize

# Cachea la configuración
php artisan config:cache

# Cachea las rutas
php artisan route:cache

# Cachea las vistas
php artisan view:cache

# Ejecuta las migraciones pendientes (NO borra datos, solo aplica cambios)
php artisan migrate --force
