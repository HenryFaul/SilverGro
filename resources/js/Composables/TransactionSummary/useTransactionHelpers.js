import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Helper functions for Transaction Summary
 * Utility functions that don't fit into other composables
 * Phase 9 of TransactionSummary refactor
 */
export function useTransactionHelpers(filterForm, filter, tempForm) {
  // Vehicle slide over props
  const vehicleSlideProps = ref(null);

  /**
   * Fetch vehicle component props from API
   */
  const getComponentProps = () => {
    axios.get(route('props.vehicle_slide_over')).then((res) => {
      vehicleSlideProps.value = res.data['vehicle_types'];
    });
  };

  /**
   * Handle new trade creation - updates filter and refreshes
   * @param {number} _id - The ID of the newly created trade
   */
  const doCreatedTrade = (_id) => {
    filterForm.selected_trans_id = _id;
    filter();
  };

  /**
   * Delete assigned commission with confirmation
   * @param {number} id - The ID of the commission to delete
   */
  const deleteAssignedComm = (id) => {
    if (confirm('Sure you want to delete?')) {
      tempForm.delete(route('assigned_user_comm.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          window.swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
          console.log(e);
        },
      });
    }
  };

  return {
    // Vehicle props
    vehicleSlideProps,
    getComponentProps,

    // Trade helpers
    doCreatedTrade,

    // Commission helpers
    deleteAssignedComm,
  };
}
