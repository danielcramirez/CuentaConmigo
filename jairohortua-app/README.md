# Jairohortua App - MVP Multiplataforma

MVP funcional en monorepo: **Flutter (iOS/Android)** + **Laravel Admin Web** + **REST API Backend**.

## 🎯 Características MVP

- ✅ **Mobile** (Flutter): Login persistente, offline-first con SQLite, sync automático
- ✅ **Backend** (Laravel): REST API segura con Sanctum, RBAC, geolocalización, notificaciones push (FCM)
- ✅ **Admin Web** (Blade): CRUD usuarios, eventos, banners, roles, referidos con grafo interactivo
- ✅ **Offline/Online**: Sync bidireccional (push/pull), eventos con proximidad geográfica
- ✅ **Referidos**: Código único, grafo de invitaciones, compartir con share_plus
- ✅ **Notificaciones**: FCM con proximidad, historial, lecturas

## 📁 Estructura Monorepo

```
jairohortua-app/
├── mobile-app/              # Flutter (iOS/Android)
│   ├── lib/
│   │   ├── core/            # constantes, http client, conectividad, utils
│   │   ├── data/            # API datasource + SQLite repository
│   │   ├── domain/          # entidades, repos abstractos, casos de uso
│   │   ├── presentation/    # pantallas, providers, widgets
│   │   └── main.dart
│   ├── pubspec.yaml
│   └── assets/
├── admin-web/               # Laravel (API + Admin Web)
│   ├── app/
│   ├── routes/
│   ├── resources/views/
│   ├── database/
│   ├── composer.json
│   ├── .env.example
│   └── public/
├── docs/
│   ├── ARCHITECTURE.md      # Diagrama y decisiones de diseño
│   ├── API.md               # Endpoints detallados
│   ├── DEPLOY_HOSTINGER.md  # Setup Hostinger, subdominios, SSL
│   └── DATABASE.md          # Esquema y migraciones
├── LICENSE                  # MIT
└── README.md                # Este archivo
```

## 🚀 Inicio Rápido

### Requisitos
- PHP 8.2+, Laravel 11, Composer
- Node.js 18+, npm/yarn
- Flutter 3.16+, Dart
- MySQL 8.0+ (online), SQLite 3 (offline en app)
- Firebase Console (proyecto FCM)

### Instalación Backend (Laravel)

```bash
cd admin-web

# 1. Dependencias
composer install

# 2. Configurar .env (copia de .env.example)
cp .env.example .env

# 3. Generar APP_KEY
php artisan key:generate

# 4. Migraciones + seeders
php artisan migrate:fresh --seed

# 5. Servidor dev
php artisan serve                    # http://localhost:8000/api
# O con subdominios (en development):
# php artisan serve --host app.test --port 8000
```

### Instalación Mobile (Flutter)

```bash
cd mobile-app

# 1. Dependencias
flutter pub get

# 2. Generar código (si usas freezed/get_it)
flutter pub run build_runner build --delete-conflicting-outputs

# 3. Ejecutar
flutter run
```

## 🔐 Configuración Inicial

### .env Backend (admin-web)
```
APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8000
SANCTUM_TOKEN_EXPIRATION_MINUTES=1440

# Firebase
FIREBASE_CREDENTIALS=/path/to/credentials.json

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jairohortua_app
DB_USERNAME=root
DB_PASSWORD=

# Email (opcional para MVP)
MAIL_MAILER=log

# Notificaciones
QUEUE_CONNECTION=sync  # (MVP) o database/redis

# Settings (pueden editarse en admin)
# notification_radius_km = 20 (km)
# notification_days_window = 15 (días)
```

### Credenciales Iniciales (Seeder)
- **Usuario Admin**: `admin` / `admin123` (cambia en producción)
- **Roles**: SuperAdmin, Candidato, Líder, Usuario Básico

