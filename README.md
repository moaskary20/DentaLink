# DentaLink — Dental Lab Management Platform

A full-stack Laravel + Filament platform connecting dentists with dental laboratories for crown, bridge, implant, and restoration workflows.

## Tech Stack

- **Backend:** Laravel 12
- **Admin Panel:** Filament v3
- **Database:** SQLite (default) / MySQL
- **Frontend Theme:** Custom DentaLink CSS (matching `test.html` design)
- **Language:** English

## Features

### Core Modules
- **Lab Registration** — profiles, licenses, admin approval, services & pricing
- **Dentist Registration** — accounts, verification, professional profiles
- **Lab Marketplace** — browse labs with filters (price, country, rating, service type)
- **Order Workflow** — multi-step creation, file uploads, lab selection, payment
- **Order Stages** — Received → In Production → Quality Review → Shipping → Delivery
- **Tracking** — Amazon-style timeline, logs, dual-party approvals
- **Payments** — wallet, Visa/Mastercard/Apple Pay/Google Pay, commissions
- **Ratings** — dentist ↔ lab reviews
- **Chat** — real-time messaging between doctor and lab
- **Notifications** — order updates, AI alerts, payment reminders
- **AI Assistant** — smart matching, image analysis, delivery prediction, chatbot
- **Reports** — analytics dashboard, lab performance, service breakdown
- **Admin Panel** — user management, approvals, commission settings

## Panels

| Panel | URL | Role | Login |
|-------|-----|------|-------|
| Doctor App | `/` | Doctor | doctor@dentalink.com |
| Admin | `/admin` | Admin | admin@dentalink.com |
| Lab | `/lab` | Lab | lab.doha@dentalink.com |

**Default password for all accounts:** `password`

## Quick Start

```bash
cd platform

# Install dependencies
composer install
npm install

# Environment (SQLite is pre-configured)
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed

# Build assets
npm run build

# Start server
php artisan serve
```

Visit **http://localhost:8000** and log in as a doctor.

## Project Structure

```
platform/
├── app/
│   ├── Enums/           # UserRole, OrderStatus, etc.
│   ├── Models/          # Eloquent models
│   ├── Filament/
│   │   ├── App/         # Doctor panel (pages, resources, widgets)
│   │   └── Admin/       # Admin panel resources
│   └── Providers/Filament/
├── database/
│   ├── migrations/
│   └── seeders/DentaLinkSeeder.php
├── resources/
│   ├── css/filament/dentalink/theme.css
│   └── views/filament/app/pages/
└── test.html            # Original design reference (parent folder)
```

## Seeded Demo Data

- 6 certified labs (Qatar, UAE, Saudi Arabia, Germany, Jordan)
- 5 sample orders (#ORD-2847, #ORD-2845, etc.)
- Wallet balance: $1,240
- Chat conversations with Doha Specialized Lab
- 7 notifications
- Commission rates: Standard 5%, Express 7%, Premium 3%

## Design

The UI replicates the `test.html` prototype with:
- Primary color `#0A6EBD`
- Dark sidebar `#0D1B2A`
- Stat cards, timelines, lab cards, wallet gradient
- Page animations (fade-in, slide-in, hover effects, chart bars)

## License

Proprietary — DentaLink Platform
