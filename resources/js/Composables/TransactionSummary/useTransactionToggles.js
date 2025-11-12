import { ref } from 'vue';

/**
 * Toggle state management for Transaction Summary
 * Manages boolean flags for show/hide UI elements
 * Phase 5 of TransactionSummary refactor
 */
export function useTransactionToggles() {
  // Show/hide details section
  const showDetails = ref(true);

  const toggleDetails = () => {
    showDetails.value = !showDetails.value;
  };

  // Show/hide driver form
  const showDriver = ref(false);

  const toggleShowDriver = () => {
    showDriver.value = !showDriver.value;
  };

  // Show/hide vehicle form
  const showVehicle = ref(false);

  const toggleShowVehicle = () => {
    showVehicle.value = !showVehicle.value;
  };

  return {
    // Details toggle
    showDetails,
    toggleDetails,

    // Driver form toggle
    showDriver,
    toggleShowDriver,

    // Vehicle form toggle
    showVehicle,
    toggleShowVehicle,
  };
}
