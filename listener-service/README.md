# 🛰️ TikTok Listener Server

Server backend berbasis Node.js yang berfungsi untuk menarik data real-time dari TikTok Live menggunakan `tiktok-live-connector` dan mendistribusikannya via Socket.io.

## 📋 Fitur
-   Koneksi stabil ke TikTok Live API.
-   Event Broadcaster (Join, Chat, Gift).
-   Dukungan Socket.io untuk multi-client.
-   Auto-fetch konfigurasi dari Laravel backend.

## 🛠️ Instalasi
```bash
npm install
```

## ⚙️ Konfigurasi (.env)
Salin `.env.example` menjadi `.env` dan sesuaikan nilainya:
-   `PORT`: Port server berjalan (default: 3000).
-   `LARAVEL_URL`: URL API Laravel untuk mengambil config (default: http://localhost:8000).

---

## 🚀 Deployment Guide (Production)

Ikuti langkah-langkah berikut untuk menjalankan server di lingkungan VPS/Production.

### 1. Persiapan Environment
Pastikan Anda sudah menyalin konfigurasi produksi:
```bash
cp .env.production .env
# Sesuaikan isi .env jika perlu
nano .env
```

### 2. Manajemen Proses dengan PM2
Gunakan PM2 agar server tetap berjalan di background dan otomatis restart jika terjadi crash.

**Install PM2 secara global:**
```bash
npm install -g pm2
```

**Jalankan aplikasi:**
```bash
pm2 start ecosystem.config.js --env production
```

**Perintah berguna PM2:**
- `pm2 status`: Melihat status aplikasi.
- `pm2 logs tiktok-live-agent`: Melihat log real-time.
- `pm2 restart tiktok-live-agent`: Merestart aplikasi.
- `pm2 save`: Menyimpan list proses agar otomatis jalan saat server reboot.

### 3. Konfigurasi Nginx (Reverse Proxy)
Agar aplikasi bisa diakses melalui domain dan menggunakan port standar (80/443), gunakan Nginx.

1. Salin isi dari `nginx-config.conf`.
2. Buat file baru di server Nginx:
   ```bash
   sudo nano /etc/nginx/sites-available/tiktok-socket
   ```
3. Tempel konfigurasi dan sesuaikan `server_name` dan `proxy_pass`.
4. Aktifkan konfigurasi:
   ```bash
   sudo ln -s /etc/nginx/sites-available/tiktok-socket /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

### 4. Keamanan SSL (Certbot)
Gunakan Certbot untuk mengamankan koneksi WebSocket (WSS):
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d subdomain.andahost.com
```

---
**Catatan:** Pastikan port yang dikonfigurasi di `proxy_pass` Nginx sama dengan port yang berjalan di Node.js (cek `.env`).
