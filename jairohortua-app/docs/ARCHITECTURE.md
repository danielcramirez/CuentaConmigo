# Arquitectura - Jairohortua App MVP

## Visión General

MVP multiplataforma con **offline-first** y **sync bidireccional**. La app funciona sin internet y sincroniza automáticamente cambios locales y remotos.

```
┌─────────────────────────────────────────────────────────────────┐
│                     FLUTTER APP (iOS/Android)                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Presentation Layer (UI, Screens, Providers)              │   │
│  │  ├─ LoginScreen (persistente con token)                  │   │
│  │  ├─ DashboardScreen (dinámico por rol)                   │   │
│  │  ├─ EventsScreen, BannerWidget, NotificationsScreen      │   │
│  │  ├─ ReferralsScreen (código + enlace), LocationScreen    │   │
│  │  └─ SocialMediaWidget (WebView)                          │   │
│  └──────────────────────────────────────────────────────────┘   │
│            ↓                                    ↓                 │
│  ┌──────────────────┐          ┌────────────────────────────┐   │
│  │  Domain Layer    │          │  Data Layer                │   │
│  │ (Entities, Repos)│          │ ┌──────────────────────┐   │   │
│  ├──────────────────┤          │ │ API Datasource (Dio)│   │   │
│  │ User, Event,     │          │ │ ├─ Auth endpoints   │   │   │
│  │ Banner, Profile, │──────→   │ │ ├─ Sync endpoints   │   │   │
│  │ Notification,    │          │ │ └─ CRUD ops         │   │   │
│  │ Referral         │          │ └──────────────────────┘   │   │
│  └──────────────────┘          │ ┌──────────────────────┐   │   │
│                                │ │ SQLite Repository    │   │   │
│  Core Layer:                   │ ├─ profiles, events   │   │   │
│  ├─ Conectividad              │ ├─ notifications      │   │   │
│  ├─ HTTP Client (interceptor) │ ├─ referrals         │   │   │
│  ├─ Token Manager             │ ├─ banners           │   │   │
│  ├─ Constantes, Utils         │ └─ pending_operations│   │   │
│  └─ Geolocalización           └────────────────────────┘   │   │
│                                                              │   │
└─────────────────────────────────────────────────────────────────┘
        ↕ (REST API + FCM)
        
┌─────────────────────────────────────────────────────────────────┐
│              LARAVEL BACKEND (API + Admin Web)                   │
│                                                                   │
│  Authentication & Authorization                                  │
│  ├─ Sanctum (token rotation: /auth/refresh)                     │
│  └─ RBAC: spatie/laravel-permission (4 roles)                   │
│                                                                   │
│  API Controllers                                                  │
│  ├─ AuthController (/auth/login, /logout, /refresh)             │
│  ├─ UserController (/users/me, /dashboard, /referrals)          │
│  ├─ EventController (/events, /events/{id})                     │
│  ├─ BannerController (/banners/active)                          │
│  ├─ LocationController (/location) → user_locations (SPATIAL)   │
│  ├─ NotificationController (/notifications, /{id}/read)         │
│  └─ SyncController (/sync/push, /sync/pull)                     │
│                                                                   │
│  Admin Blade Views                                                │
│  ├─ Usuarios (CRUD, roles)                                       │
│  ├─ Roles & Módulos                                             │
│  ├─ Eventos (CRUD + notificación geográfica)                    │
│  ├─ Banners (CRUD)                                              │
│  ├─ Settings (notification_radius_km, days_window)              │
│  ├─ Notificaciones (historial enviado/recibido)                 │
│  └─ Referidos (grafo vis-network)                               │
│                                                                   │
│  Background Jobs & Services                                      │
│  ├─ SendEventNotificationsJob                                   │
│  │  └─ Query: usuarios en radio R y últimos N días              │
│  │     ST_Distance_Sphere(location, event_point) <= R*1000      │
│  │     created_at >= NOW() - INTERVAL N DAY                     │
│  └─ FCM via kreait/laravel-firebase                             │
│                                                                   │
│  Database (MySQL)                                                │
│  ├─ users (username, password_hash, referral_code)              │
│  ├─ profiles (bio, avatar, phone)                               │
│  ├─ user_locations (POINT SRID 4326, SPATIAL INDEX)             │
│  ├─ events (title, image, coords, starts_at)                    │
│  ├─ banners (image, target_url, is_active)                      │
│  ├─ notifications (user_id, title, message, read_at)            │
│  ├─ referrals (referrer_id, referred_id, status)                │
│  ├─ device_tokens (user_id, token, platform, last_seen_at)      │
│  ├─ roles, permissions (spatie)                                 │
│  ├─ modules (id, name, key, description)                        │
│  ├─ role_modules (role_id, module_id)                           │
│  └─ settings (key, value)                                       │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Flujos Clave

### 1️⃣ Autenticación (First Login)

```
USER INPUT (username/password)
         ↓
   POST /api/auth/login
         ↓
   Laravel hashes, verifica credenciales
         ↓
   Sanctum genera access_token (1440 min default)
         ↓
   Response: {access_token, token_type, user, roles, modules}
         ↓
   Flutter: flutter_secure_storage.write('access_token', token)
         ↓
   Flutter: guarda usuario en SQLite local
         ↓
   Next opens: sin pedir credenciales (token en memoria)
