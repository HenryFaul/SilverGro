import { useFilteredList, useFilteredListMultiField } from './useFilteredList.js';

/**
 * Transport tab filters for Transaction Summary
 * Manages transporter, driver, and vehicle query filters
 */
export function useTransportTab(props) {
  // Transporter filter
  const { query: transporterQuery, filteredItems: filteredTransporters } =
    useFilteredList(props.all_transporters, 'last_legal_name');

  // Vehicle filter
  const { query: selectedVehicleQuery, filteredItems: filteredSelectedVehicles } =
    useFilteredList(props.all_vehicles, 'reg_no');

  // Driver filter (searches both first_name and last_name)
  const { query: selectedDriverQuery, filteredItems: filteredSelectedDrivers } =
    useFilteredListMultiField(props.all_drivers, ['first_name', 'last_name']);

  return {
    transporterQuery,
    filteredTransporters,
    selectedVehicleQuery,
    filteredSelectedVehicles,
    selectedDriverQuery,
    filteredSelectedDrivers,
  };
}
