/**
 * useTransactionCombinedForm
 *
 * Handles the massive combined_Form initialization for TransactionSummary
 * Extracts ~400 lines of form setup logic from Index.vue
 */

import { useForm } from '@inertiajs/vue3';

export function useTransactionCombinedForm(props) {
  // Helper to safely find array element
  const findById = (array, id) => array?.find((element) => element.id === id) || null;

  // Helper to find addressable by customer
  const findDeliveryAddress = (customerId, addressId) => {
    if (!customerId || !addressId) return null;
    const customer = findById(props.all_customers, customerId);
    return customer?.addressable?.find((element) => element.id === addressId) || null;
  };

  // Initialize the combined form with all transaction data
  const combined_Form = useForm({
    // TransportTrans fields
    product_id: findById(props.all_products, props.selected_transaction.product_id),
    supplier_id: findById(props.all_suppliers, props.selected_transaction.supplier_id),
    customer_id: findById(props.all_customers, props.selected_transaction.customer_id),
    customer_id_2: findById(
      props.all_customers,
      props.selected_transaction.customer_id_2
    ),
    customer_id_3: findById(
      props.all_customers,
      props.selected_transaction.customer_id_3
    ),
    customer_id_4: findById(
      props.all_customers,
      props.selected_transaction.customer_id_4
    ),
    customer_id_5: findById(
      props.all_customers,
      props.selected_transaction.customer_id_5
    ),
    transporter_id: findById(
      props.all_transporters,
      props.selected_transaction.transporter_id
    ),
    contract_type_id: findById(
      props.contract_types,
      props.selected_transaction.contract_type_id
    ),
    contract_no: props.selected_transaction.contract_no,
    old_id: props.selected_transaction.old_id,
    include_in_calculations: props.selected_transaction.include_in_calculations,
    transport_date_earliest: props.selected_transaction.transport_date_earliest,
    transport_date_latest: props.selected_transaction.transport_date_latest,

    // Notes fields
    suppliers_notes: props.selected_transaction.suppliers_notes,
    delivery_notes: props.selected_transaction.delivery_notes,
    product_notes: props.selected_transaction.product_notes,
    customer_notes: props.selected_transaction.customer_notes,
    traders_notes: props.selected_transaction.traders_notes,
    transport_notes: props.selected_transaction.transport_notes,
    pricing_notes: props.selected_transaction.pricing_notes,
    process_notes: props.selected_transaction.process_notes,
    document_notes: props.selected_transaction.document_notes,
    transaction_notes: props.selected_transaction.transaction_notes,
    traders_notes_supplier: props.selected_transaction.traders_notes_supplier,
    traders_notes_customer: props.selected_transaction.traders_notes_customer,
    traders_notes_transport: props.selected_transaction.traders_notes_transport,

    // Status flags
    is_transaction_done: props.selected_transaction.is_transaction_done,
    is_split_load: props.selected_transaction.is_split_load,
    is_split_load_primary: props.selected_transaction.is_split_load_primary,
    is_split_load_member: props.selected_transaction.is_split_load_member,

    // TransportLoad fields
    confirmed_by_id: findById(
      props.all_staff,
      props.selected_transaction.transport_load.confirmed_by_id
    ),
    confirmed_by_type_id: findById(
      props.confirmation_types,
      props.selected_transaction.transport_load.confirmed_by_type_id
    ),
    packaging_incoming_id: findById(
      props.all_packaging,
      props.selected_transaction.transport_load.packaging_incoming_id
    ),
    packaging_outgoing_id: findById(
      props.all_packaging,
      props.selected_transaction.transport_load.packaging_outgoing_id
    ),
    product_source_id: findById(
      props.all_product_sources,
      props.selected_transaction.transport_load.product_source_id
    ),
    billing_units_incoming_id: findById(
      props.all_billing_units,
      props.selected_transaction.transport_load.billing_units_incoming_id
    ),
    billing_units_outgoing_id: findById(
      props.all_billing_units,
      props.selected_transaction.transport_load.billing_units_outgoing_id
    ),

    // Addresses
    collection_address_id:
      props.selected_transaction.transport_load.collection_address_id,
    delivery_address_id: findDeliveryAddress(
      props.selected_transaction.customer_id,
      props.selected_transaction.transport_load.delivery_address_id
    ),
    delivery_address_id_2: findDeliveryAddress(
      props.selected_transaction.customer_id_2,
      props.selected_transaction.transport_load.delivery_address_id_2
    ),
    delivery_address_id_3: findDeliveryAddress(
      props.selected_transaction.customer_id_3,
      props.selected_transaction.transport_load.delivery_address_id_3
    ),
    delivery_address_id_4: findDeliveryAddress(
      props.selected_transaction.customer_id_4,
      props.selected_transaction.transport_load.delivery_address_id_4
    ),
    delivery_address_id_5: findDeliveryAddress(
      props.selected_transaction.customer_id_5,
      props.selected_transaction.transport_load.delivery_address_id_5
    ),

    // Load details
    product_grade_perc: props.selected_transaction.transport_load.product_grade_perc,
    no_units_incoming: props.selected_transaction.transport_load.no_units_incoming,
    no_units_outgoing: props.selected_transaction.transport_load.no_units_outgoing,
    no_units_outgoing_2: props.selected_transaction.transport_load.no_units_outgoing_2,
    no_units_outgoing_3: props.selected_transaction.transport_load.no_units_outgoing_3,
    no_units_outgoing_4: props.selected_transaction.transport_load.no_units_outgoing_4,
    no_units_outgoing_5: props.selected_transaction.transport_load.no_units_outgoing_5,
    is_weighbridge_certificate_received:
      props.selected_transaction.transport_load.is_weighbridge_certificate_received,
    delivery_note: props.selected_transaction.transport_load.delivery_note,
    calculated_route_distance:
      props.selected_transaction.transport_load.calculated_route_distance,

    // TransportJob fields
    customer_order_number: props.selected_transaction.transport_job.customer_order_number,
    supplier_loading_number:
      props.selected_transaction.transport_job.supplier_loading_number,
    customer_order_number_2:
      props.selected_transaction.transport_job.customer_order_number_2,
    supplier_loading_number_2:
      props.selected_transaction.transport_job.supplier_loading_number_2,
    customer_order_number_3:
      props.selected_transaction.transport_job.customer_order_number_3,
    supplier_loading_number_3:
      props.selected_transaction.transport_job.supplier_loading_number_3,
    customer_order_number_4:
      props.selected_transaction.transport_job.customer_order_number_4,
    supplier_loading_number_4:
      props.selected_transaction.transport_job.supplier_loading_number_4,
    customer_order_number_5:
      props.selected_transaction.transport_job.customer_order_number_5,
    supplier_loading_number_5:
      props.selected_transaction.transport_job.supplier_loading_number_5,

    // Job settings
    is_multi_loads: props.selected_transaction.transport_job.is_multi_loads,
    is_approved: props.selected_transaction.transport_job.is_approved,
    is_transport_costs_inc_price:
      props.selected_transaction.transport_job.is_transport_costs_inc_price,
    is_product_zero_rated: props.selected_transaction.transport_job.is_product_zero_rated,

    // Loading/offloading hours
    offloading_hours_from_id:
      props.selected_transaction.transport_job.offloading_hours_from_id,
    offloading_hours_to_id:
      props.selected_transaction.transport_job.offloading_hours_to_id,
    loading_hours_from_id: props.selected_transaction.transport_job.loading_hours_from_id,
    loading_hours_to_id: props.selected_transaction.transport_job.loading_hours_to_id,

    // Insurance
    load_insurance_per_ton:
      props.selected_transaction.transport_job.load_insurance_per_ton,
    total_load_insurance: props.selected_transaction.transport_job.total_load_insurance,
    number_loads: props.selected_transaction.transport_job.number_loads,

    // Instructions and contacts
    loading_instructions: props.selected_transaction.transport_job.loading_instructions,
    offloading_instructions:
      props.selected_transaction.transport_job.offloading_instructions,
    loading_contact: props.selected_transaction.transport_job.loading_contact,
    loading_contact_no: props.selected_transaction.transport_job.loading_contact_no,
    offloading_contact: props.selected_transaction.transport_job.offloading_contact,
    offloading_contact_no: props.selected_transaction.transport_job.offloading_contact_no,

    // Transport Finance fields
    transport_rate_basis_id:
      props.selected_transaction.transport_finance.transport_rate_basis_id,
    cost_price_per_unit: props.selected_transaction.transport_finance.cost_price_per_unit,
    selling_price_per_unit:
      props.selected_transaction.transport_finance.selling_price_per_unit,
    transport_rate: props.selected_transaction.transport_finance.transport_rate,
    transport_cost_2: props.selected_transaction.transport_finance.transport_cost_2,
    transport_cost_3: props.selected_transaction.transport_finance.transport_cost_3,
    transport_cost_4: props.selected_transaction.transport_finance.transport_cost_4,
    transport_cost_5: props.selected_transaction.transport_finance.transport_cost_5,
    selling_price_2: props.selected_transaction.transport_finance.selling_price_2,
    selling_price_3: props.selected_transaction.transport_finance.selling_price_3,
    selling_price_4: props.selected_transaction.transport_finance.selling_price_4,
    selling_price_5: props.selected_transaction.transport_finance.selling_price_5,

    // Additional costs
    additional_cost_1: props.selected_transaction.transport_finance.additional_cost_1,
    additional_cost_2: props.selected_transaction.transport_finance.additional_cost_2,
    additional_cost_3: props.selected_transaction.transport_finance.additional_cost_3,
    additional_cost_desc_1:
      props.selected_transaction.transport_finance.additional_cost_desc_1,
    additional_cost_desc_2:
      props.selected_transaction.transport_finance.additional_cost_desc_2,
    additional_cost_desc_3:
      props.selected_transaction.transport_finance.additional_cost_desc_3,
    adjusted_gp: props.selected_transaction.transport_finance.adjusted_gp,
    adjusted_gp_notes: props.selected_transaction.transport_finance.adjusted_gp_notes,

    // Transport Invoice fields
    transport_trans_id: props.selected_transaction.id,
    is_active: props.selected_transaction.transport_invoice.is_active,
    is_printed: props.selected_transaction.transport_invoice.is_printed,
    invoice_id:
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_id,
    is_invoiced:
      props.selected_transaction.transport_invoice.transport_invoice_details.is_invoiced,
    is_invoice_paid:
      props.selected_transaction.transport_invoice.transport_invoice_details
        .is_invoice_paid,
    invoice_no:
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_no,
    invoice_paid_date:
      props.selected_transaction.transport_invoice.transport_invoice_details
        .invoice_paid_date,
    invoice_pay_by_date:
      props.selected_transaction.transport_invoice.transport_invoice_details
        .invoice_pay_by_date,
    invoice_date:
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_date,
    invoice_amount:
      props.selected_transaction.transport_invoice.transport_invoice_details
        .invoice_amount,
    invoice_amount_paid:
      props.selected_transaction.transport_invoice.transport_invoice_details
        .invoice_amount_paid,
    status_id:
      props.selected_transaction.transport_invoice.transport_invoice_details.status_id,
    notes: props.selected_transaction.transport_invoice.transport_invoice_details.notes,

    // Driver Vehicle fields
    regular_driver_id: findById(
      props.all_drivers,
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.regular_driver_id
    ),
    regular_vehicle_id: findById(
      props.all_vehicles,
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.regular_vehicle_id
    ),
    weighbridge_upload_weight:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.weighbridge_upload_weight,
    weighbridge_offload_weight:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.weighbridge_offload_weight,
    is_weighbridge_variance:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.is_weighbridge_variance,
    is_cancelled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_cancelled,
    date_cancelled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.date_cancelled,
    is_loaded:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_loaded,
    date_loaded:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_loaded?.slice(0, 10),
    is_onroad:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_onroad,
    date_onroad:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_onroad?.slice(0, 10),
    is_delivered:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_delivered,
    date_delivered:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.date_delivered?.slice(0, 10),
    is_transport_scheduled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.is_transport_scheduled,
    date_scheduled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.date_scheduled,
    is_paid:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_paid,
    date_paid:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_paid,
    is_payment_overdue:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.is_payment_overdue,
    driver_vehicle_loading_number:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.driver_vehicle_loading_number,
    trailer_reg_1:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.trailer_reg_1,
    trailer_reg_2:
      props.selected_transaction.transport_job.transport_driver_vehicle[0]?.trailer_reg_2,
  });

  /**
   * Track the current transaction ID and supplier ID to detect when we need to reset collection_address
   */
  let currentTransactionId = props.selected_transaction.id;
  let currentSupplierId = props.selected_transaction.supplier_id;

  /**
   * Update form with fresh data from selected transaction
   * Called when transaction changes
   */
  const updateSelectValues = (statusForms = {}) => {
    const {
      transportApprovalForm,
      pcScApprovalForm,
      statusForm,
      salesOrderForm,
      purchaseOrderForm,
      transportOrderForm,
    } = statusForms;

    // Detect if we're switching to a different transaction or supplier
    const isNewTransaction = currentTransactionId !== props.selected_transaction.id;
    const hasSupplierChanged =
      currentSupplierId !== props.selected_transaction.supplier_id;

    currentTransactionId = props.selected_transaction.id;
    currentSupplierId = props.selected_transaction.supplier_id;

    // Update other forms if provided
    if (transportApprovalForm) {
      transportApprovalForm.transport_trans_id = props.selected_transaction.id;
      transportApprovalForm.transport_job_id =
        props.selected_transaction.transport_job.id;
      transportApprovalForm.deal_ticket_id = props.selected_transaction.deal_ticket.id;
    }

    if (pcScApprovalForm) {
      pcScApprovalForm.transport_trans_id = props.selected_transaction.id;
    }

    if (statusForm) {
      statusForm.transport_trans_id = props.selected_transaction.id;
    }

    if (salesOrderForm) {
      salesOrderForm.transport_trans_id = props.sales_order.transport_trans_id;
      salesOrderForm.confirmed_by_type_id = props.sales_order.confirmed_by_type_id;
      salesOrderForm.is_active = props.sales_order.is_active;
      salesOrderForm.is_so_conf_sent = props.sales_order.is_so_conf_sent;
      salesOrderForm.is_so_conf_received = props.sales_order.is_so_conf_received;
    }

    if (purchaseOrderForm) {
      purchaseOrderForm.transport_trans_id = props.purchase_order.transport_trans_id;
      purchaseOrderForm.confirmed_by_type_id = props.purchase_order.confirmed_by_type_id;
      purchaseOrderForm.is_active = props.purchase_order.is_active;
      purchaseOrderForm.is_po_sent = props.purchase_order.is_po_sent;
      purchaseOrderForm.is_po_received = props.purchase_order.is_po_received;
    }

    if (transportOrderForm) {
      transportOrderForm.transport_trans_id = props.transport_order.transport_trans_id;
      transportOrderForm.confirmed_by_type_id =
        props.transport_order.confirmed_by_type_id;
      transportOrderForm.is_active = props.transport_order.is_active;
      transportOrderForm.is_to_sent = props.transport_order.is_to_sent;
      transportOrderForm.is_to_received = props.transport_order.is_to_received;
    }

    // Update combined_Form with all transaction data
    combined_Form.contract_type_id = findById(
      props.contract_types,
      props.selected_transaction.contract_type_id
    );
    combined_Form.product_id = findById(
      props.all_products,
      props.selected_transaction.product_id
    );
    combined_Form.supplier_id = findById(
      props.all_suppliers,
      props.selected_transaction.supplier_id
    );
    combined_Form.customer_id = findById(
      props.all_customers,
      props.selected_transaction.customer_id
    );
    combined_Form.customer_id_2 = findById(
      props.all_customers,
      props.selected_transaction.customer_id_2
    );
    combined_Form.customer_id_3 = findById(
      props.all_customers,
      props.selected_transaction.customer_id_3
    );
    combined_Form.customer_id_4 = findById(
      props.all_customers,
      props.selected_transaction.customer_id_4
    );
    combined_Form.customer_id_5 = findById(
      props.all_customers,
      props.selected_transaction.customer_id_5
    );
    combined_Form.transporter_id = findById(
      props.all_transporters,
      props.selected_transaction.transporter_id
    );

    // Update all other fields (condensed for brevity)
    Object.assign(combined_Form, {
      contract_no: props.selected_transaction.contract_no,
      old_id: props.selected_transaction.old_id,
      include_in_calculations: props.selected_transaction.include_in_calculations,
      transport_date_earliest: props.selected_transaction.transport_date_earliest,
      transport_date_latest: props.selected_transaction.transport_date_latest,
      suppliers_notes: props.selected_transaction.suppliers_notes,
      delivery_notes: props.selected_transaction.delivery_notes,
      product_notes: props.selected_transaction.product_notes,
      customer_notes: props.selected_transaction.customer_notes,
      traders_notes: props.selected_transaction.traders_notes,
      transport_notes: props.selected_transaction.transport_notes,
      pricing_notes: props.selected_transaction.pricing_notes,
      process_notes: props.selected_transaction.process_notes,
      document_notes: props.selected_transaction.document_notes,
      transaction_notes: props.selected_transaction.transaction_notes,
      traders_notes_supplier: props.selected_transaction.traders_notes_supplier,
      traders_notes_customer: props.selected_transaction.traders_notes_customer,
      traders_notes_transport: props.selected_transaction.traders_notes_transport,
      is_transaction_done: props.selected_transaction.is_transaction_done,
      is_split_load: props.selected_transaction.is_split_load,
      is_split_load_primary: props.selected_transaction.is_split_load_primary,
      is_split_load_member: props.selected_transaction.is_split_load_member,
    });

    // Update TransportLoad fields
    combined_Form.confirmed_by_id = findById(
      props.all_staff,
      props.selected_transaction.transport_load.confirmed_by_id
    );
    combined_Form.confirmed_by_type_id = findById(
      props.confirmation_types,
      props.selected_transaction.transport_load.confirmed_by_type_id
    );
    combined_Form.packaging_incoming_id = findById(
      props.all_packaging,
      props.selected_transaction.transport_load.packaging_incoming_id
    );
    combined_Form.packaging_outgoing_id = findById(
      props.all_packaging,
      props.selected_transaction.transport_load.packaging_outgoing_id
    );
    combined_Form.product_source_id = findById(
      props.all_product_sources,
      props.selected_transaction.transport_load.product_source_id
    );
    combined_Form.billing_units_incoming_id = findById(
      props.all_billing_units,
      props.selected_transaction.transport_load.billing_units_incoming_id
    );
    combined_Form.billing_units_outgoing_id = findById(
      props.all_billing_units,
      props.selected_transaction.transport_load.billing_units_outgoing_id
    );

    // Update addresses
    // Only reset collection_address_id when switching to a different transaction OR when supplier changes
    // When editing the same transaction with the same supplier, keep the user's selection
    if (isNewTransaction || hasSupplierChanged) {
      combined_Form.collection_address_id = props.all_suppliers
        .find((element) => element.id === props.selected_transaction.supplier_id)
        ?.addressable?.find(
          (element) =>
            element.id === props.selected_transaction.transport_load.collection_address_id
        );
    }

    combined_Form.delivery_address_id = findDeliveryAddress(
      props.selected_transaction.customer_id,
      props.selected_transaction.transport_load.delivery_address_id
    );
    combined_Form.delivery_address_id_2 = findDeliveryAddress(
      props.selected_transaction.customer_id_2,
      props.selected_transaction.transport_load.delivery_address_id_2
    );
    combined_Form.delivery_address_id_3 = findDeliveryAddress(
      props.selected_transaction.customer_id_3,
      props.selected_transaction.transport_load.delivery_address_id_3
    );
    combined_Form.delivery_address_id_4 = findDeliveryAddress(
      props.selected_transaction.customer_id_4,
      props.selected_transaction.transport_load.delivery_address_id_4
    );
    combined_Form.delivery_address_id_5 = findDeliveryAddress(
      props.selected_transaction.customer_id_5,
      props.selected_transaction.transport_load.delivery_address_id_5
    );

    // Update all remaining transport_load fields
    Object.assign(combined_Form, {
      product_grade_perc: props.selected_transaction.transport_load.product_grade_perc,
      no_units_incoming: props.selected_transaction.transport_load.no_units_incoming,
      no_units_outgoing: props.selected_transaction.transport_load.no_units_outgoing,
      no_units_outgoing_2: props.selected_transaction.transport_load.no_units_outgoing_2,
      no_units_outgoing_3: props.selected_transaction.transport_load.no_units_outgoing_3,
      no_units_outgoing_4: props.selected_transaction.transport_load.no_units_outgoing_4,
      no_units_outgoing_5: props.selected_transaction.transport_load.no_units_outgoing_5,
      is_weighbridge_certificate_received:
        props.selected_transaction.transport_load.is_weighbridge_certificate_received,
      delivery_note: props.selected_transaction.transport_load.delivery_note,
      calculated_route_distance:
        props.selected_transaction.transport_load.calculated_route_distance,
    });

    // Update all transport_job fields
    Object.assign(combined_Form, {
      customer_order_number:
        props.selected_transaction.transport_job.customer_order_number,
      supplier_loading_number:
        props.selected_transaction.transport_job.supplier_loading_number,
      customer_order_number_2:
        props.selected_transaction.transport_job.customer_order_number_2,
      supplier_loading_number_2:
        props.selected_transaction.transport_job.supplier_loading_number_2,
      customer_order_number_3:
        props.selected_transaction.transport_job.customer_order_number_3,
      supplier_loading_number_3:
        props.selected_transaction.transport_job.supplier_loading_number_3,
      customer_order_number_4:
        props.selected_transaction.transport_job.customer_order_number_4,
      supplier_loading_number_4:
        props.selected_transaction.transport_job.supplier_loading_number_4,
      customer_order_number_5:
        props.selected_transaction.transport_job.customer_order_number_5,
      supplier_loading_number_5:
        props.selected_transaction.transport_job.supplier_loading_number_5,
      is_multi_loads: props.selected_transaction.transport_job.is_multi_loads,
      is_approved: props.selected_transaction.transport_job.is_approved,
      is_transport_costs_inc_price:
        props.selected_transaction.transport_job.is_transport_costs_inc_price,
      is_product_zero_rated:
        props.selected_transaction.transport_job.is_product_zero_rated,
      offloading_hours_from_id:
        props.selected_transaction.transport_job.offloading_hours_from_id,
      offloading_hours_to_id:
        props.selected_transaction.transport_job.offloading_hours_to_id,
      loading_hours_from_id:
        props.selected_transaction.transport_job.loading_hours_from_id,
      loading_hours_to_id: props.selected_transaction.transport_job.loading_hours_to_id,
      load_insurance_per_ton:
        props.selected_transaction.transport_job.load_insurance_per_ton,
      total_load_insurance: props.selected_transaction.transport_job.total_load_insurance,
      number_loads: props.selected_transaction.transport_job.number_loads,
      loading_instructions: props.selected_transaction.transport_job.loading_instructions,
      offloading_instructions:
        props.selected_transaction.transport_job.offloading_instructions,
      loading_contact: props.selected_transaction.transport_job.loading_contact,
      loading_contact_no: props.selected_transaction.transport_job.loading_contact_no,
      offloading_contact: props.selected_transaction.transport_job.offloading_contact,
      offloading_contact_no:
        props.selected_transaction.transport_job.offloading_contact_no,
    });

    // Update all transport_finance fields
    Object.assign(combined_Form, {
      transport_rate_basis_id:
        props.selected_transaction.transport_finance.transport_rate_basis_id,
      cost_price_per_unit:
        props.selected_transaction.transport_finance.cost_price_per_unit,
      selling_price_per_unit:
        props.selected_transaction.transport_finance.selling_price_per_unit,
      transport_rate: props.selected_transaction.transport_finance.transport_rate,
      transport_cost_2: props.selected_transaction.transport_finance.transport_cost_2,
      transport_cost_3: props.selected_transaction.transport_finance.transport_cost_3,
      transport_cost_4: props.selected_transaction.transport_finance.transport_cost_4,
      transport_cost_5: props.selected_transaction.transport_finance.transport_cost_5,
      selling_price_2: props.selected_transaction.transport_finance.selling_price_2,
      selling_price_3: props.selected_transaction.transport_finance.selling_price_3,
      selling_price_4: props.selected_transaction.transport_finance.selling_price_4,
      selling_price_5: props.selected_transaction.transport_finance.selling_price_5,
      additional_cost_1: props.selected_transaction.transport_finance.additional_cost_1,
      additional_cost_2: props.selected_transaction.transport_finance.additional_cost_2,
      additional_cost_3: props.selected_transaction.transport_finance.additional_cost_3,
      additional_cost_desc_1:
        props.selected_transaction.transport_finance.additional_cost_desc_1,
      additional_cost_desc_2:
        props.selected_transaction.transport_finance.additional_cost_desc_2,
      additional_cost_desc_3:
        props.selected_transaction.transport_finance.additional_cost_desc_3,
      adjusted_gp: props.selected_transaction.transport_finance.adjusted_gp,
      adjusted_gp_notes: props.selected_transaction.transport_finance.adjusted_gp_notes,
    });

    // Update all transport_invoice fields
    Object.assign(combined_Form, {
      transport_trans_id: props.selected_transaction.id,
      is_active: props.selected_transaction.transport_invoice.is_active,
      is_printed: props.selected_transaction.transport_invoice.is_printed,
      invoice_id:
        props.selected_transaction.transport_invoice.transport_invoice_details.invoice_id,
      is_invoiced:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .is_invoiced,
      is_invoice_paid:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .is_invoice_paid,
      invoice_no:
        props.selected_transaction.transport_invoice.transport_invoice_details.invoice_no,
      invoice_paid_date:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .invoice_paid_date,
      invoice_pay_by_date:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .invoice_pay_by_date,
      invoice_date:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .invoice_date,
      invoice_amount:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .invoice_amount,
      invoice_amount_paid:
        props.selected_transaction.transport_invoice.transport_invoice_details
          .invoice_amount_paid,
      status_id:
        props.selected_transaction.transport_invoice.transport_invoice_details.status_id,
      notes: props.selected_transaction.transport_invoice.transport_invoice_details.notes,
    });

    // Update all driver_vehicle fields
    combined_Form.regular_driver_id = findById(
      props.all_drivers,
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.regular_driver_id
    );
    combined_Form.regular_vehicle_id = findById(
      props.all_vehicles,
      props.selected_transaction.transport_job.transport_driver_vehicle[0]
        ?.regular_vehicle_id
    );

    Object.assign(combined_Form, {
      weighbridge_upload_weight:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.weighbridge_upload_weight,
      weighbridge_offload_weight:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.weighbridge_offload_weight,
      is_weighbridge_variance:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.is_weighbridge_variance,
      is_cancelled:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.is_cancelled,
      date_cancelled:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.date_cancelled,
      is_loaded:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_loaded,
      date_loaded:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_loaded?.slice(0, 10),
      is_onroad:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_onroad,
      date_onroad:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_onroad?.slice(0, 10),
      is_delivered:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.is_delivered,
      date_delivered:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.date_delivered?.slice(0, 10),
      is_transport_scheduled:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.is_transport_scheduled,
      date_scheduled:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.date_scheduled,
      is_paid:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.is_paid,
      date_paid:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]?.date_paid,
      is_payment_overdue:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.is_payment_overdue,
      driver_vehicle_loading_number:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.driver_vehicle_loading_number,
      trailer_reg_1:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.trailer_reg_1,
      trailer_reg_2:
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          ?.trailer_reg_2,
    });

    combined_Form.clearErrors();
  };

  return {
    combined_Form,
    updateSelectValuesInternal: updateSelectValues,
  };
}
