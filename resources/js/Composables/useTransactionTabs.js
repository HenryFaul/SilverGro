// filepath: /Users/henryfaul/Software/SilverGro/resources/js/Composables/useTransactionTabs.js
import { computed, ref, unref } from 'vue';

/**
 * Encapsulate the tabs state and selection logic for Transaction Summary detail tabs.
 * @param {Ref<boolean>} isSplitRef - whether the transaction is a split load
 * @param {Ref<boolean>|boolean} isAdminRef - whether the current user has AdminRole
 */
export function useTransactionTabs(isSplitRef, isAdminRef) {
  // Tab definitions
  const tabsSplit = [
    { id: 0, name: 'Supplier', current: true },
    { id: 1, name: 'Product', current: false },
    { id: 2, name: 'Customer', current: false },
    { id: 3, name: 'Transport', current: false },
    { id: 4, name: 'Pricing', current: false },
    { id: 6, name: 'Process Control', current: false },
    { id: 5, name: 'Invoice', current: false },
    { id: 8, name: 'Documents', current: false },
    { id: 7, name: 'Linked Trades', current: false },
    { id: 9, name: 'Log', current: false },
    { id: 12, name: 'Staff allocation', current: false },
    { id: 13, name: 'Split Trades', current: false },
  ];

  const tabsNonSplit = [
    { id: 0, name: 'Supplier', current: true },
    { id: 1, name: 'Product', current: false },
    { id: 2, name: 'Customer', current: false },
    { id: 3, name: 'Transport', current: false },
    { id: 4, name: 'Pricing', current: false },
    { id: 6, name: 'Process Control', current: false },
    { id: 5, name: 'Invoice', current: false },
    { id: 8, name: 'Documents', current: false },
    { id: 7, name: 'Linked Trades', current: false },
    { id: 9, name: 'Log', current: false },
    { id: 12, name: 'Staff allocation', current: false },
  ];

  const tabs = computed(() => {
    const base = isSplitRef?.value ? tabsSplit : tabsNonSplit;
    if (unref(isAdminRef)) return base;
    return base.filter((t) => t.id !== 12);
  });

  const selectedTabId = ref(0);
  const selectTab = (id) => {
    selectedTabId.value = id;
  };

  return { tabs, selectedTabId, selectTab };
}
