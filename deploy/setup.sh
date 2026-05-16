#!/usr/bin/env bash
# =============================================================================
# FileCenter – Script de despliegue en producción
# Uso: bash deploy/setup.sh
# =============================================================================
set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info()    { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# ── 1. Verificar requisitos ───────────────────────────────────────────────────
info "Verificando requisitos..."
command -v php  >/dev/null || error "PHP no instalado (requiere 8.2+)"
command -v composer >/dev/null || error "Composer no instalado"
command -v npm  >/dev/null || error "Node/npm no instalado"
command -v mysql >/dev/null || warn "mysql-client no encontrado (puede ser remoto)"

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if (( $(echo "$PHP_VERSION < 8.2" | bc -l) )); then
    error "Se requiere PHP 8.2+, encontrado: $PHP_VERSION"
fi
info "PHP $PHP_VERSION detectado"

# ── 2. Archivo .env ───────────────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    info "Creando .env desde .env.example..."
    cp .env.example .env
else
    warn ".env ya existe, no se sobreescribirá"
fi

# ── 3. Generar APP_KEY ────────────────────────────────────────────────────────
if grep -q "^APP_KEY=$" .env; then
    info "Generando APP_KEY..."
    php artisan key:generate --force
else
    info "APP_KEY ya configurada"
fi

# ── 4. Instalar dependencias ──────────────────────────────────────────────────
info "Instalando dependencias PHP (sin dev)..."
composer install --no-dev --optimize-autoloader --no-interaction

info "Instalando dependencias Node..."
npm ci

info "Compilando assets para producción..."
npm run build

# ── 5. Permisos de directorios ────────────────────────────────────────────────
info "Configurando permisos..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── 6. Base de datos ──────────────────────────────────────────────────────────
info "Ejecutando migraciones..."
php artisan migrate --force

read -rp "¿Ejecutar seeders de datos iniciales? (roles, empresa demo) [s/N]: " RUN_SEED
if [[ "$RUN_SEED" =~ ^[Ss]$ ]]; then
    php artisan db:seed --force
    info "Seeders ejecutados"
fi

# ── 7. Optimización de producción ─────────────────────────────────────────────
info "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ── 8. Enlace simbólico de storage ───────────────────────────────────────────
if [ ! -L "public/storage" ]; then
    info "Creando enlace storage..."
    php artisan storage:link
fi

# ── 9. Resumen ────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}============================================================${NC}"
echo -e "${GREEN}  Despliegue completado${NC}"
echo -e "${GREEN}============================================================${NC}"
echo ""
warn "PASOS MANUALES PENDIENTES:"
echo "  1. Editar .env con datos reales de BD y correo"
echo "  2. Apuntar Nginx/Apache al directorio /public"
echo "  3. Copiar deploy/nginx.conf a /etc/nginx/sites-available/"
echo "  4. Copiar deploy/supervisor.conf a /etc/supervisor/conf.d/"
echo "  5. Agregar cron: * * * * * www-data php $(pwd)/artisan schedule:run >> /dev/null 2>&1"
echo "  6. Reiniciar supervisor: supervisorctl reread && supervisorctl update"
echo ""
