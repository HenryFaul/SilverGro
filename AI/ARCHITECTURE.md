# SilverGro - Architecture & Technical Reference

## Tech Stack

### Backend
| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | ^10.0 |
| PHP | PHP | 8.1+ |
| Database | MySQL | 8.x |
| Auth Scaffolding | Laravel Jetstream | ^3.0 |
| API Tokens | Laravel Sanctum | ^3.2 |
| RBAC | Spatie Laravel Permission | ^5.10 |
| Activity Log | Spatie Laravel Activity Log | ^4.7 |
| Media Library | Spatie Laravel Media Library | ^10.0 |
| Excel Import/Export | Maatwebsite Excel | ^3.1 |
| Spreadsheet | PhpOffice PhpSpreadsheet | ^1.29 |
| PDF Generation | Barryvdh Laravel DomPDF | ^2.0 |
| Charts | Arielmejiadev LarapexCharts | ^6.0 |
| HTTP Client | Guzzle | ^7.2 |

### Frontend
| Component | Technology | Version |
|-----------|-----------|---------|
| JS Framework | Vue 3 | ^3.2.31 |
| Server Bridge | Inertia.js | ^0.6.8 |
| Build Tool | Vite | ^4.0 |
| CSS Framework | Tailwind CSS | ^3.1 |
| UI Components | HeadlessUI Vue | latest |
| Icons | Heroicons Vue | latest |
| Charts | ApexCharts + vue3-apexcharts | latest |
| Alerts | SweetAlert2 + vue-sweetalert2 | latest |
| Date Picker | @vuepic/vue-datepicker | latest |
| Maps | @googlemaps/js-api-loader | latest |
| Phone Validation | libphonenumber-js | latest |
| Date Utils | Moment.js | latest |
| Utilities | Lodash | latest |
| Route Helpers | Ziggy | ^1.0 |

## Architecture Pattern

### Inertia.js (Server-Driven SPA)
The app uses **Inertia.js** as the bridge between Laravel and Vue 3. This means:
- **No separate API layer** for the frontend - controllers return Inertia responses
- **Server-side routing** - Laravel routes define all page navigation
- **Props passing** - Controllers pass data directly to Vue page components via `Inertia::render()`
- **Form handling** - `useForm()` from `@inertiajs/vue3` handles form submissions with automatic CSRF
- **Page visits** - `router.get()`, `router.post()` etc. for programmatic navigation

### Request Flow
```
Browser Request
    -> Laravel Route (routes/web.php)
        -> Middleware (auth, Inertia, CSRF)
            -> Controller Method
                -> Eloquent Query / Business Logic
                    -> Inertia::render('Page/Component', props)
                        -> Vue Page Component receives props
                            -> Renders with Tailwind CSS
```

### State Management
- **No Vuex/Pinia** - State is managed via:
  - Inertia page props (server-provided data)
  - `useForm()` reactive forms
  - Local `ref()` / `reactive()` state in components
  - `usePage().props` for shared data (auth user, permissions, notifications)

## Database Architecture

### Key Entity Relationships
```
CustomerParent (1) ---> (*) Customer
Customer (1) ---> (*) TransportTransaction
Supplier (1) ---> (*) TransportTransaction
Transporter (1) ---> (*) TransportTransaction
Product (1) ---> (*) TransportTransaction

TransportTransaction (1) ---> (1) TransportJob
TransportTransaction (1) ---> (1) TransportLoad
TransportTransaction (1) ---> (1) TransportFinance
TransportTransaction (1) ---> (1) DealTicket
TransportTransaction (1) ---> (1) SalesOrder
TransportTransaction (1) ---> (1) PurchaseOrder
TransportTransaction (1) ---> (1) TransportOrder
TransportTransaction (1) ---> (1) TransportInvoice
TransportTransaction (1) ---> (*) TransportDriverVehicle
TransportTransaction (1) ---> (*) TransportStatus
TransportTransaction (1) ---> (*) AssignedUserComm
TransportTransaction (1) ---> (*) TransLink
TransportTransaction (1) ---> (*) TransLinkSplit
TransportTransaction (1) ---> (*) TransportApproval

TradeRule (1) ---> (*) TradeRuleRole (polymorphic)
TradeRuleOpp (1) ---> (*) TradeRuleRole (polymorphic)
```

### Polymorphic Relationships
```
Address -> poly_address (Customer, Supplier, Transporter, CustomerParent)
Contact -> poly_contact (Customer, Supplier, Transporter, CustomerParent)
Staff Assignment -> staff_assigned (Customer, Supplier, Transporter, CustomerParent)
EmailContactDetail -> poly_email (Contact)
NumberContactDetail -> poly_number (Contact)
TransportApproval -> poly_approval (TradeRule, TradeRuleOpp)
```

### Soft Deletes
Nearly all models use `SoftDeletes` trait. Records are never physically deleted.

