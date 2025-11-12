# 📋 Remaining Tab Extraction Plan

## Current Status Analysis

### ✅ Already Componentized

- **Tab 0 (Supplier)** - Uses TransactionSupplierCard ✅
- **Tab 1 (Product)** - Uses Product card components ✅

### ❌ Still Inline (Need Extraction)

- **Tab 2 (Customer)** - Large table with 5 customers + addresses
- **Tab 3 (Transport)** - Partial (has TransactionTransporterSelect but rest inline)
- **Tab 4 (Pricing)** - All inline
- **Tab 5 (Invoice)** - All inline
- **Tab 6 (Process Control)** - All inline
- **Tab 7 (Linked Trades)** - All inline
- **Tab 8 (Documents)** - All inline
- **Tab 9 (Log)** - All inline
- **Tab 12 (Staff Allocation)** - All inline
- **Tab 13 (Split Trades)** - All inline (only shows for split loads)

### 📊 Summary

- **2 tabs** fully componentized
- **10-11 tabs** need extraction

## Extraction Strategy

### Phase 1: High Priority Tabs (Complex, Frequently Used)

1. **Tab 2 (Customer)** - Large table, multiple customers
2. **Tab 3 (Transport)** - Complete the extraction
3. **Tab 4 (Pricing)** - Financial data
4. **Tab 6 (Process Control)** - Order management

### Phase 2: Medium Priority Tabs

5. **Tab 5 (Invoice)** - Billing information
6. **Tab 7 (Linked Trades)** - Relationships
7. **Tab 12 (Staff Allocation)** - User assignments

### Phase 3: Lower Priority Tabs (Simpler)

8. **Tab 8 (Documents)** - File management
9. **Tab 9 (Log)** - Activity log
10. **Tab 13 (Split Trades)** - Conditional tab

## Component Names to Create

1. `TransactionCustomerTab.vue` - Customer table with all 5
2. `TransactionTransportTab.vue` - Complete transport details
3. `TransactionPricingTab.vue` - Pricing and financials
4. `TransactionProcessControlTab.vue` - Order status management
5. `TransactionInvoiceTab.vue` - Invoice details
6. `TransactionLinkedTradesTab.vue` - Trade relationships
7. `TransactionStaffAllocationTab.vue` - Staff assignments
8. `TransactionDocumentsTab.vue` - Document management
9. `TransactionLogTab.vue` - Activity log
10. `TransactionSplitTradesTab.vue` - Split load management

## Implementation Approach

### For Each Tab:

1. **Extract** - Copy tab content to new component file
2. **Props** - Identify needed data (forms, computed, props)
3. **Events** - Identify needed emits (updates, actions)
4. **Replace** - Replace inline with component usage
5. **Test** - Verify tab still works
6. **Commit** - Save working state

### Pattern:

```vue
<!-- In Index.vue -->
<div v-if="selectedTabId === X">
    <TransactionXTab
        :combined-form="combined_Form"
        :selected-transaction="selected_transaction"
        :filtered-data="filteredData"
        @update:field="handleUpdate"
        @action="handleAction" />
</div>
```

## Benefits

### After Extraction:

- ✅ Each tab is a focused component (200-500 lines)
- ✅ Index.vue becomes just orchestration (~2,000 lines)
- ✅ Tabs can be tested independently
- ✅ Easier to maintain and debug
- ✅ Can lazy load tabs for performance

## Starting Point

**Let's begin with Tab 2 (Customer)** - It's the next logical step after fixing the customer dropdowns.
