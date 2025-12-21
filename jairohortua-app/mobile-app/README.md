# Mobile App - Jairohortua MVP (Flutter)

App multiplataforma (iOS/Android) con **offline-first** y **sync bidireccional**.

## 🎯 Características

- ✅ Login persistente (token rotado con `/auth/refresh`)
- ✅ Offline-first con SQLite local
- ✅ Sync automático al recuperar conexión
- ✅ Geolocalización (una vez al abrir)
- ✅ Dashboard dinámico por rol
- ✅ Eventos, banners, notificaciones push (FCM)
- ✅ Referidos con código compartible
- ✅ Widget redes sociales (WebView)
- ✅ Compartir contenido (share_plus)

## 🚀 Inicio Rápido

### Requisitos
- Flutter 3.16+
- Dart 3.0+
- Android Studio o Xcode
- Firebase project configurado (FCM)

### Instalación

```bash
cd mobile-app

# 1. Instalar dependencias
flutter pub get

# 2. Ejecutar en emulador/dispositivo
flutter run

# 3. (Opcional) Generar código
flutter pub run build_runner build --delete-conflicting-outputs
```

## 📁 Arquitectura

**Clean Architecture** con Provider:

```
lib/
├── core/
│   ├── constants/      # AppConstants, URLs, keys
│   ├── network/        # HttpClient con interceptores Dio
│   ├── services/       # TokenManager, ConnectivityService
│   └── utils/          # Router, helpers
├── data/
│   ├── datasources/    # API + SQLite
│   └── models/         # Data models (JSON mapping)
├── domain/
│   ├── entities/       # User, Event, Banner, Notification
│   ├── repositories/   # Interfaces abstractas
│   └── usecases/       # Lógica de negocio
└── presentation/
    ├── screens/        # LoginScreen, HomeScreen, EventsScreen, etc.
    ├── providers/      # State management (Provider/Riverpod)
    └── widgets/        # Componentes reutilizables
```

## 🔑 Flujo de Autenticación

```
1. Usuario abre app → SplashScreen
2. Verificar TokenManager.hasToken()
   - Si existe token: navegar a /home (validar en background)
   - Si NO existe: navegar a /login
3. Usuario ingresa username/password
4. POST /api/auth/login → guardar token en flutter_secure_storage
5. Navegar a /home
6. (Background) Token rotation cada 10 min antes de expirar
```

## 📦 Offline Sync (SQLite)

### Tablas locales
- `profiles`
- `events` (últimos 50)
- `banners` (banner activo)
- `notifications` (historial)
- `referrals`
- `role_modules`
- `pending_operations`

### Flujo de sync

```dart
// Al abrir app (con conexión)
1. GET /api/sync/pull?since=last_sync_at
   └─ Actualizar tablas locales

2. POST /api/sync/push con pending_operations
   └─ Aplicar cambios en servidor
   └─ Eliminar operaciones confirmadas

// Sin conexión
1. Leer desde SQLite
2. Guardar cambios en pending_operations
3. UI optimista (mostrar cambio inmediatamente)
```

## 🔧 Configuración

### `lib/core/constants/app_constants.dart`

```dart
static const String baseUrl = 'http://localhost:8000/api';
static const String baseUrlProduction = 'https://app.jairohortua.com/api';
```

### Firebase (FCM)

1. Descargar `google-services.json` (Android) y `GoogleService-Info.plist` (iOS)
2. Colocar en:
   - Android: `android/app/google-services.json`
   - iOS: `ios/Runner/GoogleService-Info.plist`

3. Inicializar en `main.dart`:
```dart
await Firebase.initializeApp();
final fcmToken = await FirebaseMessaging.instance.getToken();
// Enviar fcmToken al backend
```

## 🌐 Endpoints Usados

Ver [API.md](../docs/API.md) para documentación completa.

| Endpoint | Descripción |
|----------|-------------|
| `POST /auth/login` | Login |
| `POST /auth/refresh` | Refresh token |
| `GET /users/me` | Perfil actual |
| `GET /users/me/dashboard` | Módulos por rol |
| `POST /location` | Registrar ubicación |
| `GET /events` | Listar eventos |
| `GET /banners/active` | Banner vigente |
| `GET /notifications` | Mis notificaciones |
| `POST /sync/push` | Sync pendientes |
| `GET /sync/pull?since=` | Traer cambios |

## 🧪 Testing

```bash
# Tests unitarios
flutter test

# Tests de integración
flutter test integration_test/
```

## 🔒 Seguridad

- **Token storage**: flutter_secure_storage (no SharedPreferences)
- **Interceptor**: Añade `Authorization: Bearer {token}` automáticamente
- **Token rotation**: Detecta 401, llama a `/auth/refresh`, reintenta request
- **HTTPS**: Obligatorio en producción

## 📱 Build (Producción)

### Android (APK)

```bash
flutter build apk --release
# Output: build/app/outputs/flutter-apk/app-release.apk
```

### iOS (IPA)

```bash
flutter build ios --release
# Luego abrir en Xcode para firmar y distribuir
```

## 🐛 Troubleshooting

### "Dio connection timeout"
- Verificar `baseUrl` en `AppConstants`
- Verificar servidor backend está corriendo

### "Token expired"
- El interceptor debería manejar automáticamente con `/auth/refresh`
- Verificar logs de Dio

### "SQLite error"
- Reinstalar app (limpia DB local)
- Verificar migraciones en `database_helper.dart`

## 📚 Dependencias Principales

```yaml
provider: ^6.0.0             # State management
dio: ^5.3.0                  # HTTP client
sqflite: ^2.3.0              # Offline DB
flutter_secure_storage: ^9.0.0  # Token storage
geolocator: ^9.0.0           # Geolocalización
firebase_messaging: ^14.7.0  # FCM push
share_plus: ^7.1.0           # Compartir
webview_flutter: ^4.4.0      # Redes sociales
connectivity_plus: ^5.0.0    # Detectar conexión
```

## 🗺️ Roadmap

- [x] Login persistente
- [x] Arquitectura base (Clean)
- [ ] SQLite database helper
- [ ] Repositorios (API + SQLite)
- [ ] Providers (AuthProvider, EventProvider, etc.)
- [ ] Dashboard dinámico con módulos
- [ ] Pantallas de eventos, notificaciones, referidos
- [ ] Geolocalización al abrir
- [ ] Sync engine completo
- [ ] Firebase Cloud Messaging
- [ ] Widget redes sociales
- [ ] Share con share_plus

## 📜 Licencia

MIT - Ver [LICENSE](../LICENSE)

---

**Última actualización**: 20 de diciembre de 2025
