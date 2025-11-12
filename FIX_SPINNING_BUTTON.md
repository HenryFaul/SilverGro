# Bug Fix: Update Button Keeps Spinning

## Issue

When updating the supplier from the TransactionSummary tab, the success flash message appears correctly, but the Update
button (next to the Clone button) continues to show a spinning animation indefinitely, never returning to its normal
state.

## Root Cause

The `isUpdating` reactive state was only being reset in the `onSuccess` and `onError` callbacks of the Inertia form
submission. This pattern has a potential edge case:

- **onSuccess**: Runs when the server returns a 2xx response
- **onError**: Runs when the server returns an error response (validation, 500, etc.)
- **onFinish**: **Always runs** after the request completes, regardless of outcome

If the request completes but something unexpected happens (like a redirect, navigation, or edge-case response), neither
`onSuccess` nor `onError` might fire, leaving `isUpdating` stuck at `true`.

## Solution

Moved the `isUpdating.value = false` statement from `onSuccess` and `onError` callbacks to the `onFinish` callback. This
follows Inertia.js best practices and ensures the loading state is **always** reset after any request completes.

### Changes Made

Updated 7 functions in `Index.vue`:

1. **updateCombined_Form** (main update function)
2. **activateSalesOrder**
3. **activatePurchaseOrder**
4. **activateTransportOrder**
5. **sendSalesOrder**
6. **sendPurchaseOrder**
7. **sendTransportOrder**

### Before

```javascript
const updateCombined_Form = () => {
    isUpdating.value = true;

    combined_Form.put(route('transaction_summary.update', props.selected_transaction.id), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
            isUpdating.value = false;  // ❌ Only resets on success
        },
        onError: (error) => {
            isUpdating.value = false;  // ❌ Only resets on error
            alert('Something went wrong on the Transaction');
        },
    });
};
```

### After

```javascript
const updateCombined_Form = () => {
    isUpdating.value = true;

    combined_Form.put(route('transaction_summary.update', props.selected_transaction.id), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
            // State reset moved to onFinish
        },
        onError: (error) => {
            alert('Something went wrong on the Transaction');
            // State reset moved to onFinish
        },
        onFinish: () => {
            isUpdating.value = false;  // ✅ Always resets regardless of outcome
        },
    });
};
```

## Benefits

✅ **Guaranteed state reset**: `isUpdating` will always return to `false` after any request  
✅ **Follows Inertia.js best practices**: Use `onFinish` for cleanup/loading state  
✅ **Consistent behavior**: All form submissions now handle loading state the same way  
✅ **Better UX**: No more stuck spinning buttons  
✅ **Prevents edge cases**: Handles redirects, navigation, and unexpected responses

## Testing

After this fix:

1. Update supplier from the tab
2. Wait for success message
3. **✅ Verify**: Update button stops spinning immediately
4. Button should return to normal "Update" text without spinner

## Related Files

- `/resources/js/Pages/TransactionSummary/Index.vue` (modified)

## Commit

```
fix: update button keeps spinning after successful supplier update
```

## Date

November 12, 2025

