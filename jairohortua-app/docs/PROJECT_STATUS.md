# Estado del Proyecto - Jairohortua App MVP

**Fecha**: 20 de diciembre de 2025  
**Fase**: MVP en desarrollo (Estructura base completada)

## ✅ Completado

### 1. Estructura Monorepo
- [x] Directorios `/jairohortua-app/mobile-app` y `/admin-web`
- [x] Documentación: README.md, LICENSE, ARCHITECTURE.md, API.md, DEPLOY_HOSTINGER.md

### 2. Backend Laravel (API + Admin)
- [x] Composer.json con dependencias (Sanctum, spatie, firebase)
- [x] Migraciones completas (12 tablas)
  - users, profiles, user_locations (con SPATIAL INDEX), events, banners
  - notifications, referrals, device_tokens, modules, role_modules
  - settings, event_notification_batches
- [x] Seeders (roles, módulos, settings, admin user)
- [x] Models con relaciones (User, Event, Banner, Notification, etc.)
- [x] Controllers API completos:
  - AuthController (login, refresh, logout)
  - UserController (me, dashboard, referrals)
  - EventController, LocationController, BannerController
  - NotificationController, SyncController
- [x] Rutas API (routes/api.php)
- [x] Resources (UserResource, DashboardResource)
- [x] Job: SendEventNotificationsJob (con query geográfica)
- [x] Tests con Pest (AuthTest, LocationTest, SyncTest)
- [x] README con instrucciones de instalación

### 3. Flutter Mobile App
- [x] pubspec.yaml con dependencias completas
- [x] Arquitectura Clean (core, data, domain, presentation)
- [x] Core:
  - AppConstants (URLs, keys, timeouts)
  - HttpClient con interceptores Dio (token refresh automático)
  - TokenManager (flutter_secure_storage)
  - ConnectivityService (detectar online/offline)
  - Router (navegación)
- [x] Domain:
  - Entities: User, Event, Banner, Notification
- [x] Presentation:
  - LoginScreen (formulario básico)
  - HomeScreen (con tabs: Dashboard, Events, Notifications, Profile)
  - main.dart con SplashScreen
- [x] README con instrucciones de instalación

## ⏳ Pendiente (Próximas fases)

### Laravel Backend

#### Admin Blade (Panel Web)
- [ ] Layout base (Bootstrap 5)
- [ ] CRUD usuarios (index, create, edit, delete)
- [ ] CRUD roles y módulos
- [ ] CRUD eventos (con campos de notificación)
- [ ] CRUD banners (upload de imágenes)
- [ ] CRUD settings (key/value editor)
- [ ] Historial de notificaciones
- [ ] Grafo de referidos (vis-network)

#### Firebase Cloud Messaging (FCM)
- [ ] Configurar Firebase credentials en .env
- [ ] Implementar envío FCM en SendEventNotificationsJob
- [ ] Endpoint para registrar device tokens (POST /api/device-tokens)
- [ ] Tests para FCM

#### Queue System
- [ ] Migrar de `sync` a `database` o `redis` queue
- [ ] Configurar Supervisor para queue:work
- [ ] Cron jobs (Laravel Scheduler)

### Flutter Mobile App

#### Offline Sync
- [ ] DatabaseHelper (SQLite)
  - Tablas: profiles, events, banners, notifications, referrals, pending_operations
- [ ] Repositories (implementación de interfaces)
  - ApiDataSource (Dio)
  - LocalDataSource (SQLite)
  - Lógica offline fallback
- [ ] SyncEngine (push/pull automático)
- [ ] Detectar reconexión y sync automático

#### Providers (State Management)
- [ ] AuthProvider (login, logout, refresh)
- [ ] UserProvider (perfil, dashboard)
- [ ] EventProvider (listar, detalle, CRUD)
- [ ] NotificationProvider (listar, mark read)
- [ ] BannerProvider (obtener activo)
- [ ] SyncProvider (push/pull)

