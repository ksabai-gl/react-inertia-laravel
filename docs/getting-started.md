# Getting Started

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.4
- Node.js (LTS version recommended)
- Composer
- Git

## 🚀 Installation

1. **Clone the Repository**

```bash
git clone <your-repository-url> your-project-name
cd your-project-name
```

2. **Install PHP Dependencies**

```bash
composer install --working-dir=backend
```

3. **Install Node.js Dependencies**

```bash
pnpm install --dir frontend
```

4. **Environment Setup**

```bash
cp backend/.env.example backend/.env
php backend/artisan key:generate
```

5. **Configure Your Environment**
   Edit `backend/.env` with your database and other configuration settings:

6. **Create Database and Initial User**

```bash
php backend/artisan migrate --seed
```

Using the `--seed` flag will create an initial user you can use to access the dashboard.

7. **Build Assets**

```bash
pnpm run dev
```

## 🏃‍♂️ Development Workflow

### Start the Development Server

```bash
pnpm run dev
```

Your application will be available at `http://react-inertia-laravel.local`.

### Development Commands

```bash
# Run tests
php backend/artisan test

# Format PHP code
backend/vendor/bin/pint

# Type check TypeScript
pnpm run typecheck

# Lint JavaScript/TypeScript
pnpm run lint

# Format JavaScript/TypeScript
pnpm run format
```

## 📦 Production Deployment

1. **Optimize Composer**

```bash
composer install --optimize-autoloader --no-dev --working-dir=backend
```

2. **Build Frontend Assets**

```bash
pnpm run build
```

3. **Cache Configuration**

```bash
php backend/artisan config:cache
php backend/artisan route:cache
php backend/artisan view:cache
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request
