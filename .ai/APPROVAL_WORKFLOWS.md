# Approval Workflows

## Overview

The SilverGro system implements different approval workflows based on contract types. Each contract type has specific
approval requirements and generates unique sequential approval numbers for tracking and reference.

## Contract Types

### 1. Direct Sale (contract_type_id = 1)

- **No approval required**
- Transactions are immediately active
- No approval number assigned
- Used for simple, pre-approved transactions

### 2. PC - Purchase Confirmation (contract_type_id = 2)

- **Simplified approval workflow**
- Approval number: `a_pc` (Purchase Confirmation number)
- Sequential numbering (e.g., PC:1, PC:2, PC:3...)
- Controller: `PcScApprovalController`
- Route: `POST /pc_sc_approval/approve`

### 3. SC - Sales Confirmation (contract_type_id = 3)

- **Simplified approval workflow**
- Approval number: `a_sc` (Sales Confirmation number)
- Sequential numbering (e.g., SC:1, SC:2, SC:3...)
- Controller: `PcScApprovalController`
- Route: `POST /pc_sc_approval/approve`

### 4. MQ - Market Quote (contract_type_id = 4)

- **Complex approval workflow**
- Approval number: `a_mq` (Market Quote number)
- Sequential numbering (e.g., MQ:1, MQ:2, MQ:3...)
- Controller: `TransportApprovalController`
- Route: `POST /trans_approval/approve`
- Requires:
    - Trade rule evaluation based on selling price
    - Role-based approval workflow
    - Multiple approval stages
    - Deal ticket activation

## Database Fields

### transport_transactions Table

```php
$table->bigInteger('a_mq')->nullable();  // MQ approval number
$table->bigInteger('a_pc')->nullable();  // PC approval number
$table->bigInteger('a_sc')->nullable();  // SC approval number
$table->foreignId('contract_type_id');   // Links to contract_types
```

**Migration:** `2025_11_20_193314_add_pc_and_sc_to_transport_transactions_table.php`

## PC/SC Approval Process

### Frontend Flow (TransactionSummary)

1. User views transaction with contract_type_id = 2 or 3
2. Navigates to Tab 6 (Approvals/Documents)
3. Sees PC/SC Approvals section (conditionally rendered)
4. Clicks "Approve" button
5. Form data sent: `{ transport_trans_id: X }`

### Backend Processing (PcScApprovalController)

```php
public function approve(Request $request): RedirectResponse
{
    // Validate request
    $request->validate([
        'transport_trans_id' => ['required', 'integer', 'exists:transport_transactions,id'],
    ]);
    
    // Get transaction
    $transport_trans = TransportTransaction::find($request->transport_trans_id);
    $contract_type_id = $transport_trans->contract_type_id;
    
    // Guard: Only allow PC (2) or SC (3)
    if (!in_array($contract_type_id, [2, 3])) {
        return redirect()->back()->with('error', 'Invalid contract type');
    }
    
    // PC Approval (contract_type_id = 2)
    if ($contract_type_id == 2 && $transport_trans->a_pc == null) {
        $max_a_pc = TransportTransaction::max("a_pc") ?? 0;
        $transport_trans->a_pc = $max_a_pc + 1;
        $transport_trans->save();
    }
    
    // SC Approval (contract_type_id = 3)
    if ($contract_type_id == 3 && $transport_trans->a_sc == null) {
        $max_a_sc = TransportTransaction::max("a_sc") ?? 0;
        $transport_trans->a_sc = $max_a_sc + 1;
        $transport_trans->save();
    }
    
    return redirect()->back()->with('success', 'Approved');
}
```

### Key Features

- **Automatic Sequential Numbering**: System finds max existing number and increments
- **Idempotent**: Won't reassign if number already exists
- **Independent Sequences**: PC, SC, and MQ numbers are separate
- **No Duplicate Check Needed**: Database constraint ensures uniqueness via sequential assignment

## MQ Approval Process

### Frontend Flow (TransactionSummary)

1. User views transaction with contract_type_id = 4
2. Navigates to Tab 6 (Approvals/Documents)
3. Sees MQ Approvals section with:
    - Trade Rules table
    - Trade Operation Rules table
    - Approval status for each role
4. Clicks "Approve" button
5. Form data sent: `{ transport_trans_id: X, transport_job_id: Y, deal_ticket_id: Z }`

### Backend Processing (TransportApprovalController)

