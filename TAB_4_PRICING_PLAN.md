# Tab 4 (Pricing) Extraction Plan

**Date:** November 12, 2025  
**Tab Size:** ~860 lines (Line 3341 to 4200)  
**Priority:** ⭐ HIGH - Next recommended extraction

---

## Structure Analysis

### Cards Identified (4 main cards)

1. **Buying Price Card** (~200 lines)
    - Supplier info (read-only)
    - Product info (read-only)
    - Supply Packaging (HeadlessUI Combobox) ⚠️
    - Billing Units (HeadlessUI Combobox) ⚠️
    - No Units (read-only)
    - Supply Weight (read-only)
    - Cost Price / Unit (input)
    - Total Supplier Cost (calculated)
    - Cost Price / Ton (calculated)

2. **Transport Costs Card** (~150 lines)
    - Transport rate basis (select)
    - Transport rate (input)
    - Transport rate / Ton (calculated)
    - Transport costs (calculated)
    - Load Insurance per ton (calculated)
    - Transport cost in price (toggle)

3. **Selling Price Card** (~250 lines)
    - Customer info (read-only)
    - Product info (read-only)
    - Selling Packaging (HeadlessUI Combobox) ⚠️
    - Billing Units (HeadlessUI Combobox) ⚠️
    - No Units (read-only)
    - Selling Weight (read-only)
    - Selling Price / Unit (input)
    - Total Selling Price (calculated)
    - Selling Price / Ton (calculated)

4. **Margin/GP Card** (~260 lines)
    - Margin calculations
    - GP (Gross Profit) calculations
    - Additional costs (3x dynamic inputs)
    - Adjusted GP
    - Comparison table (Plan vs Actual)

---

## Challenges to Address

### 🔴 HeadlessUI Comboboxes (4 instances)

- Packaging incoming
- Billing units incoming
- Packaging outgoing
- Billing units outgoing

**Solution:** Create custom select components (like we did for supplier/customer/transporter)

### 🟡 Complex Calculations

- Multiple calculated fields
- Financial data
- Plan vs Actual table

**Solution:** Keep calculations in parent, pass as props

### 🟡 Form Interactions

- Multiple inputs affecting calculations
- Dynamic additional cost fields

**Solution:** Emit events for form updates

---

## Recommended Approach

### Option A: Full Extraction (3-4 hours)

1. Create `TransactionPricingTab.vue` component
2. Create custom packaging/billing select components
3. Extract all 4 cards
4. Wire all props and events
5. Test thoroughly

### Option B: Incremental (Recommended - 1-2 hours each)

**Phase 1:** Replace HeadlessUI Comboboxes

- Create `TransactionPackagingSelect.vue`
- Create `TransactionBillingUnitsSelect.vue`
- Replace 4 combobox instances in Tab 4
- Test and commit

**Phase 2:** Extract cards to component

- Create `TransactionPricingTab.vue`
- Extract 4 cards
- Wire props/events
- Test and commit

---

## Custom Components Needed

### 1. TransactionPackagingSelect.vue

```vue
// Similar to TransactionSupplierSelect
// Props: modelValue, packaging (array), label
// Emits: update:modelValue
// Features: search/filter, no HeadlessUI
```

### 2. TransactionBillingUnitsSelect.vue

```vue
// Similar to TransactionSupplierSelect
// Props: modelValue, billingUnits (array), label
// Emits: update:modelValue
// Features: search/filter, no HeadlessUI
```

### 3. TransactionPricingTab.vue

```vue
// Main tab component
// Props: combinedForm, selectedTransaction, filters, etc.
// Emits: form update events
// Contains: 4 pricing cards
```

---

## Props Required

```javascript
// TransactionPricingTab.vue props
{
    combinedForm: Object,
        selectedTransaction
:
    Object,
        filteredPackageIncoming
:
    Array,
        filteredPackageOutgoing
:
    Array,
        filteredBillingUnitsIncoming
:
    Array,
        filteredBillingUnitsOutgoing
:
    Array,
        allTransportRates
:
    Array,
    // ... more props for calculations
}
```

## Events to Emit

```javascript
// TransactionPricingTab.vue emits
[
    'update:packageIncomingQuery',
    'update:packageOutgoingQuery',
    'update:billingUnitsIncomingQuery',
    'update:billingUnitsOutgoingQuery',
    // ... form field updates
]
```