### Activity Logging
Models with `LogsActivity` trait:
- `TransportTransaction` - Logs changes to all key fields
- `TransportFinance` - Logs changes to all financial fields

## Frontend Architecture

### Directory Structure
```
resources/js/
├── app.js                    # Vue app bootstrapping
├── bootstrap.js              # Axios + Echo setup
├── ssr.js                    # Server-side rendering config
├── Components/
│   ├── *.vue                 # 27 base Jetstream/app components
│   └── UI/
│       ├── *SlideOver.vue    # 10 slide-over creation forms
│       ├── *Modal.vue        # 10 modal dialogs
│       └── *.vue             # Pagination, tooltips, etc.
├── Composables/
│   └── useScript.js          # Dynamic script loader
├── Layouts/
│   ├── AppLayout.vue         # Main app layout with nav
│   └── AppLayoutSpreadSheet.vue  # Lightweight spreadsheet layout
└── Pages/
    ├── Auth/                 # 7 authentication pages
    ├── Customer/             # Index, Show, DebtorStanding
    ├── CustomerParents/      # Index, Show
    ├── Supplier/             # Index, Show
    ├── Transporter/          # Index, Show
    ├── Driver/               # Index, Show
    ├── Vehicle/              # Index, Show
    ├── Products/             # Index, Show
    ├── Staff/                # Index, Show
    ├── Contact/              # Index, Show
    ├── Transaction/          # Index, Show
    ├── TransactionSummary/   # Index (Trade Summary)
    ├── TransactionSpreadSheet/ # Index (Spreadsheet View)
    ├── Planning/             # Diary, WeekPlan
    ├── HomeOverview/         # Index
    ├── PcOverview/           # Index (Purchase Contract)
    ├── ScOverview/           # Index (Sales Contract)
    ├── ReportGenerator/      # Index
    ├── DataImport/           # Index
    ├── Notifications/        # Index
    ├── Profile/              # Show + Partials
    ├── API/                  # Token management
    └── Dashboard.vue         # Main dashboard
```

### Common Frontend Patterns

#### 1. Index Page Pattern (List + Filter + Create)
All index pages follow this structure:
```vue
<script setup>
// Props from controller
const props = defineProps({ items: Object, filters: Object });

// Filter form with debounced server-side filtering
const filterForm = useForm({
    searchName: props.filters.searchName ?? null,
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 10,
});

let filter = debounce(() => {
    filterForm.get(route('resource.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 150);

watch(() => filterForm.searchName, () => filter());

// Slide-over for creation
const viewSlideOver = ref(false);
</script>
```

#### 2. Show Page Pattern (Detail + Edit)
Detail pages typically have inline editing with form submission:
```vue
const form = useForm({ ...props.entity });
const submit = () => {
    form.put(route('resource.update', props.entity.id));
};
```

#### 3. Permission Checking
```vue
const can_update = computed(() =>
    usePage().props.roles_permissions.permissions.includes("update_resource")
);
```

#### 4. Currency Formatting
```javascript
let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
};
```

#### 5. Date Formatting (SA timezone)
```javascript
const _date = new Date(date);
const options = { timeZone: "Africa/Johannesburg" };
```

## Backend Architecture

### Controller Pattern
Controllers are thin, handling:
1. Authentication/authorization checks
2. Request validation and filtering
3. Eloquent queries with eager loading
4. Inertia response rendering

### Key Controller: TransportTransactionController
The largest controller, handling:
- `index()` - Filtered, paginated transaction list with 8+ filter parameters
- `show()` - Full transaction detail with all related entities eager loaded
- `store()` - Creates transaction + all child records (Job, Load, Finance, DealTicket, Orders)
- `update()` / `updatePlayers()` - Updates transaction fields
- `clone()` - Duplicates a transaction
- `generate()` / `download()` - Excel report generation
- `getProps()` / `getPcs()` / `getScs()` - AJAX prop endpoints for modals

### Middleware Stack
```
Web: cookies -> sessions -> CSRF -> Inertia -> auth
API: throttle -> Sanctum auth
```

### Shared Inertia Data (HandleInertiaRequests middleware)
Available on every page via `usePage().props`:
- `auth.user` - Current user
- `roles_permissions` - User's roles and permissions
- `notifications` - Unread notification count
- `jetstream` features

## Configuration

### Environment Variables (.env)
- `DB_CONNECTION=mysql` - MySQL database
- `SESSION_DRIVER=database` - Database-backed sessions
- `QUEUE_CONNECTION=sync` - Synchronous queue (no background jobs)
- `CACHE_DRIVER=file` - File-based cache
- `FILESYSTEM_DISK=local` - Local file storage

### Key Config Files
- `config/permission.php` - Spatie permission settings
- `config/activitylog.php` - Activity logging configuration
- `config/media-library.php` - Media library settings
- `config/jetstream.php` - Jetstream features (API, profile photos, etc.)
