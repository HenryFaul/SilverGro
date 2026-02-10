# SilverGro Feed & Grain - Application Overview

## What is SilverGro?

SilverGro is an enterprise **Transport & Logistics Management System** built for the South African feed and grain industry. It manages the full lifecycle of transport transactions that link **Suppliers**, **Customers**, and **Transporters** for the movement of grain and feed products.

The system handles everything from trade creation, pricing and cost calculations, driver/vehicle assignment, order confirmations (Sales Orders, Purchase Orders, Transport Orders), approval workflows, invoicing, and financial reporting.

## Core Business Concept

A **Transport Transaction** is the central entity. It represents a deal to move a product from a supplier to a customer using a transporter. Each transaction flows through stages:

1. **Trade Creation** - A trade is initiated linking a supplier, customer(s) (up to 5), transporter, and product
2. **Contract Assignment** - The trade is assigned a contract type (e.g., Purchase Contract, Sales Contract)
3. **Job Planning** - Transport dates, loading instructions, and logistics are configured
4. **Driver/Vehicle Assignment** - Drivers and vehicles are assigned to the job
5. **Order Confirmation** - Sales Orders, Purchase Orders, and Transport Orders are generated, activated, sent, and received
6. **Load Tracking** - Weighbridge data, product details, delivery notes are captured
7. **Financial Calculation** - Costs, selling prices, commissions, and gross profit are calculated
8. **Approval Workflow** - Deal tickets go through rule-based approval (based on trade value thresholds)
9. **Invoicing** - Transport invoices are generated
10. **Reporting** - Dashboard KPIs, debtor standing, custom reports, and Excel exports

## Key User Roles

- **Traders** - Create and manage trades, view transaction summaries
- **Operations/Logistics** - Manage driver/vehicle assignments, track loads, update statuses
- **Finance** - Manage invoicing, debtor standing, cost/price calculations
- **Admin** - Staff management, role/permission assignments, data imports
- **Management** - Dashboard overview, approval workflows, reporting

## Currency & Locale

- **Currency:** South African Rand (R / ZAR)
- **Timezone:** Africa/Johannesburg
- **Number Format:** `R 1 234 567.89` (space as thousands separator)

## Tech Stack Summary

| Layer | Technology |
|-------|-----------|
| Backend Framework | Laravel 10.x (PHP) |
| Frontend Framework | Vue 3 + Inertia.js |
| CSS Framework | Tailwind CSS 3.x |
| Build Tool | Vite 4.x |
| Database | MySQL |
| Authentication | Laravel Jetstream + Sanctum |
| Authorization | Spatie Laravel Permission (RBAC) |
| PDF Generation | DomPDF (barryvdh/laravel-dompdf) |
| Excel Import/Export | Maatwebsite Excel 3.x |
| Charts | ApexCharts (larapex-charts) |
| Activity Logging | Spatie Laravel Activity Log |
| Media/Files | Spatie Laravel Media Library |
| UI Components | HeadlessUI, Heroicons, SweetAlert2 |
| Maps | Google Maps JS API + Places Autocomplete |
| Date Handling | Moment.js, VueDatePicker |

## Project Structure

```
SilverGro-1/
├── app/
│   ├── Actions/          # Fortify/Jetstream action classes
│   ├── Charts/           # LarapexCharts classes
│   ├── Console/          # Artisan commands
│   ├── Exceptions/       # Exception handlers
│   ├── Http/
│   │   ├── Controllers/  # 61 controllers
│   │   └── Middleware/    # Request middleware
│   ├── Imports/          # Excel import classes
│   ├── Models/           # 62 Eloquent models
│   ├── Notifications/    # Database notifications
│   ├── Providers/        # Service providers
│   └── Rules/            # Custom validation rules
├── config/               # Configuration files
├── database/
│   └── migrations/       # 70 migration files
├── resources/
│   ├── css/              # Tailwind CSS entry
│   ├── js/
│   │   ├── Components/   # 27 base + 30 UI components
│   │   ├── Composables/  # Vue composables
│   │   ├── Layouts/      # 2 app layouts
│   │   └── Pages/        # 50+ page components
│   └── views/            # Blade templates (PDF, email)
├── routes/
│   ├── web.php           # ~150 web routes
│   └── api.php           # API routes (Sanctum)
├── public/               # Public assets
├── storage/              # Logs, cache, uploads
└── tests/                # PHPUnit tests
```

## Key Navigation Areas

| Section | Description |
|---------|-------------|
| Dashboard | Monthly KPIs: planned tons, weights, costs, GP% |
| Customers | Customer & parent customer management with credit limits |
| Suppliers | Supplier profiles and contacts |
| Transporters | Transporter, driver, and vehicle management |
| Products | Product catalog |
| Trades | Transaction list with advanced filtering |
| Views | Diary, Week Plan, Spreadsheet, Trade Summary, PC/SC/Home Overviews |
| Contacts | Centralized contact directory |
| Debtor Standing | Outstanding balances and overdue tracking |
| Reports | Custom report builder and Excel exports |
| Admin | Staff management, roles/permissions, notifications |
