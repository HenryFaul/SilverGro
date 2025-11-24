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
    driverForm
      .transform((data) => ({
        ...data,
        transporter_id: usePage().props.selected_transaction?.transporter_id || null,
        transport_trans_id: usePage().props.selected_transaction?.id || null,
        transport_job_id: usePage().props.selected_transaction?.transport_job?.id || null,
        regular_vehicle_id:
          usePage().props.selected_transaction?.transport_job?.transport_driver_vehicle
            ?.regular_vehicle_id || null,
      }))
      .post(route('regular_driver.store'), {
        preserveScroll: true,
        onSuccess: () => {
          // Get flash messages from page props (Inertia automatically updates this)
          const flash = usePage().props.jetstream?.flash;

          // Show notification based on whether driver is new or existing
          if (flash && flash.banner) {
            const isExisting =
              flash.is_existing || flash.banner.includes('Similar driver found');
            const style = flash.bannerStyle || 'success';
            const message = flash.banner;

            if (window.swal) {
              window
                .swal({
                  title: isExisting ? 'Existing Driver Linked' : 'Driver Created',
                  text: message + '\n\nDriver has been linked to this transaction.',
                  icon:
                    style === 'info'
                      ? 'info'
                      : style === 'success'
                        ? 'success'
                        : 'warning',
                  button: 'OK',
                })
                .then(() => {
                  window.location.reload();
                });
            } else {
              // No swal available, reload immediately
              window.location.reload();
            }
          } else {
            // No flash message, reload immediately
            window.location.reload();
          }

          // Reset form fields
          driverForm.first_name = null;
          driverForm.last_name = null;
          driverForm.cell_no = null;
          driverForm.comment = null;
          toggleShowDriver();
        },
        onError: (e) => {
          if (window.swal) {
            window.swal({
              title: 'Error',
              text: 'Failed to create/link driver',
              icon: 'error',
              button: 'OK',
            });
          }
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
    vehicleForm
      .transform((data) => ({
        ...data,
        transporter_id: usePage().props.selected_transaction?.transporter_id || null,
        transport_trans_id: usePage().props.selected_transaction?.id || null,
        transport_job_id: usePage().props.selected_transaction?.transport_job?.id || null,
        regular_driver_id:
          usePage().props.selected_transaction?.transport_job?.transport_driver_vehicle
            ?.regular_driver_id || null,
      }))
      .post(route('regular_vehicle.store'), {
        preserveScroll: true,
        onSuccess: () => {
          // Get flash messages from page props (Inertia automatically updates this)
          const flash = usePage().props.jetstream?.flash;

          // Show notification based on whether vehicle is new or existing
          if (flash && flash.banner) {
            const isExisting =
              flash.is_existing || flash.banner.includes('Similar vehicle found');
            const style = flash.bannerStyle || 'success';
            const message = flash.banner;
            const vehicleId = flash.vehicle_id;

            if (window.swal) {
              window
                .swal({
                  title: isExisting ? 'Existing Vehicle Linked' : 'Vehicle Created',
                  text: message + '\n\nVehicle has been linked to this transaction.',
                  icon:
                    style === 'info'
                      ? 'info'
                      : style === 'success'
                        ? 'success'
                        : 'warning',
                  button: 'OK',
                })
                .then(() => {
                  window.location.reload();
                });
            } else {
              // No swal available, reload immediately
              window.location.reload();
            }
          } else {
            // No flash message, reload immediately
            window.location.reload();
          }

          // Reset form fields
          vehicleForm.reg_no = null;
          vehicleForm.comment = null;
          vehicleForm.vehicle_type_id = 1;
          toggleShowVehicle();
        },
        onError: () => {
          if (window.swal) {
            window.swal({
              title: 'Error',
              text: 'Failed to create/link vehicle',
              icon: 'error',
              button: 'OK',
            });
          }
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
          window.swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: () => {
          // Error handled by Inertia
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
