# TikTok Live Interaction Platform Architecture

## Main Stack

Frontend:

- Next.js

Backend API:

- Laravel

Realtime Engine:

- Express.js (listener-service)

Infrastructure:

- Redis
- PostgreSQL
- Socket.IO
- BullMQ
- Docker
- Nginx

---

# High Level Architecture

```txt
                        ┌───────────────────┐
                        │      Next.js      │
                        │ Dashboard & UI    │
                        └─────────┬─────────┘
                                  │
                 ┌────────────────┴────────────────┐
                 │                                 │
                 ▼                                 ▼
        ┌───────────────────┐             ┌───────────────────┐
        │    Laravel API    │             │ listener-service  │
        │ Business Backend  │◄──────────►│ Express Realtime  │
        └─────────┬─────────┘   Redis     └─────────┬─────────┘
                  │          Pub/Sub                │
                  ▼                                 ▼
        ┌───────────────────┐             ┌───────────────────┐
        │   PostgreSQL      │             │ TikTok Connector  │
        │ Main Database     │             │ WebSocket Engine  │
        └───────────────────┘             └───────────────────┘
```

---

# Service Responsibilities

# 1. Next.js

Frontend application untuk:

- dashboard
- landing page
- overlay UI
- realtime monitor
- automation builder
- analytics dashboard

---

# 2. Laravel API

Business backend untuk:

- authentication
- users
- subscriptions
- billing
- automation CRUD
- media management
- webhook management
- admin panel
- analytics API

---

# 3. listener-service (Express.js)

Realtime backend untuk:

- TikTok connector
- websocket realtime
- OBS overlay realtime
- Redis PubSub
- automation runtime
- realtime event processing
- overlay event broadcasting

---

# Core Architecture Pattern

## Event Driven Architecture

WAJIB menggunakan Redis PubSub.

Flow:

```txt
TikTok Live
↓
listener-service
↓
Redis PubSub
↓
Laravel API
↓
Next.js Dashboard
```

---

# Realtime Architecture

## Socket.IO

Digunakan untuk:

- dashboard realtime
- overlay realtime
- alerts
- notifications
- battle monitor

---

# Overlay Architecture

```txt
OBS Browser Source
↓
Next.js Overlay Page
↓
Socket.IO
↓
listener-service
```

---

# Redis Responsibilities

Redis digunakan untuk:

- PubSub
- websocket scaling
- queues
- cache
- rate limiting

---

# Queue Architecture

# Laravel Queue

Digunakan untuk:

- email
- notifications
- billing jobs
- reporting

---

# BullMQ

Digunakan untuk:

- realtime processing
- automation execution
- media processing
- webhook retries

---

# Database Architecture

## PostgreSQL

Digunakan untuk:

- users
- subscriptions
- live sessions
- automations
- overlays
- analytics
- media assets

---

# ORM Responsibilities

## Laravel

- Eloquent ORM

## listener-service

- Prisma ORM

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
```

---

# Security Architecture

WAJIB:

- JWT auth
- rate limiting
- webhook signature
- Redis auth
- Helmet
- CORS protection

---

# Scaling Architecture

Future scaling:

```txt
                ┌──────────────┐
                │   Next.js    │
                └──────┬───────┘
                       │
        ┌──────────────┼──────────────┐
        ▼                              ▼
┌──────────────┐              ┌──────────────┐
│ Laravel API  │              │ Laravel API  │
└──────┬───────┘              └──────┬───────┘
       │                              │
       └──────────────┬───────────────┘
                      ▼
               ┌────────────┐
               │ PostgreSQL │
               └────────────┘

                      │
               Redis PubSub
                      │

       ┌──────────────┼──────────────┐
       ▼                              ▼

┌──────────────┐              ┌──────────────┐
│ listener-1   │              │ listener-2   │
│ Express WS   │              │ Express WS   │
└──────────────┘              └──────────────┘
```

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
│   ├── prisma/
│   ├── package.json
│   └── tsconfig.json
│
├── resources/
├── public/
└── docker/
```

---

# Deployment Architecture

## Docker Services

- nginx
- nextjs
- laravel
- listener-service
- redis
- postgres
- worker

---

# DevOps

WAJIB:

- Docker Compose
- GitHub Actions
- PM2
- Healthcheck
- Monitoring

---

# Monitoring

Recommended:

- Grafana
- Prometheus
- Loki

---

# Main Goals

- scalable realtime platform
- low latency websocket
- stable TikTok connector
- modern SaaS dashboard
- OBS realtime overlays
- automation engine
