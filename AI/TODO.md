# SilverGro - Enhancements & Bug Fixes TODO

Track progress by updating the `[ ]` to `[x]` when completed.

---

## CRITICAL BUGS (Fix Immediately)

### BUG-001: Filter query logic bug - orWhere without closure grouping
- **Status:** [ ] Not Started
- **Priority:** CRITICAL
- **Files:**
  - `app/Models/Customer.php:92-108`
  - `app/Models/Supplier.php` (scopeFilter)
  - `app/Models/CustomerParent.php:65-81`
  - `app/Models/Contact.php` (scopeFilter)
  - `app/Models/RegularDriver.php` (scopeFilter)
  - `app/Models/RegularVehicle.php` (scopeFilter)
  - `app/Models/Product.php` (scopeFilter)
- **Description:** All `scopeFilter()` methods use `orWhere()` without wrapping in a closure. When multiple filters are active simultaneously (e.g., searchName + isActive), the SQL generates incorrect results because the OR conditions leak into the outer query.
- **Fix:** Wrap `orWhere` chains inside a closure: `$query->where(fn($q) => $q->where(...)->orWhere(...))`
- **Impact:** Affects every list/index page in the application. Users may see incorrect filtered results.

### BUG-002: Hardcoded user ID for notifications
- **Status:** [ ] Not Started
- **Priority:** CRITICAL
- **File:** `app/Http/Controllers/TransportApprovalController.php:148`
- **Description:** `User::find(6)` is hardcoded. Will fail if user 6 doesn't exist or will notify the wrong person.
- **Fix:** Make the notification recipient configurable or determine it from the transaction context.

### BUG-003: Wrong table reference in supplier validation
- **Status:** [ ] Not Started
- **Priority:** CRITICAL
- **File:** `app/Http/Controllers/SupplierController.php:63`
- **Description:** `'unique:customers,id_reg_no'` should be `'unique:suppliers,id_reg_no'`. Supplier ID/reg uniqueness is being checked against the customers table.
- **Fix:** Change to `'unique:suppliers,id_reg_no'`.

### BUG-004: Boolean logic error in password validation
- **Status:** [ ] Not Started
- **Priority:** CRITICAL
- **File:** `app/Http/Controllers/StaffController.php:137`
- **Description:** `if ($request->password != null || $request->password !='')` uses OR instead of AND. This condition is always true, meaning password updates will always be attempted even when password is empty/null.
- **Fix:** Change `||` to `&&`.

### BUG-005: Double-counted overdue amounts in debtor calculation
- **Status:** [ ] Not Started
- **Priority:** CRITICAL
- **File:** `app/Http/Controllers/CustomerController.php:74-75`
- **Description:** `$customer_total_overdue += $invoice_detail->invoice_amount;` appears twice in succession, doubling the overdue amount.
- **Fix:** Remove the duplicate line.

---

## HIGH PRIORITY (Fix Soon)

### BUG-006: Missing authorization on approval endpoint
- **Status:** [ ] Not Started
- **Priority:** HIGH
- **File:** `app/Http/Controllers/TransportApprovalController.php`
- **Description:** The `approve()` method has no authorization check. Any authenticated user can approve deal tickets regardless of their role/permissions.
- **Fix:** Add `$this->authorize()` or check `$user->can('approve_deal_ticket')`.

### BUG-007: N+1 query issue with Customer trades_count
- **Status:** [ ] Not Started
- **Priority:** HIGH
- **File:** `app/Models/Customer.php:27-36`
- **Description:** The `trades_count` computed attribute is in `$appends` and executes a DB query for every Customer instance loaded. On index pages listing 25+ customers, this triggers 25+ extra queries.
- **Fix:** Use `withCount()` at the query level, or remove from `$appends` and load explicitly when needed.

### BUG-008: Unpaginated heavy queries loading all records
- **Status:** [ ] Not Started
- **Priority:** HIGH
- **File:** `app/Http/Controllers/TransactionSummaryController.php:102-123`
- **Description:** Loads ALL customers, suppliers, transporters, products, etc. without pagination. As data grows this will cause memory issues and slow page loads.
- **Fix:** Paginate or lazy-load these collections, or use search-as-you-type endpoints.

### BUG-009: Supplier store creates with non-existent model fields
- **Status:** [ ] Not Started
- **Priority:** HIGH
- **File:** `app/Http/Controllers/SupplierController.php:68-84`
- **Description:** Store method sets `invoice_basis_id`, `customer_rating_id`, `days_overdue_allowed_id`, `is_vat_exempt`, `is_vat_cert_received`, `credit_limit`, `credit_limit_hard` - fields that don't exist on the Supplier model's fillable or table.
- **Fix:** Remove non-existent fields from the create call.

### BUG-010: Always-true condition in approval logic
- **Status:** [ ] Not Started
- **Priority:** HIGH
- **File:** `app/Http/Controllers/TransportApprovalController.php:109`
- **Description:** `if (true) { ... }` is always true - the condition is redundant and obscures the intent.
- **Fix:** Remove the `if (true)` wrapper or replace with actual condition.

---

## MEDIUM PRIORITY (Code Quality)

### ENH-001: Add authorization across all controllers
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **Description:** Many controllers have no authorization checks, commented-out abort(403), or inconsistent approaches. Standardize using Laravel Policies or Gate checks.
- **Files:** Most controllers in `app/Http/Controllers/`

### ENH-002: Add request validation to all store/update methods
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **Description:** Several controllers accept and process request data without validation. Add Form Request classes for consistent validation.

