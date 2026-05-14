# 🤖 AI & Engineering Handbook (TikTok Listener)

> **SYSTEM INSTRUCTION FOR AI ASSISTANT**:
> Setiap kali Anda (AI) menghasilkan kode, melakukan refactoring, atau menjawab pertanyaan teknis untuk proyek ini, Anda **WAJIB** membaca dan mematuhi panduan dalam dokumen ini sebagai "Single Source of Truth".
>
> 1. **Analisis Konteks**: Periksa bagian "Project Identity" di bawah untuk mengetahui stack yang aktif.
> 2. **Patuhi Prinsip**: Terapkan "Universal Principles" pada setiap baris kode.
> 3. **Gunakan Stack Spesifik**: Abaikan bagian stack yang tidak relevan dengan proyek saat ini.
> 4. **Validasi**: Pastikan kode yang dihasilkan lolos kriteria "Code Quality" yang didefinisikan.
> 5. **Penjelasan**: Jelaskan kode yang dihasilkan dengan detail menggunakan Bahasa Indonesia.

---

## 1. 🏗️ Project Identity (Active Configuration)

| Parameter      | Value (Current Project)       | Options / Notes                        |
| :------------- | :---------------------------- | :------------------------------------- |
| **Language**   | **JavaScript (Node.js)**      | `CommonJS`                             |
| **Framework**  | **Express.js**                | Backend Listener                       |
| **Realtime**   | **Socket.io v4**              | Server-side                            |
| **Connector**  | **TikTok Live Connector**     | Scraper/API                            |
| **Reporting**  | **Standard Console**          | `language: indonesia`                  |

---

## 2. 💎 Universal Principles (All Projects)

### General Philosophy
1. **KISS**: Solusi sederhana lebih baik.
2. **DRY**: Hindari pengulangan logika.
3. **Event-Driven**: Karena ini adalah listener, pastikan penanganan event (on chat, on gift, dll) efisien.
4. **Error Handling**: Koneksi TikTok sering terputus, pastikan ada mekanisme reconnect.

### Security First
- **No Secrets**: Gunakan `.env` untuk konfigurasi sensitif.
- **Input Validation**: Meskipun data dari TikTok, tetap bersihkan data sebelum dikirim ke client jika perlu.

---

## 3. 🛠️ Stack-Specific Guidelines

### 🟢 Node.js / Express
1. **Middleware**: Gunakan middleware untuk logging atau CORS.
2. **Socket.io**: Pastikan CORS diizinkan untuk client (Electron app).
3. **tiktok-live-connector**: Gunakan event listeners yang tepat (`chat`, `gift`, `member`, `roomUser`).

---

## 4. 📝 Development Workflow

### Standard Commands
- **Dev**: `node server.js` atau `nodemon server.js`
- **Install**: `npm install`

### Definition of Done (DoD)
1. [ ] Event TikTok berhasil ditangkap di console.
2. [ ] Event berhasil di-broadcast via Socket.io.
3. [ ] Tidak ada memory leak pada koneksi yang lama.