```

### 2️⃣ Token Rotation (POST /api/auth/refresh)

```
Token próximo a expirar (ej. < 10 min)
         ↓
   POST /api/auth/refresh con token actual
         ↓
   Laravel:
   ├─ Valida token actual (no expirado aún)
   ├─ Crea nuevo token
   ├─ Revoca anterior (delete en DB o lista negra)
   └─ Responde: {new_access_token, ...}
         ↓
   Flutter: actualiza secure storage
         ↓
   Dio interceptor maneja reintentos con nuevo token
```

### 3️⃣ Offline Sync (Sin Internet)

```
App abierta, sin conexión
         ↓
   Cualquier CRUD (evento local, notificación leída, etc.)
         ↓
   INSERT en tabla "pending_operations":
   {id, user_id, op_type, payload_json, status='pending'}
         ↓
   UI muestra dato localmente (optimistic)
         ↓
   Conectividad detecta conexión restaurada
         ↓
   Trigger automático: POST /api/sync/push
         ↓
   [{op_type, payload, client_uuid}, ...]
         ↓
   Laravel aplica cada operación (idempotente con client_uuid)
         ↓
   Response: [{id, status='applied', ...}, ...]
         ↓
   Flutter: DELETE pending_operations confirmadas
         ↓
   Trigger: GET /api/sync/pull?since=last_sync_at
         ↓
   Cambios remotos (eventos nuevos, banner actualizado, etc.)
         ↓
   UPDATE SQLite (tablas: events, banners, notifications, etc.)
```

### 4️⃣ Notificación por Proximidad (Event Creation)

```
Admin crea evento en Blade:
  title, image, lat, lng, starts_at
  (radio_km y days_window opcionales, sino usar settings)
         ↓
   POST /admin/events → EventController@store
         ↓
   Laravel:
   ├─ Determina R (radio_km) y N (days_window)
   ├─ Query usuarios elegibles:
   │  SELECT DISTINCT u.id, u.device_tokens
   │  FROM users u
   │  JOIN user_locations ul ON ul.user_id = u.id
   │  WHERE ST_Distance_Sphere(ul.location, event_point) <= R*1000
   │    AND ul.created_at >= NOW() - INTERVAL N DAY
   │    AND u.id != event.created_by
   ├─ Dispatch SendEventNotificationsJob (sync MVP)
   └─ Job: envía FCM a cada device_token
         ↓
   FCM entrega notificación al device
         ↓
   Flutter: firebase_messaging + flutter_local_notifications
         ↓
   INSERT en notifications table (SQLite + servidor)
         ↓
   Usuario ve en NotificationsScreen
```

### 5️⃣ Geolocalización al Abrir App

```
App init (splash screen)
         ↓
   Request permiso: geolocator.requestPermission()
         ↓
   Si permiso concedido:
   ├─ geolocator.getCurrentPosition()
   ├─ POST /api/location {lat, lng, accuracy, timestamp}
   ├─ Laravel: INSERT user_locations (con timestamp cliente validado)
   └─ Responde: {id, created_at}
         ↓
   Si permiso denegado:
   ├─ Continúa sin ubicación
   └─ Mock location si needed para testing
         ↓
   (No se pide de nuevo en esta sesión; máximo 1x al abrir)
```

### 6️⃣ Referidos & Grafo

```
USUARIO A abre app:
  ├─ Genera referral_code único (ej. "JAIRO123ABC")
  ├─ Pantalla "Invitar": código visible
  ├─ share_plus: "Únete a mi red: app.jairohortua.com/invite?code=JAIRO123ABC"
         ↓
USUARIO B recibe enlace, abre app en mobile/web:
  ├─ Deep link a /invite?code=JAIRO123ABC
  ├─ Si no logueado: login/registro
  ├─ Aplica code al perfil
         ↓