## 📱 API Endpoints (Resumen)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| **AUTH** | |
| POST | `/api/auth/login` | Login usuario/password |
| POST | `/api/auth/logout` | Logout (revoca token) |
| POST | `/api/auth/refresh` | Rota token (nuevo + revoca anterior) |
| **USERS** | |
| GET | `/api/users/me` | Perfil actual |
| GET | `/api/users/me/dashboard` | Dashboard dinámico (módulos/nav por rol) |
| GET | `/api/users/{id}/referrals` | Mi grafo de referidos |
| **EVENTS** | |
| GET | `/api/events` | Listar eventos (filtros opcionales) |
| GET | `/api/events/{id}` | Detalle evento |
| **LOCATION** | |
| POST | `/api/location` | Registrar ubicación |
| **BANNERS** | |
| GET | `/api/banners/active` | Banner vigente |
| **NOTIFICATIONS** | |
| GET | `/api/notifications` | Mis notificaciones |
| POST | `/api/notifications/{id}/read` | Marcar como leída |
| **SYNC** | |
| POST | `/api/sync/push` | Push operaciones pendientes (offline) |
| GET | `/api/sync/pull?since=timestamp` | Pull cambios desde timestamp |

Ver [API.md](docs/API.md) para detalles completos.

## 🏗️ Arquitectura Flutter

**Clean Architecture + Provider/Riverpod**:
- `core/`: Constantes, HTTP client con interceptores, conectividad, utils
- `data/`: API datasource + SQLite repository
- `domain/`: Entidades, repositorios abstractos, casos de uso
- `presentation/`: Pantallas, providers, widgets reutilizables

**Offline Sync**:
- SQLite local con tablas: `profiles`, `events`, `banners`, `notifications`, `referrals`, `role_modules`, `pending_operations`
- Al abrir: pull cambios → push operaciones pendientes
- Sin red: lectura desde SQLite; escritura en `pending_operations`

## 🔧 Stack Técnico

### Backend
- **Framework**: Laravel 11
- **Auth**: Laravel Sanctum (tokens JWT-like)
- **RBAC**: spatie/laravel-permission (roles + permissions)
- **DB**: MySQL (online) + migrations con spatial indexes
- **Push**: Firebase Cloud Messaging (kreait/laravel-firebase)
- **Queue**: sync (MVP) → database/redis (producción)
- **Tests**: Pest

### Mobile
- **Framework**: Flutter 3.16+
- **State**: Provider o Riverpod (elige UNO)
- **Offline**: sqflite + path + repositories
- **HTTP**: dio con interceptores
- **Auth**: flutter_secure_storage
- **Push**: firebase_messaging + flutter_local_notifications
- **Geo**: geolocator
- **WebView**: webview_flutter
- **Share**: share_plus

### Admin Web
- **Template**: Blade + Bootstrap 5
- **Grafo**: vis-network (JSON endpoint)

## 🌐 Hosting Hostinger (Subdominios)

```
jairohortua.com
├── API: app.jairohortua.com     (Laravel API)
├── Admin: admin.jairohortua.com (Laravel Blade)
└── (App mobile apunta a app.jairohortua.com/api)
```

Ver [DEPLOY_HOSTINGER.md](docs/DEPLOY_HOSTINGER.md) para setup.

## 📋 Plan de Implementación

1. ✅ Bootstrap monorepo + README + licencias
2. ⏳ Laravel: setup + Sanctum + spatie + migraciones
3. ⏳ API endpoints: auth + users + events + notifications + sync
4. ⏳ Admin Blade: CRUD base + grafo referidos
5. ⏳ FCM + notificaciones por proximidad
6. ⏳ Flutter: arquitectura + login + offline sync
7. ⏳ Flutter: UI + features (dashboard, eventos, banner, geo, redes, compartir)
8. ⏳ Tests + documentación final

## 📜 Licencia

- **Código**: MIT (ver [LICENSE](LICENSE))
- **Documentación**: CC BY 4.0

---

**Mantenedor**: Jairo Hortúa  
**Última actualización**: 20 de diciembre de 2025  
**Estado**: MVP en desarrollo
