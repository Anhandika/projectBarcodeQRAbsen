# 🚀 Railway Deployment & Login Debugging Guide

## ✅ Checklist Login di Railway

### 1️⃣ Pre-Deployment Setup

**Pastikan sudah dilakukan di Local:**

```bash
# Generate APP_KEY
php artisan key:generate --show

# Test migration & seeding lokal
php artisan migrate:fresh --seed

# Test login lokal
# Email: adminsekolah@example.test
# Password: password

# Build asset
npm run build
```

---

### 2️⃣ Railway Environment Variables

Di Railway Dashboard → Variables, set:

```dotenv
# APP Settings
APP_NAME="Absen Digital SMK Bina Utama"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_FROM_php_artisan_key:generate
APP_URL=https://your-railway-domain.up.railway.app

# Database (Railway auto-provides)
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=60
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Queue
QUEUE_CONNECTION=database

# Firebase (jika digunakan)
FIREBASE_ENABLED=false
```

---

### 3️⃣ Railway Build & Deploy Process

**File:** `railway.toml`

```toml
[build]
builder = "NIXPACKS"
buildCommand = "composer install --no-dev --optimize-autoloader --no-interaction && npm ci && npm run build"

[deploy]
startCommand = "bash -c 'php artisan storage:link || true; php artisan migrate --force; php artisan db:seed; php artisan optimize; php artisan serve --host=0.0.0.0 --port=$PORT'"
healthcheckPath = "/up"
healthcheckTimeout = 180
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 5
```

**Apa yang terjadi saat deploy:**
1. ✅ Dependencies install (`composer` + `npm`)
2. ✅ Assets build (`npm run build`)
3. ✅ Storage symlink (`php artisan storage:link`)
4. ✅ Database migration (`php artisan migrate --force`)
5. ✅ Seed database (`php artisan db:seed`)
6. ✅ Optimize cache (`php artisan optimize`)
7. ✅ Start Laravel server

---

### 4️⃣ Testing Login di Railway

**Setelah deploy berhasil:**

```
1. Buka https://your-railway-domain.up.railway.app/login
2. Masukkan:
   - Email: adminsekolah@example.test
   - Password: password
3. Harusnya redirect ke /dashboard
```

---

## 🐛 Debugging Login Issues di Railway

### Issue 1: "503 Service Unavailable" atau Crash

**Diagnosis:**

```bash
# Railway CLI
railway logs --follow

# Cek error terakhir
railway logs -n 50 | grep -i "error\|exception"
```

**Penyebab Umum:**

| Error | Solusi |
|-------|--------|
| `Connection refused PostgreSQL` | Database migration belum jalan. Set `php artisan migrate --force` di `startCommand` |
| `APP_KEY empty` | Set `APP_KEY` di Railway variables (jangan kosong!) |
| `SQLSTATE[08006]` | PostgreSQL belum siap saat app start. Tambah delay atau retry logic |

### Issue 2: "404 Not Found" di `/dashboard`

**Penyebab:**
- Route tidak terdaftar
- Middleware block
- Controller/view tidak ada

**Debug:**

```bash
# Di Railway, SSH ke container (jika tersedia)
php artisan route:list | grep dashboard

# Cek apakah controller ada
php artisan tinker
>>> class_exists('App\Http\Controllers\AdminDashboardController')
```

### Issue 3: "403 Unauthorized" di `/dashboard`

**Penyebab:** Role middleware blocking

**Diagnosis di Tinker:**

```bash
php artisan tinker

# Cek user di database
>>> $user = App\Models\User::where('email', 'adminsekolah@example.test')->first();
>>> $user->role
>>> $user->role->value
>>> $user->isAdmin()
```

**Harusnya return:**
```
role: admin_sekolah
isAdmin(): true
```

### Issue 4: Session tidak persist (sering logout)

**Penyebab:** Session table kosong atau encrypted session error

**Debug:**

