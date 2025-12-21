# API Endpoints - Jairohortua App MVP

## Base URL
- **Development**: `http://localhost:8000/api`
- **Production**: `https://app.jairohortua.com/api`

## Autenticación

Todos los endpoints excepto `/auth/login` requieren:
```
Authorization: Bearer {access_token}
```

---

## 🔐 AUTH

### POST /auth/login
**Descripción**: Autentica usuario y retorna token.

**Request**:
```json
{
  "username": "string",
  "password": "string"
}
```

**Response** (200):
```json
{
  "access_token": "1|abc123xyz...",
  "token_type": "Bearer",
  "expires_in": 86400,
  "user": {
    "id": 1,
    "username": "jairo",
    "email": "jairo@example.com",
    "referral_code": "JAIRO123ABC"
  },
  "roles": ["SuperAdmin"],
  "modules": ["users", "events", "settings"]
}
```

**Errores**:
- `401`: Invalid credentials
- `429`: Rate limited

---

### POST /auth/logout
**Descripción**: Revoca token actual.

**Request**: (vacío, usa Authorization header)

**Response** (200):
```json
{
  "message": "Logged out successfully"
}
```

---

### POST /auth/refresh
**Descripción**: Rota token (crea nuevo, revoca anterior).

**Request**:
```
Authorization: Bearer {access_token}
```

**Response** (200):
```json
{
  "access_token": "2|def456uvw...",
  "token_type": "Bearer",
  "expires_in": 86400,
  "message": "Token refreshed"
}
```

**Errores**:
- `401`: Token expired or invalid

---

## 👤 USERS

### GET /users/me
**Descripción**: Obtiene perfil del usuario autenticado.

**Response** (200):
```json
{
  "id": 1,
  "username": "jairo",
  "email": "jairo@example.com",
  "referral_code": "JAIRO123ABC",
  "roles": ["SuperAdmin"],
  "modules": ["users", "events", "settings"],
  "profile": {
    "id": 1,
    "user_id": 1,
    "avatar_url": "https://...",
    "bio": "Co-founder"
  },
  "updated_at": "2025-12-20T10:30:00Z"
}
```

---

### GET /users/me/dashboard
**Descripción**: Dashboard dinámico según rol/módulos.

**Response** (200):
```json
{
  "modules": [
    {
      "id": 1,
      "key": "events",
      "name": "Eventos",
      "description": "Gestionar eventos",
      "icon": "calendar"
    },
    {
      "id": 2,
      "key": "notifications",
      "name": "Notificaciones",
      "description": "Centro de notificaciones",
      "icon": "bell"
    }
  ],
  "navigation": [
    {
      "label": "Mis Eventos",
      "route": "/events",
      "icon": "calendar"
    },
    {
      "label": "Invitar",
      "route": "/referrals",
      "icon": "share"
    }
  ],
  "stats": {
    "events_count": 5,
    "notifications_count": 12
  }
}
```

---

### GET /users/{id}/referrals
**Descripción**: Grafo de referidos del usuario (árbol de invitaciones).

**Query Params**:
- `depth` (int, default 3): Profundidad del árbol

**Response** (200):
```json
{
  "user_id": 1,
  "referral_code": "JAIRO123ABC",
  "tree": {
    "id": 1,
    "username": "jairo",
    "children": [
      {
        "id": 2,
        "username": "user2",
        "status": "active",
        "children": []
      }
    ]
  },
  "stats": {
    "total_referred": 1,
    "active_referred": 1
  }
}
```

---

## 📍 LOCATION

### POST /location
**Descripción**: Registra ubicación del usuario (1x al abrir app).

**Request**:
```json
{
  "latitude": 40.7128,
  "longitude": -74.0060,
  "accuracy": 20.5,
  "timestamp": "2025-12-20T10:30:00Z"
}
```

**Response** (201):
```json
{
  "id": 1,
  "user_id": 1,
  "latitude": 40.7128,
  "longitude": -74.0060,
  "created_at": "2025-12-20T10:30:00Z"
}
```

**Errores**:
- `422`: Invalid coordinates

---

## 📅 EVENTS

### GET /events
**Descripción**: Lista eventos (con filtros opcionales).

**Query Params**:
- `latitude` (float): Mi latitud (para filtrar por proximidad)
- `longitude` (float): Mi longitud
- `radius_km` (float, default 50): Radio en km
- `limit` (int, default 50): Límite de resultados
- `offset` (int, default 0): Para paginación