#### Pantallas & Features
- [ ] Login funcional (llamar API)
- [ ] Dashboard dinámico (mostrar módulos por rol)
- [ ] Pantalla de eventos:
  - Listar eventos
  - Detalle evento
  - Compartir evento
  - Filtro por proximidad
- [ ] Pantalla de notificaciones:
  - Listar notificaciones
  - Marcar como leída
  - Badge con contador
- [ ] Pantalla de referidos:
  - Mostrar código propio
  - Generar enlace de invitación
  - Compartir con share_plus
  - Ver árbol (opcional: visualización gráfica)
- [ ] Pantalla de perfil:
  - Editar bio, avatar, teléfono
  - Logout
- [ ] Banner widget (carrusel o single)
- [ ] Widget redes sociales (WebView con URLs configurables)
- [ ] Geolocalización al abrir app (una vez)

#### Firebase Cloud Messaging
- [ ] Configurar google-services.json / GoogleService-Info.plist
- [ ] Inicializar Firebase en main.dart
- [ ] Registrar token FCM y enviar a backend
- [ ] Mostrar notificaciones foreground/background
- [ ] Deep links (navegar a eventos desde notificación)

## 🧪 Testing

### Backend (Completado)
- [x] AuthTest (login, refresh, logout)
- [x] LocationTest (POST /location, validación)
- [x] SyncTest (push, pull)

### Backend (Pendiente)
- [ ] EventTest (CRUD, filtros)
- [ ] NotificationTest (listar, mark read)
- [ ] BannerTest (obtener activo)
- [ ] ReferralTest (grafo)

### Frontend (Pendiente)
- [ ] Widget tests (LoginScreen, HomeScreen)
- [ ] Integration tests (flujo completo: login → dashboard → logout)
- [ ] Tests offline/online sync
- [ ] Tests geolocalización

## 📦 Deploy (Pendiente)

- [ ] Configurar subdominios en Hostinger:
  - app.jairohortua.com (API)
  - admin.jairohortua.com (Admin Blade)
- [ ] Configurar MySQL en producción
- [ ] SSL (Let's Encrypt)
- [ ] Storage symlink en servidor
- [ ] Configurar colas (supervisor/cron)
- [ ] Firebase credentials en servidor
- [ ] Publicar APK en Google Play (o beta testing)
- [ ] Publicar IPA en App Store (o TestFlight)

## 📊 Métricas Actuales

- **Líneas de código (aprox)**:
  - Backend: ~3000 líneas (PHP)
  - Frontend: ~1000 líneas (Dart)
- **Archivos creados**: ~70+
- **Endpoints API implementados**: 15+
- **Tablas DB**: 12
- **Pantallas Flutter**: 5 (básicas)

## 🚀 Próximos Pasos Inmediatos (Prioridad)

1. **Backend**: Implementar Admin Blade (CRUD básico para gestionar eventos/banners/usuarios)
2. **Frontend**: Implementar SQLite helper + repositorios (offline-first)
3. **Frontend**: Implementar AuthProvider + login funcional
4. **Testing**: Ejecutar tests backend con `php artisan test`
5. **Testing**: Ejecutar `flutter run` para verificar UI básica

## 💡 Notas Importantes

- **Token rotation** está implementado pero requiere testing en producción
- **Geolocalización spatial queries** usa fallback si MySQL no soporta POINT
- **FCM** tiene estructura base pero requiere credenciales de Firebase
- **Admin Blade** pendiente (prioritario para gestión de contenido)
- **Offline sync** tiene estructura pero requiere implementar SQLite helper

---

## 🛠️ Comandos Útiles

### Backend (Laravel)
```bash
cd admin-web

# Instalar (si composer install no se ha ejecutado)
composer install

# Configurar .env
cp .env.example .env
php artisan key:generate

# Migraciones + seeds
php artisan migrate:fresh --seed

# Tests
php artisan test

# Servidor dev
php artisan serve
```

### Frontend (Flutter)
```bash
cd mobile-app

# Instalar
flutter pub get

# Ejecutar
flutter run

# Tests
flutter test
```

---

**Última actualización**: 20 de diciembre de 2025  
**Autor**: Jairo Hortúa (con Copilot AI)
