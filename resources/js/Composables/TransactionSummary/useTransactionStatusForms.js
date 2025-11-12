import { useForm, usePage } from '@inertiajs/vue3';

/**
 * Status and approval forms management for Transaction Summary
 * Manages status, approval, sales order, purchase order, and transport order forms
 * Phase 8 of TransactionSummary refactor
 */
export function useTransactionStatusForms(props, isUpdating) {
  // Status form (transport status management)
  const statusForm = useForm({
    transport_trans_id: props.selected_transaction.id,
    status_entity_id: 1,
    status_type_id: 1,
  });

  const createStatus = () => {
    statusForm.post(route('transport_status.store'), {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  const deleteStatus = (id) => {
    statusForm.delete(route('transport_status.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  // Transport approval form
  const transportApprovalForm = useForm({
    transport_trans_id: props.selected_transaction.id,
    transport_job_id: props.selected_transaction.transport_job.id,
    deal_ticket_id: props.selected_transaction.deal_ticket.id,
  });

  // Sales Order form
  const salesOrderForm = useForm({
    transport_trans_id: props.sales_order.transport_trans_id,
    confirmed_by_type_id: props.sales_order.confirmed_by_type_id,
    is_active: props.sales_order.is_active,
    is_so_conf_sent: props.sales_order.is_so_conf_sent,
    is_so_conf_received: props.sales_order.is_so_conf_received,
  });

  const activateSalesOrder = () => {
    isUpdating.value = true;
    salesOrderForm.post(route('sales_order.activate'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const sendSalesOrder = () => {
    isUpdating.value = true;
    salesOrderForm.post(route('sales_order.send'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const receiveSalesOrder = () => {
    isUpdating.value = true;
    salesOrderForm.post(route('sales_order.received'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
        isUpdating.value = false;
      },
      onError: (error) => {
        isUpdating.value = false;
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
    });
  };

  // Purchase Order form
  const purchaseOrderForm = useForm({
    transport_trans_id: props.purchase_order.transport_trans_id,
    confirmed_by_type_id: props.purchase_order.confirmed_by_type_id,
    is_active: props.purchase_order.is_active,
    is_po_sent: props.purchase_order.is_po_sent,
    is_po_received: props.purchase_order.is_po_received,
  });

  const activatePurchaseOrder = () => {
    isUpdating.value = true;
    purchaseOrderForm.post(route('purchase_order.activate'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const sendPurchaseOrder = () => {
    isUpdating.value = true;
    purchaseOrderForm.post(route('purchase_order.send'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const receivePurchaseOrder = () => {
    isUpdating.value = true;
    purchaseOrderForm.post(route('purchase_order.received'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
        isUpdating.value = false;
      },
      onError: (error) => {
        isUpdating.value = false;
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
    });
  };

  // Transport Order form
  const transportOrderForm = useForm({
    transport_trans_id: props.transport_order.transport_trans_id,
    confirmed_by_type_id: props.transport_order.confirmed_by_type_id,
    is_active: props.transport_order.is_active,
    is_to_sent: props.transport_order.is_to_sent,
    is_to_received: props.transport_order.is_to_received,
  });

  const activateTransportOrder = () => {
    isUpdating.value = true;
    transportOrderForm.post(route('transport_order.activate'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const sendTransportOrder = () => {
    isUpdating.value = true;
    transportOrderForm.post(route('transport_order.send'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (error) => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onFinish: () => {
        isUpdating.value = false;
      },
    });
  };

  const receiveTransportOrder = () => {
    isUpdating.value = true;
    transportOrderForm.post(route('transport_order.received'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
        isUpdating.value = false;
      },
      onError: (error) => {
        isUpdating.value = false;
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
    });
  };

  return {
    // Status form
    statusForm,
    createStatus,
    deleteStatus,

    // Transport approval form
    transportApprovalForm,

    // Sales order form and handlers
    salesOrderForm,
    activateSalesOrder,
    sendSalesOrder,
    receiveSalesOrder,

    // Purchase order form and handlers
    purchaseOrderForm,
    activatePurchaseOrder,
    sendPurchaseOrder,
    receivePurchaseOrder,

    // Transport order form and handlers
    transportOrderForm,
    activateTransportOrder,
    sendTransportOrder,
    receiveTransportOrder,
  };
}
