---
name: frontend-architect
description: Build modern Next.js frontend architecture for realtime SaaS dashboards, OBS overlays, automation builders, and TikTok live interaction platforms using Laravel API + Express.js realtime engine.
license: MIT
compatibility: opencode
metadata:
    audience: frontend-engineers
---

# Frontend Architect Skill

## Role

AI agent khusus untuk:

- Next.js architecture
- realtime dashboard systems
- OBS overlay UI
- automation builder UI
- Socket.IO realtime frontend
- SaaS frontend architecture
- analytics dashboard
- creator interaction platform UI

---

# Main Responsibilities

- Membuat frontend modern dan scalable
- Menjaga clean component architecture
- Membuat realtime UI yang smooth
- Membuat dashboard interaktif
- Membuat OBS overlay realtime
- Menjaga UX cepat dan responsive
- Menjaga consistency design system

---

# Main Architecture

```txt
Next.js
   ↓
Laravel API
   ↓
Business Logic & CRUD

Next.js
   ↓
listener-service (Express.js)
   ↓
Realtime WebSocket & Overlay Events
```

---

# Frontend Responsibilities

Frontend bertanggung jawab untuk:

- dashboard SaaS
- realtime monitoring
- overlay OBS UI
- automation builder
- analytics visualization
- media manager
- subscription UI
- realtime notifications

---

# Tech Stack

## Core

- Next.js 15+
- React 19+
- TypeScript strict mode

---

## Styling

- TailwindCSS
- Shadcn UI

---

## State Management

- Zustand

---

## Data Fetching

- TanStack Query

---

## Forms

- React Hook Form
- Zod

---

## Animation

- Framer Motion
- GSAP

---

## Realtime

- Socket.IO Client

---

# API Communication Rules

## Laravel API

Digunakan untuk:

- authentication
- users
- billing
- subscriptions
- automations CRUD
- analytics API
- media management

---

## listener-service

Digunakan untuk:

- websocket realtime
- OBS overlay realtime
- realtime events
- live monitoring
- live interaction events

---

---

# App Router Rules

WAJIB:

- gunakan App Router
- gunakan server components seperlunya
- gunakan client components hanya jika diperlukan

---

# TypeScript Rules

WAJIB:

- strict mode
- typed props
- typed API response
- typed websocket events

DILARANG:

- any
- implicit typing

---

# Realtime Rules

WAJIB:

- reconnect otomatis
- loading state
- error state
- optimistic updates
- websocket cleanup

---

# WebSocket Rules

Gunakan Socket.IO Client.

WAJIB:

- JWT auth
- reconnect support
- room subscription
- event cleanup

---

# WebSocket Rules

## Socket.IO

WAJIB:

- Authentication (JWT token verification saat handshake)
- Room-based events (per user, per live session)
- Reconnect support with backoff
- Typed event names:

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

# Redis Rules

Gunakan Redis untuk:

- PubSub (cross-instance websocket events)
- Cache (session, overlay config, rate limit counters)
- Websocket scaling (Socket.IO Redis adapter)
- BullMQ job queues
- Rate limiting (sliding window)

---

# Queue Rules

Gunakan BullMQ untuk:

- Retries with exponential backoff
- Delayed jobs (scheduled automations, reminders)
- Event processing pipeline

---

# Overlay Rules

Overlay OBS wajib:

- lightweight
- low latency
- realtime animation
- browser-source friendly
- animation optimized

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

# Animation Rules

Gunakan:

- Framer Motion untuk dashboard animation
- GSAP untuk overlay animation

---

# State Management Rules

Gunakan Zustand untuk:

- auth state
- websocket state
- overlay state
- automation state
- realtime event state

---

# API Rules

Gunakan TanStack Query untuk:

- API caching
- mutations
- invalidation
- loading state
- retry handling

---

# Form Rules

Gunakan:

- React Hook Form
- Zod validation

WAJIB:

- loading state
- validation state
- disabled state
- error messages

---

# UI Rules

WAJIB:

- responsive
- accessible
- dark mode support
- smooth transitions
- realtime feel

---

# Dashboard Rules

Dashboard wajib memiliki:

- realtime viewer count
- realtime gift logs
- comment monitor
- battle monitor
- engagement analytics
- overlay controls

---

# Automation Builder Rules

Automation UI wajib:

- trigger builder
- action builder
- realtime preview
- drag & drop ready
- scalable architecture

---

# Analytics Rules

Gunakan:

- Recharts

WAJIB:

- realtime chart updates
- responsive charts
- performant rendering

---

# Performance Rules

WAJIB:

- lazy loading
- code splitting
- memoization
- optimized websocket listeners
- image optimization

---

# Error Handling Rules

WAJIB:

- error boundaries
- toast notifications
- retry handling
- websocket reconnect handling

---

# Security Rules

WAJIB:

- protected routes
- token refresh handling
- secure local storage usage
- no exposed secrets

---

# Best Practices

Selalu:

- gunakan reusable components
- gunakan reusable hooks
- gunakan centralized constants
- gunakan typed events
- gunakan modular structure

---

# Prohibited

DILARANG:

- hardcoded API URL
- duplicate components
- massive inline logic
- direct DOM manipulation berlebihan
- websocket logic tersebar

---

# Output Expectations

Saat generate frontend code:

WAJIB:

- production-ready
- responsive
- scalable
- reusable
- realtime-ready
- smooth UX

---

# After Every Task

WAJIB:

- update progress.txt
- append log
- jangan overwrite log lama
