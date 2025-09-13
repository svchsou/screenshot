# Shots — Screenshot Sharing Platform

Web-based screenshot sharing built on Laravel 11 (PHP 8.2+), Flysystem v3, and Blade. Supports Local/Public, FTP, AWS S3, and DigitalOcean Spaces. Simple Material UI, AdSense-ready, privacy‑minded, and extensible.

---

## Features
- Upload PNG/JPG/WebP with server‑side MIME sniffing and size limits
- Short links with delete tokens, thumbnails, view counters, expiry
- Pluggable storage: Local/Public, FTP, S3, Spaces (admin‑configurable)
- Admin portal: Storage CRUD + live “Test Connection”, AdSense settings, logs/usage monitor, purge expired screenshots
- Material‑styled UI; three AdSense placements on the upload page (left/right/bottom)
- Security: CSRF, rate‑limits (uploads/views), IP hashing, admin login with throttle

---

## Quick Start (Windows WAMP)
1) From `shots/` run:
   ```sh
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```

2) Access in browser:
   - Direct path: http://localhost/screenshot/shots/public/
   - Or create an Apache vhost pointing `DocumentRoot` to `shots/public` and visit http://shots.local/

3) Temporary admin login (change after first login):
   - Username: `admin`
   - Password: set `ADMIN_PASSWORD=admin` in `.env` (then `php artisan config:clear`)
   - Login: http://localhost/screenshot/shots/public/admin/login
   - After login go to Admin → Users to set a new password (stored as `ADMIN_PASSWORD_HASH`).

---

## Configuration (.env)

Core
- `APP_URL`=full base URL (e.g., `http://localhost/screenshot/shots/public`)
- `UPLOAD_MAX_MB`=12
- `ALLOWED_MIMES`=image/png,image/jpeg,image/webp
- `DEFAULT_UPLOAD_DISK`=public | local | s3 | spaces | ftp
- `CDN_BASE_URL`=optional CDN for public URLs

Sessions & Limits
- `SESSION_DRIVER`=file
- `RATE_UPLOADS_PER_MIN`=5
- `RATE_VIEWS_PER_MIN`=120
- `ADMIN_LOGIN_MAX_ATTEMPTS`=5
- `ADMIN_LOGIN_DECAY_SECONDS`=60

Admin Authentication
- `ADMIN_USERNAME`=admin
- `ADMIN_PASSWORD`= (temporary, optional)
- `ADMIN_PASSWORD_HASH`=bcrypt hash (preferred; set via Admin → Users)

AdSense
- `ADS_ENABLED`=true|false
- `ADSENSE_CLIENT_ID`=ca-pub-xxxxxxxxxxxxxxxx (your AdSense client)
- `ADSENSE_SLOT_LEFT`=slot id for left of upload form
- `ADSENSE_SLOT_RIGHT`=slot id for right of upload form
- `ADSENSE_SLOT_BOTTOM`=slot id under upload form
- `ADSENSE_TEST_MODE`=true to render test ads (`data-adtest="on"`)

S3 / Spaces
- `S3_KEY`, `S3_SECRET`, `S3_REGION`, `S3_BUCKET`
- `S3_ENDPOINT` (Spaces example: `https://sgp1.digitaloceanspaces.com`)
- `S3_URL` (optional public URL), `S3_USE_PATH_STYLE` (true/false)

FTP
- `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_ROOT`
- `FTP_PORT`=21, `FTP_SSL`=false, `FTP_PASSIVE`=true, `FTP_TIMEOUT`=30

---

## Admin Portal
- URL: `/admin` → `/admin/login` (session login)
- Menus after login: Storage, Ads, Monitor, Users

Storage Destinations
- Add/Edit a destination; dynamic form per type (Local/FTP/S3/Spaces)
- “Test Connection” performs write→verify→cleanup and shows detailed errors
  - Local: validates path exists and writable
  - FTP: checks PHP `ftp` extension and Flysystem adapter
  - S3/Spaces: checks AWS SDK + S3 adapter; shows bucket/region

Ads Settings
- Configure Client ID and 3 slot IDs (Left/Right/Bottom) for the Upload page
- Enable Test Ads to add `data-adtest="on"` for safe development (Google‑recommended for testing)

Monitoring & Purge
- Monitor shows counts and total size by disk plus last 200 lines of `storage/logs/laravel.log`
- Purge expired screenshots (also scheduled hourly)

Users (ACL)
- Set admin username and update password (stores `ADMIN_PASSWORD_HASH` in `.env`)

---

## Storage Adapter Dependencies
Install only what you use:
- FTP: `composer require league/flysystem-ftp:^3.0` and enable PHP `extension=ftp`
- S3/Spaces: `composer require aws/aws-sdk-php:^3.0 league/flysystem-aws-s3-v3:^3.0`

---

## Troubleshooting
- Symfony “Please provide a valid cache path”
  - Ensure `config/view.php` exists (included) and `storage/framework/views` directory exists and is writable
- 404 on `/admin/login`
  - App routes exclude the `admin` prefix from short-link catch‑all; ensure URL includes `/public/` if not using a vhost
- FTP error “Class League\Flysystem\Ftp\FtpAdapter not found”
  - Install the adapter package and enable PHP ftp extension (see dependencies above)
- S3/Spaces credential/region errors
  - Verify region, bucket, and endpoint; for Spaces set `S3_ENDPOINT` and usually `S3_USE_PATH_STYLE=false`

---

## Testing
If Artisan’s `test` command is unavailable in your setup, run PHPUnit directly:
```sh
vendor\bin\phpunit.bat
```

---

## License
MIT