**Response** (200):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Networking Startup",
      "description": "Encuentro de emprendedores",
      "image_url": "https://...",
      "latitude": 40.7128,
      "longitude": -74.0060,
      "starts_at": "2025-12-25T18:00:00Z",
      "distance_km": 2.3,
      "created_at": "2025-12-20T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 15,
    "limit": 50,
    "offset": 0
  }
}
```

---

### GET /events/{id}
**Descripción**: Detalle de un evento.

**Response** (200):
```json
{
  "id": 1,
  "title": "Networking Startup",
  "description": "Encuentro de emprendedores",
  "image_url": "https://...",
  "latitude": 40.7128,
  "longitude": -74.0060,
  "starts_at": "2025-12-25T18:00:00Z",
  "created_by": {
    "id": 1,
    "username": "admin"
  },
  "created_at": "2025-12-20T10:30:00Z",
  "updated_at": "2025-12-20T10:30:00Z"
}
```

---

## 🎨 BANNERS

### GET /banners/active
**Descripción**: Obtiene banner activo (más reciente por order).

**Response** (200):
```json
{
  "id": 1,
  "image_url": "https://banner.jpg",
  "target_url": "https://example.com/offer",
  "order": 1,
  "is_active": true,
  "updated_at": "2025-12-20T10:30:00Z"
}
```

**Response** (204): Si no hay banner activo.

---

## 🔔 NOTIFICATIONS

### GET /notifications
**Descripción**: Mis notificaciones (historial).

**Query Params**:
- `read` (bool): Filtrar leídas/no leídas
- `limit` (int, default 50)
- `offset` (int, default 0)

**Response** (200):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Nuevo evento cerca",
      "message": "Hay un evento a 2 km de tu ubicación",
      "type": "event_proximity",
      "read_at": null,
      "created_at": "2025-12-20T10:30:00Z"
    }
  ],
  "unread_count": 3,
  "pagination": {
    "total": 15,
    "limit": 50,
    "offset": 0
  }
}
```

---

### POST /notifications/{id}/read
**Descripción**: Marca notificación como leída.

**Request**: (vacío)

**Response** (200):
```json
{
  "id": 1,
  "read_at": "2025-12-20T10:35:00Z"
}
```

---

## 🔄 SYNC (Offline)

### POST /sync/push
**Descripción**: Sincroniza operaciones pendientes desde mobile.

**Request**:
```json
{
  "operations": [
    {
      "client_uuid": "550e8400-e29b-41d4-a716-446655440000",
      "op_type": "create_event",
      "payload": {
        "title": "Mi evento local",
        "description": "...",
        "latitude": 40.7128,
        "longitude": -74.0060
      }
    },
    {
      "client_uuid": "550e8400-e29b-41d4-a716-446655440001",
      "op_type": "mark_notification_read",
      "payload": {
        "notification_id": 1
      }
    }
  ]
}
```

**Response** (200):
```json
{
  "results": [
    {
      "client_uuid": "550e8400-e29b-41d4-a716-446655440000",
      "status": "applied",
      "server_id": 5,
      "message": "Event created"
    },
    {
      "client_uuid": "550e8400-e29b-41d4-a716-446655440001",
      "status": "applied",
      "message": "Notification marked as read"
    }
  ],
  "server_time": "2025-12-20T10:35:00Z"
}
```

**Errores**:
- `422`: Validation errors (retorna por operación)

---

### GET /sync/pull?since={timestamp}
**Descripción**: Obtiene cambios desde un timestamp.

**Query Params**:
- `since` (ISO 8601): Timestamp desde el cual traer cambios (default: 24 horas atrás)

**Response** (200):
```json
{
  "server_time": "2025-12-20T10:35:00Z",
  "changes": {
    "events": [
      {
        "id": 1,
        "title": "Nuevo evento",
        "action": "created",
        "updated_at": "2025-12-20T10:32:00Z"
      }
    ],
    "banners": [
      {
        "id": 1,
        "is_active": true,
        "action": "updated",
        "updated_at": "2025-12-20T10:33:00Z"
      }
    ],
    "notifications": [
      {
        "id": 2,
        "title": "Evento próximo",
        "action": "created",
        "updated_at": "2025-12-20T10:34:00Z"
      }
    ],
    "referrals": [],
    "modules": [],
    "settings": {
      "notification_radius_km": 20,
      "notification_days_window": 15
    }
  }
}
```

---

## ❌ Error Responses

Todos los errores siguen formato:

```json
{
  "message": "Error message",
  "status": 400,
  "errors": {
    "field": ["Error detail"]
  }
}
```

| Status | Causa |
|--------|-------|
| `400` | Bad request (validación) |
| `401` | Unauthorized (sin token o inválido) |
| `403` | Forbidden (sin permiso) |
| `404` | Not found |
| `422` | Unprocessable entity (validación) |
| `429` | Too many requests (rate limit) |
| `500` | Server error |

---

## Rate Limiting

- **POST /auth/login**: 5 intentos / 15 minutos por IP
- Otros endpoints: 100 requests / 1 minuto por usuario

---

## Ejemplos con cURL

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Get profile (con token)
curl -X GET http://localhost:8000/api/users/me \
  -H "Authorization: Bearer 1|abc123xyz..."

# Refresh token
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Authorization: Bearer 1|abc123xyz..."

# Post location
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{
    "latitude": 40.7128,
    "longitude": -74.0060,
    "accuracy": 20.5,
    "timestamp": "2025-12-20T10:30:00Z"
  }'

# Sync pull
curl -X GET "http://localhost:8000/api/sync/pull?since=2025-12-19T10:30:00Z" \
  -H "Authorization: Bearer 1|abc123xyz..."

# Sync push
curl -X POST http://localhost:8000/api/sync/push \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{
    "operations": [
      {
        "client_uuid": "550e8400-e29b-41d4-a716-446655440000",
        "op_type": "create_event",
        "payload": {"title":"Mi evento","latitude":40.7128,"longitude":-74.0060}
      }
    ]
  }'
```

---

**Última actualización**: 20 de diciembre de 2025
