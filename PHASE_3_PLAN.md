# Phase 3: Extract Table Components - Detailed Plan

## Overview
Phase 3 will extract the large transaction table into multiple smaller, manageable components.

## Sub-Phase Breakdown

### Phase 3A: Extract Table Row Component (FIRST) ✅
**Goal:** Create reusable row component
**File:** `TransactionTableRow.vue`
**Lines:** ~100-150 lines
**What to extract:**
- Single transaction row rendering
- Row click handler
- Cell rendering logic
- Conditional column display
- Row styling/classes

**Props needed:**
- transaction (object)
- showDetails (boolean)
- isSelected (boolean)
- Various style classes

**Emits:**
- @select - when row is clicked

**Testing:** Verify rows display and selection works

---

### Phase 3B: Extract Table Header Component (SECOND)
**Goal:** Create sortable header component
**File:** `TransactionTableHeader.vue`
**Lines:** ~80-100 lines
**What to extract:**
- Table header row
- Column headers
- Sort indicators
- Conditional columns (showDetails)
- Click handlers for sorting

**Props needed:**
- showDetails (boolean)
- sortField (string)
- sortDirection (string)
- headerStyles (object/string)

**Emits:**
- @sort - when header is clicked

**Testing:** Verify headers display and sorting works

---

### Phase 3C: Extract Main Table Component (THIRD)
**Goal:** Compose table from header + rows
**File:** `TransactionTable.vue`
**Lines:** ~150-200 lines
**What to extract:**
- Table wrapper
- Loading state
- Empty state
- Uses TransactionTableHeader
- Uses TransactionTableRow (loop)
- Pagination component

**Props needed:**
- transactions (array)
- selectedTransaction (object)
- showDetails (boolean)
- isLoading (boolean)
- sortField (string)
- sortDirection (string)

**Emits:**
- @sort
- @select-transaction

**Testing:** Verify complete table works

---

### Phase 3D: Extract Filter UI Component (FOURTH - OPTIONAL)
**Goal:** Create filters UI component
**File:** `TransactionFilters.vue`
**Lines:** ~200-300 lines
**What to extract:**
- Filter form UI
- Input fields
- Date pickers
- Day checkboxes
- Search/Clear buttons

**Props needed:**
- filterForm (object)
- dayFlags (object with mon-sun)
- contractTypes (array)

**Emits:**
- @clear
- @search
- @add-trade
- @toggle-details

**Testing:** Verify filters display and work

---

## Implementation Order

1. **Start Small:** Phase 3A (Row Component)
   - Smallest piece
   - Easy to test
   - Commit

2. **Add Header:** Phase 3B (Header Component)
   - Independent piece
   - Commit

3. **Compose Table:** Phase 3C (Main Table)
   - Uses 3A + 3B
   - Commit

4. **Optional:** Phase 3D (Filter UI)
   - Can be separate phase if needed
   - Commit

## Success Criteria

After Phase 3:
- ✅ Table renders correctly
- ✅ Rows are clickable
- ✅ Sorting works
- ✅ Selection works
- ✅ Loading state works
- ✅ Pagination works
- ✅ ~600-800 lines removed from Index.vue
- ✅ 3-4 new components created
- ✅ Build successful
- ✅ All functionality preserved

## Estimated Time
- Phase 3A: 10-15 minutes
- Phase 3B: 10-15 minutes
- Phase 3C: 15-20 minutes
- Phase 3D: 20-30 minutes (optional)

**Total:** 35-50 minutes (or ~1 hour with 3D)

## Start With
**Phase 3A: TransactionTableRow.vue**
- Smallest, most isolated component
- Easy to test
- Good foundation for 3B and 3C

