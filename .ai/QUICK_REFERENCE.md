# Quick Reference Cheat Sheet

> **Quick reference for common tasks and commands in the SilverGro project**

## 🚦 Project Stack at a Glance

- **Backend:** Laravel 12.0, PHP 8.2+
- **Frontend:** Vue 3.5.0 (Composition API), Inertia.js 2.0
- **Styling:** Tailwind CSS 3.4.4
- **Database:** MySQL/PostgreSQL
- **Auth:** Laravel Jetstream + Sanctum
- **Permissions:** Spatie Laravel Permission

## 📁 Key Directories

```
app/
├── Http/Controllers/       # Request handlers
├── Models/                 # Eloquent models
├── Actions/               # Business logic classes
├── Imports/               # Excel import handlers
└── Mail/                  # Email templates

resources/
├── js/
│   ├── Pages/            # Inertia pages
│   ├── Components/       # Reusable Vue components
│   └── Layouts/          # Layout components
└── views/                # Blade templates (minimal)

routes/
├── web.php               # Web routes
└── api.php               # API routes
```

## 🛠️ Common Artisan Commands

```bash
# Development
php artisan serve                    # Start dev server
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Fresh DB with seeds
php artisan db:seed                  # Run seeders

# Code Generation
php artisan make:model Customer -mcr  # Model + migration + controller + resource
php artisan make:controller CustomerController --resource
php artisan make:request StoreCustomerRequest
php artisan make:migration create_customers_table

# Cache & Optimization
php artisan optimize:clear           # Clear all caches
php artisan config:cache             # Cache config
php artisan route:cache              # Cache routes
php artisan view:cache               # Cache views

# Permissions (Spatie)
php artisan permission:cache-reset   # Reset permission cache

# Queue & Jobs
php artisan queue:work               # Process queue
php artisan queue:listen             # Listen for jobs

# Maintenance
php artisan down                     # Enable maintenance mode
php artisan up                       # Disable maintenance mode
```

## 🎨 Common Tailwind Classes

```css
/* Layout */
flex flex-col flex-row items-center justify-between
grid grid-cols-1 md:grid-cols-3 gap-4
container mx-auto px-4 py-6

/* Spacing */
m-4 mt-2 mb-6 mx-auto my-8    # Margin
p-4 pt-2 pb-6 px-4 py-8       # Padding
space-x-4 space-y-2            # Gap between children

/* Sizing */
w-full w-1/2 w-64 h-screen h-32
max-w-7xl max-h-96 min-w-0

/* Typography */
text-sm text-base text-lg text-xl text-2xl
font-normal font-medium font-semibold font-bold
text-gray-900 text-blue-600 text-red-500

/* Backgrounds & Borders */
bg-white bg-gray-50 bg-blue-600
border border-gray-300 rounded-md rounded-lg
shadow shadow-sm shadow-lg

/* Interactive */
hover:bg-gray-50 focus:ring-2 active:bg-gray-100
cursor-pointer disabled:opacity-50
transition duration-150 ease-in-out
```

## 🔐 Permission Checks

### Backend (PHP)
```php
// In controller
$this->authorize('update', $customer);
abort_unless(auth()->user()->can('edit customers'), 403);

// In blade/view
@can('edit customers')
    <!-- content -->
@endcan

// In code
if (auth()->user()->hasPermissionTo('edit customers')) {
    // ...
}
```

### Frontend (Vue)
```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const can = computed(() => page.props.auth.permissions || {});
</script>

<template>
  <button v-if="can['edit customers']">Edit</button>
</template>
```

## 📝 Route Patterns

```php
// Resource routes
Route::resource('customers', CustomerController::class);

// Named routes with middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

// Routes with permissions
Route::middleware(['auth', 'permission:manage customers'])->group(function () {
    Route::resource('customers', CustomerController::class);
});
```

## 🎭 Inertia Patterns

```javascript
// Render a page
return Inertia::render('Customer/Index', [
    'customers' => $customers,
    'filters' => $request->only(['search']),
]);

// Navigate
router.visit(route('customers.show', customerId));

// Form submission
const form = useForm({ name: '' });
form.post(route('customers.store'), {
    preserveScroll: true,
    onSuccess: () => { /* ... */ },
});

// Reload data
router.reload({ only: ['customers'] });
```

## 🔍 Eloquent Patterns

```php
// Basic queries
Customer::find($id);
Customer::where('is_active', true)->get();
Customer::active()->paginate(15);

// Relationships
$customer->load('transportTransactions');
Customer::with(['customerRating', 'addresses'])->get();

// Scopes
Customer::active()->whereHas('transportTransactions')->get();

// Aggregates
Customer::count();
TransportTransaction::sum('total_price');
Customer::where('is_active', true)->avg('credit_limit');
```

