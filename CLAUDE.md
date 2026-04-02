# Mosul Boulevard Project (MBP)

Construction management system for a real estate development in Mosul, Iraq.
241 villas (Type A: 165, Type B: 76) + 6 towers × 80 apartments = 480 units.

## Commands

- `composer run dev` — Start Laravel + Vite
- `php artisan migrate` — Run migrations
- `php artisan migrate:fresh --seed` — Reset & seed
- `php artisan test` — Run Pest tests
- `./vendor/bin/pint` — Code style fix (PSR-12)
- `npx tsc --noEmit` — TypeScript check
- `php artisan route:list --path=api` — List API routes

## Architecture

Laravel 13 monolith with two React frontends via Inertia.js. Deployed on Laravel Cloud.

- `app/Http/Controllers/Api/V1/` — REST API for mobile app
- `app/Http/Controllers/Dashboard/` — Inertia admin CRUD
- `app/Http/Controllers/Website/` — Inertia public pages
- `app/Http/Requests/` — FormRequest per action
- `app/Http/Resources/` — API Resource transformers
- `app/Models/` — Eloquent models
- `app/Enums/` — PHP 8.3 enums
- `app/Services/` — Business logic
- `app/Policies/` — Authorization
- `app/Observers/` — Event side-effects
- `resources/js/dashboard/` — React admin pages
- `resources/js/website/` — React public pages
- `resources/js/shared/` — Shared components
- `routes/api.php` — API routes (/api/v1/*)
- `routes/web.php` — Web routes
- `routes/dashboard.php` — Dashboard routes

## Tech Stack

- Backend: Laravel 13.x, PHP 8.3+, Sanctum
- Database: MySQL 8 (Laravel Cloud managed)
- Cache: Redis (Laravel Cloud Cache)
- Frontend: React 19, TypeScript, Inertia.js, Tailwind 4, shadcn/ui
- Storage: Laravel Cloud Object Storage (R2)
- Real-time: Reverb WebSockets
- Deployment: Laravel Cloud PaaS (push-to-deploy)

## Code Style

- PSR-12 via Pint. Always use FormRequest for validation.
- Always use API Resources for JSON. Never return raw models.
- Always use Policies for auth. Never check roles in controllers.
- Keep controllers thin. Business logic in Services.
- TypeScript strict. No `any`. Zod for forms. TanStack Query for data.
- Tailwind + shadcn/ui only. No inline styles.

## Do NOT

- Use dd() in committed code
- Hardcode status strings (use status_options FK)
- Store files locally (use R2 Storage facade)
- Return raw Eloquent models from API
- Put business logic in controllers
- Use env() outside config files

## Brand

- Gold: #B8860B, Dark: #1B1B1B, Blue: #1B4F72
- Font: Inter (UI), Arial (docs)
