# SilverGro Project Overview

## Project Type

**Agricultural Trading & Transport Management System**

A Laravel-based web application for managing agricultural commodities trading, transport logistics, customer
relationships, and financial transactions.

## Tech Stack

### Backend

- **Framework**: Laravel 12.0 (PHP 8.2+)
- **Authentication**: Laravel Jetstream with Sanctum
- **Database**: MySQL/PostgreSQL (configurable)
- **Key Packages**:
    - `spatie/laravel-permission` - Role & Permission management
    - `spatie/laravel-activitylog` - Activity logging
    - `spatie/laravel-medialibrary` - Media management
    - `barryvdh/laravel-dompdf` - PDF generation
    - `maatwebsite/excel` - Excel import/export
    - `marineusde/larapex-charts` - Charts and graphs

### Frontend

- **Framework**: Vue.js 3.5.0 with Composition API
- **Inertia.js**: 2.0.3 (Server-side rendering)
- **UI Components**:
    - Tailwind CSS 3.4.4
    - HeadlessUI Vue
    - Heroicons
    - ApexCharts (for data visualization)
    - VueDatePicker
    - SweetAlert2
    - Maz-UI components

### Build Tools

- **Vite** 4.5.0
- **ESLint** with Prettier
- **PostCSS** with Autoprefixer

## Project Structure

### Core Business Domains

#### 1. Customer Management

- Customer records with parent-child relationships
- Customer ratings and credit management
- Debtor standing tracking
- Contact details (email, phone)

#### 2. Transaction Management

- Transaction summaries
- Sales orders and purchase orders
- Old transaction imports
- Contract summaries (PC, SC)
- Trans links and splits

#### 3. Transport & Logistics

- Transport orders and jobs
- Transport loads and invoices
- Driver and vehicle management
- Transport approvals and finance
- Deal tickets
- **Approval Workflows:**
    - **MQ (Market Quote) - contract_type_id = 4**: Complex approval workflow with trade rules, role-based approvals,
      and deal ticket activation. Assigns sequential `a_mq` number.
    - **PC (Purchase Confirmation) - contract_type_id = 2**: Simplified approval that assigns sequential `a_pc` number
      for purchase-focused contracts.
    - **SC (Sales Confirmation) - contract_type_id = 3**: Simplified approval that assigns sequential `a_sc` number for
      sales-focused contracts.
    - **Direct Sale - contract_type_id = 1**: No approval workflow required.

#### 4. Product Management

- Product catalog
- Product sources
- Packaging types
- Billing units

#### 5. Trading & Pricing

- Trade rules and opportunities
- Contract types
- Pricing calculations
- Commission management (assigned user comm)

#### 6. Staff & Users

- Staff management
- Role-based access control
- User permissions
- Staff commission assignments

#### 7. Document Management

- Document storage
- Deal ticket notifications
- Activity logs
- Media library

#### 8. Reporting & Analytics

- Custom reports
- Monthly GP charts
- Monthly PC charts
- Dashboard analytics
- Planning diary and weekly planning

## Key Models

### Primary Entities

- `Customer` - Customer records with soft deletes
- `Supplier` - Supplier information
- `TransactionSummary` - Financial transactions
- `TransportTransaction` - Transport-related transactions
- `TransportOrder` - Transport job orders
- `TransportLoad` - Load details
- `DealTicket` - Deal documentation
- `Staff` - Internal staff members
- `User` - System users (extends Jetstream)
- `Product` - Product catalog
- `SalesOrder` / `PurchaseOrder` - Order management

### Supporting Entities

- `Address` / `AddressType` - Address management
- `Contact` / `ContactType` - Contact information
- `TermsOfPayment` - Payment terms
- `CustomerRating` - Credit ratings
- `TransportStatus` - Status tracking
- `TradeRule` / `TradeRuleOpp` - Trading rules for MQ approvals
- `ContractType` - Contract type definitions (Direct, PC, SC, MQ)
- `ContractSummary` - Contract summaries
- `PcSummary` - PC contract summaries
- `TransportApproval` - MQ approval records with role-based workflow
- `PcScApprovalController` - Handles PC/SC approval logic (controller, not model)

## Routes Structure

- Web routes in `routes/web.php`
- API routes in `routes/api.php`
- Protected by authentication middleware
- Permission-based access control

## Key Features

### Data Management

- Excel imports for customers, suppliers, transporters, products, transactions
- Bulk data operations
- Data validation and error handling

### User Interface

- Responsive design with Tailwind CSS
- Interactive data tables with pagination
- Modal dialogs and slide-overs
- Date pickers and comboboxes
- Real-time notifications

### Business Logic

- Credit limit management (soft and hard limits)
- Days overdue tracking
- Commission calculations
- Trade rule enforcement
- Transport rate calculations
- Invoice generation and status tracking
- **Approval Workflows:**
    - MQ contracts require multi-stage approval with trade rules and role-based permissions
    - PC/SC contracts use simplified approval with sequential numbering
    - Automatic assignment of unique approval numbers (a_mq, a_pc, a_sc)
    - Approval numbers used for searching and filtering across HomeOverview and TransactionSummary views
    - Display format: `(MQ:123)`, `(PC:45)`, `(SC:67)` in ID columns

### Reporting

- Custom report builder
- Monthly GP and PC charts
- Transaction summaries
- Transport finance reports
- Debtor standing reports

## Development Patterns

### Backend Patterns

- Controller-Model architecture
- Service classes in `app/Actions/`
- Form requests for validation
- Eloquent relationships extensively used
- Activity logging for audit trails
- Queue jobs for background processing

### Frontend Patterns

- Vue 3 Composition API with `<script setup>`
- Inertia.js for SPA-like experience
- Reusable components in `resources/js/Components/`
- Page components in `resources/js/Pages/`
- Layout components for consistent UI
- Prop-based component communication

### Code Organization

- Namespaced controllers
- Model relationships defined in models
- Middleware for auth and permissions
- Custom form requests for validation
- Resource classes for API responses (if applicable)

## Database Conventions

- Snake case for column names
- Soft deletes where applicable
- Foreign key relationships
- Timestamps on all tables
- Activity log table for auditing

## Testing

- PHPUnit configured
- Feature and Unit test directories
- `CreatesApplication` trait for tests

## Deployment

- Docker support via `docker-compose.yml`
- Laravel Sail for local development
- Artisan commands for maintenance
- Queue workers for background jobs

## Common Issues to Watch For

1. **Permission Checks**: Ensure proper permission checks on all routes
2. **Soft Deletes**: Remember to handle soft-deleted records
3. **Relationship Loading**: Use eager loading to avoid N+1 queries
4. **Date Handling**: Consistent timezone handling (Africa/Johannesburg)
5. **Validation**: Server-side validation for all forms
6. **Transaction Integrity**: Use database transactions for multi-step operations
7. **File Uploads**: Proper validation and storage handling

## Future Enhancement Areas

1. API development for mobile apps
2. Advanced reporting and analytics
3. Real-time notifications via WebSockets
4. Enhanced document management
5. Integration with external systems
6. Automated testing coverage
7. Performance optimization for large datasets

