# Frontend Documentation

## Frontend Architecture

The frontend is a React 19 + TypeScript app loaded through Inertia. Vite builds both client and SSR bundles.

### Current Frontend Structure

```
frontend/
├── resources/js/
│   ├── Pages/Dashboard.tsx
│   ├── layouts/AppLayout.tsx
│   ├── components/theme-provider.tsx
│   ├── app.tsx
│   └── ssr.tsx
├── resources/css/app.css
├── vite.config.js
└── package.json
```

## Entry Points

- `resources/js/app.tsx`: browser client bootstrap
- `resources/js/ssr.tsx`: server-side render bootstrap

## Theme Handling

`theme-provider.tsx` stores user theme preference in `localStorage` and now guards browser-only APIs during SSR.

## Dashboard Page Contract

`Pages/Dashboard.tsx` consumes these props:

- `stats`
- `activity`
- `breakdown`
- `regions`

All are provided by Laravel through Inertia from `DashboardService`.

## Tooling

Frontend scripts:

```bash
pnpm --dir frontend run dev
pnpm --dir frontend run lint
pnpm --dir frontend run typecheck
pnpm --dir frontend run build
```

No Jest/Vitest test suite is configured in this repository yet.
