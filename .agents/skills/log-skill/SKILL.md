---
name: backend-architect
description: Build scalable Laravel API + NestJS realtime architecture for TikTok live interaction platforms, websocket systems, automation engines, OBS overlays, and realtime SaaS platforms.
license: MIT
compatibility: opencode
metadata:
    audience: backend-engineers
---

# Backend Architect Skill

## Role

AI agent khusus untuk:

- Laravel API architecture
- NestJS realtime architecture
- Redis event-driven systems
- TikTok connector systems
- WebSocket management
- Socket.IO gateway systems
- automation engines
- scalable realtime SaaS
- queue & worker systems
- Docker production infrastructure

---

# Main Architecture

```txt
Next.js
   ↓
Laravel API
   ↓
PostgreSQL

Next.js
   ↓
listener-stream (NestJS)
   ↓
Socket.IO + TikTok Connector
```

---

# System Responsibilities

# Laravel API

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
- database management

---

# listener-stream (NestJS)

listener-stream bertanggung jawab untuk:

- TikTok connector
- websocket management
- realtime websocket gateway
- OBS overlay realtime
- Redis PubSub
- automation runtime
- realtime event processing
- overlay event broadcasting
- websocket scaling
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

## listener-stream (NestJS)

- NestJS
- TypeScript strict mode
- Socket.IO
- Redis
- BullMQ
- TikTok Live Connector

---

# Infrastructure

- Docker
- Docker Compose
- Nginx
- PM2
- GitHub Actions

---

# Project Structure

```txt
project-root/
├── app/
├── bootstrap/
├── config/
├── database/
├── routes/
├── storage/
├── listener-stream/
│   ├── src/
│   │   ├── modules/
│   │   │   ├── tiktok/
│   │   │   ├── websocket/
│   │   │   ├── overlays/
│   │   │   ├── automations/
│   │   │   ├── analytics/
│   │   │   ├── webhooks/
│   │   │   └── workers/
│   │   │
│   │   ├── common/
│   │   │   ├── guards/
│   │   │   ├── decorators/
│   │   │   ├── filters/
│   │   │   ├── interceptors/
│   │   │   └── interfaces/
│   │   │
│   │   ├── config/
│   │   ├── redis/
│   │   ├── queue/
│   │   ├── gateways/
│   │   ├── shared/
│   │   └── main.ts
│   │
│   ├── package.json
│   ├── tsconfig.json
│   └── nest-cli.json
│
└── public/
```

---

# Architecture Rules

## WAJIB

### 1. Gunakan Event Driven Architecture

Semua realtime wajib menggunakan:

- Redis PubSub
- Event Bus
- BullMQ

---

### 2. Semua websocket wajib di NestJS

Laravel TIDAK BOLEH menangani:

- websocket gateway
- realtime overlay
- realtime event processing
- TikTok connector
- websocket scaling

---

### 3. Laravel adalah Database Owner

Laravel menjadi source of truth untuk:

- users
- subscriptions
- automations
- overlays
- billing
- analytics

NestJS tidak mengelola database utama.

---

### 4. Gunakan Modular Architecture

Setiap feature wajib memiliki:

- module
- controller
- service
- dto
- gateway (jika websocket)
- interfaces

---

### 5. Gunakan TypeScript Strict Mode

DILARANG:

- any
- implicit typing
- unsafe websocket payload

---

### 6. Gunakan DTO Validation

WAJIB:

- class-validator
- class-transformer
- ValidationPipe global

---

### 7. Gunakan Queue untuk Heavy Process

Gunakan BullMQ untuk:

- webhook retry
- automation execution
- realtime processing
- media processing
- notifications

---

# Redis Rules

Redis digunakan untuk:

- PubSub
- websocket scaling
- Socket.IO adapter
- BullMQ
- cache
- rate limiting

---

# WebSocket Rules

## Gunakan Socket.IO

WAJIB:

- JWT auth saat handshake
- room-based events
- reconnect support
- typed events
- websocket cleanup

---

# WebSocket Event Naming

```txt
gift.created
comment.created
follow.created
share.created
battle.updated
overlay.updated
live.started
live.ended
automation.triggered
viewer.updated
```

---

# Realtime Flow

```txt
TikTok Live
↓
listener-stream (NestJS)
↓
Redis PubSub
↓
Socket.IO Gateway
↓
Next.js Dashboard / OBS Overlay
```

---

# TikTok Connector Rules

WAJIB:

- reconnect otomatis
- heartbeat monitoring
- proxy support
- failover handling
- normalized event mapping

---

# Overlay Rules

Overlay wajib realtime.

Gunakan:

- Socket.IO
- lightweight payload
- optimized browser source rendering

---

# Queue Rules

## Laravel Queue

Digunakan untuk:

- email
- notifications
- billing jobs
- reports

---

## BullMQ

Digunakan untuk:

- realtime jobs
- automation runtime
- websocket jobs
- webhook retry
- media processing

---

# Security Rules

WAJIB:

- JWT auth
- refresh token rotation
- rate limiting
- Redis auth
- Helmet
- webhook signature
- CORS protection

---

# Logging Rules

WAJIB:

- structured logging
- websocket logs
- connector logs
- webhook logs
- automation logs

---

# DevOps Rules

WAJIB:

- Docker support
- docker-compose
- healthcheck endpoint
- Redis healthcheck
- PostgreSQL backup
- PM2 ecosystem

---

# Code Style

WAJIB:

- SOLID principles
- clean architecture
- reusable services
- reusable guards
- reusable DTO
- centralized config

---

# Best Practices

Selalu:

- gunakan typed events
- gunakan centralized constants
- gunakan modular architecture
- gunakan queue untuk heavy jobs
- gunakan Redis PubSub untuk realtime

---

# Prohibited

DILARANG:

- websocket logic di Laravel
- TikTok connector di Laravel
- hardcoded secrets
- direct emit antar service
- monolithic god-service
- database ownership di NestJS

---

# Output Expectations

Saat generate code:

WAJIB:

- production-ready
- scalable
- realtime-ready
- modular
- maintainable
- typed
- event-driven

---

# After Every Task

WAJIB:

- update progress.txt
- append log
- jangan overwrite log lama
