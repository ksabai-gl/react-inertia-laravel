# Backend Documentation

## 🏗️ Backend Architecture

The backend of this application is built with Laravel 12.x and PHP 8.4, following modern best practices and design patterns. It uses Laravel Fortify for authentication, Laravel Sanctum for API tokens, and Ziggy for route handling.

### Directory Structure

All backend code lives under `backend/`:

```
backend/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
├── config/
├── resources/views/   # Inertia root Blade template
└── tests/
```

## 🔐 Authentication (Laravel Fortify)

We use Laravel Fortify for authentication, which provides:

- Login and Registration
- Email Verification
- Two-Factor Authentication
- Password Reset
- Password Confirmation
- Profile Information Updates

### Fortify Configuration

Fortify is configured in `config/fortify.php`. Key features enabled:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication(),
],
```

### Authentication Flow

1. User submits credentials
2. Fortify validates credentials
3. On success:
    - Session is created
    - User is redirected to dashboard
4. On failure:
    - Error response is returned
    - User stays on login page

## 📡 API Endpoints

### Authentication Endpoints

```
POST   /login                    # User login
POST   /logout                   # User logout
POST   /register                 # User registration
POST   /forgot-password         # Password reset request
POST   /reset-password          # Password reset
GET    /email/verify            # Email verification
POST   /email/verification-notification  # Resend verification email
```

### User Management Endpoints

```
PUT    /user/profile-information  # Update profile
PUT    /user/password            # Update password
POST   /user/two-factor-authentication  # Enable 2FA
DELETE /user/two-factor-authentication  # Disable 2FA
```

## 💾 Database

### Database Configuration

The project uses SQLite by default, but supports any database. Configure your database in `backend/.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

### Database Seeding

The project includes database seeders to create initial data. Run migrations with seeders:

```bash
php backend/artisan migrate --seed
```

This will create an initial user account that you can use to access the dashboard.

### Key Models

- `User.php`: User account information
- `Profile.php`: Extended user profile data
- `Session.php`: User session management

### Migrations

All database schemas are defined in migrations under `database/migrations/`. Key migrations:

- User table
- Password reset tokens
- Failed jobs table
- Sessions table
- Two-factor authentication settings

Run migrations with:

```bash
php backend/artisan migrate
```

## 🔄 Inertia Integration

### Middleware

The `HandleInertiaRequests` middleware (`app/Http/Middleware/HandleInertiaRequests.php`) manages:

- Sharing common data with all pages
- Managing the Inertia response
- Handling version conflicts

### Shared Data

Common data shared with all pages through Inertia:

```php
public function share(Request $request): array
{
    return [
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'message' => fn () => $request->session()->get('message')
        ],
    ];
}
```

## 🧪 Testing

### Test Structure

```
tests/
├── Feature/          # Feature tests
├── Unit/            # Unit tests
└── TestCase.php     # Base test class
```

### Running Tests

```bash
# Run all tests
php backend/artisan test

# Run specific test
php backend/artisan test --filter=UserTest

# Run with coverage
php backend/artisan test --coverage
```

## 🛠️ Development Tools

### Artisan Commands

Useful Artisan commands for development:

```bash
# Create a new controller
php backend/artisan make:controller UserController

# Create a new model with migration
php backend/artisan make:model Post -m

# Create a new test
php backend/artisan make:test UserTest

# Clear cache
php backend/artisan cache:clear
```

### Code Style

We use Laravel Pint for PHP code styling. Run:

```bash
./vendor/bin/pint
```

## 🔍 Debugging

### Laravel Telescope

If enabled, Laravel Telescope provides debugging tools for:

- Request/Response information
- Database queries
- Cache operations
- Queue jobs
- Scheduled tasks

Access Telescope at `/telescope` in development.

### Error Handling

Custom error handling is configured in `app/Exceptions/Handler.php`. Errors are:

1. Logged to storage/logs
2. Reported to error tracking service (if configured)
3. Rendered appropriately based on the request type
