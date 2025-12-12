# Ensemble Calendar (Laravel 12 + Filament v4)

Calendar and ICS feed for rehearsals with multi-day support, rehearsal plans as PDF, and German localization by default.

## Features
- Admin panel (Filament v4) with roles `admin` and `ensemble`
- Rehearsals with multiple days (individual start/end per day) and private PDF plan upload
- Public calendar page (login required) with mobile-first cards and ICS subscription link
- Secure ICS feed protected by token; links back to the protected PDF download
- GDPR helpers: privacy/data deletion pages and cookie consent banner (session + consent cookies only)

## Setup
1. Copy `.env.example` to `.env` and adjust:
   - `APP_URL`, `APP_LOCALE=de`, `APP_TIMEZONE=Europe/Berlin`
   - `ADMIN_EMAIL` / `ADMIN_PASSWORD` and `ENSEMBLE_EMAIL` / `ENSEMBLE_PASSWORD`
   - Set a strong `ICS_FEED_TOKEN`
2. Install dependencies: `composer install`
3. Generate key: `php artisan key:generate`
4. Create database (default sqlite): `touch database/database.sqlite`
5. Run migrations and seed default users: `php artisan migrate --seed`
6. Frontend assets: `npm install` then `npm run dev` (or `build`) to compile Tailwind.

## Usage
- Ensemble login: `/login` (shared ensemble account from seeder; ready for per-user accounts later)
- Calendar view: `/calendar` (lists rehearsals, PDF plan buttons, per-day times, ICS link)
- ICS feed: `/calendar/feed/{ICS_FEED_TOKEN}` — copy this URL into Apple/Google/Outlook
- Admin panel: `/admin` (Filament, only accessible for `admin` role)
- Plan download route stays behind auth: `/rehearsals/{id}/plan`

## Legal/GDPR
- Pages: `/imprint`, `/privacy`, `/data-deletion`
- Cookies: session + `cookieConsent` only; plans stored on private disk (`storage/app/private`)
- ICS feed guarded by `ICS_FEED_TOKEN`; rotate regularly if shared externally.

## Tests
Run the feature test suite (sqlite in memory):
```bash
php artisan test
```
