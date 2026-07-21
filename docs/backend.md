# Backend Documentation

## Backend Architecture

The backend is Laravel 12 on PHP 8.4. It exposes one web route and one API route, both using the same `DashboardService` data source.

### Current Route Surface

- `GET /` -> `App\Http\Controllers\DashboardController`
- `GET /api/dashboard` -> `App\Http\Controllers\Api\DashboardController` (throttled by `throttle:api`)

### Key Backend Modules

- `app/Services/DashboardService.php`: returns the dashboard data payload used by both web and API controllers
- `app/Http/Controllers/DashboardController.php`: renders the `Dashboard` Inertia page
- `app/Http/Controllers/Api/DashboardController.php`: returns JSON summary payload
- `app/Http/Middleware/HandleInertiaRequests.php`: shares Ziggy route metadata with Inertia pages

## Data and Persistence

Current dashboard data is static in-memory data inside `DashboardService` (no model or database read for dashboard payload yet).

## Testing

Pest is configured in `tests/Pest.php` and feature tests include API shape assertions and route-level behavior checks.

Run tests:

```bash
php backend/artisan test
```

## Notes

- Authentication endpoints are not currently implemented in this reduced dashboard-focused codebase.
- Add authentication modules (Fortify/Sanctum/etc.) only when corresponding backend routes/controllers are introduced.
