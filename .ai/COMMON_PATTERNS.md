# Common Code Patterns

This document contains reusable code patterns frequently used in the SilverGro application.

## Table of Contents
1. [Laravel Backend Patterns](#laravel-backend-patterns)
2. [Vue Frontend Patterns](#vue-frontend-patterns)
3. [Inertia.js Patterns](#inertiajs-patterns)
4. [Form Patterns](#form-patterns)
5. [Permission Patterns](#permission-patterns)
6. [Query Patterns](#query-patterns)
7. [Notification Patterns](#notification-patterns)

---

## ⚠️ Critical Architecture Gotcha: Dual Transaction Controllers

The Transaction Summary page (`/transaction_summary`) uses **`TransactionSummaryController`**, NOT `TransportTransactionController`, for all create/update operations from the UI.

| Action | Controller |
|--------|-----------|
| Create new trade (from list page) | `TransportTransactionController::store()` |
| Update trade (from Transaction Summary page) | `TransactionSummaryController::update()` |

**`TransportTransactionController::update()` is never called from the main UI.**

### Consequence
Any business logic added to `TransportTransactionController::update()` will **not run** when a user saves a trade from the transaction summary page. Logic that must run on every save must be in **both** controllers, or extracted to a shared location.

### Pattern: Shared logic lives on the model
Cross-controller business logic (e.g. `AssignedUserComm::syncDefaultCommUsers`) is implemented as **public static methods on the relevant Eloquent model** so both controllers can call it:

```php
// In AssignedUserComm model
public static function syncDefaultCommUsers(int $transactionId, int $supplierId, int $customerId): void { ... }
public static function removeStaleCommUsers(int $transactionId, ...): void { ... }

// Called from BOTH controllers after update:
if (!$isSplitLoad) {
    AssignedUserComm::removeStaleCommUsers($transactionId, $oldSuppId, $newSuppId, $oldCustId, $newCustId);
    AssignedUserComm::syncDefaultCommUsers($transactionId, $newSuppId, $newCustId);
}
```

### Split Load Guard
For split load trades (`is_split_load_primary || is_split_load_member`), skip comm sync entirely — the primary transaction uses `customer_id = 2` (Unallocated) which would incorrectly mark real customer staff as stale. Capture the flag **before** calling `->update()` since the model instance is mutated during the call:

```php
$isSplitLoad = (bool) ($request->is_split_load_primary || $request->is_split_load_member);
$transportTransaction->update([...]);
// Safe to use $isSplitLoad here — model instance already mutated
if (!$isSplitLoad) { ... }
```

---

## Laravel Backend Patterns

### Standard Controller Index (with search, filters, pagination)

```php
public function index(Request $request)
{
    $query = Customer::query()
        ->with(['customerParent', 'termsOfPayment', 'customerRating'])
        ->when($request->search, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_legal_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        })
        ->when($request->is_active !== null, function ($q) use ($request) {
            $q->where('is_active', $request->is_active);
        })
        ->when($request->rating_id, function ($q, $ratingId) {
            $q->where('customer_rating_id', $ratingId);
        });

    $customers = $query
        ->orderBy($request->sort_by ?? 'first_name', $request->sort_direction ?? 'asc')
        ->paginate($request->per_page ?? 15)
        ->withQueryString();

    return Inertia::render('Customer/Index', [
        'customers' => $customers,
        'filters' => $request->only(['search', 'is_active', 'rating_id', 'sort_by', 'sort_direction']),
        'ratings' => CustomerRating::select('id', 'name')->get(),
    ]);
}
```

### Standard Controller Store

```php
public function store(StoreCustomerRequest $request)
{
    DB::beginTransaction();
    
    try {
        $customer = Customer::create($request->validated());
        
        // Handle relationships if needed
        if ($request->has('contacts')) {
            foreach ($request->contacts as $contactData) {
                $customer->contacts()->create($contactData);
            }
        }
        
        // Log activity
        activity()
            ->performedOn($customer)
            ->causedBy(auth()->user())
            ->log('Customer created');
        
        DB::commit();
        
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Failed to create customer', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'data' => $request->validated(),
        ]);
        
        return back()
            ->withErrors(['error' => 'Failed to create customer. Please try again.'])
            ->withInput();
    }
}
```

### Standard Controller Update

```php
public function update(UpdateCustomerRequest $request, Customer $customer)
{
    DB::beginTransaction();
    
    try {
        $customer->update($request->validated());
        
        // Log activity
        activity()
            ->performedOn($customer)
            ->causedBy(auth()->user())
            ->log('Customer updated');
        
        DB::commit();
        
        return back()->with('success', 'Customer updated successfully.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Failed to update customer', [
            'error' => $e->getMessage(),
            'customer_id' => $customer->id,
            'user_id' => auth()->id(),
        ]);
        
        return back()->withErrors(['error' => 'Failed to update customer.']);
    }
}
```

### Standard Controller Destroy (with soft delete)

```php
public function destroy(Customer $customer)
{
    try {
        // Check for dependencies
        if ($customer->transportTransactions()->where('is_transaction_done', false)->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete customer with active transactions.'
            ]);
        }
        
        $customer->delete(); // Soft delete
        
        activity()
            ->performedOn($customer)
            ->causedBy(auth()->user())
            ->log('Customer deleted');
        
        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
            
    } catch (\Exception $e) {
        Log::error('Failed to delete customer', [
            'error' => $e->getMessage(),
            'customer_id' => $customer->id,
        ]);
        
        return back()->withErrors(['error' => 'Failed to delete customer.']);
    }
}
```

### Form Request Validation

```php
class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create customers');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_legal_name' => ['nullable', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'customer_parent_id' => ['nullable', 'exists:customer_parents,id'],
            'email' => ['nullable', 'email', 'unique:customers,email'],
            'is_active' => ['boolean'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'credit_limit_hard' => ['nullable', 'numeric', 'min:0'],
            'terms_of_payment_id' => ['required', 'exists:terms_of_payments,id'],
            'customer_rating_id' => ['nullable', 'exists:customer_ratings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Customer name is required.',
            'terms_of_payment_id.required' => 'Payment terms must be selected.',
        ];
    }
}
```

### Model with Relationships and Accessors

```php
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_legal_name',
        'nickname',
        'customer_parent_id',
        'terms_of_payment_id',
        'customer_rating_id',
        'is_active',
        'credit_limit',
        'credit_limit_hard',
        'comment',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'credit_limit_hard' => 'decimal:2',
    ];

    protected $appends = [
        'full_name',
        'active_trades_count',
    ];

    // Relationships
    public function customerParent(): BelongsTo
    {
        return $this->belongsTo(CustomerParent::class);
    }

    public function termsOfPayment(): BelongsTo
    {
        return $this->belongsTo(TermsOfPayment::class);
    }

    public function customerRating(): BelongsTo
    {
        return $this->belongsTo(CustomerRating::class);
    }

    public function transportTransactions(): HasMany
    {
        return $this->hasMany(TransportTransaction::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    // Accessors
    protected function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => trim("{$this->first_name} {$this->last_legal_name}") ?: $this->nickname
        );
    }

    protected function activeTradesCount(): Attribute
    {
        return new Attribute(
            get: fn () => $this->transportTransactions()
                ->where('is_transaction_done', false)
                ->where('include_in_calculations', true)
                ->count()
        );
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeWithActiveTransactions(Builder $query): void
    {
        $query->whereHas('transportTransactions', function ($q) {
            $q->where('is_transaction_done', false);
        });
    }
}
```

---

## Vue Frontend Patterns

### Standard Index Page with Search and Filters

```vue
<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationModified from '@/Components/UI/PaginationModified.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
    ratings: Array,
});

// Search and filter state
const search = ref(props.filters.search || '');
const isActive = ref(props.filters.is_active);
const ratingId = ref(props.filters.rating_id || '');
const sortBy = ref(props.filters.sort_by || 'first_name');
const sortDirection = ref(props.filters.sort_direction || 'asc');

// Debounced search
const performSearch = debounce(() => {
    router.get(route('customers.index'), {
        search: search.value,
        is_active: isActive.value,
        rating_id: ratingId.value,
        sort_by: sortBy.value,
        sort_direction: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

watch([search, isActive, ratingId], () => {
    performSearch();
});

const sort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDirection.value = 'asc';
    }
    performSearch();
};

const resetFilters = () => {
    search.value = '';
    isActive.value = null;
    ratingId.value = '';
    sortBy.value = 'first_name';
    sortDirection.value = 'asc';
};
</script>

<template>
  <AppLayout title="Customers">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
          <Link
            :href="route('customers.create')"
            class="btn-primary"
          >
            Add Customer
          </Link>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Search
              </label>
              <input
                v-model="search"
                type="text"
                placeholder="Search customers..."
                class="w-full rounded-md border-gray-300"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Status
              </label>
              <select
                v-model="isActive"
                class="w-full rounded-md border-gray-300"
              >
                <option :value="null">All</option>
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>

            <!-- Rating Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Rating
              </label>
              <select
                v-model="ratingId"
                class="w-full rounded-md border-gray-300"
              >
                <option value="">All Ratings</option>
                <option
                  v-for="rating in ratings"
                  :key="rating.id"
                  :value="rating.id"
                >
                  {{ rating.name }}
                </option>
              </select>
            </div>

            <!-- Reset -->
            <div class="flex items-end">
              <button
                @click="resetFilters"
                class="w-full btn-secondary"
              >
                Reset Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  @click="sort('first_name')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                >
                  Name
                  <Icon
                    v-if="sortBy === 'first_name'"
                    :name="sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'"
                    class="inline w-4 h-4"
                  />
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contact
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Rating
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="customer in customers.data"
                :key="customer.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ customer.full_name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ customer.nickname }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.customer_rating?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'px-2 py-1 text-xs font-semibold rounded-full',
                      customer.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ customer.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link
                    :href="route('customers.show', customer.id)"
                    class="text-blue-600 hover:text-blue-900 mr-4"
                  >
                    View
                  </Link>
                  <Link
                    :href="route('customers.edit', customer.id)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Edit
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <PaginationModified :data="customers" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

### Modal Form Component

```vue
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    customer: Object,
    ratings: Array,
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    first_name: props.customer?.first_name || '',
    last_legal_name: props.customer?.last_legal_name || '',
    nickname: props.customer?.nickname || '',
    email: props.customer?.email || '',
    customer_rating_id: props.customer?.customer_rating_id || '',
    is_active: props.customer?.is_active ?? true,
});

const submit = () => {
    const route = props.customer
        ? 'customers.update'
        : 'customers.store';
    
    const params = props.customer ? [props.customer.id] : [];

    form.post(route(route, ...params), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
            form.reset();
        },
        onError: () => {
            // Errors handled by Inertia
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};
</script>

<template>
  <Modal
    :show="show"
    @close="close"
  >
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">
        {{ customer ? 'Edit Customer' : 'Add Customer' }}
      </h2>

      <form @submit.prevent="submit">
        <div class="space-y-4">
          <!-- First Name -->
          <div>
            <InputLabel
              for="first_name"
              value="First Name *"
            />
            <TextInput
              id="first_name"
              v-model="form.first_name"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError
              :message="form.errors.first_name"
              class="mt-2"
            />
          </div>

          <!-- Last Name -->
          <div>
            <InputLabel
              for="last_legal_name"
              value="Last Name"
            />
            <TextInput
              id="last_legal_name"
              v-model="form.last_legal_name"
              type="text"
              class="mt-1 block w-full"
            />
            <InputError
              :message="form.errors.last_legal_name"
              class="mt-2"
            />
          </div>

          <!-- Nickname -->
          <div>
            <InputLabel
              for="nickname"
              value="Nickname *"
            />
            <TextInput
              id="nickname"
              v-model="form.nickname"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError
              :message="form.errors.nickname"
              class="mt-2"
            />
          </div>

          <!-- Email -->
          <div>
            <InputLabel
              for="email"
              value="Email"
            />
            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1 block w-full"
            />
            <InputError
              :message="form.errors.email"
              class="mt-2"
            />
          </div>

          <!-- Rating -->
          <div>
            <InputLabel
              for="customer_rating_id"
              value="Rating"
            />
            <select
              id="customer_rating_id"
              v-model="form.customer_rating_id"
              class="mt-1 block w-full border-gray-300 rounded-md"
            >
              <option value="">Select Rating</option>
              <option
                v-for="rating in ratings"
                :key="rating.id"
                :value="rating.id"
              >
                {{ rating.name }}
              </option>
            </select>
            <InputError
              :message="form.errors.customer_rating_id"
              class="mt-2"
            />
          </div>

          <!-- Active Status -->
          <div class="flex items-center">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="h-4 w-4 text-blue-600 border-gray-300 rounded"
            />
            <InputLabel
              for="is_active"
              value="Active"
              class="ml-2"
            />
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton
            type="button"
            @click="close"
          >
            Cancel
          </SecondaryButton>
          <PrimaryButton
            type="submit"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Saving...' : 'Save' }}
          </PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>
</template>
```

---

## Inertia.js Patterns

### Navigation with Preserved State

```javascript
import { router } from '@inertiajs/vue3';

// Navigate to a route
router.visit(route('customers.show', customerId));

// Navigate with preserved scroll position
router.visit(route('customers.index'), {
    preserveScroll: true,
});

// Navigate with preserved state
router.visit(route('customers.index'), {
    preserveState: true,
    preserveScroll: true,
});

// Reload specific data only
router.reload({
    only: ['customers', 'filters'],
    preserveScroll: true,
});
```

### Form Submission Patterns

```javascript
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
});

// POST request
form.post(route('customers.store'), {
    preserveScroll: true,
    onSuccess: () => {
        console.log('Success!');
        form.reset();
    },
    onError: (errors) => {
        console.log('Errors:', errors);
    },
    onFinish: () => {
        console.log('Request finished');
    },
});

// PUT/PATCH request
form.put(route('customers.update', customer.id), {
    preserveScroll: true,
    onSuccess: () => {
        emit('saved');
    },
});

// DELETE request
form.delete(route('customers.destroy', customer.id), {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure?'),
    onSuccess: () => {
        emit('deleted');
    },
});
```

---

## Form Patterns

### Complex Form with Nested Data

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    customer: {
        first_name: '',
        last_legal_name: '',
        email: '',
    },
    addresses: [
        {
            line_1: '',
            city: '',
            postal_code: '',
        },
    ],
    contacts: [],
});

const addAddress = () => {
    form.addresses.push({
        line_1: '',
        city: '',
        postal_code: '',
    });
};

const removeAddress = (index) => {
    form.addresses.splice(index, 1);
};

const submit = () => {
    form.post(route('customers.store'));
};
</script>
```

### Form with File Upload

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    document: null,
});

const handleFileChange = (event) => {
    form.document = event.target.files[0];
};

const submit = () => {
    form.post(route('documents.store'), {
        forceFormData: true, // Force multipart/form-data
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
  <form @submit.prevent="submit">
    <input
      type="file"
      @change="handleFileChange"
    />
    <button type="submit">
      Upload
    </button>
  </form>
</template>
```

---

## Permission Patterns

### Backend Permission Check

```php
// In Controller
public function edit(Customer $customer)
{
    $this->authorize('update', $customer);
    // or
    abort_unless(auth()->user()->can('edit customers'), 403);
    
    return Inertia::render('Customer/Edit', [
        'customer' => $customer,
    ]);
}

// In Middleware
Route::middleware(['auth', 'permission:manage customers'])->group(function () {
    Route::resource('customers', CustomerController::class);
});

// In Policy
class CustomerPolicy
{
    public function update(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('edit customers');
    }
}
```

### Frontend Permission Check

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const can = computed(() => page.props.auth.permissions || {});

const canEditCustomer = computed(() => {
    return can.value['edit customers'] === true;
});
</script>

<template>
  <Link
    v-if="canEditCustomer"
    :href="route('customers.edit', customer.id)"
  >
    Edit
  </Link>
</template>
```

---

## Query Patterns

### Eager Loading with Constraints

```php
$customers = Customer::with([
    'transportTransactions' => function ($query) {
        $query->where('is_transaction_done', false)
              ->orderBy('transport_date_earliest', 'desc')
              ->limit(5);
    },
    'addresses' => function ($query) {
        $query->where('is_primary', true);
    },
    'customerRating',
])
->active()
->get();
```

### Complex Queries with Subqueries

```php
$customers = Customer::select('customers.*')
    ->selectSub(function ($query) {
        $query->from('transport_transactions')
              ->selectRaw('COUNT(*)')
              ->whereColumn('customer_id', 'customers.id')
              ->where('is_transaction_done', false);
    }, 'active_transactions_count')
    ->having('active_transactions_count', '>', 0)
    ->get();
```

### Search Across Relationships

```php
$query = TransportTransaction::query()
    ->with(['customer', 'supplier', 'product'])
    ->when($search, function ($q, $search) {
        $q->where(function ($q) use ($search) {
            $q->whereHas('customer', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            })
            ->orWhereHas('supplier', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    });
```

---

## Notification Patterns

### SweetAlert Success/Error

```javascript
import { inject } from 'vue';

const swal = inject('$swal');

// Success notification
const showSuccess = (message) => {
    swal.fire({
        icon: 'success',
        title: 'Success!',
        text: message,
        timer: 3000,
        showConfirmButton: false,
    });
};

// Error notification
const showError = (message) => {
    swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message || 'Something went wrong!',
    });
};

// Confirmation dialog
const confirmDelete = async () => {
    const result = await swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    });
    
    if (result.isConfirmed) {
        // Proceed with deletion
        form.delete(route('customers.destroy', customer.id), {
            onSuccess: () => {
                showSuccess('Customer deleted successfully!');
            },
        });
    }
};
```

### Flash Messages

```php
// In Controller
return redirect()
    ->route('customers.index')
    ->with('success', 'Customer created successfully.');

// or
return back()
    ->with('error', 'Failed to update customer.')
    ->withInput();
```

```vue
<!-- In Layout or Page -->
<script setup>
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { inject } from 'vue';

const page = usePage();
const swal = inject('$swal');

watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        swal.fire({
            icon: 'success',
            title: 'Success!',
            text: flash.success,
            timer: 3000,
        });
    }
    if (flash.error) {
        swal.fire({
            icon: 'error',
            title: 'Error',
            text: flash.error,
        });
    }
}, { deep: true });
</script>
```

---

## Additional Patterns

### Date Formatting

```javascript
// Using moment.js
import moment from 'moment';

const formatDate = (date) => {
    return moment(date).format('DD MMM YYYY');
};

const formatDateTime = (date) => {
    return moment(date).format('DD MMM YYYY HH:mm');
};

// Relative time
const fromNow = (date) => {
    return moment(date).fromNow();
};
```

### Currency Formatting

```javascript
const formatCurrency = (amount, currency = 'ZAR') => {
    return new Intl.NumberFormat('en-ZA', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};
```

### Debounced Input

```vue
<script setup>
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';

const searchTerm = ref('');

const performSearch = debounce((term) => {
    router.get(route('customers.index'), { search: term }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

watch(searchTerm, (newTerm) => {
    performSearch(newTerm);
});
</script>
```

### Combobox (Searchable Select)

```vue
<script setup>
import { ref, computed } from 'vue';
import {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
} from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    options: Array,
    modelValue: [String, Number],
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');

const filteredOptions = computed(() => {
    if (!query.value) return props.options;
    
    return props.options.filter((option) => {
        return option.name
            .toLowerCase()
            .includes(query.value.toLowerCase());
    });
});

const selected = computed({
    get: () => props.options.find(o => o.id === props.modelValue),
    set: (value) => emit('update:modelValue', value?.id),
});
</script>

<template>
  <Combobox
    v-model="selected"
    as="div"
  >
    <div class="relative">
      <ComboboxInput
        class="w-full rounded-md border-gray-300"
        :display-value="(option) => option?.name"
        @change="query = $event.target.value"
      />
      <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
      </ComboboxButton>
      
      <ComboboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 shadow-lg">
        <ComboboxOption
          v-for="option in filteredOptions"
          :key="option.id"
          :value="option"
          v-slot="{ active, selected }"
        >
          <li :class="[
            'relative cursor-pointer select-none py-2 pl-3 pr-9',
            active ? 'bg-blue-600 text-white' : 'text-gray-900'
          ]">
            <span :class="['block truncate', selected && 'font-semibold']">
              {{ option.name }}
            </span>
            <span
              v-if="selected"
              :class="[
                'absolute inset-y-0 right-0 flex items-center pr-4',
                active ? 'text-white' : 'text-blue-600'
              ]"
            >
              <CheckIcon class="h-5 w-5" />
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>
```

---

These patterns cover the most common scenarios in the SilverGro application. Refer to existing code for more specific implementations and variations.

