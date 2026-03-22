Create a complete CRUD implementation for a new entity called **$ARGUMENTS** following the SilverGro project patterns.

Read these files first to understand the patterns:
- `app/Http/Controllers/CustomerController.php` — controller pattern
- `app/Models/Customer.php` — model pattern
- `resources/js/Pages/Customer/Index.vue` — index page pattern
- `resources/js/Pages/Customer/Show.vue` — show page pattern
- `resources/js/Components/UI/CustomerSlideOver.vue` — slide-over pattern
- `routes/web.php` — how routes are registered

Then create all of the following:

1. **Migration** in `database/migrations/` — include `id()`, standard fields for this entity, `timestamps()`, `softDeletes()`
2. **Model** in `app/Models/` — extend `Model`, use `HasFactory`, `SoftDeletes`, `LogsActivity`; define `$fillable`; add relationships (PascalCase method names); add `scopeFilter(Builder $query, array $filters)` using closure-wrapped `orWhere` chains
3. **Controller** in `app/Http/Controllers/` — `index()` with filter support and Inertia render, `store()` with inline validation + flash message, `show()` with relations loaded, `update()` with inline validation + flash message, `destroy()` for soft delete
4. **Route** — add `Route::resource('entity-name', EntityController::class)` to `routes/web.php`
5. **Index page** at `resources/js/Pages/[Entity]/Index.vue` — follow Customer/Index.vue exactly: `useForm` filters, debounced filter function, sortable column headers, SlideOver for create, PaginationModified at bottom
6. **Show page** at `resources/js/Pages/[Entity]/Show.vue` — follow Customer/Show.vue: `useForm` with entity data, `form.put()` to update, sections for related data
7. **SlideOver component** at `resources/js/Components/UI/[Entity]SlideOver.vue` — HeadlessUI Dialog for creating new records

**Critical conventions to follow:**
- Relationship methods are PascalCase (e.g., `TransportTransaction()` not `transactionTransaction()`)
- Flash messages: `$request->session()->flash('flash.bannerStyle', 'success')` + `$request->session()->flash('flash.banner', 'Created.')`
- scopeFilter must wrap orWhere in closures: `$query->where(fn($q) => $q->where('col', 'LIKE', "%{$filters['search']}%")->orWhere(...))`
- Vue uses `<script setup>`, no TypeScript
- Routes via Ziggy: `route('entity.index')`, `route('entity.store')`, etc.
