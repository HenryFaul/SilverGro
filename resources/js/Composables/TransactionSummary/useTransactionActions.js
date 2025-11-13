import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Composable for transaction CRUD actions (clone, delete, approve, activate, etc.)
 * Centralizes action handlers to reduce Index.vue complexity
 */
export function useTransactionActions(
  tempForm,
  transportApprovalForm,
  filter,
  isUpdating,
  props
) {
  /**
   * Clone the current transaction
   */
  const cloneTransportTrans = () => {
    tempForm.post(route('transport_transaction.clone'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        isUpdating.value = false;
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
    });
  };

  /**
   * Delete a driver-vehicle association
   */
  const deleteDriverVehicle = (id) => {
    if (confirm('Sure you want to delete?')) {
      tempForm.delete(route('transport_driver_vehicle.destroy', id), {
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

  /**
   * Create an approval for the transaction
   */
  const createApproval = () => {
    transportApprovalForm.post(route('trans_approval.approve'), {
      preserveScroll: true,
      onSuccess: () => {
        filter();
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  /**
   * Activate the transaction approval
   */
  const createActivation = () => {
    transportApprovalForm.post(route('trans_approval.activate'), {
      preserveScroll: true,
      onSuccess: () => {
        filter();
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  /**
   * Create a final deal ticket PDF
   */
  const createFinalDealTicket = () => {
    tempForm.post(route('pdf_report.deal_ticket_final'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  /**
   * Download the deal ticket PDF
   */
  const downloadDealTicket = () => {
    if (props.deal_ticket?.report_path) {
      axios
        .get(
          route('pdf_report.deal_ticket_final.download', props.deal_ticket.report_path)
        )
        .then((res) => {
          // Handle success if needed
        })
        .catch((error) => {
          console.error('Error downloading deal ticket:', error);
        });
    }
  };

  return {
    cloneTransportTrans,
    deleteDriverVehicle,
    createApproval,
    createActivation,
    createFinalDealTicket,
    downloadDealTicket,
  };
}
