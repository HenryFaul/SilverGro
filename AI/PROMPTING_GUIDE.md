# SilverGro - AI Prompting Guide

This guide helps AI assistants (Claude, Cursor, Copilot, etc.) work effectively with the SilverGro codebase. Reference this file when starting any coding session.

## Quick Context

**SilverGro** is a Laravel 10 + Vue 3 + Inertia.js transport logistics management app for the South African grain/feed industry. It manages trades linking Suppliers, Customers, and Transporters for product delivery.

## Essential Files to Read First

When working on any feature or bug, always start by reading these:

| Area | Files |
|------|-------|
| Routes | `routes/web.php` |
| Central Model | `app/Models/TransportTransaction.php` |
| Financial Logic | `app/Models/TransportFinance.php` (calculateFields method) |
| Approval Logic | `app/Models/DealTicket.php` (calculateRules method) |
| Main Layout | `resources/js/Layouts/AppLayout.vue` |
| Example Controller | `app/Http/Controllers/CustomerController.php` |
| Example Index Page | `resources/js/Pages/Customer/Index.vue` |
| Example Show Page | `resources/js/Pages/Customer/Show.vue` |

## Coding Conventions in This Project

### Backend (PHP/Laravel)
- **Relationship methods:** PascalCase (e.g., `TransportFinance()`, `Customer()`) - NOT the standard camelCase convention
- **Fillable arrays:** Defined on models as `public $fillable = [...]`
- **Soft deletes:** Used on nearly all models
- **Filter scopes:** Models use `scopeFilter(Builder $query, array $filters)` for search/filter
- **No Form Requests:** Validation is inline in controllers
- **No Policies:** Authorization is ad-hoc (some controllers check, most don't)
- **Flash messages:** `$request->session()->flash('flash.bannerStyle', 'success|danger')` and `$request->session()->flash('flash.banner', 'Message')`
- **Inertia responses:** `return inertia('Page/Name', ['prop' => $value])` or `Inertia::render()`

### Frontend (Vue 3/Inertia)
- **Composition API:** All components use `<script setup>`
- **Form handling:** `useForm()` from `@inertiajs/vue3`
- **Routing:** `route('name')` via Ziggy (Laravel route names in JS)
- **Filtering:** Debounced with `lodash/debounce`, sends GET request to server
- **Modals:** HeadlessUI `Dialog` with `TransitionRoot`
- **Slide-overs:** HeadlessUI `Dialog` sliding from right
- **Icons:** `@heroicons/vue/24/solid` and `/outline`
- **Alerts:** `inject('$swal')` for SweetAlert2
- **Permissions:** `usePage().props.roles_permissions.permissions.includes('permission_name')`
- **Currency format:** `"R " + number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, " ")`
- **Date format:** Uses `Africa/Johannesburg` timezone throughout
- **No TypeScript:** Pure JavaScript

## Key Patterns

### Creating a New Index Page (List View)
Follow the pattern in `Customer/Index.vue`:
1. Accept `items` and `filters` props from controller
2. Create `filterForm = useForm({...})` with filter fields
3. Create debounced `filter()` function that calls `filterForm.get(route('resource.index'))`
4. Watch filter fields to trigger filter()
5. Sortable column headers that update `field` and `direction`
6. SlideOver component for "Add New" functionality
7. PaginationModified component at bottom

### Creating a New Show Page (Detail View)
Follow the pattern in `Customer/Show.vue`:
1. Accept entity and lookup data as props
2. Create `form = useForm({...entity data})`
3. Submit via `form.put(route('resource.update', id))`
4. Inline editing with save button
5. Related data displayed in sections

### Creating a New Controller
Follow the pattern in `SupplierController.php`:
1. `index()` - Accept filters, query with scope, paginate, return Inertia
2. `store()` - Validate, create, flash message, redirect back
3. `show()` - Load with relations, pass to Inertia
4. `update()` - Validate, update, flash message, redirect back
5. `destroy()` - Soft delete

### Creating a New Model
1. Extend `Model`, use `HasFactory`, `SoftDeletes`
2. Define `$fillable` array
3. Add relationships (PascalCase methods in this project)
4. Add `scopeFilter()` if it will appear in an index/list

## Common Prompts for Working on This App

### Bug Fixes
```
Read the file [path] and fix [description]. Follow the existing patterns in the codebase.
Make sure to check for the orWhere grouping bug pattern (see AI/TODO.md BUG-001).
```

### Adding a New Entity/CRUD
```
Create a full CRUD for [EntityName] following the existing patterns:
1. Migration in database/migrations/
2. Model in app/Models/ with fillable, SoftDeletes, relationships, and scopeFilter
3. Controller in app/Http/Controllers/ with index, store, show, update methods
4. Route resource in routes/web.php
5. Index.vue page in resources/js/Pages/[Entity]/
6. Show.vue page in resources/js/Pages/[Entity]/
7. [Entity]SlideOver.vue component in resources/js/Components/UI/

Reference Customer/CustomerController/Customer pages as the pattern to follow.
```

### Financial Logic Changes
```
Read app/Models/TransportFinance.php - specifically the calculateFields() method.
Also read AI/BUSINESS_RULES.md section 2 "Financial Calculations" for the formulas.
[Describe your change]
```

### Approval Workflow Changes
```
Read app/Models/DealTicket.php - specifically calculateRules() and getAppliedRules().
Also read AI/BUSINESS_RULES.md section 3 "Approval Workflow".
[Describe your change]
```

### Frontend Component Changes
```
Read resources/js/Layouts/AppLayout.vue for navigation structure.
Read resources/js/Pages/[relevant page].vue for the current implementation.
This project uses Vue 3 Composition API (<script setup>), Tailwind CSS, HeadlessUI, and Inertia.js.
[Describe your change]
```

## Known Gotchas

1. **Relationship naming:** All Eloquent relationships use PascalCase (non-standard). Don't change to camelCase without updating all references.
2. **Transaction ID starts at 13000:** Defined in migration, not auto-increment from 1.
3. **Up to 5 customers per transaction:** `customer_id` through `customer_id_5` with matching `selling_price_2-5` and `transport_cost_2-5` fields.
4. **Actual vs Planned financials:** The system maintains dual calculations - planned (from load units) and actual (from weighbridge weights). Both must be updated.
5. **Commission is split evenly:** `gross_profit / 2` per side (supplier/customer), then divided equally among assigned users.
6. **Transport rate basis ID 3 = Per Load:** This is a flat rate. Other IDs use per-ton calculation.
7. **is_transport_costs_inc_price:** When true, transport costs are NOT added to total_cost_price (they're included in the selling price).
8. **Split loads:** Complex feature - be careful with `is_split_load`, `is_split_load_primary`, `is_split_load_member` flags.
9. **Flash messages use Jetstream's banner:** Not standard Laravel session flash.
10. **No queue processing:** QUEUE_CONNECTION=sync means all jobs run synchronously.

## File Naming Conventions

| Type | Convention | Example |
|------|-----------|---------|
| Model | PascalCase singular | `TransportTransaction.php` |
| Controller | PascalCase + Controller | `TransportTransactionController.php` |
| Migration | snake_case with timestamp | `2023_05_28_165403_create_transport_transactions_table.php` |
| Vue Page | PascalCase in folder | `Pages/Customer/Index.vue` |
| Vue Component | PascalCase | `Components/UI/CustomerSlideOver.vue` |
| Route name | snake_case with dots | `transport_transaction.index` |