### ENH-003: Remove all commented-out code blocks
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **Files:**
  - `app/Http/Controllers/DashboardController.php:39-95` (50+ lines)
  - `app/Http/Controllers/TransactionSummaryController.php:131-135`
  - `app/Http/Controllers/DebtorStandingController.php:45,80-82`
  - `app/Http/Controllers/CustomerController.php` (abort(403))
  - `app/Http/Controllers/CustomerParentController.php:22`

### ENH-004: Remove empty boot() method on TransportFinance
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **File:** `app/Models/TransportFinance.php:327-334`
- **Description:** Empty `self::updating()` callback does nothing. Remove or implement.

### ENH-005: Remove TODO comments or implement them
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **Files:**
  - `app/Models/TransportTransaction.php:256`
  - `app/Models/TransportFinance.php:316`

### ENH-006: Fix duplicate validation rule
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **File:** `app/Http/Controllers/StaffController.php:58`
- **Description:** `'last_legal_name' => ['required','string','string']` - 'string' listed twice.

### ENH-007: Optimize DealTicket count() calls
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **File:** `app/Models/DealTicket.php:50,68,160,176`
- **Description:** Replace `count($collection) > 0` with `$collection->isNotEmpty()` for better performance.

### ENH-008: Fix NiceNumber utility function
- **Status:** [ ] Not Started
- **Priority:** MEDIUM
- **File:** `resources/js/Pages/Dashboard.vue:29`
- **Description:** `.replace(".", ".")` is a no-op. Either remove or fix to intended logic.

---

## LOW PRIORITY (Improvements)

### ENH-009: Add comprehensive test coverage
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Description:** Currently only 15 auth-related feature tests and 1 example unit test. Need tests for:
  - [ ] TransportFinance::calculateFields() - critical financial logic
  - [ ] DealTicket::calculateRules() - approval workflow
  - [ ] All controller CRUD operations
  - [ ] Filter scopes (scopeFilter, scopeIndex, etc.)
  - [ ] DebtorStanding calculations
  - [ ] Excel import/export
  - [ ] PDF generation

### ENH-010: Standardize Eloquent relationship method naming
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Description:** All relationship methods use PascalCase (`TransportTransaction()`) instead of Laravel convention camelCase (`transportTransaction()`). While functional, this deviates from convention and can cause confusion.

### ENH-011: Remove hardcoded dashboard stats
- **Status:** [ ] Not Started
- **Priority:** LOW
- **File:** `resources/js/Pages/Dashboard.vue:21-26`
- **Description:** Placeholder stats ("8,000+ Creators", "3% platform fee") don't match the grain/feed business context. Remove or replace with real data.

### ENH-012: Add null safety to related record access
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Files:** Multiple controllers
- **Description:** Accessing related records without null checks (e.g., `$transport_trans->PurchaseOrder->load(...)` will throw if PurchaseOrder is null). Add optional chaining or null checks.

### ENH-013: Implement proper error handling in controllers
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Description:** Most controllers use basic try/flash patterns. Implement consistent error handling with proper HTTP status codes and user-friendly messages.

### ENH-014: Add database indexes for frequently filtered columns
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Description:** Add indexes on columns used in filters and sorting:
  - `transport_transactions.transport_date_earliest`
  - `transport_transactions.contract_type_id`
  - `transport_transactions.supplier_id`, `customer_id`, `transporter_id`
  - `customers.is_active`, `suppliers.is_active`
  - `customers.customer_parent_id`

### ENH-015: Consolidate pagination components
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Files:** `resources/js/Components/UI/Pagination.vue`, `PaginationModified.vue`, `CustomPagination.vue`
- **Description:** Three separate pagination components exist. Consolidate into one configurable component.

### ENH-016: Remove test.vue and Test.vue development artifacts
- **Status:** [ ] Not Started
- **Priority:** LOW
- **Files:** `resources/js/Components/UI/test.vue`, `resources/js/Pages/Test.vue`
- **Description:** Development test files should not be in production code. The Test.vue page is 14,453 lines.

---

## FEATURE REQUESTS (Future Enhancements)

### FEAT-001: Real-time notifications via WebSocket
- **Status:** [ ] Not Started
- **Description:** Laravel Echo + Pusher configuration is commented out in `bootstrap.js`. Enable real-time notification delivery.

### FEAT-002: Background job processing for heavy calculations
- **Status:** [ ] Not Started
- **Description:** Queue connection is set to `sync`. Move heavy operations (financial recalculations, Excel generation, PDF generation, debtor calculations) to background jobs.

### FEAT-003: API endpoints for external integrations
- **Status:** [ ] Not Started
- **Description:** Only 1 API route exists (`/api/user`). Build out RESTful API for integrations with accounting systems, ERP, etc.

### FEAT-004: Audit trail UI
- **Status:** [ ] Not Started
- **Description:** Activity logging (Spatie) is configured but there's no UI to view the audit trail. Add an activity log viewer page.

### FEAT-005: Document management improvements
- **Status:** [ ] Not Started
- **Description:** DocumentStore model exists but functionality appears minimal. Enhance with file upload, categorization, and linking to transactions.

### FEAT-006: Email notifications for order confirmations
- **Status:** [ ] Not Started
- **Description:** Orders can be "sent" but there's no email sending implementation. Add email delivery for Sales Order, Purchase Order, and Transport Order confirmations.

---

## Progress Summary

| Category | Total | Done | Remaining |
|----------|-------|------|-----------|
| Critical Bugs | 5 | 0 | 5 |
| High Priority | 5 | 0 | 5 |
| Medium Priority | 8 | 0 | 8 |
| Low Priority | 8 | 0 | 8 |
| Feature Requests | 6 | 0 | 6 |
| **Total** | **32** | **0** | **32** |
