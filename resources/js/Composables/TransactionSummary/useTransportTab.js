import { computed } from 'vue';
import { useFilteredList, useFilteredListMultiField } from './useFilteredList.js';

/**
 * Transport tab filters for Transaction Summary
 * Manages transporter, driver, and vehicle query filters
 */
export function useTransportTab(props, combined_Form) {
  // Transporter filter
  const { query: transporterQuery, filteredItems: filteredTransporters } =
    useFilteredList(props.all_transporters, 'last_legal_name');

  // Filter vehicles by selected transporter
  const vehiclesForTransporter = computed(() => {
    if (!combined_Form.transporter_id || !combined_Form.transporter_id.id) {
      return props.all_vehicles;
    }
    const transporterId = combined_Form.transporter_id.id;
    return props.all_vehicles.filter(
      (vehicle) => vehicle.transporter && vehicle.transporter.id === transporterId
    );
  });

  // Filter drivers by selected transporter
  const driversForTransporter = computed(() => {
    if (!combined_Form.transporter_id || !combined_Form.transporter_id.id) {
      return props.all_drivers;
    }
    const transporterId = combined_Form.transporter_id.id;
    return props.all_drivers.filter(
      (driver) => driver.transporter && driver.transporter.id === transporterId
    );
  });

  // Vehicle filter (now filtered by transporter)
  const { query: selectedVehicleQuery, filteredItems: filteredSelectedVehicles } =
    useFilteredList(vehiclesForTransporter, 'reg_no');

  // Driver filter (now filtered by transporter, searches both first_name and last_name)
  const { query: selectedDriverQuery, filteredItems: filteredSelectedDrivers } =
    useFilteredListMultiField(driversForTransporter, ['first_name', 'last_name']);

  return {
    transporterQuery,
    filteredTransporters,
    selectedVehicleQuery,
    filteredSelectedVehicles,
    selectedDriverQuery,
    filteredSelectedDrivers,
  };
}
