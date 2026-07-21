# React Inertia Laravel - Project Starter

A focused Laravel + Inertia + React starter that currently ships a dashboard experience served by Laravel controllers and rendered via Inertia.

## Features

- Monorepo layout with Laravel in `backend/` and React app in `frontend/`
- Server-side rendering support through Inertia + Vite SSR entry
- Dashboard web page (`/`) and JSON API (`/api/dashboard`) backed by shared PHP service data
- Frontend quality tooling (ESLint, Prettier, TypeScript typecheck)
- Backend test suite using Pest feature tests

## Architecture Overview

This project is a monolithic repository:

```
/
├── backend/          # Laravel app, routes, config, migrations, tests
├── frontend/         # Vite + React + TypeScript source and tooling
├── public/           # Document root (index.php bootstraps backend/)
├── docs/
└── package.json      # Root scripts delegating to backend/frontend
```

## Tech Stack

- Backend: Laravel 12.x, PHP 8.4, Inertia Laravel, Ziggy
- Frontend: React 19, TypeScript 5.9, Vite 7, Tailwind CSS 4
- Testing: Pest (Laravel feature and unit tests)

## Getting Started

### Prerequisites

- PHP 8.4
- Composer
- Node.js (latest LTS)
- PNPM

### Installation

1. Clone repository and install dependencies:

```bash
composer install --working-dir=backend
pnpm install --dir frontend
```

2. Prepare environment:

```bash
cp backend/.env.example backend/.env
php backend/artisan key:generate
```

3. Run migrations:

```bash
php backend/artisan migrate
```

4. Start development services:

```bash
pnpm run dev
```

By default the Laravel server runs on `http://127.0.0.1:8000`.

## Useful Commands

```bash
# Backend tests
php backend/artisan test

# Frontend checks
pnpm run lint
pnpm run typecheck
pnpm run format:check

# Production build
pnpm run build
```
