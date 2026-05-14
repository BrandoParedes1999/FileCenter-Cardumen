# FileCenter Cardumen

Sistema de gestión documental multi-empresa construido con **Laravel 12**, Blade, Alpine.js y Tailwind CSS.

---

## Descripción

FileCenter Cardumen permite a un grupo de empresas administrar sus archivos y carpetas con control granular de permisos, flujos de aprobación y auditoría completa de actividad. Una empresa corporativa puede ser visible para todas las demás; cada empresa tiene su propio espacio privado.

---

## Características principales

| Módulo | Descripción |
|---|---|
| **Autenticación** | Login con bloqueo por intentos fallidos (5 intentos → bloqueo temporal configurable) |
| **Multi-empresa** | `CompanyScope` middleware garantiza que cada usuario solo vea datos de su empresa |
| **Carpetas** | Estructura jerárquica con herencia de permisos hacia subcarpetas |
| **Permisos granulares** | 5 bits por entrada: leer, descargar, subir, editar, borrar + heredar |
| **Solicitudes de acceso** | Empleados solicitan acceso a carpetas de otras empresas; Admin/Gerente aprueba |
| **Solicitudes de subida** | Carpetas con `requiere_aprobacion_subida` enrutan archivos a revisión antes de publicarlos |
| **Notificaciones email** | Avisos automáticos al crear/aprobar/rechazar solicitudes (mailer configurable) |
| **Reporte de actividad** | Historial filtrable con exportación CSV; acceso restringido a Superadmin/Aux_QHSE |
| **Gestor de permisos global** | Vista centralizada de todos los permisos con edición inline |
| **Limpieza automática** | Comando Artisan `filecenter:limpiar-temporales` ejecutado diariamente a las 02:00 |
| **Auditoría** | Registro de todas las acciones relevantes vía Spatie Activity Log |

---

## Roles y jerarquía

```
Superadmin   — acceso total a todas las empresas y configuración global
Aux_QHSE     — lectura global + reportes; no puede modificar permisos
Admin        — gestiona usuarios, carpetas y permisos de su empresa
Gerente      — aprueba solicitudes; acceso amplio dentro de su empresa
Auxiliar     — acceso según permisos explícitos de carpeta
Empleado     — acceso mínimo; puede solicitar acceso o subida
```

---

## Requisitos

- PHP 8.2+
- MySQL 8+ (o MariaDB 10.6+)
- Composer
- Node.js 18+ / npm

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd FileCenter-Cardumen

# 2. Instalar dependencias
composer install
npm install && npm run build

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
#    DB_DATABASE=FileCenter
#    DB_USERNAME=...
#    DB_PASSWORD=...

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. (Opcional) Configurar el scheduler de Laravel en cron
#    * * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1

# 7. Levantar el servidor
php artisan serve
```

---

## Estructura de base de datos (tablas principales)

| Tabla | Propósito |
|---|---|
| `empresas` | Empresas del grupo; `es_corporativo=1` identifica la corporativa |
| `usuarios` | Usuarios con `rol` ENUM y control de bloqueo por fuerza bruta |
| `carpetas` | Árbol de carpetas con `padre_id`, `path`, `modo_acceso` |
| `archivos` | Archivos con versiones y metadatos |
| `permisos_de_carpeta` | Permisos usuario/rol por carpeta (5 bits + heredar) |
| `solicitudes_acceso` | Solicitudes de acceso inter-empresa |
| `solicitudes_subida` | Archivos en cola de aprobación antes de publicarse |
| `registro_actividad` | Log de auditoría de todas las acciones |

---

## Variables de entorno relevantes

```env
# Correo — en desarrollo usar 'log', en producción configurar SMTP
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@filecenter.example.com"

# Cola — usar 'database' para producción, ejecutar: php artisan queue:work
QUEUE_CONNECTION=database

# Disco de archivos (configurable en config/filesystems.php)
FILESYSTEM_DISK=local
```

---

## Tests

```bash
php artisan test
```

La suite cubre: permisos de carpeta, acceso a archivos, solicitudes de acceso, solicitudes de subida, bloqueo por fuerza bruta y control de acceso por roles (89 tests, 151 assertions).

---

## Comandos Artisan

```bash
# Limpiar archivos temporales huérfanos (se ejecuta automáticamente vía scheduler)
php artisan filecenter:limpiar-temporales --dias=7

# Vista previa sin borrar nada
php artisan filecenter:limpiar-temporales --dry-run
```

---

## Licencia

Uso interno — todos los derechos reservados.
