---
name: backend-architect
description: Build scalable Laravel + Express.js backend architecture for realtime SaaS, websocket systems, TikTok live interaction platforms, automation engines, and OBS overlays.
license: MIT
compatibility: opencode
metadata:
    audience: backend-engineers
---

# Backend Architect Skill

## Role

AI agent khusus untuk:

- Laravel API architecture
- Express.js realtime engine
- Redis event-driven architecture
- TikTok connector backend
- Socket.IO realtime systems
- automation engine
- scalable backend SaaS
- PostgreSQL architecture
- queue & worker system
- Docker production backend

---

# Main Architecture

```txt
Next.js
   ↓
Laravel API
   ↓
Redis PubSub
   ↓
listener-service (Express.js)
   ↓
TikTok Connector + WebSocket
```

---

# Service Responsibilities

## Laravel API

Laravel bertanggung jawab untuk:

- authentication
- users
- subscriptions
- billing
- admin panel
- automation CRUD
- analytics API
- media management
- webhook management
- SaaS business logic

---

## listener-service

listener-service bertanggung jawab untuk:

- TikTok connector
- websocket realtime
- OBS overlay realtime
- realtime events
- Redis PubSub
- automation runtime
- event processing
- live monitoring

---

# Tech Stack

## Laravel API

- Laravel 12+
- PHP 8.4+
- PostgreSQL
- Redis
- Laravel Queue
- Sanctum / JWT
- Horizon

---

## listener-service

- Express.js
- TypeScript
- Socket.IO
- Redis
- BullMQ
- Prisma ORM
- TikTok Live Connector

---

# Architecture Rules

## WAJIB

### Semua realtime wajib di listener-service

Laravel tidak boleh menangani:

- websocket utama
- TikTok connector
- realtime overlay
- realtime event processing

---

### Gunakan Redis PubSub

Semua komunikasi realtime:

- Laravel → listener-service
- listener-service → websocket
- listener-service → overlay

---

### Gunakan Queue

Laravel Queue:

- billing
- email
- notifications

BullMQ:

- realtime jobs
- webhook retry
- automation execution

---

# Folder Structure

```txt
project-root/
├── app/
├── bootstrap/
├── config/
├── database/
├── routes/
├── storage/
├── listener-service/
│   ├── src/
│   │   ├── modules/
│   │   ├── redis/
│   │   ├── queues/
│   │   ├── shared/
│   │   └── main.ts
│   │
│   ├── prisma/
│   ├── package.json
│   └── tsconfig.json
│
└── public/
```

---

# WebSocket Rules

WAJIB:

- Socket.IO
- JWT auth
- room-based events
- reconnect support

---

# Event Naming

```txt
gift.created
comment.created
follow.created
battle.updated
overlay.updated
live.started
live.ended
automation.triggered
```

---

# Security Rules

WAJIB:

- JWT auth
- rate limiting
- Redis auth
- webhook signature
- Helmet
- CORS protection

---

# Database Rules

## Laravel

- Eloquent ORM

## listener-service

- Prisma ORM

---

# Best Practices

Selalu:

- gunakan Redis PubSub
- gunakan typed events
- gunakan modular architecture
- gunakan centralized config
- gunakan queue untuk heavy process

---

# Output Expectations

Saat generate backend code:

- production-ready
- scalable
- modular
- maintainable
- event-driven
- realtime-ready

---

# After Every Task

WAJIB:

- update progress.txt
- append log
- jangan overwrite log lama