```php
public function approve(Request $request): RedirectResponse
{
    $transport_trans = TransportTransaction::find($request->transport_trans_id);
    
    // Guard: Only allow MQ contracts
    if ($transport_trans->contract_type_id != 4) {
        return redirect()->back()->with('error', 'Only for MQ contracts');
    }
    
    // Get applicable trade rules based on selling price
    $selling_price = $transport_trans->TransportFinance->selling_price ?? 0;
    $trade_rule = TradeRule::where('max_trade_value', '>=', $selling_price)
                           ->where('min_trade_value', '<=', $selling_price)
                           ->first();
    
    // Create approval records for each user role
    $user = Auth::user();
    $roles = $user->getRoleNames();
    
    foreach ($roles as $role) {
        TransportApproval::create([
            'transport_trans_id' => $request->transport_trans_id,
            'deal_ticket_id' => $request->deal_ticket_id,
            'poly_approval_id' => $trade_rule->id,
            'poly_approval_type' => get_class($trade_rule),
            'transport_job_id' => $request->transport_job_id,
            'approved_by_id' => $user->id,
            'is_approved' => true,
            'role_name' => $role
        ]);
    }
    
    // Check if all required approvals are complete
    $deal_ticket->calculateRules();
    $is_approved = $deal_ticket->is_approved;
    
    // If fully approved, activate and assign MQ number
    if ($is_approved && $deal_ticket->is_active) {
        if ($transport_trans->a_mq == null) {
            $max_a_mq = TransportTransaction::max("a_mq") ?? 0;
            $transport_trans->a_mq = $max_a_mq + 1;
            $transport_trans->save();
            
            // Send notifications
            $user->notify(new DealTicketApproved($deal_ticket, $transport_trans));
        }
    }
    
    return redirect()->back()->with('success', 'Approval updated');
}
```

## Display in UI

### HomeOverview (Table View)

```vue

<Link :data="{ selected_trans_id: transaction.id }" href="/transaction_summary">
<span v-if="transaction.a_mq" class="font-bold">(MQ:{{ transaction.a_mq }})</span>
<span v-if="transaction.a_pc" class="font-bold">(PC:{{ transaction.a_pc }})</span>
<span v-if="transaction.a_sc" class="font-bold">(SC:{{ transaction.a_sc }})</span>
<span>(ID:{{ transaction.id }})</span>
<span>(Old:{{ transaction.old_id }})</span>
</Link>
```

### TransactionSummary (Header)

```vue

<div v-if="selected_transaction.a_mq" class="py-2 inline-flex text-xl font-bold text-black">
    MQ{{ selected_transaction.a_mq }}
</div>
<div v-if="selected_transaction.a_pc" class="py-2 inline-flex text-xl font-bold text-black">
    PC{{ selected_transaction.a_pc }}
</div>
<div v-if="selected_transaction.a_sc" class="py-2 inline-flex text-xl font-bold text-black">
    SC{{ selected_transaction.a_sc }}
</div>
```

### TransactionTableRow (ID Column)

```vue
<span v-if="transaction.a_mq" class="font-bold">(MQ:{{ transaction.a_mq }})</span>
<span v-if="transaction.a_pc" class="font-bold">(PC:{{ transaction.a_pc }})</span>
<span v-if="transaction.a_sc" class="font-bold">(SC:{{ transaction.a_sc }})</span>
(ID:{{ transaction.id }})
```

### Tab 6 - Approvals Section (PC/SC)

```vue

<li v-if="selected_transaction.contract_type_id === 2 || selected_transaction.contract_type_id === 3">
    <div class="text-sm font-medium">
        {{ selected_transaction.contract_type_id === 2 ? 'PC' : 'SC' }} Approvals
    </div>

    <div v-if="selected_transaction.a_pc" class="font-bold text-indigo-400">
        PC#: {{ selected_transaction.a_pc }}
    </div>
    <div v-if="selected_transaction.a_sc" class="font-bold text-indigo-400">
        SC#: {{ selected_transaction.a_sc }}
    </div>
    <div v-else class="text-red-400">Not Yet Approved</div>

    <SecondaryButton @click="createPcScApproval">Approve</SecondaryButton>
</li>
```

## Search & Filtering

### Backend (Controller)

```php
$filters = $request->only([
    'supplier_name',
    'customer_name',
    'a_mq',
    'a_pc',  // Added
    'a_sc',  // Added
]);
```

### Backend (Model Scope)

```php
public function scopeIndex(Builder $query, array $filters): Builder
{
    return $query
        ->when($filters['a_mq'] ?? false, 
            fn ($query, $value) => $query->where('a_mq','like', '%'.$value.'%'))
        ->when($filters['a_pc'] ?? false, 
            fn ($query, $value) => $query->where('a_pc','like', '%'.$value.'%'))
        ->when($filters['a_sc'] ?? false, 
            fn ($query, $value) => $query->where('a_sc','like', '%'.$value.'%'));
}
```

### Frontend (Filter Inputs)

```vue
<!-- MQ Search -->
<input v-model.number="filterForm.a_mq" placeholder="MQ no..." />

<!-- PC Search -->
<input v-model.number="filterForm.a_pc" placeholder="PC no..." />

<!-- SC Search -->
<input v-model.number="filterForm.a_sc" placeholder="SC no..." />
```

### Frontend (Watchers)