## 🧪 Testing Quick Start

```php
// Feature Test
public function test_customer_can_be_created()
{
    $response = $this->actingAs($user)
        ->post(route('customers.store'), [
            'first_name' => 'John',
            'nickname' => 'JD',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('customers', ['first_name' => 'John']);
}

// Run tests
php artisan test
php artisan test --filter CustomerTest
```

## 📦 NPM Commands

```bash
# Development
npm run dev                  # Start Vite dev server
npm run build               # Build for production
npm run build -- --ssr      # Build SSR

# Code Quality
npm run lint                # Run ESLint
npm run format              # Run Prettier
```

## 🗃️ Database Conventions

```php
// Table names: plural, snake_case
customers, transport_transactions, customer_parents

// Foreign keys: singular_table_name_id
customer_id, transport_transaction_id

// Pivot tables: alphabetical order
customer_product, not product_customer

// Timestamps: created_at, updated_at, deleted_at
// Boolean: is_active, has_access, can_edit
```

## 🎨 Vue Component Template

```vue
<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';

// Props
const props = defineProps({
    customer: Object,
});

// Emits
const emit = defineEmits(['saved', 'closed']);

// Reactive state
const form = useForm({
    name: props.customer?.name || '',
});

// Computed
const isValid = computed(() => form.name.length > 0);

// Methods
const submit = () => {
    form.post(route('customers.store'), {
        onSuccess: () => emit('saved'),
    });
};

// Lifecycle
onMounted(() => {
    // initialization
});

// Watchers
watch(() => props.customer, (newVal) => {
    // react to changes
});
</script>

<template>
  <div>
    <!-- Template -->
  </div>
</template>
```

## 🐛 Debugging

```php
// Dump and die
dd($variable);
dump($variable); // Continue execution

// Logging
Log::info('Message', ['context' => $data]);
Log::error('Error', ['exception' => $e]);

// Query logging
DB::enableQueryLog();
// ... queries ...
dd(DB::getQueryLog());

// Ray (if installed)
ray($variable);
ray($customer)->blue()->large();
```

```javascript
// JavaScript debugging
console.log('Debug:', data);
console.table(array);
console.error('Error:', error);

// Vue DevTools
// Install browser extension for component inspection
```

## 📊 Common Validation Rules

```php
'email' => ['required', 'email', 'unique:users,email'],
'name' => ['required', 'string', 'max:255'],
'age' => ['required', 'integer', 'min:18', 'max:100'],
'status' => ['required', 'in:active,inactive,pending'],
'date' => ['required', 'date', 'after:today'],
'price' => ['required', 'numeric', 'min:0'],
'customer_id' => ['required', 'exists:customers,id'],
'image' => ['nullable', 'image', 'max:2048'], // KB
```

## 🔄 Common Git Commands

```bash
# Status & Changes
git status
git diff
git log --oneline

# Branching
git checkout -b feature/new-feature
git checkout main
git branch -d feature/old-feature

# Committing
git add .
git commit -m "Add customer management feature"
git push origin feature/new-feature

# Pulling & Merging
git pull origin main
git merge feature/new-feature
```

## 📞 Quick Help

**When stuck, check:**
1. `.ai/README.md` - For which documentation file to read
2. `.ai/PROJECT_OVERVIEW.md` - For understanding the project
3. `.ai/COMMON_PATTERNS.md` - For code examples
4. `.ai/CODING_STANDARDS.md` - For best practices
5. `.ai/DATABASE_SCHEMA.md` - For database structure

**Common error fixes:**
- Clear caches: `php artisan optimize:clear`
- Rebuild assets: `npm run build`
- Reset permissions: `php artisan permission:cache-reset`
- Check logs: `storage/logs/laravel.log`
- Check browser console for JS errors

## 🔗 Important URLs (Local Dev)

```
Application:     http://localhost:8000
Vite HMR:        http://localhost:5173
MySQL:           localhost:3306
```

## 📱 Responsive Breakpoints

```
sm:   640px   // Small devices
md:   768px   // Medium devices  
lg:   1024px  // Large devices
xl:   1280px  // Extra large devices
2xl:  1536px  // 2X Extra large devices
```

## 🎯 Key Models & Relationships

```
Customer
├── hasMany → TransportTransactions
├── belongsTo → CustomerParent
├── belongsTo → CustomerRating
└── morphMany → Addresses, Contacts

TransportTransaction
├── belongsTo → Customer
├── belongsTo → Supplier
├── belongsTo → Product
└── hasMany → TransportOrders

TransportOrder
├── belongsTo → TransportTransaction
├── belongsTo → Transporter
└── hasMany → TransportLoads
```

---

**💡 Pro Tip:** Keep this file open while working for quick reference. Bookmark commonly used sections!

