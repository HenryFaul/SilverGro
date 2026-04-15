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

  // Filter vehicles by selected transporter - show only vehicles for this transporter or unallocated
  const vehiclesForTransporter = computed(() => {
    if (!combined_Form.transporter_id || !combined_Form.transporter_id.id) {
      return props.all_vehicles;
    }

    const transporterId = combined_Form.transporter_id.id;

    return props.all_vehicles.filter((vehicle) => {
      // Always include "N/A" or generic vehicles (Unallocated placeholder)
      if (vehicle.reg_no === 'N/A' || vehicle.reg_no === 'n/a') {
        return true;
      }

      // Only include vehicles associated with the selected transporter
      return vehicle.transporter && vehicle.transporter.id === transporterId;
    }).sort((a, b) => {
      // Always show Unallocated (N/A) first
      if (a.reg_no === 'N/A' || a.reg_no === 'n/a') return -1;
      if (b.reg_no === 'N/A' || b.reg_no === 'n/a') return 1;
      return 0;
    });
  });

  // Filter drivers by selected transporter - show only drivers for this transporter or unallocated
  const driversForTransporter = computed(() => {
    if (!combined_Form.transporter_id || !combined_Form.transporter_id.id) {
      return props.all_drivers;
    }

    const transporterId = combined_Form.transporter_id.id;

    return props.all_drivers.filter((driver) => {
      // Always include "Unallocated" driver (generic default)
      if (driver.first_name === 'Unallocated' || driver.last_name === 'Unallocated') {
        return true;
      }

      // Include drivers linked to the selected transporter via any past trade
      return driver.transporter_ids && driver.transporter_ids.includes(transporterId);
    });
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
