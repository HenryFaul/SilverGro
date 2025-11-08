# SilverGro - Agricultural Trading & Transport Management

> A comprehensive Laravel-based web application for managing agricultural commodities trading, transport logistics, customer relationships, and financial transactions.

## 🚀 Tech Stack

- **Backend:** Laravel 12.0 (PHP 8.2+)
- **Frontend:** Vue.js 3.5.0 with Composition API
- **SPA Framework:** Inertia.js 2.0
- **Styling:** Tailwind CSS 3.4.4
- **Authentication:** Laravel Jetstream with Sanctum
- **Permissions:** Spatie Laravel Permission
- **Database:** MySQL/PostgreSQL

## 📚 AI-Enhanced Documentation

This project includes comprehensive AI-friendly documentation to help with development, debugging, and feature implementation. All documentation is located in the `.ai/` directory:

### 📖 Available Documentation

- **[.ai/README.md](.ai/README.md)** - Start here! Guide to all AI documentation
- **[.ai/PROJECT_OVERVIEW.md](.ai/PROJECT_OVERVIEW.md)** - High-level project understanding
- **[.ai/CODING_STANDARDS.md](.ai/CODING_STANDARDS.md)** - Code style and best practices
- **[.ai/DATABASE_SCHEMA.md](.ai/DATABASE_SCHEMA.md)** - Complete database structure reference
- **[.ai/COMMON_PATTERNS.md](.ai/COMMON_PATTERNS.md)** - Reusable code patterns and examples
- **[.ai/AI_PROMPTING_GUIDE.md](.ai/AI_PROMPTING_GUIDE.md)** - Templates for effective AI prompts
- **[.ai/QUICK_REFERENCE.md](.ai/QUICK_REFERENCE.md)** - Quick reference cheat sheet

### 🤖 Using AI Assistance

When working with AI assistants on this project, provide context from the `.ai/` documentation:

```
I need help with [TASK]. Please reference:
- .ai/PROJECT_OVERVIEW.md for project context
- .ai/DATABASE_SCHEMA.md for database structure
- .ai/COMMON_PATTERNS.md for implementation patterns
```

See [.ai/AI_PROMPTING_GUIDE.md](.ai/AI_PROMPTING_GUIDE.md) for detailed prompt templates.

## 🏗️ Project Structure

```
app/
├── Actions/          # Business logic classes
├── Http/
│   ├── Controllers/  # Request handlers
│   └── Middleware/   # Custom middleware
├── Models/           # Eloquent models
├── Imports/          # Excel import handlers
└── Mail/             # Email templates

resources/
├── js/
│   ├── Pages/        # Inertia page components
│   ├── Components/   # Reusable Vue components
│   └── Layouts/      # Layout components
└── css/              # Styles

routes/
├── web.php           # Web routes
└── api.php           # API routes

.ai/                  # AI documentation (see above)
```

## 🚦 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd SilverGro
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   Edit `.env` with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=silvergro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` in your browser.

## 🧪 Development

### Running in Development Mode

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server (hot reload)
npm run dev
```

### Building for Production

```bash
npm run build
```

## 🔑 Key Features

- **Customer Management** - Customer records, credit management, parent-child relationships
- **Transport & Logistics** - Transport orders, jobs, loads, driver/vehicle management
- **Transaction Management** - Sales orders, purchase orders, contract tracking
- **Product Management** - Product catalog, sources, packaging
- **Trading & Pricing** - Trade rules, pricing calculations, commission management
- **Staff & Users** - Role-based access control, permissions, staff commission
- **Document Management** - Document storage, deal tickets, activity logs
- **Reporting & Analytics** - Custom reports, charts, dashboard analytics

## 📋 Common Tasks

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run ESLint
npm run lint

# Format JavaScript with Prettier
npm run format
```

### Database

```bash
# Run migrations
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Fresh database with seeds
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

### Cache Management

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter CustomerTest

# Run with coverage
php artisan test --coverage
```

## 📖 Documentation

For detailed documentation on:
- **Project architecture** → See [.ai/PROJECT_OVERVIEW.md](.ai/PROJECT_OVERVIEW.md)
- **Coding standards** → See [.ai/CODING_STANDARDS.md](.ai/CODING_STANDARDS.md)
- **Database schema** → See [.ai/DATABASE_SCHEMA.md](.ai/DATABASE_SCHEMA.md)
- **Code patterns** → See [.ai/COMMON_PATTERNS.md](.ai/COMMON_PATTERNS.md)
- **Quick reference** → See [.ai/QUICK_REFERENCE.md](.ai/QUICK_REFERENCE.md)

## 🤝 Contributing

1. Read the [Coding Standards](.ai/CODING_STANDARDS.md)
2. Create a feature branch
3. Make your changes following the established patterns
4. Write/update tests
5. Submit a pull request

## 📝 License

This project is proprietary software. All rights reserved.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
