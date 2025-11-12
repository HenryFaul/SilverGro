import { useFilteredList } from './useFilteredList.js';

/**
 * Supplier tab state/computations for Transaction Summary
 * Uses the generic useFilteredList composable
 */
export function useSupplierTab(props) {
  const { query: supplierQuery, filteredItems: filteredSuppliers } = useFilteredList(
    props.all_suppliers,
    'last_legal_name'
  );

  return {
    supplierQuery,
    filteredSuppliers,
  };
}