```bash
# Cek session table
php artisan tinker
>>> DB::table('sessions')->count()

# Cek session configuration
>>> config('session')

# Harus: encrypt=true, driver=database, lifetime=60
```

**Solusi:**

```bash
# Di Railway startCommand, pastikan ada:
php artisan migrate --force
```

---

## 🔍 Advanced Debugging untuk Railway

### 1️⃣ Logs & Monitoring

**Real-time logs:**
```bash
railway logs --follow
```

**Specific error logs:**
```bash
railway logs | grep "LOGIN\|LOGOUT\|ERROR" | tail -30
```

### 2️⃣ SSH ke Railway Container

```bash
# List running services
railway service list

# Connect to service
railway connect --service <service-name>

# Jalankan artisan command
php artisan tinker
php artisan migrate:status
php artisan config:show session
```

### 3️⃣ Database Connection Test

```bash
php artisan tinker

# Test connection
>>> DB::connection()->getPdo();

# Cek tables
>>> DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'")

# Cek users
>>> App\Models\User::all()
```

### 4️⃣ Test Login Flow Manually

```bash
php artisan tinker

>>> $user = App\Models\User::where('email', 'adminsekolah@example.test')->first();
>>> Hash::check('password', $user->password); // true?
>>> $user->active; // true?
>>> $user->role->value; // admin_sekolah?

# Try auth attempt
>>> auth()->attempt(['email' => 'adminsekolah@example.test', 'password' => 'password', 'active' => true]);
// true = login berhasil
```

---

## 🛠️ Railway Troubleshooting Commands

### Clear Cache & Optimize

```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
php artisan optimize
```

### Reset Database (hati-hati!)

```bash
php artisan migrate:refresh --seed
```

### Check Services

```bash
# Railway CLI
railway status
railway service list
railway service status <service-name>
```

---

## 📋 Production Checklist sebelum Go Live

- [ ] `APP_DEBUG=false` di Railway
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` sudah di-set (gunakan `php artisan key:generate --show`)
- [ ] `APP_URL` sesuai domain Railway
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=strict`
- [ ] `SESSION_ENCRYPT=true`
- [ ] Database migration sudah run
- [ ] Seeder sudah populate user admin
- [ ] Test login dengan akun admin
- [ ] Check logs untuk error
- [ ] Backup database konfigurasi

---

## 🚀 Quick Deploy Checklist

```bash
# 1. Local: Generate key & verify
php artisan key:generate --show

# 2. Local: Test migration & seed
php artisan migrate:fresh --seed

# 3. Local: Test login
# http://localhost:8000/login
# adminsekolah@example.test / password

# 4. Commit & push
git add .
git commit -m "Fix: Session security & login flow"
git push origin main

# 5. Railway: Check deploy status
railway logs --follow

# 6. Railway: Test login
# https://your-app.up.railway.app/login

# 7. Railway: Verify database
railway connect
php artisan tinker
>>> App\Models\User::where('email', 'adminsekolah@example.test')->first()
```

---

## 📞 Emergency Fixes

### Jika Login Tidak Bisa Login

```bash
# 1. Check logs
railway logs

# 2. Reset & reseed database
railway connect
php artisan migrate:refresh --seed

# 3. Check user
php artisan tinker
>>> App\Models\User::where('email', 'adminsekolah@example.test')->first()

# 4. Restart service
railway service restart
```

### Jika Dashboard 403/404

```bash
# 1. Check route
php artisan route:list | grep dashboard

# 2. Check middleware
php artisan tinker
>>> auth()->check()
>>> auth()->user()->role->value

# 3. Check controller exists
>>> class_exists('App\Http\Controllers\AdminDashboardController')

# 4. Check view exists
>>> view()->exists('admin.dashboard')
```

---

**Need Help?** Check:
1. Railway logs: `railway logs --follow`
2. Local test first: `php artisan serve`
3. Database status: `php artisan migrate:status`
4. User data: `php artisan tinker` → `App\Models\User::all()`

