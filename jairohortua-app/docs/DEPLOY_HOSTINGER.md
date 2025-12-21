# Deploy en Hostinger - Jairohortua App MVP

## 🌐 Configuración de Subdominios

### Planificación DNS

```
jairohortua.com (MX)
├── app.jairohortua.com       → API Backend (Laravel)
├── admin.jairohortua.com     → Admin Web (Blade)
└── (www.jairohortua.com      → Landing page opcional)
```

### Pasos en Hostinger

1. **cPanel > Addons Domains**
   - Crear dominio: `app.jairohortua.com` (documento_root: `/public_html/app`)
   - Crear dominio: `admin.jairohortua.com` (document_root: `/public_html/admin`)

2. **DNS Manager**
   - A record: `app.jairohortua.com` → IP del servidor
   - A record: `admin.jairohortua.com` → IP del servidor
   - (generalmente automático si usas cPanel)

3. **SSL Certificates** (Let's Encrypt gratuito)
   - cPanel > AutoSSL
   - Activar para todos los dominios

---

## 📦 Estructura en Hosting

```
/public_html/
├── app/                      # API (app.jairohortua.com)
│   ├── public/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── .env                  # Producción
│   ├── storage/ → symlink /public_html/app/storage
│   └── (archivos Laravel)
├── admin/                    # Admin Web (admin.jairohortua.com)
│   ├── public/
│   ├── app/
│   └── (mismo proyecto que /app si es monorepo)
└── (otros dominios/landing pages)
```

**Nota**: En Hostinger, `/public` debe apuntar a `public/` de cada Laravel.

---

## 🚀 Pasos de Deploy (SSH)

### 1. Clonar repositorio

```bash
cd /home/jairohortua/
git clone https://github.com/tu-repo/jairohortua-app.git
cd jairohortua-app/admin-web

# O, si estás en el repo ya:
cd admin-web
```

### 2. Instalar dependencias

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Configurar .env (Producción)

```bash
cp .env.example .env
```

Editar `.env`:

```env
APP_NAME=JairohuortaApp
APP_ENV=production
APP_KEY=                        # php artisan key:generate
APP_DEBUG=false
APP_URL=https://app.jairohortua.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost              # o IP MySQL
DB_PORT=3306
DB_DATABASE=jairohortua_app
DB_USERNAME=jairohortua_user
DB_PASSWORD=StrongPassword123!

# Redis (si tienes queue=redis)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Sanctum (CORS + Stateful)
SESSION_DOMAIN=.jairohortua.com
SANCTUM_STATEFUL_DOMAINS=localhost:3000,admin.jairohortua.com,app.jairohortua.com
SANCTUM_TOKEN_EXPIRATION_MINUTES=1440

# Mail (ajusta según tu proveedor)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com   # o tu SMTP
MAIL_PORT=465
MAIL_USERNAME=noreply@jairohortua.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@jairohortua.com
MAIL_FROM_NAME=JairohuortaApp

# Firebase
FIREBASE_CREDENTIALS=/home/jairohortua/jairohortua-app/admin-web/storage/firebase-credentials.json

# Queue
QUEUE_CONNECTION=database     # MVP; upgrade a redis después

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### 4. Generar APP_KEY

```bash
php artisan key:generate --force
```

### 5. Migraciones + Seeders

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

### 6. Storage Link (para uploads)

```bash
php artisan storage:link

# Verifica que exista symlink:
ls -la public/storage
# Debe apuntar a ../storage/app/public
```

### 7. Caché de config

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Permisos

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
```

---

## 🔧 Configuración Nginx (si usas Nginx en lugar de Apache)

Si Hostinger usa Nginx, crea/edita `/etc/nginx/sites-available/jairohortua`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name app.jairohortua.com;
    root /home/jairohortua/jairohortua-app/admin-web/public;

    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Denegar acceso a sensibles
    location ~ /\. {
        deny all;
    }

    location ~ /storage {
        expires 30d;
    }
}

server {
    listen 80;
    listen [::]:80;
    server_name admin.jairohortua.com;
    root /home/jairohortua/jairohortua-app/admin-web/public;

    # ... mismo config ...
}
```

Luego:
```bash
sudo ln -s /etc/nginx/sites-available/jairohortua /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔒 .htaccess (Apache, si aplica)