```javascript
watch(() => filterForm.a_mq, () => filter());
watch(() => filterForm.a_pc, () => filter());
watch(() => filterForm.a_sc, () => filter());
```

## State Management

### Form State (useTransactionStatusForms.js)

```javascript
// PC/SC Approval form
const pcScApprovalForm = useForm({
    transport_trans_id: props.selected_transaction.id,
});

// Export
return {
    pcScApprovalForm,
    // ...other forms
};
```

### State Reset on Transaction Change

When user switches transactions, all forms must reset to avoid wrong transaction approval:

```javascript
// useTransactionCombinedForm.js
const updateSelectValues = (statusForms = {}) => {
    const { transportApprovalForm, pcScApprovalForm, statusForm } = statusForms;

    if (pcScApprovalForm) {
        pcScApprovalForm.transport_trans_id = props.selected_transaction.id;
    }
    // ...update other forms
};
```

### Action Handler (useTransactionActions.js)

```javascript
const createPcScApproval = () => {
    pcScApprovalForm.post(route('pc_sc_approval.approve'), {
        preserveScroll: true,
        onSuccess: () => {
            filter();  // Refresh data
            window.swal(usePage().props.jetstream.flash?.banner || '');
        },
    });
};
```

## Routes

```php
// routes/web.php

// MQ Approval (contract_type_id = 4)
Route::post('trans_approval/approve', [TransportApprovalController::class, 'approve'])
    ->middleware('auth')
    ->name('trans_approval.approve');

Route::post('trans_approval/activate', [TransportApprovalController::class, 'activate'])
    ->middleware('auth')
    ->name('trans_approval.activate');

// PC/SC Approval (contract_type_id = 2 or 3)
Route::post('pc_sc_approval/approve', [PcScApprovalController::class, 'approve'])
    ->middleware('auth')
    ->name('pc_sc_approval.approve');
```

## Controllers

- **TransportApprovalController**: Handles MQ approvals (complex workflow)
- **PcScApprovalController**: Handles PC and SC approvals (simplified workflow)

Both controllers have guards to prevent wrong contract types:

```php
// TransportApprovalController - Guard for MQ only
if ($transport_trans->contract_type_id != 4) {
    return redirect()->back()->with('error', 'This is only for MQ contracts');
}

// PcScApprovalController - Guard for PC/SC only
if (!in_array($contract_type_id, [2, 3])) {
    return redirect()->back()->with('error', 'Invalid contract type');
}
```

## Testing Checklist

### PC Approval

- [ ] Create PC transaction (contract_type_id = 2)
- [ ] Navigate to Tab 6, see PC Approvals section
- [ ] Click Approve button
- [ ] Verify PC# assigned (e.g., PC:1)
- [ ] Verify display in HomeOverview table
- [ ] Verify display in TransactionSummary header
- [ ] Search by PC number works
- [ ] Switch transactions - verify state resets correctly

### SC Approval

- [ ] Create SC transaction (contract_type_id = 3)
- [ ] Navigate to Tab 6, see SC Approvals section
- [ ] Click Approve button
- [ ] Verify SC# assigned (e.g., SC:1)
- [ ] Verify display in HomeOverview table
- [ ] Verify display in TransactionSummary header
- [ ] Search by SC number works
- [ ] Switch transactions - verify state resets correctly

### MQ Approval

- [ ] Create MQ transaction (contract_type_id = 4)
- [ ] Navigate to Tab 6, see MQ Approvals with Trade Rules
- [ ] Click Approve button
- [ ] Verify trade rules evaluated
- [ ] Verify MQ# assigned after full approval
- [ ] Verify display across all views
- [ ] Search by MQ number works

### Edge Cases

- [ ] Attempt PC approval on MQ transaction - should fail
- [ ] Attempt MQ approval on PC transaction - should fail
- [ ] Re-approve already approved transaction - should not reassign number
- [ ] Sequential numbering correct (no gaps or duplicates)
- [ ] Filtering by approval number (partial match works)

## Common Pitfalls

1. **Wrong Controller**: Using TransportApprovalController for PC/SC or vice versa
2. **State Not Reset**: Form retains old transaction_id when switching transactions
3. **Missing Guards**: Controllers not checking contract_type_id
4. **Display Missing**: Approval numbers added to backend but not displayed in frontend
5. **Filter Not Working**: Backend accepts filter but model scope doesn't filter by it

## Best Practices

1. **Always validate contract_type_id** in controllers before processing
2. **Reset form state** when transaction changes to prevent cross-transaction approvals
3. **Use sequential numbering** to ensure unique, ordered approval numbers
4. **Display consistently** across all views (HomeOverview, TransactionSummary, etc.)
5. **Implement both backend filtering and frontend search** for complete search functionality
6. **Guard both approve and activate** methods in controllers
7. **Document approval numbers** in database schema and UI for maintainability

