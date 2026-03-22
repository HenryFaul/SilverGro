# SilverGro - Claude Code Context

Laravel 10 + Vue 3 + Inertia.js transport logistics app for the South African grain/feed industry. Central entity is **TransportTransaction** (Supplier → Customer → Transporter for product delivery).

Full context in `AI/` directory:
- `AI/PROMPTING_GUIDE.md` — coding conventions and patterns (read first)
- `AI/BUSINESS_RULES.md` — financial formulas, approval logic, domain rules
- `AI/DATA_MODELS.md` — all model fields and relationships
- `AI/ARCHITECTURE.md` — request flow, frontend patterns
- `AI/ROUTES.md` — all ~150 routes
- `AI/TODO.md` — 32 tracked bugs/enhancements (5 critical, none fixed)

---

## Critical Gotchas

1. **Eloquent relationships use PascalCase** — `$model->TransportFinance()` not `transportFinance()`. Non-standard but project-wide. Do NOT change to camelCase without updating all call sites.
2. **scopeFilter has a known orWhere bug** — When editing any `scopeFilter()` method, always wrap `orWhere` chains in a closure: `$query->where(fn($q) => $q->where(...)->orWhere(...))`. See BUG-001 in `AI/TODO.md`.
3. **No Form Requests** — Validation is inline in controllers.
4. **Flash messages via Jetstream banner** — `$request->session()->flash('flash.bannerStyle', 'success')` + `$request->session()->flash('flash.banner', 'Message')`.
5. **Up to 5 customers per transaction** — `customer_id` through `customer_id_5` with matching price/cost fields.
6. **Actual vs Planned financials** — Always update both sets when touching financial calculations.
7. **Currency:** South African Rand (ZAR). Format: `R 1 234 567.89` (space thousands separator).
8. **Transaction IDs start at 13000** — set in migration, not auto-increment from 1.

---

## Critical Business Rules (see `AI/BUSINESS_RULES.md` for full detail)

- **Financial engine:** `TransportFinance::calculateFields()` — all calculations flow through here
- **Transport cost:** Per Load (rate_basis_id=3) = flat rate; all others = rate × weight_ton_incoming
- **Total cost price:** if `is_transport_costs_inc_price` → exclude transport_cost from total; otherwise include it
- **GP:** `selling_price - (total_cost_price + adjusted_gp)`
- **Commission:** GP split 50/50 supplier/customer side, then divided equally among AssignedUserComm users
- **Approval:** DealTicket triggers by trade value range (TradeRule) + opportunity rules (COD = terms_id 1, suspended customer = is_active != 1)
- **Actual vs Planned:** Planned from TransportLoad units; Actual from TransportDriverVehicle weighbridge weights — both sets must stay in sync
- **Order workflow:** Create → Activate → Send → Receive (Sales, Purchase, Transport orders)
- **Data import:** Uses Maatwebsite Excel with `ToCollection` + `WithHeadingRow`

## Key Files

| Purpose | File |
|---------|------|
| Central model | `app/Models/TransportTransaction.php` |
| Financial logic | `app/Models/TransportFinance.php` (`calculateFields()`) |
| Approval workflow | `app/Models/DealTicket.php` (`calculateRules()`) |
| Controller pattern | `app/Http/Controllers/CustomerController.php` |
| Index page pattern | `resources/js/Pages/Customer/Index.vue` |
| Show page pattern | `resources/js/Pages/Customer/Show.vue` |
| All routes | `routes/web.php` |

---

## Backend Conventions

- Soft deletes on nearly all models
- `scopeFilter(Builder $query, array $filters)` on list models
- `Inertia::render('Page/Name', ['prop' => $value])` in controllers
- No Policies — auth is ad-hoc (most controllers lack it; see ENH-001)

## Frontend Conventions

- Vue 3 Composition API — all components use `<script setup>`
- Forms: `useForm()` from `@inertiajs/vue3`, submit via `form.put(route('resource.update', id))`
- Routes: `route('name')` via Ziggy
- Modals/Slide-overs: HeadlessUI `Dialog` with `TransitionRoot`
- Icons: `@heroicons/vue/24/solid` or `/outline`
- Alerts: `inject('$swal')` for SweetAlert2
- Permissions: `usePage().props.roles_permissions.permissions.includes('permission_name')`
- No TypeScript

---

## Slash Commands

| Command | Use |
|---------|-----|
| `/new-crud` | Scaffold a complete new CRUD entity |
| `/fix-filter` | Fix scopeFilter orWhere bug in a model |
| `/finance` | Context for financial calculation changes |