Hostinger típicamente usa cPanel con Apache. El .htaccess estándar de Laravel debería estar en `/public`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 🗄️ Base de Datos MySQL

### 1. Crear DB en Hostinger

**cPanel > MySQL Databases**:
- Nombre: `jairohortua_app`
- Usuario: `jairohortua_user`
- Contraseña: (genera fuerte)

### 2. Caracterset UTF8MB4

```bash
# Conectar via phpMyAdmin o SSH:
mysql -u jairohortua_user -p jairohortua_app

# Al crear DB, usar:
CREATE DATABASE jairohortua_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Índices Espaciales

Las migraciones de Laravel automáticamente crean índices con `spatialIndex()`. Si necesitas manualmente:

```sql
ALTER TABLE user_locations ADD SPATIAL INDEX idx_location (location);
```

---

## 🚨 Firebase Credentials

1. **Google Cloud Console**:
   - Crea proyecto "jairohortua-app"
   - Enable Firebase Messaging API
   - Service Account → Key (JSON)

2. **Descargar JSON** y guardar en:
   ```
   /home/jairohortua/jairohortua-app/admin-web/storage/firebase-credentials.json
   ```

3. **En .env**:
   ```
   FIREBASE_CREDENTIALS=/home/jairohortua/jairohortua-app/admin-web/storage/firebase-credentials.json
   ```

4. **Permisos**:
   ```bash
   chmod 600 storage/firebase-credentials.json
   ```

---

## 📧 Colas (Queue) - MVP a Producción

**MVP**: `QUEUE_CONNECTION=sync` (procesa inmediato, ok para testing)

**Producción**: Upgrade a `database` o `redis`:

```env
# Database queue (sin Redis)
QUEUE_CONNECTION=database
```

Luego enquelar worker en crontab:

```bash
* * * * * cd /home/jairohortua/jairohortua-app/admin-web && php artisan schedule:run >> /dev/null 2>&1
```

O usar Supervisor (si Hostinger lo permite):

```bash
# Instalar queue worker como servicio
php artisan queue:work --daemon --sleep=3 --tries=3
```

---

## 🔄 Cron Jobs

**Añadir a cPanel > Cron Jobs** (o vía SSH):

```bash
# Laravel Scheduler
* * * * * cd /home/jairohortua/jairohortua-app/admin-web && php artisan schedule:run >> /dev/null 2>&1

# Backup diario (opcional)
0 2 * * * cd /home/jairohortua && mysqldump -u jairohortua_user -p'password' jairohortua_app | gzip > backups/db-$(date +\%Y\%m\%d).sql.gz
```

---

## 🔍 Verificación Post-Deploy

```bash
# Conectar SSH y verificar:
curl https://app.jairohortua.com/api/auth/login
# Debería retornar JSON (error 422 sin body, pero eso es OK)

curl https://admin.jairohortua.com/
# Debería cargar la página de login del admin

# Ver logs
tail -f /home/jairohortua/jairohortua-app/admin-web/storage/logs/laravel.log
```

---

## 🐛 Troubleshooting

| Problema | Solución |
|----------|----------|
| 500 Internal Server Error | Revisar `storage/logs/laravel.log` |
| 502 Bad Gateway | Reiniciar PHP-FPM: `sudo systemctl restart php-fpm` |
| Permission denied en storage | `chmod -R 777 storage` + `chown -R www-data:www-data storage` |
| Database connection error | Verificar credenciales en `.env` y acceso desde host |
| SSL certificate not found | Activar AutoSSL nuevamente en cPanel |
| CORS error desde mobile | Verificar SANCTUM_STATEFUL_DOMAINS en .env |

---

## 🚀 Actualización de código (Post-Deploy)

```bash
cd /home/jairohortua/jairohortua-app/admin-web

git pull origin main
composer install --no-dev --optimize-autoloader

# Si hay cambios en DB:
php artisan migrate --force

php artisan config:cache
php artisan view:cache
php artisan cache:clear
```

---

**Última actualización**: 20 de diciembre de 2025  
**Hostinger Version**: cPanel + PHP 8.2 + MySQL 8.0
