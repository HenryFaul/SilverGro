import { useForm, usePage } from '@inertiajs/vue3';

/**
 * Small form management for Transaction Summary
 * Manages driver, vehicle, and transaction link forms
 * Phase 6 of TransactionSummary refactor
 */
export function useTransactionForms() {
  // Driver creation form
  const driverForm = useForm({
    first_name: null,
    last_name: null,
    cell_no: null,
    comment: null,
  });

  const createDriver = (toggleShowDriver) => {
    driverForm.post(route('regular_driver.store'), {
      preserveScroll: true,
      onSuccess: () => {
        // Reset form fields
        driverForm.first_name = null;
        driverForm.last_name = null;
        driverForm.cell_no = null;
        driverForm.comment = null;
        toggleShowDriver();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  // Vehicle creation form
  const vehicleForm = useForm({
    vehicle_type_id: 1,
    comment: null,
    reg_no: null,
  });

  const createVehicle = (toggleShowVehicle) => {
    vehicleForm.post(route('regular_vehicle.store'), {
      preserveScroll: true,
      onSuccess: () => {
        toggleShowVehicle();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  // Transaction link form (for split loads)
  const transLinkForm = useForm({
    link_type_id: 5,
  });

  const deleteTransLink = (id) => {
    if (confirm('Sure you want to delete?')) {
      transLinkForm.delete(route('trans_link.split.delete', id), {
        preserveScroll: true,
        onSuccess: () => {
          swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
          console.log(e);
        },
      });
    }
  };

  return {
    // Driver form
    driverForm,
    createDriver,

    // Vehicle form
    vehicleForm,
    createVehicle,

    // Transaction link form
    transLinkForm,
    deleteTransLink,
  };
}
