import { useFilteredList } from './useFilteredList.js';

/**
 * Customer tab filters for Transaction Summary
 * Manages multiple customer queries (for split loads with multiple customers)
 */
export function useCustomerTab(props) {
  // Main customer filter
  const { query: customerQuery, filteredItems: filteredCustomers } = useFilteredList(
    props.all_customers,
    'last_legal_name'
  );

  // Customer 2 filter (for split loads)
  const { query: customerQuery2, filteredItems: filteredCustomers2 } = useFilteredList(
    props.all_customers,
    'last_legal_name'
  );

  // Customer 3 filter (for split loads)
  const { query: customerQuery3, filteredItems: filteredCustomers3 } = useFilteredList(
    props.all_customers,
    'last_legal_name'
  );

  // Customer 4 filter (for split loads)
  const { query: customerQuery4, filteredItems: filteredCustomers4 } = useFilteredList(
    props.all_customers,
    'last_legal_name'
  );

  // Customer 5 filter (for split loads)
  const { query: customerQuery5, filteredItems: filteredCustomers5 } = useFilteredList(
    props.all_customers,
    'last_legal_name'
  );

  return {
    customerQuery,
    filteredCustomers,
    customerQuery2,
    filteredCustomers2,
    customerQuery3,
    filteredCustomers3,
    customerQuery4,
    filteredCustomers4,
    customerQuery5,
    filteredCustomers5,
  };
}