BACKEND:
  ├─ Valida code pertenece a usuario A
  ├─ INSERT referrals (referrer_id=A, referred_id=B)
  ├─ UPDATE users B con referrer_id (si guarda relación)
         ↓
ADMIN:
  ├─ GET /admin/referrals/graph-data
  ├─ Retorna: {nodes: [{id, label, size}], edges: [{from, to}]}
  ├─ vis-network renderiza grafo interactivo
```

### 7️⃣ Banner Dinámico

```
Admin crea/edita banner:
  ├─ image_url, target_url, order, is_active
         ↓
   Automático en GET /api/banners/active:
  ├─ SELECT * FROM banners WHERE is_active=1 ORDER BY order
  ├─ LIMIT 1 (o implementar rotación)
         ↓
   Flutter:
  ├─ Fetch en login/sync
  ├─ Cache en SQLite 24h
  ├─ Mostrar en DashboardScreen
  ├─ Tap → launch(target_url) con url_launcher
```

## Decisiones de Diseño

| Aspecto | Decisión | Rationale |
|---------|----------|-----------|
| **Auth** | Sanctum (tokens JWT-like) | Nativa en Laravel, compatible con mobile |
| **RBAC** | spatie/laravel-permission | Estándar, flexible, maduro |
| **Módulos** | Tabla `modules` + pivot `role_modules` | Dashboard dinámico sin hardcodear |
| **Offline** | SQLite + tabla `pending_operations` | Ligero, cross-platform, sin DB remota |
| **Sync** | Pull + Push (MVP) | Simple, no requiere WebSockets |
| **Geo-query** | ST_Distance_Sphere (MySQL) | Preciso para radios 10-50 km, índice spatial |
| **Push** | Firebase (kreait) | Gratuito, multiplataforma, confiable |
| **Token Rotation** | POST /refresh genera nuevo + revoca | Seguridad: expiración + rotación activa |
| **Roles** | 4 roles base (SuperAdmin, Candidato, Líder, Usuario) | Escalable, RBAC explícito |

## Patrones de Código

### Flutter: Repositorio + Caso de Uso

```dart
// domain/repositories/user_repository.dart
abstract class UserRepository {
  Future<User> getMe();
  Future<Dashboard> getDashboard();
}

// data/repositories/user_repository_impl.dart
class UserRepositoryImpl implements UserRepository {
  final ApiDataSource apiDataSource;
  final LocalDataSource localDataSource;

  @override
  Future<User> getMe() async {
    try {
      return await apiDataSource.getMe();
    } catch (e) {
      return await localDataSource.getProfile();
    }
  }
}

// domain/usecases/get_user_me_usecase.dart
class GetUserMeUseCase {
  final UserRepository repository;
  
  Future<User> call() => repository.getMe();
}
```

### Laravel: Resource + Controlador

```php
// app/Http/Controllers/Api/UserController.php
class UserController extends Controller {
    public function me(): UserResource {
        return UserResource::make(auth()->user());
    }
    
    public function dashboard(): DashboardResource {
        $modules = auth()->user()->modules()->pluck('key');
        return new DashboardResource(['modules' => $modules]);
    }
}

// app/Http/Resources/UserResource.php
class UserResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'roles' => $this->roles->pluck('name'),
            'modules' => $this->modules->pluck('key'),
        ];
    }
}
```

## Flujo de Push (FCM)

```
Event creado en admin
    ↓
Laravel: Job SendEventNotificationsJob encolado
    ↓
Job ejecuta (sync MVP):
  ├─ Query usuarios en rango geográfico/temporal
  ├─ Itera device_tokens
  ├─ firebase_admin.messaging.send({token, title, body, data})
    ↓
Firebase Cloud Messaging:
  ├─ Entrega a APNs (iOS) o GCM (Android)
    ↓
App Flutter:
  ├─ firebase_messaging.onMessage (foreground)
  ├─ firebase_messaging.onMessageOpenedApp (background/terminated)
  ├─ flutter_local_notifications renderiza badge/sound
    ↓
User toca notificación:
  ├─ Deep link a /events/{id}
  ├─ Mark read: POST /api/notifications/{id}/read
```

## Seguridad

1. **Token Storage**: flutter_secure_storage (no SharedPreferences)
2. **Rate Limiting**: Laravel por IP en /auth/login
3. **HTTPS**: Obligatorio en producción (Hostinger SSL)
4. **CORS**: Sincronizado entre admin.jairohortua.com y app.jairohortua.com
5. **CSRF**: Sanctum maneja; Blade tradicional con token
6. **RBAC**: Verificar permiso en cada endpoint + vista Blade

---

**Última actualización**: 20 de diciembre de 2025
