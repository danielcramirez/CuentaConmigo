# Admin Web Backend - Jairohortua App MVP

Backend API y Panel Administrativo en Laravel.

## Instalación Local

### Requisitos
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (opcional, para assets)

### Pasos

```bash
# 1. Clonar y navegar
cd admin-web

# 2. Instalar dependencias
composer install

# 3. Configurar .env
cp .env.example .env
# Editar: APP_KEY, DB_CONNECTION, etc.

# 4. Generar APP_KEY
php artisan key:generate

# 5. Crear base de datos
mysql -u root -p -e "CREATE DATABASE jairohortua_app;"

# 6. Migraciones y seeders
php artisan migrate --seed

# 7. Servidor de desarrollo
php artisan serve

# 8. (Opcional) Tests
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
php artisan test
```

API estará en: `http://localhost:8000/api`

## Endpoints Principales

### Autenticación
- **POST** `/api/auth/login` - Login (username/password)
- **POST** `/api/auth/refresh` - Refresh token
- **POST** `/api/auth/logout` - Logout

### Usuarios
- **GET** `/api/users/me` - Perfil actual
- **GET** `/api/users/me/dashboard` - Dashboard dinámico
- **GET** `/api/users/{id}/referrals` - Grafo de referidos

### Eventos
- **GET** `/api/events` - Listar eventos
- **GET** `/api/events/{id}` - Detalle evento

### Ubicación
- **POST** `/api/location` - Registrar ubicación

### Banners
- **GET** `/api/banners/active` - Banner activo

### Notificaciones
- **GET** `/api/notifications` - Mis notificaciones
- **POST** `/api/notifications/{id}/read` - Marcar como leída

### Sync (Offline)
- **POST** `/api/sync/push` - Enviar operaciones pendientes
- **GET** `/api/sync/pull?since=timestamp` - Traer cambios

## Credenciales Iniciales (Seeder)

Usuario Admin:
- **Username**: `admin`
- **Password**: `admin123` ⚠️ Cambiar en producción

Usuario Candidato:
- **Username**: `candidato1`
- **Password**: `password123`

Usuario Líder:
- **Username**: `lider1`
- **Password**: `password123`

## Estructura del Proyecto

```
admin-web/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/   # API controllers
│   │   ├── Controllers/Admin/ # Admin web controllers (próximamente)
│   │   ├── Requests/          # Form request validation
│   │   └── Resources/         # JSON resources
│   ├── Models/                # Eloquent models
│   ├── Jobs/                  # Background jobs (FCM, etc)
│   └── Services/              # Business logic
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php               # API routes
│   └── web.php               # Web routes
├── resources/
│   └── views/                # Blade templates (admin)
├── tests/                    # Pest tests
├── config/                   # Configuration files
└── bootstrap/                # Bootstrap files
```

## Migraciones

Tablas principales:
- `users` - Usuarios del sistema
- `profiles` - Perfiles extendidos
- `user_locations` - Historial de ubicaciones (con SPATIAL INDEX)
- `events` - Eventos geográficos
- `banners` - Banners dinámicos
- `notifications` - Historial de notificaciones
- `referrals` - Grafo de referidos
- `device_tokens` - Tokens FCM por device
- `modules` - Módulos de la app
- `role_modules` - Asociación roles ↔ módulos
- `settings` - Configuración key/value

Ver: `database/migrations/`

## Autenticación (Sanctum)

- Tokens de corta duración (default 1440 min = 24h)
- Token rotation en `/auth/refresh` (revoca anterior, emite nuevo)
- Almacenamiento seguro en mobile (flutter_secure_storage)

## Offline Sync

```
Cliente (Flutter):
  ├─ guarda operaciones locales en pending_operations
  ├─ al detectar conexión, POST /sync/push
  └─ GET /sync/pull para traer cambios remotos

Servidor (Laravel):
  ├─ aplica operaciones idempotentes (client_uuid)
  └─ retorna cambios ordenados por updated_at
```

## RBAC (Roles & Permissions)

Roles predefinidos:
- **SuperAdmin**: Acceso total
- **Candidato**: Ver eventos, notificaciones
- **Líder**: Crear eventos, ver referidos
- **Usuario Básico**: Solo notificaciones y perfil

Módulos (visibilidad dinámica):
- users, roles, modules, settings, banners, events, notifications, referrals

## Testing

```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/LocationTest.php
php artisan test tests/Feature/SyncTest.php
```

Tests incluyen:
- ✅ Autenticación (login, refresh, logout)
- ✅ Geolocalización (POST /location)
- ✅ Sync (push/pull)
- ✅ Notificaciones
- ✅ Eventos (próximamente)

## Troubleshooting

### "database connection error"
```bash
# Verificar .env
cat .env | grep DB_

# Crear manualmente
mysql -u root -p -e "CREATE DATABASE jairohortua_app;"
```

### "MIGRATION ERROR" (POINT type)
- Si MySQL no soporta POINT, las queries usarán lat/lng fallback
- Recomendado: MySQL 8.0.5+ con SPATIAL support

### "Token expired"
- Llamar a POST `/auth/refresh`
- Dio interceptor en Flutter lo hace automáticamente

## Próximas Features (Fase 2)

- ✅ Admin Blade (CRUD)
- ✅ Firebase Cloud Messaging (FCM)
- ✅ Queue system (database/redis)
- ✅ Rate limiting avanzado
- ✅ Audit logs

## Licencia

MIT - Ver [LICENSE](../LICENSE)

---

**Última actualización**: 20 de diciembre de 2025
