import { ref } from 'vue';

/**
 * Modal state management for Transaction Summary
 * Centralizes all modal visibility flags and handlers
 * Phase 4 of TransactionSummary refactor
 */
export function useTransactionModals() {
  // Driver/Vehicle modals
  const currentDriverVehicle = ref(null);
  const viewDriverVehicleModal = ref(false);
  const viewDriverVehicleNewModal = ref(false);

  const viewDriverVehicle = (driver_vehicle) => {
    currentDriverVehicle.value = driver_vehicle;
    viewDriverVehicleModal.value = true;
  };

  const viewDriverNewVehicle = () => {
    viewDriverVehicleNewModal.value = true;
  };

  const closeDriverVehicleModal = () => {
    viewDriverVehicleModal.value = false;
    viewDriverVehicleNewModal.value = false;
  };

  // Contract link modals
  const viewContractLinkModal = ref(false);
  const viewContractLinkModalSc = ref(false);

  const viewContractLink = () => {
    viewContractLinkModal.value = true;
  };

  const viewContractLinkSc = () => {
    viewContractLinkModalSc.value = true;
  };

  const closeContractLink = () => {
    viewContractLinkModal.value = false;
  };

  const closeContractLinkSc = () => {
    viewContractLinkModalSc.value = false;
  };

  // Split link modal
  const viewSplitLinkModal = ref(false);

  const viewSplitLink = () => {
    viewSplitLinkModal.value = true;
  };

  const closeSplitLink = () => {
    viewSplitLinkModal.value = false;
  };

  // Assigned commission modals
  const currentAssignedComm = ref(null);
  const viewAssignedCommModal = ref(false);
  const viewAssignedCommNewModal = ref(false);

  const viewAssignedComm = (assigned_user_comm) => {
    currentAssignedComm.value = assigned_user_comm;
    viewAssignedCommModal.value = true;
  };

  const viewAssignedNewComm = () => {
    currentAssignedComm.value = null;
    viewAssignedCommNewModal.value = true;
  };

  const closeAssignedComm = () => {
    viewAssignedCommModal.value = false;
  };

  const closeAssignedNewComm = () => {
    viewAssignedCommNewModal.value = false;
  };

  // Trade slide over
  const viewTradeSlideOver = ref(false);

  const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
  };

  const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
  };

  return {
    // Driver/Vehicle modal state
    currentDriverVehicle,
    viewDriverVehicleModal,
    viewDriverVehicleNewModal,
    viewDriverVehicle,
    viewDriverNewVehicle,
    closeDriverVehicleModal,

    // Contract link modal state
    viewContractLinkModal,
    viewContractLinkModalSc,
    viewContractLink,
    viewContractLinkSc,
    closeContractLink,
    closeContractLinkSc,

    // Split link modal state
    viewSplitLinkModal,
    viewSplitLink,
    closeSplitLink,

    // Assigned commission modal state
    currentAssignedComm,
    viewAssignedCommModal,
    viewAssignedCommNewModal,
    viewAssignedComm,
    viewAssignedNewComm,
    closeAssignedComm,
    closeAssignedNewComm,

    // Trade slide over state
    viewTradeSlideOver,
    showTradeSlideOver,
    closeTradeSlideOver,
  };
}