---

## Estimated Effort

| Task                                   | Time         | Priority |
|----------------------------------------|--------------|----------|
| Create TransactionPackagingSelect      | 30 min       | High     |
| Create TransactionBillingUnitsSelect   | 30 min       | High     |
| Replace 4 comboboxes in Tab 4          | 30 min       | High     |
| Test combobox replacements             | 15 min       | High     |
| **Phase 1 Total**                      | **~2 hours** | -        |
|                                        |              |          |
| Create TransactionPricingTab component | 1 hour       | Medium   |
| Extract 4 pricing cards                | 1.5 hours    | Medium   |
| Wire all props and events              | 45 min       | Medium   |
| Test full extraction                   | 30 min       | Medium   |
| **Phase 2 Total**                      | **~4 hours** | -        |
|                                        |              |          |
| **Grand Total**                        | **~6 hours** | -        |

---

## Benefits of Extraction

### Code Organization

- ✅ ~860 lines removed from Index.vue
- ✅ Pricing logic isolated
- ✅ Easier to maintain

### Consistency

- ✅ Same patterns as Tabs 0-2
- ✅ Custom dropdowns (no HeadlessUI issues)
- ✅ Reusable components

### Testing

- ✅ Can test pricing component independently
- ✅ Financial calculations isolated
- ✅ Easier to add unit tests

---

## Next Session Checklist

### Before Starting

- [ ] Read this plan
- [ ] Review Tab 4 current code (lines 3341-4200)
- [ ] Check Tab 2 extraction pattern
- [ ] Ensure Tab 2 user testing complete

### Phase 1: Replace Comboboxes ✅ COMPLETE

- [x] Create TransactionPackagingSelect.vue
- [x] Create TransactionBillingUnitsSelect.vue
- [x] Replace packaging_incoming combobox
- [x] Replace packaging_outgoing combobox
- [x] Replace billing_units_incoming combobox
- [x] Replace billing_units_outgoing combobox
- [x] Build and test
- [x] Commit Phase 1

### Phase 2: Full Extraction (Optional)

- [ ] Create TransactionPricingTab.vue
- [ ] Extract Buying Price card
- [ ] Extract Transport Costs card
- [ ] Extract Selling Price card
- [ ] Extract Margin/GP card
- [ ] Wire all props (10-15 props)
- [ ] Wire all events (5-8 events)
- [ ] Build and test
- [ ] Commit Phase 2

---

## Risk Assessment

### Low Risk ✅

- Replacing comboboxes (proven pattern)
- Using custom select components
- Following established patterns

### Medium Risk ⚠️

- Financial calculation wiring
- Many props/events to manage
- Complex margin/GP card

### Mitigation

- Test after each card extraction
- Keep calculations in parent
- Incremental approach (Phase 1 then Phase 2)

---

## Success Criteria

### Phase 1 Complete ✅

- [x] All 4 comboboxes replaced
- [x] Build succeeds
- [x] No HeadlessUI in Tab 4
- [x] Dropdowns work with search
- [x] Form updates correctly

### Phase 2 Complete (Full Extraction)

- [ ] TransactionPricingTab component created
- [ ] ~860 lines extracted from Index.vue
- [ ] All 4 cards working
- [ ] Build succeeds
- [ ] Calculations accurate
- [ ] User testing passed

---

## Current Status

**Status:** ✅ **Phase 1 COMPLETE!**  
**Phase 2:** Not started (optional)  
**Blockers:** None  
**Build:** ✅ Succeeds  
**Ready for Testing:** Yes ✅

**Phase 1 Achievements:**

- ✅ TransactionPackagingSelect.vue created (135 lines)
- ✅ TransactionBillingUnitsSelect.vue created (135 lines)
- ✅ All 4 HeadlessUI Comboboxes replaced (~200 lines cleaned)
- ✅ Build succeeds without errors
- ✅ Custom dropdowns with search/filter functionality
- ✅ No more HeadlessUI in Tab 4 pricing dropdowns

**Next:** User testing or proceed to Phase 2 (full extraction)

---

**Recommendation:** Start with Phase 1 (combobox replacement) in next session. Low risk, high value, ~2 hours.

After Phase 1 is tested and working, decide whether to continue with Phase 2 (full extraction) or move to another tab.
