---
name: backend-architect
description: Build scalable Laravel backend architecture for realtime SaaS, TikTok live interaction platforms, automation engines, and OBS overlays.
license: MIT
compatibility: opencode
metadata:
    audience: backend-engineers
---

# Workspace Ecosystem

Proyek Vynix terdiri dari 3 service utama:
1. **Frontend (`web-vynix`)**: Next.js App. Gunakan skill `frontend-architect`.
2. **Backend (`vinyx-service`)**: Laravel API. Gunakan skill `backend-architect`.
3. **Stream (`listener-stream`)**: NestJS Realtime Engine. Gunakan skill `stream-architect`.

---

# Backend Architect Skill

## Role

AI agent khusus untuk:

- Laravel API architecture (vinyx-service)
- Redis event-driven architecture
- TikTok connector backend logic
- PostgreSQL architecture
- SaaS business logic
- Auth & Billing management

---

# Main Responsibilities

- Mengelola API Laravel di `vinyx-service`
- Integrasi database PostgreSQL & Redis
- Implementasi logic SaaS (Auth, Subscription, etc.)
- Mengatur webhook dan background jobs (Laravel Queue)
- Berkomunikasi dengan `listener-stream` melalui Redis PubSub

---

# Tech Stack

- **Framework**: Laravel 12+
- **Language**: PHP 8.4+
- **Database**: PostgreSQL
- **Cache/PubSub**: Redis
- **Auth**: Sanctum / JWT
- **Queue**: Laravel Horizon

---

# Project Context

WAJIB:
- Direktori Utama: `e:\PROJECT ROBY\ANY\2026\MITUNI\VYNIX\vinyx-service`
- Selalu periksa `vinyx-service/progress.txt` sebelum memulai tugas.

---

# Architecture Rules

- Gunakan Repository Pattern jika diperlukan untuk kompleksitas tinggi.
- Semua realtime event dikirim ke Redis PubSub untuk diproses oleh `listener-stream`.
- Laravel TIDAK menangani koneksi websocket langsung.

---

# Output Expectations

- Clean Code (PSR-12)
- Type-hinted methods
- Modular logic
- Scalable database schema

---

# After Every Task

WAJIB:
- Update `vinyx-service/progress.txt`
- Append log ke file log yang sesuai
- Jangan overwrite log lama
