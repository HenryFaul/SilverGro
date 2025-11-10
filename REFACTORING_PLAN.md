# Transaction Summary Refactoring Plan

## Objective
Break down the 11,836-line Index.vue into digestible, maintainable components while preserving ALL functionality.

## Approach: Incremental Refactoring
- Create feature branch ✅
- Extract one piece at a time
- Test after each extraction
- Commit working changes
- Easy rollback if needed

## Refactoring Steps

### Phase 1: Extract Utility Functions (Step 1) ✅ COMPLETE
**Goal:** Create composables for utility functions
**Files to create:**
- `useDateFormatters.js` - Date formatting functions
- `useNumberFormatters.js` - Number formatting functions
- `useTextFormatters.js` - Text formatting functions

**Functions to extract:**
- `dayIncluded()` - Check if day is included
- `NiceDay()` - Get day of week
- `NiceTDate()` - Format date nicely
- `TrunkCateText()` - Truncate text
- `format()`, `formatStart()`, `formatEarly()`, `formatLate()`, etc. - Date formatters
- `NiceNumber()` - Format numbers
- `NiceVariance()` - Calculate variance

**Testing:** Verify all date/number displays still work correctly

### Phase 2: Extract Filter Logic (Step 2) ✅ COMPLETE
**Goal:** Create filter composable
**Files to create:**
- `useTransactionFilters.js`

**What to extract:**
- Filter form state
- Filter debounce logic
- Day selection (mon, tue, wed, etc.)
- filteredTrans computed
- clear() function

**Testing:** Verify filtering still works

### Phase 3: Extract Table Component (Step 3)
**Goal:** Create reusable table component
**Files to create:**
- `TransactionTable.vue`

**What to extract:**
- Table rendering template
- Column headers
- Row rendering
- Sort functionality
- Pagination

**Testing:** Verify table displays and sorts correctly

### Phase 4: Extract Filter UI Component (Step 4)
**Goal:** Create filter UI component
**Files to create:**
- `TransactionFilters.vue`

**What to extract:**
- Filter form UI
- Date pickers
- Day checkboxes
- Search/clear buttons

**Testing:** Verify filters work

### Phase 5: Extract Detail Tabs Component (Step 5)
**Goal:** Create tab container component
**Files to create:**
- `TransactionDetails.vue`
- Individual tab components (as needed)

**What to extract:**
- Tab navigation
- Tab panels
- Form handling for each tab

**Testing:** Verify tabs switch and display correctly

### Phase 6: Extract Modal Components (Step 6)
**Goal:** Clean up modal management
**What to extract:**
- Modal state management
- Modal handlers

**Testing:** Verify modals open/close correctly

### Phase 7: Final Cleanup (Step 7)
**Goal:** Clean main component
- Remove extracted code
- Keep only composition
- Add proper prop passing

**Testing:** Full end-to-end testing

## Success Criteria
- ✅ All features work as before
- ✅ No regressions
- ✅ Code is more maintainable
- ✅ Components are reusable
- ✅ Build succeeds
- ✅ No console errors

## Rollback Strategy
Each step is committed separately. If issues arise:
1. Test the specific step
2. Fix the issue
3. OR rollback the last commit
4. Continue from previous working state

## Current Status
- Branch: `feature/refactor-transaction-summary` ✅
- Phase 1: **Utility Functions** ✅ COMPLETE (Commit: 07c58db)
- Phase 2: **Filter Logic** ✅ COMPLETE (Commit: 871279e)
- Progress: **2/7 phases (28%)** 🔄
- Next: Phase 3 - Extract table component

