<script setup>
  import { computed, onBeforeMount, ref, watch } from 'vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import InputError from '@/Components/InputError.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    Switch,
    SwitchGroup,
  } from '@headlessui/vue';
  import {
    CheckIcon,
    ChevronUpDownIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    XMarkIcon,
  } from '@heroicons/vue/20/solid';
  import AreaInput from '@/Components/AreaInput.vue';
  import ContractLinkModal from '@/Components/UI/ContractLinkModal.vue';
  import ContractLinkModalSc from '@/Components/UI/ContractLinkModal.vue';
  import SplitLinkModal from '@/Components/UI/SplitLinkModal.vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  import { useTransactionFilters } from '@/Composables/useTransactionFilters.js';
  import { useTransactionTabs } from '@/Composables/useTransactionTabs.js';
  import { useSupplierTab } from '@/Composables/TransactionSummary/useSupplierTab.js';
  import { useCustomerTab } from '@/Composables/TransactionSummary/useCustomerTab.js';
  import { useProductTab } from '@/Composables/TransactionSummary/useProductTab.js';
  import { useTransportTab } from '@/Composables/TransactionSummary/useTransportTab.js';
  import { useTransactionModals } from '@/Composables/TransactionSummary/useTransactionModals.js';
  import { useTransactionToggles } from '@/Composables/TransactionSummary/useTransactionToggles.js';
  import { useTransactionForms } from '@/Composables/TransactionSummary/useTransactionForms.js';
  import { useTransactionComputed } from '@/Composables/TransactionSummary/useTransactionComputed.js';
  import { useTransactionStatusForms } from '@/Composables/TransactionSummary/useTransactionStatusForms.js';
  import { useTransactionHelpers } from '@/Composables/TransactionSummary/useTransactionHelpers.js';
  import {
    formatNiceNumber,
    formatNiceVariance,
  } from '@/Composables/useNumberFormatters.js';
  import { formatShortDate } from '@/Composables/useDateFormatters.js';
  import Swal from 'sweetalert2';
  import TransactionFilters from '@/Components/TransactionSummary/TransactionFilters.vue';
  import TransactionTable from '@/Components/TransactionSummary/TransactionTable.vue';
  import TradeSlideOver from '@/Components/UI/TradeSlideOver.vue';
  import TransactionTabNav from '@/Components/TransactionSummary/TransactionTabNav.vue';
  import TransactionSupplierAccountCard from '@/Components/TransactionSummary/TransactionSupplierAccountCard.vue';
  import TransactionSupplierCard from '@/Components/TransactionSummary/TransactionSupplierCard.vue';
  import TransactionCustomerSelect from '@/Components/TransactionSummary/TransactionCustomerSelect.vue';
  import TransactionCustomerTab from '@/Components/TransactionSummary/TransactionCustomerTab.vue';
  import TransactionTransporterSelect from '@/Components/TransactionSummary/TransactionTransporterSelect.vue';
  import TransactionProductCard from '@/Components/TransactionSummary/TransactionProductCard.vue';
  import TransactionSupplierNotesCard from '@/Components/TransactionSummary/TransactionSupplierNotesCard.vue';
  import TransactionProductIncomingCard from '@/Components/TransactionSummary/TransactionProductIncomingCard.vue';
  import TransactionProductOutgoingCard from '@/Components/TransactionSummary/TransactionProductOutgoingCard.vue';
  import TransactionProductCalculationsCard from '@/Components/TransactionSummary/TransactionProductCalculationsCard.vue';
  import TransactionProductNotesCard from '@/Components/TransactionSummary/TransactionProductNotesCard.vue';
  import AssignedCommModal from '@/Components/UI/AssignedCommModal.vue'; // Expose Swal globally for legacy code

  // Expose Swal globally for legacy code
  if (typeof window !== 'undefined') {
    window.Swal = Swal;
    window.swal = Swal.fire.bind(Swal);
  }

  const NiceVariance = formatNiceVariance;
  const NiceNumber = formatNiceNumber;

  // Date format functions - using composables
  const format = () => formatShortDate(filterForm.end_date);
  const formatStart = () => formatShortDate(filterForm.start_date);

  const formatEarly = () => formatShortDate(combined_Form.transport_date_earliest);

  const formatLate = () => formatShortDate(combined_Form.transport_date_latest);
  const formatInvoicePdDay = () => formatShortDate(combined_Form.invoice_paid_date);
  const formatInvoicePayByDay = () => formatShortDate(combined_Form.invoice_pay_by_date);
  const formatInvoiceDate = () => formatShortDate(combined_Form.invoice_date);

  const props = defineProps({
    transactions: Object,
    selected_transaction: Object,
    filters: Object,
    start_date: String,
    end_date: String,
    all_customers: Object,
    contract_types: Object,
    all_products: Object,
    all_suppliers: Object,
    all_transporters: Object,
    all_staff: Object,
    confirmation_types: Object,
    all_product_sources: Object,
    all_packaging: Object,
    all_billing_units: Object,
    loading_hour_options: Object,
    all_drivers: Object,
    all_vehicles: Object,
    all_vehicle_types: Object,
    all_transport_rates: Object,
    all_status_entities: Object,
    all_status_types: Object,
    all_invoice_statuses: Object,
    rules_with_approvals: Object,
    deal_ticket: Object,
    transport_order: Object,
    purchase_order: Object,
    sales_order: Object,
    all_terms_of_payments: Object,
    linked_trans_sc: Object,
    linked_trans_pc: Object,
    linked_trans_other: Object,
    linked_trans_split: Object,
    primary_linked_trans_split: Object,
    model_activity: Object,
    split_totals: Object,
  });

  onBeforeMount(async () => {});

  // tabs and tab selection moved to composable for cleaner code
  const { tabs, selectedTabId, selectTab } = useTransactionTabs(
    computed(() => props.selected_transaction.is_split_load)
  );

  // Tab-specific query filters moved to composables for cleaner code
  const { supplierQuery, filteredSuppliers } = useSupplierTab(props);

  const {
    customerQuery,
    filteredCustomers,
    customerQuery2,
    filteredCustomers2,
    customerQuery3,
    filteredCustomers3,
    customerQuery4,
    filteredCustomers4,
    customerQuery5,
    filteredCustomers5,
  } = useCustomerTab(props);

  const {
    productQuery,
    filteredProducts,
    productSourceQuery,
    filteredProductSources,
    billingUnitsIncomingQuery,
    filteredBillingUnitsIncoming,
    packageIncomingQuery,
    filteredPackageIncoming,
    billingUnitsOutgoingQuery,
    filteredBillingUnitsOutgoing,
    packageOutgoingQuery,
    filteredPackageOutgoing,
    contractTypeQuery,
    filteredContractTypes,
  } = useProductTab(props);

  const {
    transporterQuery,
    filteredTransporters,
    selectedVehicleQuery,
    filteredSelectedVehicles,
    selectedDriverQuery,
    filteredSelectedDrivers,
  } = useTransportTab(props);

  const permissions = computed(() => usePage().props.permissions);
  const roles_permissions = computed(() => usePage().props.roles_permissions);
  computed(() =>
    usePage().props.roles_permissions.permissions.includes('edit_adjusted_gp')
  );
  const selectedSplitCustomer = ref(2);

  // isLoading now comes from useTransactionFilters composable
  let isUpdating = ref(false);

  // selectedTabId and selectTab now come from useTransactionTabs composable

  // All modal state and handlers now come from useTransactionModals composable
  const {
    currentDriverVehicle,
    viewDriverVehicleModal,
    viewDriverVehicleNewModal,
    viewDriverVehicle,
    viewDriverNewVehicle,
    closeDriverVehicleModal,
    viewContractLinkModal,
    viewContractLinkModalSc,
    viewContractLink,
    viewContractLinkSc,
    closeContractLink,
    closeContractLinkSc,
    viewSplitLinkModal,
    viewSplitLink,
    closeSplitLink,
    currentAssignedComm,
    viewAssignedCommModal,
    viewAssignedCommNewModal,
    viewAssignedComm,
    viewAssignedNewComm,
    closeAssignedComm,
    closeAssignedNewComm,
    viewTradeSlideOver,
    showTradeSlideOver,
    closeTradeSlideOver,
  } = useTransactionModals();

  // All toggle state (show/hide UI elements) from useTransactionToggles composable
  const {
    showDetails,
    toggleDetails,
    showDriver,
    toggleShowDriver,
    showVehicle,
    toggleShowVehicle,
  } = useTransactionToggles();

  // Small forms (driver, vehicle, trans link) from useTransactionForms composable
  const {
    driverForm,
    createDriver,
    vehicleForm,
    createVehicle,
    transLinkForm,
    deleteTransLink,
  } = useTransactionForms();

  // Status and approval forms from useTransactionStatusForms composable
  const {
    statusForm,
    createStatus,
    deleteStatus,
    transportApprovalForm,
    salesOrderForm,
    activateSalesOrder,
    sendSalesOrder,
    receiveSalesOrder,
    purchaseOrderForm,
    activatePurchaseOrder,
    sendPurchaseOrder,
    receivePurchaseOrder,
    transportOrderForm,
    activateTransportOrder,
    sendTransportOrder,
    receiveTransportOrder,
  } = useTransactionStatusForms(props, isUpdating);

  // Declare updateSelectValues first (defined below)
  let updateSelectValues;

  // Initialize transaction filters composable
  const {
    filterForm,
    isLoading,
    mon,
    tue,
    wed,
    thu,
    fri,
    sat,
    sun,
    filteredTrans,
    filter,
    sort,
    updateSelectedTrans,
    clear,
  } = useTransactionFilters(props, () => updateSelectValues());

  const newTradeAdded = () => {
    filterForm.new_trade_added = true;
  };

  // Define updateSelectValues
  updateSelectValues = () => {
    //transportApprovalForm (from composable)
    transportApprovalForm.transport_trans_id = props.selected_transaction.id;
    transportApprovalForm.transport_job_id = props.selected_transaction.transport_job.id;
    transportApprovalForm.deal_ticket_id = props.selected_transaction.deal_ticket.id;

    //temp_form
    temp_form.transport_trans_id = props.selected_transaction.id;

    //statusForm (from composable)
    statusForm.transport_trans_id = props.selected_transaction.id;

    //combined_Form
    combined_Form.contract_type_id = props.contract_types.find(
      (element) => element.id === props.selected_transaction.contract_type_id
    );
    combined_Form.product_id = props.all_products.find(
      (element) => element.id === props.selected_transaction.product_id
    );
    combined_Form.supplier_id = props.all_suppliers.find(
      (element) => element.id === props.selected_transaction.supplier_id
    );
    combined_Form.customer_id = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id
    );
    combined_Form.customer_id_2 = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_2
    );
    combined_Form.customer_id_3 = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_3
    );
    combined_Form.customer_id_4 = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_4
    );
    combined_Form.customer_id_4 = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_4
    );
    combined_Form.customer_id_5 = props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_5
    );

    combined_Form.transporter_id = props.all_transporters.find(
      (element) => element.id === props.selected_transaction.transporter_id
    );
    combined_Form.contract_type_id = props.contract_types.find(
      (element) => element.id === props.selected_transaction.contract_type_id
    );
    combined_Form.contract_no = props.selected_transaction.contract_no;
    combined_Form.old_id = props.selected_transaction.old_id;
    combined_Form.include_in_calculations =
      props.selected_transaction.include_in_calculations;
    combined_Form.transport_date_earliest =
      props.selected_transaction.transport_date_earliest;
    combined_Form.transport_date_latest =
      props.selected_transaction.transport_date_latest;
    combined_Form.suppliers_notes = props.selected_transaction.suppliers_notes;
    combined_Form.delivery_notes = props.selected_transaction.delivery_notes;
    combined_Form.product_notes = props.selected_transaction.product_notes;
    combined_Form.customer_notes = props.selected_transaction.customer_notes;
    combined_Form.traders_notes = props.selected_transaction.traders_notes;
    combined_Form.transport_notes = props.selected_transaction.transport_notes;
    combined_Form.pricing_notes = props.selected_transaction.pricing_notes;
    combined_Form.process_notes = props.selected_transaction.process_notes;
    combined_Form.document_notes = props.selected_transaction.document_notes;
    combined_Form.transaction_notes = props.selected_transaction.transaction_notes;
    combined_Form.traders_notes_supplier =
      props.selected_transaction.traders_notes_supplier;
    combined_Form.traders_notes_customer =
      props.selected_transaction.traders_notes_customer;
    combined_Form.traders_notes_transport =
      props.selected_transaction.traders_notes_transport;
    combined_Form.is_transaction_done = props.selected_transaction.is_transaction_done;
    combined_Form.is_split_load = props.selected_transaction.is_split_load;

    combined_Form.is_split_load_primary =
      props.selected_transaction.is_split_load_primary;
    combined_Form.is_split_load_member = props.selected_transaction.is_split_load_member;

    //combined_Form
    combined_Form.confirmed_by_id = props.all_staff.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.confirmed_by_id
    );
    combined_Form.confirmed_by_type_id = props.confirmation_types.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.confirmed_by_type_id
    );
    combined_Form.packaging_incoming_id = props.all_packaging.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.packaging_incoming_id
    );
    combined_Form.packaging_outgoing_id = props.all_packaging.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.packaging_outgoing_id
    );
    combined_Form.product_source_id = props.all_product_sources.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.product_source_id
    );
    combined_Form.billing_units_incoming_id = props.all_billing_units.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.billing_units_incoming_id
    );
    combined_Form.billing_units_outgoing_id = props.all_billing_units.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.billing_units_outgoing_id
    );
    combined_Form.collection_address_id = props.all_suppliers
      .find((element) => element.id === props.selected_transaction.supplier_id)
      .addressable.find(
        (element) =>
          element.id === props.selected_transaction.transport_load.collection_address_id
      );
    combined_Form.delivery_address_id = props.all_customers
      .find((element) => element.id === props.selected_transaction.customer_id)
      .addressable.find(
        (element) =>
          element.id === props.selected_transaction.transport_load.delivery_address_id
      );
    combined_Form.delivery_address_id_2 =
      props.selected_transaction.transport_load.delivery_address_id_2 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_2)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_2
            );
    combined_Form.delivery_address_id_3 =
      props.selected_transaction.transport_load.delivery_address_id_3 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_3)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_3
            );
    combined_Form.delivery_address_id_4 =
      props.selected_transaction.transport_load.delivery_address_id_4 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_4)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_4
            );
    combined_Form.delivery_address_id_5 =
      props.selected_transaction.transport_load.delivery_address_id_5 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_5)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_5
            );

    combined_Form.product_grade_perc =
      props.selected_transaction.transport_load.product_grade_perc;
    combined_Form.no_units_incoming =
      props.selected_transaction.transport_load.no_units_incoming;
    combined_Form.no_units_outgoing =
      props.selected_transaction.transport_load.no_units_outgoing;

    combined_Form.no_units_outgoing_2 =
      props.selected_transaction.transport_load.no_units_outgoing_2;
    combined_Form.no_units_outgoing_3 =
      props.selected_transaction.transport_load.no_units_outgoing_3;
    combined_Form.no_units_outgoing_4 =
      props.selected_transaction.transport_load.no_units_outgoing_4;
    combined_Form.no_units_outgoing_5 =
      props.selected_transaction.transport_load.no_units_outgoing_5;

    combined_Form.no_units_outgoing_total =
      props.selected_transaction.transport_load.no_units_outgoing_total;

    combined_Form.is_weighbridge_certificate_received =
      props.selected_transaction.transport_load.is_weighbridge_certificate_received;
    combined_Form.delivery_note = props.selected_transaction.transport_load.delivery_note;
    combined_Form.calculated_route_distance =
      props.selected_transaction.transport_load.calculated_route_distance;

    //salesOrderForm (from composable)
    salesOrderForm.transport_trans_id = props.sales_order.transport_trans_id;
    salesOrderForm.confirmed_by_type_id = props.sales_order.confirmed_by_type_id;
    salesOrderForm.is_active = props.sales_order.is_active;
    salesOrderForm.is_so_conf_sent = props.sales_order.is_so_conf_sent;
    salesOrderForm.is_so_conf_received = props.sales_order.is_so_conf_received;

    //purchaseOrderForm (from composable)
    purchaseOrderForm.transport_trans_id = props.purchase_order.transport_trans_id;
    purchaseOrderForm.confirmed_by_type_id = props.purchase_order.confirmed_by_type_id;
    purchaseOrderForm.is_active = props.purchase_order.is_active;
    purchaseOrderForm.is_po_sent = props.purchase_order.is_po_sent;
    purchaseOrderForm.is_po_received = props.purchase_order.is_po_received;

    //transportOrderForm (from composable)
    transportOrderForm.transport_trans_id = props.transport_order.transport_trans_id;
    transportOrderForm.confirmed_by_type_id = props.transport_order.confirmed_by_type_id;
    transportOrderForm.is_active = props.transport_order.is_active;
    transportOrderForm.is_to_sent = props.transport_order.is_to_sent;
    transportOrderForm.is_to_received = props.transport_order.is_to_received;

    //combined_Form

    combined_Form.customer_order_number =
      props.selected_transaction.transport_job.customer_order_number;
    combined_Form.supplier_loading_number =
      props.selected_transaction.transport_job.supplier_loading_number;
    combined_Form.customer_order_number_2 =
      props.selected_transaction.transport_job.customer_order_number_2;
    combined_Form.supplier_loading_number_2 =
      props.selected_transaction.transport_job.supplier_loading_number_2;
    combined_Form.customer_order_number_3 =
      props.selected_transaction.transport_job.customer_order_number_3;
    combined_Form.supplier_loading_number_3 =
      props.selected_transaction.transport_job.supplier_loading_number_3;
    combined_Form.customer_order_number_4 =
      props.selected_transaction.transport_job.customer_order_number_4;
    combined_Form.supplier_loading_number_4 =
      props.selected_transaction.transport_job.supplier_loading_number_4;
    combined_Form.customer_order_number_5 =
      props.selected_transaction.transport_job.customer_order_number_5;
    combined_Form.supplier_loading_number_5 =
      props.selected_transaction.transport_job.supplier_loading_number_5;

    combined_Form.is_multi_loads =
      props.selected_transaction.transport_job.is_multi_loads;
    combined_Form.is_approved = props.selected_transaction.transport_job.is_approved;
    combined_Form.is_transport_costs_inc_price =
      props.selected_transaction.transport_job.is_transport_costs_inc_price;
    combined_Form.is_product_zero_rated =
      props.selected_transaction.transport_job.is_product_zero_rated;
    combined_Form.offloading_hours_from_id =
      props.selected_transaction.transport_job.offloading_hours_from_id;
    combined_Form.offloading_hours_to_id =
      props.selected_transaction.transport_job.offloading_hours_to_id;
    combined_Form.loading_hours_from_id =
      props.selected_transaction.transport_job.loading_hours_from_id;
    combined_Form.loading_hours_to_id =
      props.selected_transaction.transport_job.loading_hours_to_id;
    combined_Form.load_insurance_per_ton =
      props.selected_transaction.transport_job.load_insurance_per_ton;
    combined_Form.total_load_insurance =
      props.selected_transaction.transport_job.total_load_insurance;
    combined_Form.number_loads = props.selected_transaction.transport_job.number_loads;
    combined_Form.loading_instructions =
      props.selected_transaction.transport_job.loading_instructions;
    combined_Form.offloading_instructions =
      props.selected_transaction.transport_job.offloading_instructions;

    combined_Form.loading_contact =
      props.selected_transaction.transport_job.loading_contact;
    combined_Form.loading_contact_no =
      props.selected_transaction.transport_job.loading_contact_no;
    combined_Form.offloading_contact =
      props.selected_transaction.transport_job.offloading_contact;
    combined_Form.offloading_contact_no =
      props.selected_transaction.transport_job.offloading_contact_no;

    //combined_Form

    combined_Form.transport_rate_basis_id =
      props.selected_transaction.transport_finance.transport_rate_basis_id;
    combined_Form.cost_price_per_unit =
      props.selected_transaction.transport_finance.cost_price_per_unit;
    combined_Form.selling_price_per_unit =
      props.selected_transaction.transport_finance.selling_price_per_unit;
    combined_Form.transport_rate =
      props.selected_transaction.transport_finance.transport_rate;
    combined_Form.transport_cost_2 =
      props.selected_transaction.transport_finance.transport_cost_2;
    combined_Form.transport_cost_3 =
      props.selected_transaction.transport_finance.transport_cost_3;
    combined_Form.transport_cost_4 =
      props.selected_transaction.transport_finance.transport_cost_4;
    combined_Form.transport_cost_5 =
      props.selected_transaction.transport_finance.transport_cost_5;

    combined_Form.selling_price_2 =
      props.selected_transaction.transport_finance.selling_price_2;
    combined_Form.selling_price_3 =
      props.selected_transaction.transport_finance.selling_price_3;
    combined_Form.selling_price_4 =
      props.selected_transaction.transport_finance.selling_price_4;
    combined_Form.selling_price_5 =
      props.selected_transaction.transport_finance.selling_price_5;

    combined_Form.additional_cost_1 =
      props.selected_transaction.transport_finance.additional_cost_1;
    combined_Form.additional_cost_2 =
      props.selected_transaction.transport_finance.additional_cost_2;
    combined_Form.additional_cost_3 =
      props.selected_transaction.transport_finance.additional_cost_3;
    combined_Form.additional_cost_desc_1 =
      props.selected_transaction.transport_finance.additional_cost_desc_1;
    combined_Form.additional_cost_desc_2 =
      props.selected_transaction.transport_finance.additional_cost_desc_2;
    combined_Form.additional_cost_desc_3 =
      props.selected_transaction.transport_finance.additional_cost_desc_3;
    combined_Form.adjusted_gp = props.selected_transaction.transport_finance.adjusted_gp;
    combined_Form.adjusted_gp_notes =
      props.selected_transaction.transport_finance.adjusted_gp_notes;

    //combined_Form
    combined_Form.transport_trans_id = props.selected_transaction.id;
    combined_Form.old_id = props.selected_transaction.transport_invoice.old_id;
    combined_Form.is_active = props.selected_transaction.transport_invoice.is_active;
    combined_Form.is_printed = props.selected_transaction.transport_invoice.is_printed;
    combined_Form.invoice_id =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_id;
    combined_Form.is_invoiced =
      props.selected_transaction.transport_invoice.transport_invoice_details.is_invoiced;
    combined_Form.is_invoice_paid =
      props.selected_transaction.transport_invoice.transport_invoice_details.is_invoice_paid;
    combined_Form.invoice_no =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_no;
    combined_Form.invoice_paid_date =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_paid_date;
    combined_Form.invoice_pay_by_date =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_pay_by_date;
    combined_Form.invoice_date =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_date;
    combined_Form.invoice_amount =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_amount;
    combined_Form.invoice_amount_paid =
      props.selected_transaction.transport_invoice.transport_invoice_details.invoice_amount_paid;
    combined_Form.status_id =
      props.selected_transaction.transport_invoice.transport_invoice_details.status_id;
    combined_Form.notes =
      props.selected_transaction.transport_invoice.transport_invoice_details.notes;

    //Driver vehicle
    combined_Form.regular_driver_id = props.all_drivers.find(
      (element) =>
        element.id ===
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          .regular_driver_id
    );
    combined_Form.regular_vehicle_id = props.all_vehicles.find(
      (element) =>
        element.id ===
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          .regular_vehicle_id
    );
    combined_Form.weighbridge_upload_weight =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].weighbridge_upload_weight;
    combined_Form.weighbridge_offload_weight =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].weighbridge_offload_weight;
    combined_Form.is_weighbridge_variance =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_weighbridge_variance;
    combined_Form.is_cancelled =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_cancelled;
    combined_Form.date_cancelled =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_cancelled;
    combined_Form.is_loaded =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_loaded;
    combined_Form.date_loaded =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_loaded;
    combined_Form.is_onroad =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_onroad;
    combined_Form.date_onroad =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_onroad;
    combined_Form.is_delivered =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_delivered;
    combined_Form.date_delivered =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_delivered;
    combined_Form.is_transport_scheduled =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_transport_scheduled;
    combined_Form.date_scheduled =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_scheduled;
    combined_Form.is_paid =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_paid;
    combined_Form.date_paid =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].date_paid;
    combined_Form.is_payment_overdue =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].is_payment_overdue;
    combined_Form.driver_vehicle_loading_number =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].driver_vehicle_loading_number;
    combined_Form.trailer_reg_1 =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].trailer_reg_1;
    combined_Form.trailer_reg_2 =
      props.selected_transaction.transport_job.transport_driver_vehicle[0].trailer_reg_2;

    combined_Form.clearErrors();
  };

  let no_units_to_allocate = computed(
    () =>
      combined_Form.no_units_outgoing -
      combined_Form.no_units_outgoing_2 -
      combined_Form.no_units_outgoing_3 -
      combined_Form.no_units_outgoing_4 -
      combined_Form.no_units_outgoing_5
  );

  let selling_price_to_allocate = computed(
    () =>
      props.selected_transaction.transport_finance.selling_price -
      combined_Form.selling_price_2 -
      combined_Form.selling_price_3 -
      combined_Form.selling_price_4 -
      combined_Form.selling_price_5
  );
  let transport_cost_to_allocate = computed(
    () =>
      props.selected_transaction.transport_finance.transport_cost -
      combined_Form.transport_cost_2 -
      combined_Form.transport_cost_3 -
      combined_Form.transport_cost_4 -
      combined_Form.transport_cost_5
  );

  // Status and order forms now provided by useTransactionStatusForms composable

  let combined_Form = useForm({
    //TransportTrans
    product_id: props.all_products.find(
      (element) => element.id === props.selected_transaction.product_id
    ),
    supplier_id: props.all_suppliers.find(
      (element) => element.id === props.selected_transaction.supplier_id
    ),
    customer_id: props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id
    ),
    customer_id_2: props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_2
    ),
    customer_id_3: props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_3
    ),
    customer_id_4: props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_4
    ),
    customer_id_5: props.all_customers.find(
      (element) => element.id === props.selected_transaction.customer_id_5
    ),
    transporter_id: props.all_transporters.find(
      (element) => element.id === props.selected_transaction.transporter_id
    ),
    contract_type_id: props.contract_types.find(
      (element) => element.id === props.selected_transaction.contract_type_id
    ),
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

    //TransportLoad

    confirmed_by_id: props.all_staff.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.confirmed_by_id
    ),
    confirmed_by_type_id: props.confirmation_types.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.confirmed_by_type_id
    ),
    packaging_incoming_id: props.all_packaging.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.packaging_incoming_id
    ),
    packaging_outgoing_id: props.all_packaging.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.packaging_outgoing_id
    ),
    product_source_id: props.all_product_sources.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.product_source_id
    ),
    billing_units_incoming_id: props.all_billing_units.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.billing_units_incoming_id
    ),
    billing_units_outgoing_id: props.all_billing_units.find(
      (element) =>
        element.id === props.selected_transaction.transport_load.billing_units_outgoing_id
    ),
    collection_address_id:
      props.selected_transaction.transport_load.collection_address_id,
    delivery_address_id: props.all_customers
      .find((element) => element.id === props.selected_transaction.customer_id)
      .addressable.find(
        (element) =>
          element.id === props.selected_transaction.transport_load.delivery_address_id
      ),
    delivery_address_id_2: props.all_customers
      .find((element) => element.id === props.selected_transaction.customer_id_2)
      .addressable.find(
        (element) =>
          element.id === props.selected_transaction.transport_load.delivery_address_id_2
      ),
    delivery_address_id_3:
      props.selected_transaction.transport_load.delivery_address_id_3 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_3)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_3
            ),
    delivery_address_id_4:
      props.selected_transaction.transport_load.delivery_address_id_4 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_4)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_4
            ),
    delivery_address_id_5:
      props.selected_transaction.transport_load.delivery_address_id_5 === null
        ? null
        : props.all_customers
            .find((element) => element.id === props.selected_transaction.customer_id_5)
            .addressable.find(
              (element) =>
                element.id ===
                props.selected_transaction.transport_load.delivery_address_id_5
            ),
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

    //TransportJob

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
    is_multi_loads: props.selected_transaction.transport_job.is_multi_loads,
    is_approved: props.selected_transaction.transport_job.is_approved,
    is_transport_costs_inc_price:
      props.selected_transaction.transport_job.is_transport_costs_inc_price,
    is_product_zero_rated: props.selected_transaction.transport_job.is_product_zero_rated,
    offloading_hours_from_id:
      props.selected_transaction.transport_job.offloading_hours_from_id,
    offloading_hours_to_id:
      props.selected_transaction.transport_job.offloading_hours_to_id,
    loading_hours_from_id: props.selected_transaction.transport_job.loading_hours_from_id,
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
    offloading_contact_no: props.selected_transaction.transport_job.offloading_contact_no,

    //Transport Finance

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

    //Transport Invoice

    transport_trans_id: props.selected_transaction.id,
    invoice_old_id: props.selected_transaction.transport_invoice.old_id,
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

    //driver_vehicle

    regular_driver_id: props.all_drivers.find(
      (element) =>
        element.id ===
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          .regular_driver_id
    ),
    regular_vehicle_id: props.all_vehicles.find(
      (element) =>
        element.id ===
        props.selected_transaction.transport_job.transport_driver_vehicle[0]
          .regular_vehicle_id
    ),

    weighbridge_upload_weight:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? 0
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .weighbridge_upload_weight,
    weighbridge_offload_weight:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? 0
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .weighbridge_offload_weight,
    is_weighbridge_variance:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .is_weighbridge_variance,
    is_cancelled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .is_cancelled,
    date_cancelled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .date_cancelled,
    is_loaded:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0].is_loaded,
    date_loaded:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .date_loaded,
    is_onroad:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0].is_onroad,
    date_onroad:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .date_onroad,
    is_delivered:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .is_delivered,
    date_delivered:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .date_delivered,
    is_transport_scheduled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .is_transport_scheduled,
    date_scheduled:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .date_scheduled,
    is_paid:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0].is_paid,
    date_paid:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0].date_paid,
    is_payment_overdue:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? false
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .is_payment_overdue,
    driver_vehicle_loading_number:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .driver_vehicle_loading_number,
    trailer_reg_1:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .trailer_reg_1,
    trailer_reg_2:
      props.selected_transaction.transport_job.transport_driver_vehicle[0] == null
        ? null
        : props.selected_transaction.transport_job.transport_driver_vehicle[0]
            .trailer_reg_2,

    update_related_models: 0,
  });

  // Computed values (filters, sums, validation) from useTransactionComputed composable
  const {
    filteredLinkedContractsMq,
    filteredLinkedContractsPc,
    filteredLinkedContractsSc,
    sumLinkedContractsMq,
    sumLinkedContractsPc,
    emptyErrorsTrans,
    paymentTerms,
    getTitle,
  } = useTransactionComputed(props, combined_Form);

  //combined_Form.contract_type_id = props.contract_types.find(element => element.id === props.selected_transaction.contract_type_id);

  //Form CRUD

  let startTime = 0;
  let endTime = 0;

  const updateCombined_Form = () => {
    isUpdating.value = true;
    // startTime = performance.now()

    combined_Form.put(
      route('transaction_summary.update', props.selected_transaction.id),
      {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire(usePage().props.jetstream.flash?.banner || '');

          //endTime = performance.now()
          //console.log(`Call to transportTrans took ${(endTime - startTime)/1000} seconds`);
          startTime = 0;
          endTime = 0;
        },
        onError: (error) => {
          alert('Something went wrong on the Transaction');
          console.log(error);
        },
        onFinish: () => {
          // Always reset loading state when request completes (success or error)
          isUpdating.value = false;
        },
      }
    );
  };

  const updateAll = () => {
    updateCombined_Form();
  };

  // Handle supplier update from child component - no reactive logic
  const handleSupplierUpdate = (newSupplier) => {
    // Direct assignment without any reactive checks
    combined_Form.supplier_id = newSupplier;

    // Clear collection address when supplier changes (old address is invalid for new supplier)
    combined_Form.collection_address_id = null;
  };

  // Watch for customer changes to clear delivery addresses
  watch(
    () => combined_Form.customer_id,
    (newCustomer, oldCustomer) => {
      // Only clear if customer actually changed (not initial load)
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combined_Form.delivery_address_id = null;
      }
    }
  );

  watch(
    () => combined_Form.customer_id_2,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combined_Form.delivery_address_id_2 = null;
      }
    }
  );

  watch(
    () => combined_Form.customer_id_3,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combined_Form.delivery_address_id_3 = null;
      }
    }
  );

  watch(
    () => combined_Form.customer_id_4,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combined_Form.delivery_address_id_4 = null;
      }
    }
  );

  watch(
    () => combined_Form.customer_id_5,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combined_Form.delivery_address_id_5 = null;
      }
    }
  );

  const cloneTransportTrans = () => {
    temp_form.post(route('transport_transaction.clone'), {
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

  // Order activation, send, and receive handlers now provided by useTransactionStatusForms composable

  const temp_form = useForm({
    transport_trans_id: props.selected_transaction.id,
  });

  // Helper functions from useTransactionHelpers composable
  const { vehicleSlideProps, getComponentProps, doCreatedTrade, deleteAssignedComm } =
    useTransactionHelpers(filterForm, filter, temp_form);

  const deleteDriverVehicle = (id) => {
    if (confirm('Sure you want to delete?')) {
      temp_form.delete(route('transport_driver_vehicle.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          window.swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
          close();
          console.log(e);
        },
      });

      close();
    }
  };

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

  const createFinalDealTicket = () => {
    temp_form.post(route('pdf_report.deal_ticket_final'), {
      preserveScroll: true,
      onSuccess: () => {
        window.swal(usePage().props.jetstream.flash?.banner || '');
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  //pdf_report.deal_ticket_final
  const downloadDealTicket = () => {
    axios
      .get(route('pdf_report.deal_ticket_final.download', props.deal_ticket.report_path))
      .then((res) => {});
  };

  // NOTE: All filter queries (customer, product, transport, etc.) are now provided by composables
  // See: useCustomerTab, useProductTab, useTransportTab, useSupplierTab
  // This removed ~250 lines of duplicated filter logic

  // Address filters for specific form fields (these filter addressable relationships)
  let collectionAddressQuery = ref('');

  const filteredCollectionAddress = computed(() =>
    collectionAddressQuery.value === ''
      ? combined_Form.supplier_id.addressable
      : combined_Form.supplier_id.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(collectionAddressQuery.value.toLowerCase());
        })
  );

  let deliveryAddressQuery = ref('');

  const filteredDeliveryAddress = computed(() =>
    deliveryAddressQuery.value === ''
      ? combined_Form.customer_id.addressable
      : combined_Form.customer_id.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery.value.toLowerCase());
        })
  );

  let deliveryAddressQuery2 = ref('');

  const filteredDeliveryAddress2 = computed(() =>
    deliveryAddressQuery2.value === ''
      ? combined_Form.customer_id_2.addressable
      : combined_Form.customer_id_2.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery2.value.toLowerCase());
        })
  );

  let deliveryAddressQuery3 = ref('');

  const filteredDeliveryAddress3 = computed(() =>
    deliveryAddressQuery3.value === ''
      ? combined_Form.customer_id_3.addressable
      : combined_Form.customer_id_3.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery3.value.toLowerCase());
        })
  );

  let deliveryAddressQuery4 = ref('');

  const filteredDeliveryAddress4 = computed(() =>
    deliveryAddressQuery4.value === ''
      ? combined_Form.customer_id_4.addressable
      : combined_Form.customer_id_4.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery4.value.toLowerCase());
        })
  );

  let deliveryAddressQuery5 = ref('');

  const filteredDeliveryAddress5 = computed(() =>
    deliveryAddressQuery5.value === ''
      ? combined_Form.customer_id_5.addressable
      : combined_Form.customer_id_5.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery5.value.toLowerCase());
        })
  );

  // Modal state removed - now provided by useTransactionModals composable
  // filteredLinkedContracts and sumLinkedContracts now provided by useTransactionComputed composable

  // showDetails and toggleDetails now provided by useTransactionToggles composable
  // createStatus and deleteStatus now provided by useTransactionStatusForms composable

  // getTitle now provided by useTransactionComputed composable

  // showDriver, showVehicle, toggleShowDriver, toggleShowVehicle now provided by useTransactionToggles composable
  // driverForm, vehicleForm now provided by useTransactionForms composable
  // vehicleSlideProps, getComponentProps now provided by useTransactionHelpers composable

  // createDriver, createVehicle, deleteTransLink now provided by useTransactionForms composable
  // Wrapper functions to pass toggle handlers
  const createProduct = () => createDriver(toggleShowDriver);
  const createProductVehicle = () => createVehicle(toggleShowVehicle);

  // header_styler and row_styler moved to TransactionTable components
  // doCreatedTrade, deleteAssignedComm now provided by useTransactionHelpers composable
</script>

<template>
  <AppLayout :title="getTitle">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <div v-if="selected_transaction != null">Summary: {{ getTitle }}</div>
        <div v-else>Transaction Summary</div>
      </h2>
    </template>

    <div class="p-1">
      <div class="bg-white overflow-x-auto m-1 p-1 shadow-xl sm:rounded-lg">
        <div>
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="mt-3 flow-root">
              <div class="-mx-4 -my-4 sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                  <!-- Transaction Filters Component -->
                  <transaction-filters
                    :contract-types="contract_types"
                    :filter-form="filterForm"
                    :format-end="format"
                    :format-start="formatStart"
                    :fri="fri"
                    :mon="mon"
                    :sat="sat"
                    :sun="sun"
                    :thu="thu"
                    :tue="tue"
                    :wed="wed"
                    @clear="clear"
                    @search="filter"
                    @add-trade="showTradeSlideOver"
                    @toggle-details="toggleDetails"
                    @update:mon="mon = $event"
                    @update:tue="tue = $event"
                    @update:wed="wed = $event"
                    @update:thu="thu = $event"
                    @update:fri="fri = $event"
                    @update:sat="sat = $event"
                    @update:sun="sun = $event" />

                  <div>
                    <trade-slide-over
                      :show="viewTradeSlideOver"
                      @close="closeTradeSlideOver"
                      @created_trade="doCreatedTrade" />
                  </div>

                  <!-- Transaction Table Component -->
                  <transaction-table
                    :filtered-transactions="filteredTrans"
                    :is-loading="isLoading"
                    :selected-transaction="selected_transaction"
                    :show-details="showDetails"
                    :sort-direction="filterForm.direction"
                    :sort-field="filterForm.field"
                    :transactions="transactions"
                    @sort="sort"
                    @select-transaction="updateSelectedTrans" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="sticky bg-white m-2 p-1 shadow-xl sm:rounded-lg">
        <div>
          <div class="px-2 sm:px-3 lg:px-4">
            <div>
              <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                <div class="md:flex md:items-center md:justify-between">
                  <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Selected Transaction

                    <div>
                      <div
                        v-if="selected_transaction.a_mq"
                        class="py-2 inline-flex text-xl font-bold text-black">
                        MQ{{ selected_transaction.a_mq }}
                      </div>
                      <div class="py-3 ml-2 inline-flex text-sm font-light text-gray-900">
                        (ID:{{ selected_transaction.id }})
                      </div>
                    </div>
                  </h3>

                  <div class="mt-4 flex md:absolute md:right-0 md:top-3 md:mt-0">
                    <button
                      class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                      type="button"
                      @click="cloneTransportTrans">
                      Clone
                    </button>
                    <button
                      class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                      type="button"
                      @click.prevent="updateAll">
                      <exclamation-triangle-icon
                        v-if="isUpdating"
                        class="w-3 h-3 animate-spin mr-2 fill-white" />
                      Update
                    </button>
                  </div>
                </div>
                <div class="mt-1">
                  <div class="hidden sm:block">
                    <transaction-tab-nav
                      :selected-tab-id="selectedTabId"
                      :tabs="tabs"
                      @select="selectTab" />
                  </div>
                </div>
              </div>
              <div class="m-1 p-1">
                <div
                  v-if="!emptyErrorsTrans"
                  class="mb-1">
                  <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                      <div class="flex-shrink-0">
                        <XCircleIcon
                          aria-hidden="true"
                          class="h-5 w-5 text-red-400" />
                      </div>
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                          There were errors when updating. Fix & update again.
                        </h3>
                        <div class="mt-2 text-lg text-red-700">
                          <ul
                            class="list-disc space-y-1 pl-5"
                            role="list">
                            <li v-if="!emptyErrorsTrans">
                              Transaction errors (see appropriate tab)
                            </li>
                          </ul>
                        </div>

                        <div class="mt-1 ml-6">
                          <InputError
                            :message="combined_Form.errors['confirmed_by_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['confirmed_by_type_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['packaging_incoming_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['packaging_outgoing_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="
                              combined_Form.errors['billing_units_incoming_id.id']
                            "
                            class="mt-2" />
                          <InputError
                            :message="
                              combined_Form.errors['billing_units_outgoing_id.id']
                            "
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['collection_address_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['delivery_address_id.id']"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors['product_source_id.id']"
                            class="mt-2" />

                          <InputError
                            :message="combined_Form.errors.product_grade_perc"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.no_units_incoming"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.no_units_outgoing"
                            class="mt-2" />
                          <InputError
                            :message="
                              combined_Form.errors.is_weighbridge_certificate_received
                            "
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.delivery_note"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.calculated_route_distance"
                            class="mt-2" />

                          <InputError
                            :message="combined_Form.errors.customer_order_number"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.supplier_loading_number"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.is_multi_loads"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.is_approved"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.is_transport_costs_inc_price"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.offloading_hours_from_id"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.offloading_hours_to_id"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.loading_hours_from_id"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.loading_hours_to_id"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.load_insurance_per_ton"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.total_load_insurance"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.loading_instructions"
                            class="mt-2" />
                          <InputError
                            :message="combined_Form.errors.offloading_instructions"
                            class="mt-2" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="isLoading">
                  <span class="text-lg text-indigo-400">Loading...</span>
                </div>

                <div v-else>
                  <div v-if="selectedTabId === 0">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <transaction-supplier-card
                        :combined-form="combined_Form"
                        :filtered-linked-contracts-pc="filteredLinkedContractsPc"
                        :filtered-suppliers="filteredSuppliers"
                        :selected-transaction="selected_transaction"
                        :show-contract-link-modal="viewContractLinkModal"
                        :supplier-query="supplierQuery"
                        @update:supplier-query="supplierQuery = $event"
                        @update:supplier="handleSupplierUpdate"
                        @view-contract-link="viewContractLink"
                        @close-contract-link="closeContractLink" />

                      <transaction-supplier-account-card
                        :collection-address-query="collectionAddressQuery"
                        :combined-form="combined_Form"
                        :filtered-collection-address="filteredCollectionAddress"
                        :selected-transaction="selected_transaction"
                        @update:collectionAddressQuery="
                          (v) => (collectionAddressQuery = v)
                        " />

                      <transaction-product-card
                        :combined-form="combined_Form"
                        :filtered-billing-units-incoming="filteredBillingUnitsIncoming"
                        :filtered-package-incoming="filteredPackageIncoming"
                        :selected-transaction="selected_transaction"
                        @update:billing-units-incoming-query="
                          billingUnitsIncomingQuery = $event
                        "
                        @update:package-incoming-query="packageIncomingQuery = $event" />

                      <transaction-supplier-notes-card :combined-form="combined_Form" />
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 1">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <transaction-product-incoming-card
                        :combined-form="combined_Form"
                        :filtered-billing-units-incoming="filteredBillingUnitsIncoming"
                        :filtered-package-incoming="filteredPackageIncoming"
                        :filtered-product-sources="filteredProductSources"
                        :filtered-products="filteredProducts"
                        @update:product-query="productQuery = $event"
                        @update:billing-units-incoming-query="
                          billingUnitsIncomingQuery = $event
                        "
                        @update:package-incoming-query="packageIncomingQuery = $event"
                        @update:product-source-query="productSourceQuery = $event" />

                      <transaction-product-outgoing-card
                        :combined-form="combined_Form"
                        :filtered-billing-units-outgoing="filteredBillingUnitsOutgoing"
                        :filtered-package-outgoing="filteredPackageOutgoing"
                        @update:billing-units-outgoing-query="
                          billingUnitsOutgoingQuery = $event
                        "
                        @update:package-outgoing-query="packageOutgoingQuery = $event" />

                      <transaction-product-calculations-card
                        :selected-transaction="selected_transaction" />

                      <transaction-product-notes-card :combined-form="combined_Form" />
                    </ul>
                  </div>

                  <!-- Tab 2: Customer -->
                  <div v-if="selectedTabId === 2">
                    <TransactionCustomerTab
                      :combined-form="combined_Form"
                      :selected-transaction="selected_transaction"
                      :filtered-customers="filteredCustomers"
                      :filtered-delivery-address="filteredDeliveryAddress"
                      :filtered-billing-units-outgoing="filteredBillingUnitsOutgoing"
                      :filtered-package-outgoing="filteredPackageOutgoing"
                      :filtered-linked-contracts-sc="filteredLinkedContractsSc"
                      :primary-linked-trans-split="primary_linked_trans_split"
                      :view-split-link-modal="viewSplitLinkModal"
                      :view-contract-link-modal-sc="viewContractLinkModalSc"
                      @update:delivery-address-query="deliveryAddressQuery = $event"
                      @update:billing-units-outgoing-query="billingUnitsOutgoingQuery = $event"
                      @update:package-outgoing-query="packageOutgoingQuery = $event"
                      @view-split-link="viewSplitLink"
                      @close-split-link="closeSplitLink"
                      @delete-trans-link="deleteTransLink"
                      @view-contract-link-sc="viewContractLinkSc"
                      @close-contract-link-sc="closeContractLinkSc" />
                  </div>

                  <!-- Tab 111: Multi-Customer Table (Split loads) -->
                  <div v-if="selectedTabId === 111">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Customer details
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-2 text-sm leading-6">
                          <!--                                                    <div class="flex justify-between gap-x-4 py-3">
                                                                                                            <dt class="text-gray-500">Split Load 2</dt>
                                                                                                            <dd class="flex items-start gap-x-2">
                                                                                                                <div>
                                                                                                                    <SwitchGroup as="div" class="flex m-2 items-center">
                                                                                                                        <Switch v-model="combined_Form.is_split_load"
                                                                                                                                :class="[combined_Form.is_split_load ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                                                                                    <span aria-hidden="true"
                                                                                                                          :class="[combined_Form.is_split_load ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                                                                        </Switch>
                                                                                                                    </SwitchGroup>
                                                                                                                </div>
                                                                                                            </dd>
                                                                                                        </div>-->

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Split Load Primary</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-2 items-center">
                                  <Switch
                                    v-model="combined_Form.is_split_load_primary"
                                    :class="[
                                      combined_Form.is_split_load_primary
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_split_load_primary
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div
                            v-if="!combined_Form.is_split_load_primary"
                            class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Split Load Primary ID</dt>
                            <dd class="flex items-start gap-x-2">
                              <div v-if="primary_linked_trans_split">
                                <div v-if="primary_linked_trans_split">
                                  <Link
                                    :data="{
                                      selected_trans_id: primary_linked_trans_split.id,
                                    }"
                                    href="/transaction_summary"
                                    method="get"
                                    target="_blank">
                                    ID
                                    {{ primary_linked_trans_split.id }}
                                  </Link>
                                </div>
                              </div>

                              <div v-else>Nothing linked</div>
                            </dd>
                          </div>

                          <div v-if="!combined_Form.is_split_load_primary">
                            <SecondaryButton
                              class="m-1 mt-3"
                              @click="viewSplitLink">
                              Link Split Trade
                            </SecondaryButton>

                            <SecondaryButton
                              class="m-1 mt-3"
                              @click="deleteTransLink(selected_transaction.id)">
                              Remove Link
                            </SecondaryButton>

                            <SplitLinkModal
                              :link_type_id="5"
                              :mq_trans_id="selected_transaction.id"
                              :show="viewSplitLinkModal"
                              @close="closeSplitLink" />
                          </div>

                          <TransactionCustomerSelect
                            v-model="combined_Form.customer_id"
                            :customers="filteredCustomers"
                            label="Customer" />

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Customer Order number</dt>
                            <dd class="flex items-start gap-x-2">
                              <input
                                v-model="combined_Form.customer_order_number"
                                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Offloading no</dt>
                            <dd class="flex items-start gap-x-2">
                              <input
                                id="loading_no"
                                v-model="combined_Form.driver_vehicle_loading_number"
                                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </dd>
                          </div>

                          <div
                            v-if="selected_transaction.contract_type_id === 4"
                            class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">SC Linked</dt>
                            <dd class="flex items-start gap-x-2">
                              <div v-if="filteredLinkedContractsSc[0]">
                                <div>
                                  SC:{{ filteredLinkedContractsSc[0].transport_trans_id }}
                                </div>
                                <div>
                                  {{
                                    filteredLinkedContractsSc[0].transport_transaction_pc
                                      .customer.last_legal_name
                                  }}
                                </div>
                                <div>
                                  {{
                                    filteredLinkedContractsSc[0].transport_transaction_pc
                                      .supplier.last_legal_name
                                  }}
                                </div>
                                <div>
                                  {{
                                    filteredLinkedContractsSc[0].transport_transaction_pc
                                      .product.name
                                  }}
                                </div>
                                <div>
                                  {{
                                    filteredLinkedContractsSc[0].transport_transaction_pc
                                      .transport_load.no_units_incoming
                                  }}
                                </div>
                              </div>
                              <div v-else>Nothing linked..</div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dd class="text-gray-700">
                              <div>
                                <div v-if="selected_transaction.contract_type_id === 4">
                                  <SecondaryButton
                                    class="m-1 mt-3"
                                    @click="viewContractLinkSc">
                                    Link MQ to SC
                                  </SecondaryButton>

                                  <ContractLinkModalSc
                                    :link_type_id="4"
                                    :mq_trans_id="selected_transaction.id"
                                    :show="viewContractLinkModalSc"
                                    @close="closeContractLinkSc" />
                                </div>
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Customer Account details
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Delivery Address</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <div class="mt-2">
                                  <Combobox
                                    v-model="combined_Form.delivery_address_id"
                                    as="div">
                                    <div class="relative mt-2">
                                      <ComboboxInput
                                        :display-value="(address) => address?.line_1"
                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        @change="
                                          deliveryAddressQuery = $event.target.value
                                        " />
                                      <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                        <ChevronUpDownIcon
                                          aria-hidden="true"
                                          class="h-5 w-5 text-gray-400" />
                                      </ComboboxButton>

                                      <ComboboxOptions
                                        v-if="filteredDeliveryAddress.length > 0"
                                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                        <ComboboxOption
                                          v-for="address in filteredDeliveryAddress"
                                          :key="address.id"
                                          v-slot="{ active, selected }"
                                          :value="address"
                                          as="template">
                                          <ul>
                                            <li
                                              :class="[
                                                'relative cursor-default select-none py-2 pl-3 pr-9',
                                                active
                                                  ? 'bg-indigo-600 text-white'
                                                  : 'text-gray-900',
                                              ]">
                                              <div class="flex items-center">
                                                <span
                                                  :class="[
                                                    'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                                    address.is_primary
                                                      ? 'bg-green-400'
                                                      : 'bg-gray-200',
                                                  ]"
                                                  aria-hidden="true" />
                                                <span
                                                  :class="[
                                                    'ml-3 truncate',
                                                    selected && 'font-semibold',
                                                  ]">
                                                  <span>
                                                    {{ address.line_1 }}
                                                  </span>
                                                  <span v-if="address.line_2">
                                                    ,
                                                    {{ address.line_2 }}
                                                  </span>
                                                  <span v-if="address.line_3">
                                                    ,
                                                    {{ address.line_3 }}
                                                  </span>
                                                  <span class="sr-only">
                                                    is
                                                    {{
                                                      address.is_primary
                                                        ? 'online'
                                                        : 'offline'
                                                    }}
                                                  </span>
                                                </span>
                                              </div>

                                              <span
                                                v-if="selected"
                                                :class="[
                                                  'absolute inset-y-0 right-0 flex items-center pr-4',
                                                  active
                                                    ? 'text-white'
                                                    : 'text-indigo-600',
                                                ]">
                                                <CheckIcon
                                                  aria-hidden="true"
                                                  class="h-5 w-5" />
                                              </span>
                                            </li>
                                          </ul>
                                        </ComboboxOption>
                                      </ComboboxOptions>
                                    </div>
                                  </Combobox>
                                </div>
                                <div class="mt-2">
                                  <Link
                                    :href="
                                      route('customer.show', combined_Form.customer_id)
                                    "
                                    class="underline text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    + Add customer address
                                  </Link>
                                </div>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-3">
                            <div>
                              <div v-if="combined_Form.delivery_address_id">
                                <h3
                                  class="text-base font-semibold leading-7 text-indigo-400">
                                  Selected Delivery Address:
                                </h3>

                                <div class="flex justify-between gap-x-4 py-3">
                                  <dt class="text-gray-500">Line 1</dt>
                                  <dd class="text-gray-700">
                                    <div>
                                      {{ combined_Form.delivery_address_id.line_1 }}
                                    </div>
                                  </dd>
                                </div>

                                <div class="flex justify-between gap-x-4 py-3">
                                  <dt class="text-gray-500">Line 2</dt>
                                  <dd class="text-gray-700">
                                    <div>
                                      {{ combined_Form.delivery_address_id.line_2 }}
                                    </div>
                                  </dd>
                                </div>

                                <div class="flex justify-between gap-x-4 py-3">
                                  <dt class="text-gray-500">Line 3</dt>
                                  <dd class="text-gray-700">
                                    <div>
                                      {{ combined_Form.delivery_address_id.line_3 }}
                                    </div>
                                  </dd>
                                </div>

                                <div class="flex justify-between gap-x-4 py-3">
                                  <dt class="text-gray-500">Country</dt>
                                  <dd class="text-gray-700">
                                    <div>
                                      {{ combined_Form.delivery_address_id.country }}
                                    </div>
                                  </dd>
                                </div>

                                <div class="flex justify-between gap-x-4 py-3">
                                  <dt class="text-gray-500">Code</dt>
                                  <dd class="text-gray-700">
                                    <div>
                                      {{ combined_Form.delivery_address_id.code }}
                                    </div>
                                  </dd>
                                </div>
                              </div>

                              <div v-else>No customer addresses loaded...</div>
                            </div>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Payment Terms</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.customer.terms_of_payment.value }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Invoice basis</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.customer.invoice_basis.value }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">VAT Exempt</dt>
                            <dd class="text-gray-700">
                              <div>
                                <check-icon
                                  v-if="selected_transaction.customer.is_vat_exempt"
                                  class="w-6 h-6 fill-green-300" />
                                <XCircleIcon
                                  v-else
                                  class="w-6 h-6 fill-red-400" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">VAT Certificate</dt>
                            <dd class="text-gray-700">
                              <div>
                                <check-icon
                                  v-if="
                                    selected_transaction.customer.is_vat_cert_received
                                  "
                                  class="w-6 h-6 fill-green-300" />
                                <XCircleIcon
                                  v-else
                                  class="w-6 h-6 fill-red-400" />
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Product details
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Product</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.product.name }}
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Billing Units outgoing</dt>
                            <dd class="text-gray-700">
                              <Combobox
                                v-model="combined_Form.billing_units_outgoing_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(units) => units?.name"
                                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      billingUnitsOutgoingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredBillingUnitsOutgoing.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="units in filteredBillingUnitsOutgoing"
                                      :key="units.id"
                                      v-slot="{ active, selected }"
                                      :value="units"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ units.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Packaging</dt>
                            <dd class="text-gray-700">
                              <Combobox
                                v-model="combined_Form.packaging_outgoing_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(packaging) => packaging?.name"
                                    class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      packageOutgoingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredPackageOutgoing.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="packaging in filteredPackageOutgoing"
                                      :key="packaging.id"
                                      v-slot="{ active, selected }"
                                      :value="packaging"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ packaging.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Price / Unit</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_finance
                                      .selling_price_per_unit
                                  )
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Price / Ton</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_finance
                                      .selling_price_per_ton
                                  )
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Total Selling Price</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_finance.selling_price
                                  )
                                }}
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>
                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Customer notes
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <AreaInput
                              id="customer_notes"
                              v-model="combined_Form.customer_notes"
                              :rows="12"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />

                            <InputError
                              :message="combined_Form.errors.customer_notes"
                              class="mt-2" />
                          </div>
                        </dl>
                      </li>
                    </ul>
                  </div>
                  END OLD TAB 2 CONTENT -->

                  <!-- Tab 111: Multi-Customer Table (Split loads) -->
                  <div v-if="selectedTabId === 111">
                    <div>
                      <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                          <tr>
                            <th
                              class="whitespace py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              No
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Customer
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Address
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Deal ticket
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Customer order no
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Supplier loading no
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Unit Split
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Transport Split
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Selling Split
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              2
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <TransactionCustomerSelect
                                v-model="combined_Form.customer_id_2"
                                :customers="filteredCustomers2"
                                label="Customer 2" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  <Combobox
                                    v-model="combined_Form.delivery_address_id_2"
                                    as="div">
                                    <div class="relative mt-2">
                                      <ComboboxInput
                                        :display-value="(address) => address?.line_1"
                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        @change="
                                          deliveryAddressQuery2 = $event.target.value
                                        " />
                                      <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                        <ChevronUpDownIcon
                                          aria-hidden="true"
                                          class="h-5 w-5 text-gray-400" />
                                      </ComboboxButton>

                                      <div v-if="filteredDeliveryAddress2 != null">
                                        <ComboboxOptions
                                          v-if="filteredDeliveryAddress2.length > 0"
                                          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                          <ComboboxOption
                                            v-for="address in filteredDeliveryAddress2"
                                            :key="address.id"
                                            v-slot="{ active, selected }"
                                            :value="address"
                                            as="template">
                                            <ul>
                                              <li
                                                :class="[
                                                  'relative cursor-default select-none py-2 pl-3 pr-9',
                                                  active
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900',
                                                ]">
                                                <div class="flex items-center">
                                                  <span
                                                    :class="[
                                                      'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                                      address.is_primary
                                                        ? 'bg-green-400'
                                                        : 'bg-gray-200',
                                                    ]"
                                                    aria-hidden="true" />
                                                  <span
                                                    :class="[
                                                      'ml-3 truncate',
                                                      selected && 'font-semibold',
                                                    ]">
                                                    <span>
                                                      {{ address.line_1 }}
                                                    </span>
                                                    <span v-if="address.line_2">
                                                      ,
                                                      {{ address.line_2 }}
                                                    </span>
                                                    <span v-if="address.line_3">
                                                      ,
                                                      {{ address.line_3 }}
                                                    </span>
                                                    <span class="sr-only">
                                                      is
                                                      {{
                                                        address.is_primary
                                                          ? 'online'
                                                          : 'offline'
                                                      }}
                                                    </span>
                                                  </span>
                                                </div>

                                                <span
                                                  v-if="selected"
                                                  :class="[
                                                    'absolute inset-y-0 right-0 flex items-center pr-4',
                                                    active
                                                      ? 'text-white'
                                                      : 'text-indigo-600',
                                                  ]">
                                                  <CheckIcon
                                                    aria-hidden="true"
                                                    class="h-5 w-5" />
                                                </span>
                                              </li>
                                            </ul>
                                          </ComboboxOption>
                                        </ComboboxOptions>
                                      </div>
                                    </div>
                                  </Combobox>
                                </div>
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div v-if="selected_transaction.deal_ticket.is_active">
                                <div class="text-green-400">Deal Ticket is Active</div>
                                <div
                                  v-if="selected_transaction.a_mq"
                                  class="font-bold text-indigo-400">
                                  Approved MQ:
                                  {{ selected_transaction.a_mq }}_b
                                </div>
                              </div>

                              <div
                                v-else
                                class="text-red-400">
                                Deal Ticket Not Active
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.customer_order_number_2"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.supplier_loading_number_2"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.no_units_outgoing_2"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.transport_cost_2"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.selling_price_2"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>
                          </tr>

                          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              3
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <TransactionCustomerSelect
                                v-model="combined_Form.customer_id_3"
                                :customers="filteredCustomers3"
                                label="Customer 3" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  <Combobox
                                    v-model="combined_Form.delivery_address_id_3"
                                    as="div">
                                    <div class="relative mt-2">
                                      <ComboboxInput
                                        :display-value="(address) => address?.line_1"
                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        @change="
                                          deliveryAddressQuery3 = $event.target.value
                                        " />
                                      <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                        <ChevronUpDownIcon
                                          aria-hidden="true"
                                          class="h-5 w-5 text-gray-400" />
                                      </ComboboxButton>

                                      <div v-if="filteredDeliveryAddress3 != null">
                                        <ComboboxOptions
                                          v-if="filteredDeliveryAddress3.length > 0"
                                          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                          <ComboboxOption
                                            v-for="address in filteredDeliveryAddress3"
                                            :key="address.id"
                                            v-slot="{ active, selected }"
                                            :value="address"
                                            as="template">
                                            <ul>
                                              <li
                                                :class="[
                                                  'relative cursor-default select-none py-2 pl-3 pr-9',
                                                  active
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900',
                                                ]">
                                                <div class="flex items-center">
                                                  <span
                                                    :class="[
                                                      'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                                      address.is_primary
                                                        ? 'bg-green-400'
                                                        : 'bg-gray-200',
                                                    ]"
                                                    aria-hidden="true" />
                                                  <span
                                                    :class="[
                                                      'ml-3 truncate',
                                                      selected && 'font-semibold',
                                                    ]">
                                                    <span>
                                                      {{ address.line_1 }}
                                                    </span>
                                                    <span v-if="address.line_2">
                                                      ,
                                                      {{ address.line_2 }}
                                                    </span>
                                                    <span v-if="address.line_3">
                                                      ,
                                                      {{ address.line_3 }}
                                                    </span>
                                                    <span class="sr-only">
                                                      is
                                                      {{
                                                        address.is_primary
                                                          ? 'online'
                                                          : 'offline'
                                                      }}
                                                    </span>
                                                  </span>
                                                </div>

                                                <span
                                                  v-if="selected"
                                                  :class="[
                                                    'absolute inset-y-0 right-0 flex items-center pr-4',
                                                    active
                                                      ? 'text-white'
                                                      : 'text-indigo-600',
                                                  ]">
                                                  <CheckIcon
                                                    aria-hidden="true"
                                                    class="h-5 w-5" />
                                                </span>
                                              </li>
                                            </ul>
                                          </ComboboxOption>
                                        </ComboboxOptions>
                                      </div>
                                    </div>
                                  </Combobox>
                                </div>
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div v-if="selected_transaction.deal_ticket.is_active">
                                <div class="text-green-400">Deal Ticket is Active</div>
                                <div
                                  v-if="selected_transaction.a_mq"
                                  class="font-bold text-indigo-400">
                                  Approved MQ:
                                  {{ selected_transaction.a_mq }}_c
                                </div>
                              </div>

                              <div
                                v-else
                                class="text-red-400">
                                Deal Ticket Not Active
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.customer_order_number_3"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.supplier_loading_number_3"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.no_units_outgoing_3"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.transport_cost_3"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.selling_price_3"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>
                          </tr>

                          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              4
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <TransactionCustomerSelect
                                v-model="combined_Form.customer_id_4"
                                :customers="filteredCustomers4"
                                label="Customer 4" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  <Combobox
                                    v-model="combined_Form.delivery_address_id_4"
                                    as="div">
                                    <div class="relative mt-2">
                                      <ComboboxInput
                                        :display-value="(address) => address?.line_1"
                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        @change="
                                          deliveryAddressQuery4 = $event.target.value
                                        " />
                                      <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                        <ChevronUpDownIcon
                                          aria-hidden="true"
                                          class="h-5 w-5 text-gray-400" />
                                      </ComboboxButton>

                                      <div v-if="filteredDeliveryAddress4 != null">
                                        <ComboboxOptions
                                          v-if="filteredDeliveryAddress4.length > 0"
                                          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                          <ComboboxOption
                                            v-for="address in filteredDeliveryAddress4"
                                            :key="address.id"
                                            v-slot="{ active, selected }"
                                            :value="address"
                                            as="template">
                                            <ul>
                                              <li
                                                :class="[
                                                  'relative cursor-default select-none py-2 pl-3 pr-9',
                                                  active
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900',
                                                ]">
                                                <div class="flex items-center">
                                                  <span
                                                    :class="[
                                                      'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                                      address.is_primary
                                                        ? 'bg-green-400'
                                                        : 'bg-gray-200',
                                                    ]"
                                                    aria-hidden="true" />
                                                  <span
                                                    :class="[
                                                      'ml-3 truncate',
                                                      selected && 'font-semibold',
                                                    ]">
                                                    <span>
                                                      {{ address.line_1 }}
                                                    </span>
                                                    <span v-if="address.line_2">
                                                      ,
                                                      {{ address.line_2 }}
                                                    </span>
                                                    <span v-if="address.line_3">
                                                      ,
                                                      {{ address.line_3 }}
                                                    </span>
                                                    <span class="sr-only">
                                                      is
                                                      {{
                                                        address.is_primary
                                                          ? 'online'
                                                          : 'offline'
                                                      }}
                                                    </span>
                                                  </span>
                                                </div>

                                                <span
                                                  v-if="selected"
                                                  :class="[
                                                    'absolute inset-y-0 right-0 flex items-center pr-4',
                                                    active
                                                      ? 'text-white'
                                                      : 'text-indigo-600',
                                                  ]">
                                                  <CheckIcon
                                                    aria-hidden="true"
                                                    class="h-5 w-5" />
                                                </span>
                                              </li>
                                            </ul>
                                          </ComboboxOption>
                                        </ComboboxOptions>
                                      </div>
                                    </div>
                                  </Combobox>
                                </div>
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div v-if="selected_transaction.deal_ticket.is_active">
                                <div class="text-green-400">Deal Ticket is Active</div>
                                <div
                                  v-if="selected_transaction.a_mq"
                                  class="font-bold text-indigo-400">
                                  Approved MQ:
                                  {{ selected_transaction.a_mq }}_d
                                </div>
                              </div>

                              <div
                                v-else
                                class="text-red-400">
                                Deal Ticket Not Active
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.customer_order_number_4"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.supplier_loading_number_4"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.no_units_outgoing_4"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.transport_cost_4"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.selling_price_4"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>
                          </tr>

                          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              5
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <TransactionCustomerSelect
                                v-model="combined_Form.customer_id_5"
                                :customers="filteredCustomers5"
                                label="Customer 5" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  <Combobox
                                    v-model="combined_Form.delivery_address_id_5"
                                    as="div">
                                    <div class="relative mt-2">
                                      <ComboboxInput
                                        :display-value="(address) => address?.line_1"
                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        @change="
                                          deliveryAddressQuery5 = $event.target.value
                                        " />
                                      <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                        <ChevronUpDownIcon
                                          aria-hidden="true"
                                          class="h-5 w-5 text-gray-400" />
                                      </ComboboxButton>

                                      <div v-if="filteredDeliveryAddress5 != null">
                                        <ComboboxOptions
                                          v-if="filteredDeliveryAddress5.length > 0"
                                          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                          <ComboboxOption
                                            v-for="address in filteredDeliveryAddress5"
                                            :key="address.id"
                                            v-slot="{ active, selected }"
                                            :value="address"
                                            as="template">
                                            <ul>
                                              <li
                                                :class="[
                                                  'relative cursor-default select-none py-2 pl-3 pr-9',
                                                  active
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900',
                                                ]">
                                                <div class="flex items-center">
                                                  <span
                                                    :class="[
                                                      'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                                      address.is_primary
                                                        ? 'bg-green-400'
                                                        : 'bg-gray-200',
                                                    ]"
                                                    aria-hidden="true" />
                                                  <span
                                                    :class="[
                                                      'ml-3 truncate',
                                                      selected && 'font-semibold',
                                                    ]">
                                                    <span>
                                                      {{ address.line_1 }}
                                                    </span>
                                                    <span v-if="address.line_2">
                                                      ,
                                                      {{ address.line_2 }}
                                                    </span>
                                                    <span v-if="address.line_3">
                                                      ,
                                                      {{ address.line_3 }}
                                                    </span>
                                                    <span class="sr-only">
                                                      is
                                                      {{
                                                        address.is_primary
                                                          ? 'online'
                                                          : 'offline'
                                                      }}
                                                    </span>
                                                  </span>
                                                </div>

                                                <span
                                                  v-if="selected"
                                                  :class="[
                                                    'absolute inset-y-0 right-0 flex items-center pr-4',
                                                    active
                                                      ? 'text-white'
                                                      : 'text-indigo-600',
                                                  ]">
                                                  <CheckIcon
                                                    aria-hidden="true"
                                                    class="h-5 w-5" />
                                                </span>
                                              </li>
                                            </ul>
                                          </ComboboxOption>
                                        </ComboboxOptions>
                                      </div>
                                    </div>
                                  </Combobox>
                                </div>
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div v-if="selected_transaction.deal_ticket.is_active">
                                <div class="text-green-400">Deal Ticket is Active</div>
                                <div
                                  v-if="selected_transaction.a_mq"
                                  class="font-bold text-indigo-400">
                                  Approved MQ:
                                  {{ selected_transaction.a_mq }}_e
                                </div>
                              </div>

                              <div
                                v-else
                                class="text-red-400">
                                Deal Ticket Not Active
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.customer_order_number_5"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.supplier_loading_number_5"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.no_units_outgoing_5"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.transport_cost_5"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <input
                                v-model="combined_Form.selling_price_5"
                                class="block w-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </td>
                          </tr>

                          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"></td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900"></td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900"></td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900"></td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900"></td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              Balances to allocate:
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div class="m-2">
                                {{ no_units_to_allocate }}
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div class="m-2">
                                {{ NiceNumber(transport_cost_to_allocate) }}
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div class="m-2">
                                {{ NiceNumber(selling_price_to_allocate) }}
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div v-if="selectedTabId === 3">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Transport details
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <Combobox
                                  v-model="combined_Form.transporter_id"
                                  as="div">
                                  <div class="relative mt-2">
                                    <ComboboxInput
                                      :display-value="
                                        (transporter) => transporter?.last_legal_name
                                      "
                                      class="w-70 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      @change="transporterQuery = $event.target.value" />
                                    <ComboboxButton
                                      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                      <ChevronUpDownIcon
                                        aria-hidden="true"
                                        class="h-5 w-5 text-gray-400" />
                                    </ComboboxButton>

                                    <ComboboxOptions
                                      v-if="filteredTransporters.length > 0"
                                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                      <ComboboxOption
                                        v-for="transporter in filteredTransporters"
                                        :key="transporter.id"
                                        v-slot="{ active, selected }"
                                        :value="transporter"
                                        as="template">
                                        <ul>
                                          <li
                                            :class="[
                                              'relative cursor-default select-none py-2 pl-3 pr-9',
                                              active
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-gray-900',
                                            ]">
                                            <span
                                              :class="[
                                                'block truncate',
                                                selected && 'font-semibold',
                                              ]">
                                              {{ transporter.last_legal_name }}
                                            </span>

                                            <span
                                              v-if="selected"
                                              :class="[
                                                'absolute inset-y-0 right-0 flex items-center pr-4',
                                                active ? 'text-white' : 'text-indigo-600',
                                              ]">
                                              <CheckIcon
                                                aria-hidden="true"
                                                class="h-5 w-5" />
                                            </span>
                                          </li>
                                        </ul>
                                      </ComboboxOption>
                                    </ComboboxOptions>
                                  </div>
                                </Combobox>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dd class="flex items-start gap-x-2">
                              <div class="w-70">
                                <VueDatePicker
                                  v-model="combined_Form.transport_date_earliest"
                                  :format="formatEarly"
                                  :teleport="true"></VueDatePicker>

                                <div class="ml-3 text-sm text-indigo-400">
                                  Transport date earliest
                                </div>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dd class="flex items-start gap-x-2">
                              <div class="w-70">
                                <VueDatePicker
                                  v-model="combined_Form.transport_date_latest"
                                  :format="formatLate"
                                  :teleport="true"></VueDatePicker>

                                <div class="ml-3 text-sm text-indigo-400">
                                  Transport date latest
                                </div>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">No loads</dt>
                            <dd class="flex items-start gap-x-2">
                              <input
                                v-model="combined_Form.number_loads"
                                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="text" />
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Driver & vehicles
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div>
                            <div class="mt-1">
                              <label
                                class="block text-sm font-medium leading-6 text-gray-900">
                                Driver
                              </label>

                              <div>
                                <Combobox
                                  v-model="combined_Form.regular_driver_id"
                                  as="div">
                                  <div class="relative mt-2">
                                    <ComboboxInput
                                      :display-value="
                                        (driver) =>
                                          driver?.first_name + ' ' + driver?.last_name
                                      "
                                      class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      @change="
                                        selectedDriverQuery = $event.target.value
                                      " />
                                    <ComboboxButton
                                      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                      <ChevronUpDownIcon
                                        aria-hidden="true"
                                        class="h-5 w-5 text-gray-400" />
                                    </ComboboxButton>

                                    <ComboboxOptions
                                      v-if="filteredSelectedDrivers.length > 0"
                                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                      <ComboboxOption
                                        v-for="driver in filteredSelectedDrivers"
                                        :key="driver.id"
                                        v-slot="{ active, selected }"
                                        :value="driver"
                                        as="template">
                                        <ul>
                                          <li
                                            :class="[
                                              'relative cursor-default select-none py-2 pl-3 pr-9',
                                              active
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-gray-900',
                                            ]">
                                            <span
                                              :class="[
                                                'block truncate',
                                                selected && 'font-semibold',
                                              ]">
                                              {{ driver.first_name }}
                                              {{ driver.last_name }}
                                            </span>
                                            <span
                                              v-if="selected"
                                              :class="[
                                                'absolute inset-y-0 right-0 flex items-center pr-4',
                                                active ? 'text-white' : 'text-indigo-600',
                                              ]">
                                              <CheckIcon
                                                aria-hidden="true"
                                                class="h-5 w-5" />
                                            </span>
                                          </li>
                                        </ul>
                                      </ComboboxOption>
                                    </ComboboxOptions>
                                  </div>
                                </Combobox>
                              </div>

                              <InputError
                                :message="combined_Form.errors.regular_driver_id"
                                class="mt-2" />
                              <div
                                class="ml-3 underline text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                @click="toggleShowDriver">
                                + Add driver
                              </div>

                              <div
                                v-if="showDriver"
                                class="m-4 p-4 border-solid border-2 border-green-500">
                                <div
                                  class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                  <!-- First name -->
                                  <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                    <div>
                                      <label
                                        class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                                        First name
                                      </label>
                                    </div>
                                    <div class="sm:col-span-2">
                                      <input
                                        id="name"
                                        v-model="driverForm.first_name"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        name="name"
                                        type="text" />
                                      <InputError
                                        :message="driverForm.errors.first_name"
                                        class="mt-2" />
                                    </div>
                                  </div>

                                  <!-- Last name -->
                                  <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                    <div>
                                      <label
                                        class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                                        Last name
                                      </label>
                                    </div>
                                    <div class="sm:col-span-2">
                                      <input
                                        id="last_name"
                                        v-model="driverForm.last_name"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        name="last_name"
                                        type="text" />
                                      <InputError
                                        :message="driverForm.errors.last_name"
                                        class="mt-2" />
                                    </div>
                                  </div>

                                  <!-- Cell no -->
                                  <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                    <div>
                                      <label
                                        class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                                        Cell no
                                      </label>
                                    </div>
                                    <div class="sm:col-span-2">
                                      <input
                                        id="cell_no"
                                        v-model="driverForm.cell_no"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        name="cell_no"
                                        type="text" />
                                      <InputError
                                        :message="driverForm.errors.cell_no"
                                        class="mt-2" />
                                    </div>
                                  </div>

                                  <!-- Comment -->
                                  <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                    <div></div>
                                    <div class="sm:col-span-4">
                                      <AreaInput
                                        id="comments2"
                                        v-model="driverForm.comment"
                                        :rows="6"
                                        class="mt-1 block w-full"
                                        placeholder="Optional comments..."
                                        type="text" />
                                      <InputError
                                        :message="driverForm.errors.comment"
                                        class="mt-2" />
                                    </div>
                                  </div>

                                  <!-- Action buttons -->
                                  <div
                                    class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                                    <div class="flex justify-end space-x-3">
                                      <button
                                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                        type="button"
                                        @click="toggleShowDriver">
                                        Cancel
                                      </button>
                                      <button
                                        class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                        type="button"
                                        @click="createProduct">
                                        Create
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="mt-1">
                              <label
                                class="block text-sm font-medium leading-6 text-gray-900">
                                Vehicle
                              </label>

                              <div>
                                <Combobox
                                  v-model="combined_Form.regular_vehicle_id"
                                  as="div">
                                  <div class="relative mt-2">
                                    <ComboboxInput
                                      :display-value="
                                        (vehicle) =>
                                          vehicle?.reg_no +
                                          ' ' +
                                          vehicle?.vehicle_type.name
                                      "
                                      class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      @change="
                                        selectedVehicleQuery = $event.target.value
                                      " />
                                    <ComboboxButton
                                      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                      <ChevronUpDownIcon
                                        aria-hidden="true"
                                        class="h-5 w-5 text-gray-400" />
                                    </ComboboxButton>

                                    <ComboboxOptions
                                      v-if="filteredSelectedVehicles.length > 0"
                                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                      <ComboboxOption
                                        v-for="vehicle in filteredSelectedVehicles"
                                        :key="vehicle.id"
                                        v-slot="{ active, selected }"
                                        :value="vehicle"
                                        as="template">
                                        <ul>
                                          <li
                                            :class="[
                                              'relative cursor-default select-none py-2 pl-3 pr-9',
                                              active
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-gray-900',
                                            ]">
                                            <span
                                              :class="[
                                                'block truncate',
                                                selected && 'font-semibold',
                                              ]">
                                              {{ vehicle.reg_no }}
                                              {{ vehicle.vehicle_type.name }}
                                            </span>
                                            <span
                                              v-if="selected"
                                              :class="[
                                                'absolute inset-y-0 right-0 flex items-center pr-4',
                                                active ? 'text-white' : 'text-indigo-600',
                                              ]">
                                              <CheckIcon
                                                aria-hidden="true"
                                                class="h-5 w-5" />
                                            </span>
                                          </li>
                                        </ul>
                                      </ComboboxOption>
                                    </ComboboxOptions>
                                  </div>
                                </Combobox>
                              </div>

                              <InputError
                                :message="combined_Form.errors.regular_driver_id"
                                class="mt-2" />

                              <div
                                class="ml-3 underline text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                @click="toggleShowVehicle">
                                + Add vehicle
                              </div>

                              <div
                                v-if="showVehicle"
                                class="m-4 p-4 border-solid border-2 border-green-500">
                                <div
                                  class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                  <!-- Divider container -->
                                  <div
                                    class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                    <!--  reg no -->
                                    <div
                                      class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                      <div>
                                        <label
                                          class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                                          Reg no
                                        </label>
                                      </div>
                                      <div class="sm:col-span-2">
                                        <input
                                          id="reg_no"
                                          v-model="vehicleForm.reg_no"
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                          name="reg_no"
                                          type="text" />
                                        <InputError
                                          :message="vehicleForm.errors.reg_no"
                                          class="mt-2" />
                                      </div>
                                    </div>

                                    <!--  vehicle type -->
                                    <div
                                      class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                      <div>
                                        <label
                                          class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                                          Vehicle type
                                        </label>
                                      </div>
                                      <div class="sm:col-span-2">
                                        <select
                                          v-model="vehicleForm.vehicle_type_id"
                                          class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                          <option
                                            v-for="n in all_vehicle_types"
                                            :key="n.id"
                                            :value="n.id">
                                            {{ n.name }}
                                          </option>
                                        </select>
                                        <InputError
                                          :message="vehicleForm.errors.vehicle_type_id"
                                          class="mt-2" />
                                      </div>
                                    </div>

                                    <!-- Comment -->
                                    <div
                                      class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                      <div></div>
                                      <div class="sm:col-span-4">
                                        <AreaInput
                                          id="comments"
                                          v-model="vehicleForm.comment"
                                          :rows="6"
                                          class="mt-1 block w-full"
                                          placeholder="Optional comments..."
                                          type="text" />
                                        <InputError
                                          :message="vehicleForm.errors.comment"
                                          class="mt-2" />
                                      </div>
                                    </div>
                                  </div>

                                  <!-- Action buttons -->
                                  <div
                                    class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                                    <div class="flex justify-end space-x-3">
                                      <button
                                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                        type="button"
                                        @click="toggleShowVehicle">
                                        Cancel
                                      </button>
                                      <button
                                        class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                        type="button"
                                        @click="createProductVehicle">
                                        Create
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <dl
                            class="-my-3 mt-3 divide-y divide-gray-100 px-1 py-1 text-sm leading-6">
                            <div>Captured details</div>
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Driver First</dt>
                              <dd class="flex items-start gap-x-2">
                                {{ combined_Form.regular_driver_id.first_name }}
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Driver Last</dt>
                              <dd class="flex items-start gap-x-2">
                                {{ combined_Form.regular_driver_id.last_name }}
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Driver Cell</dt>
                              <dd class="flex items-start gap-x-2">
                                {{ combined_Form.regular_driver_id.cell_no }}
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Vehicle reg</dt>
                              <dd class="flex items-start gap-x-2">
                                {{ combined_Form.regular_vehicle_id.reg_no }}
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Vehicle type</dt>
                              <dd class="flex items-start gap-x-2">
                                {{ combined_Form.regular_vehicle_id.vehicle_type.name }}
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Trailer 1</dt>
                              <dd class="flex items-start gap-x-2">
                                <input
                                  id="trailer_reg_1"
                                  v-model="combined_Form.trailer_reg_1"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="text" />
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Trailer 2</dt>
                              <dd class="flex items-start gap-x-2">
                                <input
                                  id="trailer_reg_2"
                                  v-model="combined_Form.trailer_reg_2"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="text" />
                              </dd>
                            </div>
                          </dl>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Loading & Offloading instructions
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-1 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <div class="mt-2 flex col-span-4">
                              <div class="col">
                                <label
                                  class="block text-sm font-medium leading-6 text-gray-900">
                                  Loading hour from:
                                </label>

                                <select
                                  v-model="combined_Form.loading_hours_from_id"
                                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  <option
                                    v-for="n in props.loading_hour_options"
                                    :key="n.id"
                                    :value="n.id">
                                    {{ n.name }}
                                  </option>
                                </select>

                                <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->
                              </div>

                              <div class="col ml-2">
                                <label
                                  class="block text-sm font-medium leading-6 text-gray-900">
                                  Loading hour to:
                                </label>

                                <select
                                  v-model="combined_Form.loading_hours_to_id"
                                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  <option
                                    v-for="n in props.loading_hour_options"
                                    :key="n.id"
                                    :value="n.id">
                                    {{ n.name }}
                                  </option>
                                </select>

                                <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->
                              </div>
                            </div>
                          </div>

                          <div>
                            <dl
                              class="-my-3 mt-3 divide-y divide-gray-100 px-1 py-1 text-sm leading-6">
                              <div class="flex justify-between gap-x-4 py-1">
                                <dt class="text-gray-500">Contact Person</dt>
                                <dd class="flex items-start gap-x-2">
                                  <input
                                    id="contact1"
                                    v-model="combined_Form.loading_contact"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="text" />
                                </dd>
                              </div>

                              <div class="flex justify-between gap-x-4 py-1">
                                <dt class="text-gray-500">Contact No</dt>
                                <dd class="flex items-start gap-x-2">
                                  <input
                                    id="contact_no_1"
                                    v-model="combined_Form.loading_contact_no"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="text" />
                                </dd>
                              </div>
                            </dl>
                          </div>

                          <div class="flex mt-1 justify-between gap-x-4 py-1">
                            <AreaInput
                              id="loading_instructions"
                              v-model="combined_Form.loading_instructions"
                              :rows="2"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />

                            <InputError
                              :message="combined_Form.errors.customer_notes"
                              class="mt-2" />
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <div class="mt-2 flex col-span-4">
                              <div class="col ml-2">
                                <label
                                  class="block text-sm font-medium leading-6 text-gray-900">
                                  Offloading hour from:
                                </label>

                                <select
                                  v-model="combined_Form.offloading_hours_from_id"
                                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  <option
                                    v-for="n in props.loading_hour_options"
                                    :key="n.id"
                                    :value="n.id">
                                    {{ n.name }}
                                  </option>
                                </select>

                                <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->
                              </div>

                              <div class="col ml-2">
                                <label
                                  class="block text-sm font-medium leading-6 text-gray-900">
                                  Offloading hour to:
                                </label>

                                <select
                                  v-model="combined_Form.offloading_hours_to_id"
                                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  <option
                                    v-for="n in props.loading_hour_options"
                                    :key="n.id"
                                    :value="n.id">
                                    {{ n.name }}
                                  </option>
                                </select>

                                <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->
                              </div>
                            </div>
                          </div>

                          <div>
                            <dl
                              class="-my-3 mt-3 divide-y divide-gray-100 px-1 py-1 text-sm leading-6">
                              <div class="flex justify-between gap-x-4 py-1">
                                <dt class="text-gray-500">Contact Person</dt>
                                <dd class="flex items-start gap-x-2">
                                  <input
                                    id="contact2"
                                    v-model="combined_Form.offloading_contact"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="text" />
                                </dd>
                              </div>

                              <div class="flex justify-between gap-x-4 py-1">
                                <dt class="text-gray-500">Contact No</dt>
                                <dd class="flex items-start gap-x-2">
                                  <input
                                    id="contact_no_2"
                                    v-model="combined_Form.offloading_contact_no"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="text" />
                                </dd>
                              </div>
                            </dl>
                          </div>

                          <div class="flex justify-between gap-x-4 mb-2 py-1">
                            <AreaInput
                              id="offloading_instructions"
                              v-model="combined_Form.offloading_instructions"
                              :rows="2"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Transport notes
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <AreaInput
                              id="transport_notes"
                              v-model="combined_Form.transport_notes"
                              :rows="6"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />

                            <InputError
                              :message="combined_Form.errors.customer_notes"
                              class="mt-2" />
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transporter contacts</dt>
                            <dd class="text-gray-700">
                              <div v-if="selected_transaction.transporter.contactable">
                                <div
                                  v-for="contact in selected_transaction.transporter
                                    .contactable"
                                  :key="contact.id">
                                  <div>
                                    <Link
                                      :href="route('contact.show', contact.id)"
                                      class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                      <span>{{ contact.first_name }}</span>
                                      <span>{{ contact.last_legal_name }}</span>
                                    </Link>

                                    <div v-if="contact.numberable">
                                      <span>T:</span>
                                      <span
                                        v-for="number in contact.numberable"
                                        :key="number.id">
                                        {{ number.value }}
                                      </span>
                                    </div>

                                    <div v-if="contact.emailable">
                                      <span>E:</span>
                                      <span
                                        v-for="email in contact.emailable"
                                        :key="email.id">
                                        {{ email.value }}
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Customer contacts</dt>
                            <dd class="text-gray-700">
                              <div v-if="selected_transaction.customer.contactable">
                                <div
                                  v-for="contact in selected_transaction.customer
                                    .contactable"
                                  :key="contact.id">
                                  <div>
                                    <Link
                                      :href="route('contact.show', contact.id)"
                                      class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                      <span>{{ contact.first_name }}</span>
                                      <span>{{ contact.last_legal_name }}</span>
                                    </Link>

                                    <div v-if="contact.numberable">
                                      <span>T:</span>
                                      <span
                                        v-for="number in contact.numberable"
                                        :key="number.id">
                                        {{ number.value }}
                                      </span>
                                    </div>

                                    <div v-if="contact.emailable">
                                      <span>E:</span>
                                      <span
                                        v-for="email in contact.emailable"
                                        :key="email.id">
                                        {{ email.value }}
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Supplier contacts</dt>
                            <dd class="text-gray-700">
                              <div v-if="selected_transaction.supplier.contactable">
                                <div
                                  v-for="contact in selected_transaction.supplier
                                    .contactable"
                                  :key="contact.id">
                                  <div>
                                    <Link
                                      :href="route('contact.show', contact.id)"
                                      class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                      <span>{{ contact.first_name }}</span>
                                      <span>{{ contact.last_legal_name }}</span>
                                    </Link>

                                    <div v-if="contact.numberable">
                                      <span>T:</span>
                                      <span
                                        v-for="number in contact.numberable"
                                        :key="number.id">
                                        {{ number.value }}
                                      </span>
                                    </div>

                                    <div v-if="contact.emailable">
                                      <span>E:</span>
                                      <span
                                        v-for="email in contact.emailable"
                                        :key="email.id">
                                        {{ email.value }}
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 4">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Buying price
                          </div>
                          <div class="text-sm font-light text-gray-900">
                            (From Supplier)
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Supplier</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.supplier.last_legal_name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Product</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.product.name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Supply Packaging</dt>
                            <dd class="text-gray-700">
                              <Combobox
                                v-model="combined_Form.packaging_incoming_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(packaging) => packaging?.name"
                                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      packageIncomingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredPackageIncoming.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="packaging in filteredPackageIncoming"
                                      :key="packaging.id"
                                      v-slot="{ active, selected }"
                                      :value="packaging"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ packaging.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Billing Units</dt>
                            <dd class="text-gray-700">
                              <Combobox
                                v-model="combined_Form.billing_units_incoming_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(units) => units?.name"
                                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      billingUnitsIncomingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredBillingUnitsIncoming.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="units in filteredBillingUnitsIncoming"
                                      :key="units.id"
                                      v-slot="{ active, selected }"
                                      :value="units"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ units.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">No Units</dt>
                            <dd class="text-gray-700">
                              {{ selected_transaction.transport_load.no_units_incoming }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Supply Weight (tons)</dt>
                            <dd class="text-gray-700">
                              {{
                                selected_transaction.transport_finance.weight_ton_incoming
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Cost Price / Unit</dt>
                            <dd class="text-gray-700">
                              <input
                                v-model="combined_Form.cost_price_per_unit"
                                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Total Supplier Cost</dt>
                            <dd class="text-gray-700">
                              {{
                                NiceNumber(
                                  selected_transaction.transport_finance.cost_price
                                )
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Cost Price / Ton</dt>
                            <dd class="text-gray-700">
                              {{
                                NiceNumber(
                                  selected_transaction.transport_finance
                                    .cost_price_per_ton
                                )
                              }}
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Transport costs
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transport rate basis</dt>
                            <dd class="text-gray-700">
                              <div>
                                <select
                                  v-model="combined_Form.transport_rate_basis_id"
                                  class="mt-2 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  <option
                                    v-for="n in props.all_transport_rates"
                                    :key="n.id"
                                    :value="n.id">
                                    {{ n.name }}
                                  </option>
                                </select>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transport rate</dt>
                            <dd class="text-gray-700">
                              <div>
                                <input
                                  v-model="combined_Form.transport_rate"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="number" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transport rate / Ton</dt>
                            <dd class="text-gray-700">
                              {{
                                NiceNumber(
                                  selected_transaction.transport_finance
                                    .transport_rate_per_ton
                                )
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transport costs</dt>
                            <dd class="text-gray-700">
                              {{
                                NiceNumber(
                                  selected_transaction.transport_finance.transport_cost
                                )
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Load Insurance per ton</dt>
                            <dd class="text-gray-700">
                              {{
                                NiceNumber(
                                  selected_transaction.transport_finance
                                    .load_insurance_per_ton
                                )
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Transport cost in price</dt>
                            <dd class="text-gray-700">
                              <SwitchGroup
                                as="div"
                                class="flex m-2 items-center">
                                <Switch
                                  v-model="combined_Form.is_transport_costs_inc_price"
                                  :class="[
                                    combined_Form.is_transport_costs_inc_price
                                      ? 'bg-indigo-600'
                                      : 'bg-gray-200',
                                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                  ]">
                                  <span
                                    :class="[
                                      combined_Form.is_transport_costs_inc_price
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    ]"
                                    aria-hidden="true" />
                                </Switch>
                              </SwitchGroup>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Selling Price
                          </div>
                          <div class="text-sm font-light text-gray-900">
                            (To Customer)
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Customer</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.customer.last_legal_name }}
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Product</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{ selected_transaction.product.name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Packaging</dt>
                            <dd class="flex items-start gap-x-2">
                              <Combobox
                                v-model="combined_Form.packaging_outgoing_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(packaging) => packaging?.name"
                                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      packageOutgoingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredPackageOutgoing.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="packaging in filteredPackageOutgoing"
                                      :key="packaging.id"
                                      v-slot="{ active, selected }"
                                      :value="packaging"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ packaging.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Billing units</dt>
                            <dd class="flex items-start gap-x-2">
                              <Combobox
                                v-model="combined_Form.billing_units_outgoing_id"
                                as="div">
                                <div class="relative mt-2">
                                  <ComboboxInput
                                    :display-value="(units) => units?.name"
                                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    @change="
                                      billingUnitsOutgoingQuery = $event.target.value
                                    " />
                                  <ComboboxButton
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                    <ChevronUpDownIcon
                                      aria-hidden="true"
                                      class="h-5 w-5 text-gray-400" />
                                  </ComboboxButton>

                                  <ComboboxOptions
                                    v-if="filteredBillingUnitsOutgoing.length > 0"
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                      v-for="units in filteredBillingUnitsOutgoing"
                                      :key="units.id"
                                      v-slot="{ active, selected }"
                                      :value="units"
                                      as="template">
                                      <ul>
                                        <li
                                          :class="[
                                            'relative cursor-default select-none py-2 pl-3 pr-9',
                                            active
                                              ? 'bg-indigo-600 text-white'
                                              : 'text-gray-900',
                                          ]">
                                          <span
                                            :class="[
                                              'block truncate',
                                              selected && 'font-semibold',
                                            ]">
                                            {{ units.name }}
                                          </span>

                                          <span
                                            v-if="selected"
                                            :class="[
                                              'absolute inset-y-0 right-0 flex items-center pr-4',
                                              active ? 'text-white' : 'text-indigo-600',
                                            ]">
                                            <CheckIcon
                                              aria-hidden="true"
                                              class="h-5 w-5" />
                                          </span>
                                        </li>
                                      </ul>
                                    </ComboboxOption>
                                  </ComboboxOptions>
                                </div>
                              </Combobox>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">No Units</dt>
                            <dd class="text-gray-700">
                              {{ selected_transaction.transport_load.no_units_outgoing }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Weight (tons)</dt>
                            <dd class="text-gray-700">
                              {{
                                selected_transaction.transport_finance.weight_ton_outgoing
                              }}
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Price / Unit</dt>
                            <dd class="text-gray-700">
                              <input
                                v-model="combined_Form.selling_price_per_unit"
                                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="number" />
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Total Selling Price</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_finance.selling_price
                                  )
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Selling Price / Ton</dt>
                            <dd class="text-gray-700">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_finance
                                      .selling_price_per_ton
                                  )
                                }}
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Margin Calculation
                          </div>
                        </div>

                        <div>
                          <dl
                            class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                            <h3
                              class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                              Additional costs:
                            </h3>

                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">
                                <input
                                  v-model="combined_Form.additional_cost_desc_1"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  placeholder="Description..."
                                  type="text" />
                              </dt>
                              <dd class="text-gray-700">
                                <div>
                                  <input
                                    v-model="combined_Form.additional_cost_1"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="number" />
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">
                                <input
                                  v-model="combined_Form.additional_cost_desc_2"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  placeholder="Description..."
                                  type="text" />
                              </dt>
                              <dd class="text-gray-700">
                                <div>
                                  <input
                                    v-model="combined_Form.additional_cost_2"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="number" />
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">
                                <input
                                  v-model="combined_Form.additional_cost_desc_3"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  placeholder="Description..."
                                  type="text" />
                              </dt>
                              <dd class="text-gray-700">
                                <div>
                                  <input
                                    v-model="combined_Form.additional_cost_3"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    type="number" />
                                </div>
                              </dd>
                            </div>

                            <div>
                              <h3
                                class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                                Adjusted GP:
                              </h3>
                              <div class="flex justify-between gap-x-4 py-3">
                                <dt class="text-gray-500">
                                  <input
                                    v-model="combined_Form.adjusted_gp_notes"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Description..."
                                    type="text" />
                                </dt>
                                <dd class="text-gray-700">
                                  <div>
                                    <input
                                      v-model="combined_Form.adjusted_gp"
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      type="number" />
                                  </div>
                                </dd>
                              </div>
                            </div>
                          </dl>

                          <div class="m-2 p-2">
                            <table class="min-w-full divide-y divide-gray-300">
                              <thead>
                                <tr class="divide-x divide-gray-200">
                                  <th
                                    class="py-2 pl-4 pr-4 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    scope="col">
                                    Item
                                  </th>
                                  <th
                                    class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    scope="col">
                                    Plan
                                  </th>
                                  <th
                                    class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    scope="col">
                                    Actual
                                  </th>
                                </tr>
                              </thead>
                              <tbody class="divide-y divide-gray-200 bg-white">
                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Tons In
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .weight_ton_incoming
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .weight_ton_incoming_actual
                                    }}
                                  </td>
                                </tr>
                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Tons Out
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .weight_ton_outgoing
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .weight_ton_outgoing_actual
                                    }}
                                  </td>
                                </tr>
                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Selling Price
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .selling_price
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .selling_price_actual
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Cost Price
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance.cost_price
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .cost_price_actual
                                      )
                                    }}
                                  </td>
                                </tr>

                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Transport Cost
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .transport_cost
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .transport_cost_actual
                                      )
                                    }}
                                  </td>
                                </tr>

                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    Total Cost Price
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .total_cost_price
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .total_cost_price_actual
                                      )
                                    }}
                                  </td>
                                </tr>

                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    GP
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .gross_profit
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .gross_profit_actual
                                      )
                                    }}
                                  </td>
                                </tr>

                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    GP / Ton
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .gross_profit_per_ton
                                      )
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      NiceNumber(
                                        selected_transaction.transport_finance
                                          .gross_profit_per_ton_actual
                                      )
                                    }}
                                  </td>
                                </tr>

                                <tr class="divide-x divide-gray-200">
                                  <td
                                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                                    GP %
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .gross_profit_percent
                                    }}
                                  </td>
                                  <td
                                    class="whitespace-nowrap text-right text-sm text-gray-500">
                                    {{
                                      selected_transaction.transport_finance
                                        .gross_profit_percent_actual
                                    }}
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 5">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8"
                      role="list">
                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Invoice
                          </div>
                          <div class="text-sm font-light text-gray-900">(Basis)</div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Supplier</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ selected_transaction.supplier.last_legal_name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Product</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ selected_transaction.product.name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Customer</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ selected_transaction.customer.last_legal_name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Order no</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ combined_Form.customer_order_number }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Billing basis</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ selected_transaction.customer.invoice_basis.value }}
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Invoice
                          </div>
                          <div class="text-sm font-light text-gray-900">(Inputs)</div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">
                              Weighbridge Certificate received
                            </dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-1 items-center">
                                  <Switch
                                    v-model="
                                      combined_Form.is_weighbridge_certificate_received
                                    "
                                    :class="[
                                      combined_Form.is_weighbridge_certificate_received
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_weighbridge_certificate_received
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Transporter</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{ selected_transaction.transporter.last_legal_name }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Vehicle registration</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{
                                  selected_transaction.transport_job
                                    .transport_driver_vehicle[0].vehicle.reg_no
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Weighbridge Upload</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <input
                                  v-model="combined_Form.weighbridge_upload_weight"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="number" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Weighbridge Offload</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <input
                                  v-model="combined_Form.weighbridge_offload_weight"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="number" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Weighbridge variance</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{
                                  NiceVariance(
                                    combined_Form.weighbridge_upload_weight,
                                    combined_Form.weighbridge_offload_weight
                                  )
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <AreaInput
                              v-model="combined_Form.notes"
                              :rows="4"
                              class="mt-1 block w-full"
                              placeholder="Optional comments..."
                              type="text" />
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Invoice
                          </div>
                          <div class="text-sm font-light text-gray-900">
                            (and Debtor control)
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice No</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <input
                                  v-model="combined_Form.invoice_no"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="text" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice date</dt>
                            <dd class="flex items-start gap-x-2">
                              <div class="w-48">
                                <VueDatePicker
                                  v-model="combined_Form.invoice_date"
                                  :format="formatInvoiceDate"
                                  :teleport="true"></VueDatePicker>

                                <div class="ml-3 text-sm text-indigo-400">
                                  Invoice date
                                </div>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice pay by date</dt>

                            <dd class="flex items-start gap-x-2">
                              <div class="w-48">
                                <VueDatePicker
                                  v-model="combined_Form.invoice_pay_by_date"
                                  :format="formatInvoicePayByDay"
                                  :teleport="true"></VueDatePicker>
                                <div class="ml-3 text-sm text-indigo-400">
                                  Invoice pay by date
                                  <span v-if="paymentTerms">
                                    ({{ paymentTerms.value }}
                                    /
                                    {{ paymentTerms.days }}
                                    days)
                                  </span>
                                </div>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice paid date</dt>

                            <dd class="flex items-start gap-x-2">
                              <div class="w-48">
                                <VueDatePicker
                                  v-model="combined_Form.invoice_paid_date"
                                  :format="formatInvoicePdDay"
                                  :teleport="true"></VueDatePicker>
                                <div class="ml-3 text-sm text-indigo-400">
                                  Invoice paid date
                                </div>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice amount</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <input
                                  v-model="combined_Form.invoice_amount"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="number" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoice amount paid</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <input
                                  v-model="combined_Form.invoice_amount_paid"
                                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                  type="number" />
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Balance overdue</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_invoice
                                      .transport_invoice_details.overdue
                                  )
                                }}
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Outstanding</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                {{
                                  NiceNumber(
                                    selected_transaction.transport_invoice
                                      .transport_invoice_details.outstanding
                                  )
                                }}
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Transaction done</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-1 items-center">
                                  <Switch
                                    v-model="combined_Form.is_transaction_done"
                                    :class="[
                                      combined_Form.is_transaction_done
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_transaction_done
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Phase</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <div class="">
                                  <select
                                    v-model="combined_Form.status_id"
                                    class="mt-2 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option
                                      v-for="n in props.all_invoice_statuses"
                                      :key="n.id"
                                      :value="n.id">
                                      {{ n.name }}
                                    </option>
                                  </select>
                                </div>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Active</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-1 items-center">
                                  <Switch
                                    v-model="combined_Form.is_active"
                                    :class="[
                                      combined_Form.is_active
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_active
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Invoiced</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-1 items-center">
                                  <Switch
                                    v-model="combined_Form.is_invoiced"
                                    :class="[
                                      combined_Form.is_invoiced
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_invoiced
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Paid</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-1 items-center">
                                  <Switch
                                    v-model="combined_Form.is_invoice_paid"
                                    :class="[
                                      combined_Form.is_invoice_paid
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_invoice_paid
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>
                        </dl>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 6">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <li
                        v-if="selected_transaction.contract_type_id != 1"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Approvals
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="mb-2">
                            <div
                              v-if="selected_transaction.deal_ticket.is_active"
                              class="mt-3">
                              <div class="text-green-400">Deal Ticket is Active</div>
                              <div
                                v-if="selected_transaction.a_mq"
                                class="font-bold text-indigo-400">
                                MQ:
                                {{ selected_transaction.a_mq }}
                              </div>
                              <div class="text-indigo-400">
                                ID:{{ selected_transaction.id }}
                              </div>
                              <div
                                v-if="selected_transaction.old_id"
                                class="mb-2 text-gray-400">
                                (OLD:{{ selected_transaction.old_id }})
                              </div>
                            </div>

                            <div
                              v-else
                              class="text-red-400 mt-3">
                              Deal Ticket Not Active
                            </div>
                          </div>

                          <div>
                            <div class="col-span-4">
                              <div class="">
                                <div class="">
                                  <div class="flex-row text-indigo-400 text-lg mb-2">
                                    <span>Trade Rules</span>
                                  </div>
                                  <div class="mt-2 flow-root">
                                    <div
                                      class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                      <div
                                        class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                        <table
                                          class="min-w-full divide-y divide-gray-300">
                                          <thead>
                                            <tr>
                                              <th
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                scope="col">
                                                Rule
                                              </th>
                                              <th
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                scope="col">
                                                Role
                                              </th>
                                              <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                scope="col">
                                                Approved
                                              </th>
                                            </tr>
                                          </thead>
                                          <tbody class="divide-y divide-gray-200">
                                            <tr
                                              v-for="(
                                                n, index
                                              ) in rules_with_approvals.TradeRule"
                                              :key="index">
                                              <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                {{ n.rule }}
                                              </td>

                                              <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                {{ n.role }}
                                              </td>

                                              <td
                                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <ul>
                                                  <li
                                                    v-for="(m, index) in n.approvals"
                                                    v-if="n.approvals.length > 0"
                                                    :key="index">
                                                    <div class="flex">
                                                      <span>
                                                        <check-icon
                                                          class="w-6 h-6 fill-green-300 mr-3" />
                                                      </span>
                                                      <div>
                                                        <span>{{ m.user.name }}</span>
                                                      </div>
                                                    </div>
                                                  </li>

                                                  <div
                                                    v-else
                                                    class="flex">
                                                    <span>
                                                      <XCircleIcon
                                                        class="w-6 h-6 fill-red-400 mr-3" />
                                                    </span>
                                                    <span>None...</span>
                                                  </div>
                                                </ul>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="col-span-4">
                              <div class="">
                                <div class="">
                                  <div class="flex-row text-indigo-400 text-lg mb-2">
                                    <span>Trade Operation Rules</span>
                                  </div>

                                  <div class="sm:flex sm:items-center"></div>
                                  <div class="mt-2 flow-root">
                                    <div
                                      class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                      <div
                                        class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                        <table
                                          class="min-w-full divide-y divide-gray-300">
                                          <thead>
                                            <tr>
                                              <th
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                scope="col">
                                                Rule
                                              </th>
                                              <th
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                scope="col">
                                                Role
                                              </th>
                                              <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                scope="col">
                                                Approved
                                              </th>
                                            </tr>
                                          </thead>
                                          <tbody class="divide-y divide-gray-200">
                                            <tr
                                              v-for="(
                                                n, index
                                              ) in rules_with_approvals.TradeRuleOpp"
                                              :key="index">
                                              <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                {{ n.rule }}
                                              </td>

                                              <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                {{ n.role }}
                                              </td>

                                              <td
                                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <ul>
                                                  <li
                                                    v-for="(m, index) in n.approvals"
                                                    v-if="n.approvals.length > 0"
                                                    :key="index">
                                                    <div class="flex">
                                                      <span>
                                                        <check-icon
                                                          class="w-6 h-6 fill-green-300 mr-3" />
                                                      </span>
                                                      <span>
                                                        {{ m.user.name }}
                                                      </span>
                                                    </div>
                                                  </li>

                                                  <div
                                                    v-else
                                                    class="flex">
                                                    <span>
                                                      <XCircleIcon
                                                        class="w-6 h-6 fill-red-400 mr-3" />
                                                    </span>
                                                    <span>None received..</span>
                                                  </div>
                                                </ul>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="flex-row text-indigo-400 text-lg mb-2">
                            <span>Deal Ticket</span>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Is Active</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <check-icon
                                  v-if="selected_transaction.deal_ticket.is_active"
                                  class="w-6 h-6 fill-green-300" />
                                <XCircleIcon
                                  v-else
                                  class="w-6 h-6 fill-red-400" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <dt class="text-gray-500">Is Approved</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <check-icon
                                  v-if="selected_transaction.deal_ticket.is_approved"
                                  class="w-6 h-6 fill-green-300" />
                                <XCircleIcon
                                  v-else
                                  class="w-6 h-6 fill-red-400" />
                              </div>
                            </dd>
                          </div>

                          <div class="mt-2">
                            <SecondaryButton
                              class="m-1"
                              @click="createApproval">
                              Approve
                            </SecondaryButton>

                            <!--                                                        <SecondaryButton @click="createActivation" class="m-1">
                                                                                                                    Activate
                                                                                                                </SecondaryButton>-->

                            <a
                              :href="
                                '/pdf_report/deal_ticket_view/' +
                                props.selected_transaction.id
                              "
                              class="ml-3 mt-2 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                              target="_blank">
                              View (working doc)
                            </a>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Documents
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Transport planned</dt>
                            <dd class="flex items-start gap-x-2">
                              <div v-if="selected_transaction.transporter_id !== 1">
                                <check-icon class="h-5 h-5" />
                              </div>
                              <div v-else>
                                <x-mark-icon class="h-5 h-5" />
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Transport scheduled</dt>
                            <dd class="flex items-start gap-x-2">
                              <div
                                v-if="
                                  props.selected_transaction.transport_job
                                    .transport_driver_vehicle[0].regular_vehicle_id !== 1
                                ">
                                <check-icon class="h-5 h-5" />
                              </div>
                              <div v-else>
                                <x-mark-icon class="h-5 h-5" />
                              </div>
                            </dd>
                          </div>

                          <div v-if="transport_order.is_active">
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Transport Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="transport_order.is_to_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendTransportOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">
                                Transport Confirmation Received
                              </dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="transport_order.is_to_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receiveTransportOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                          </div>
                          <div v-else>
                            <p class="text-red-400 font-bold">
                              Transport order Not Active
                            </p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Transport Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activateTransportOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>

                          <!--                                                    purchase order-->
                          <div v-if="purchase_order.is_active">
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Purchase Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="purchase_order.is_po_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendPurchaseOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">
                                Purchase Confirmation Received
                              </dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="purchase_order.is_po_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receivePurchaseOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                          </div>
                          <div v-else>
                            <p class="text-red-400 font-bold">
                              Purchase order Not Active
                            </p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Purchase Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activatePurchaseOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>
                          <!--                                                    sales order-->
                          <div v-if="sales_order.is_active">
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Sales Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="sales_order.is_sa_conf_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendSalesOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-1">
                              <dt class="text-gray-500">Sales Confirmation Received</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="sales_order.is_sa_conf_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receiveSalesOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                          </div>
                          <div v-else>
                            <p class="text-red-400 font-bold">Sales order Not Active</p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Sales Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activateSalesOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Cancelled</dt>
                            <dd class="flex items-start gap-x-2">
                              <SwitchGroup
                                as="div"
                                class="flex m-2 items-center">
                                <Switch
                                  v-model="combined_Form.is_cancelled"
                                  :class="[
                                    combined_Form.is_cancelled
                                      ? 'bg-indigo-600'
                                      : 'bg-gray-200',
                                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                  ]">
                                  <span
                                    :class="[
                                      combined_Form.is_cancelled
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    ]"
                                    aria-hidden="true" />
                                </Switch>
                              </SwitchGroup>
                            </dd>
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Operations
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-2 text-sm leading-6">
                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Include in calculations</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-2 items-center">
                                  <Switch
                                    v-model="combined_Form.include_in_calculations"
                                    :class="[
                                      combined_Form.include_in_calculations
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.include_in_calculations
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Loaded</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-2 items-center">
                                  <Switch
                                    v-model="combined_Form.is_loaded"
                                    :class="[
                                      combined_Form.is_loaded
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_loaded
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Delivered</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-2 items-center">
                                  <Switch
                                    v-model="combined_Form.is_delivered"
                                    :class="[
                                      combined_Form.is_delivered
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_delivered
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">On road</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <SwitchGroup
                                  as="div"
                                  class="flex m-2 items-center">
                                  <Switch
                                    v-model="combined_Form.is_onroad"
                                    :class="[
                                      combined_Form.is_onroad
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200',
                                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                    ]">
                                    <span
                                      :class="[
                                        combined_Form.is_onroad
                                          ? 'translate-x-5'
                                          : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                      ]"
                                      aria-hidden="true" />
                                  </Switch>
                                </SwitchGroup>
                              </div>
                            </dd>
                          </div>

                          <div class="flex justify-between gap-x-4 py-1">
                            <dt class="text-gray-500">Contract type</dt>
                            <dd class="flex items-start gap-x-2">
                              <div>
                                <Combobox
                                  v-model="combined_Form.contract_type_id"
                                  as="div">
                                  <div class="relative mt-2">
                                    <ComboboxInput
                                      :display-value="(type) => type?.name"
                                      class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      @change="contractTypeQuery = $event.target.value" />
                                    <ComboboxButton
                                      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                      <ChevronUpDownIcon
                                        aria-hidden="true"
                                        class="h-5 w-5 text-gray-400" />
                                    </ComboboxButton>

                                    <ComboboxOptions
                                      v-if="filteredContractTypes.length > 0"
                                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                      <ComboboxOption
                                        v-for="type in filteredContractTypes"
                                        :key="type.id"
                                        v-slot="{ active, selected }"
                                        :value="type"
                                        as="template">
                                        <ul>
                                          <li
                                            :class="[
                                              'relative cursor-default select-none py-2 pl-3 pr-9',
                                              active
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-gray-900',
                                            ]">
                                            <span
                                              :class="[
                                                'block truncate',
                                                selected && 'font-semibold',
                                              ]">
                                              {{ type.name }}
                                            </span>
                                            <span
                                              v-if="selected"
                                              :class="[
                                                'absolute inset-y-0 right-0 flex items-center pr-4',
                                                active ? 'text-white' : 'text-indigo-600',
                                              ]">
                                              <CheckIcon
                                                aria-hidden="true"
                                                class="h-5 w-5" />
                                            </span>
                                          </li>
                                        </ul>
                                      </ComboboxOption>
                                    </ComboboxOptions>
                                  </div>
                                </Combobox>
                              </div>
                            </dd>
                          </div>

                          <div class="text-lg mb-2 text-indigo-400">Process notes</div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <AreaInput
                              id="process_notes"
                              v-model="combined_Form.process_notes"
                              :rows="4"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />
                          </div>

                          <div class="text-lg mb-2 text-indigo-400">Traders notes</div>

                          <div class="flex justify-between gap-x-4 py-3">
                            <AreaInput
                              id="traders_notes"
                              v-model="combined_Form.traders_notes"
                              :rows="4"
                              class="mt-1 block w-1/3"
                              placeholder="Optional notes..."
                              type="text" />
                          </div>
                        </dl>
                      </li>

                      <li class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Operational Alerts (Generic)
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="">
                            <form class="mt-5">
                              <div
                                class="mt-2 grid grid-cols-1 gap-x-6 gap-y-1 sm:grid-cols-6">
                                <div class="col-span-6">
                                  <div class="text-sm mb-2 text-indigo-400">
                                    Add status
                                  </div>

                                  <div class="flex">
                                    <div class="w-48">
                                      <label
                                        class="block text-sm font-medium leading-6 text-gray-900">
                                        Type:
                                      </label>
                                      <div class="mt-2">
                                        <div class="">
                                          <select
                                            v-model="statusForm.status_entity_id"
                                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option
                                              v-for="n in props.all_status_entities"
                                              :key="n.id"
                                              :value="n.id">
                                              {{ n.entity }}
                                            </option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="ml-3 w-48">
                                      <label
                                        class="block text-sm font-medium leading-6 text-gray-900">
                                        Status:
                                      </label>
                                      <div class="mt-2">
                                        <div class="">
                                          <select
                                            v-model="statusForm.status_type_id"
                                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option
                                              v-for="n in props.all_status_types"
                                              :key="n.id"
                                              :value="n.id">
                                              {{ n.type }}
                                            </option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-span-4">
                                  <SecondaryButton
                                    class=""
                                    @click="createStatus">
                                    Add Status (+)
                                  </SecondaryButton>
                                </div>

                                <div class="col-span-6">
                                  <div>
                                    <div class="flex">
                                      <ul class="w-full">
                                        <li
                                          v-for="n in selected_transaction.transport_status"
                                          :key="n.id"
                                          :value="n.id"
                                          class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
                                          <div class="flex">
                                            <div class="flex-auto font-bold w-2/3">
                                              <div class="rounded-md bg-yellow-50 p-1">
                                                <div class="flex">
                                                  <div class="flex-shrink-0">
                                                    <XCircleIcon
                                                      aria-hidden="true"
                                                      class="h-5 w-5 text-red-400 hover:text-black"
                                                      @click="deleteStatus(n.id)" />
                                                  </div>
                                                  <div class="ml-3">
                                                    <h3
                                                      class="text-lg uppercase font-medium text-yellow-800">
                                                      {{ n.status_entity.entity }}
                                                    </h3>
                                                    <div
                                                      class="mt-2 text-sm text-yellow-700">
                                                      <p class="uppercase">
                                                        {{ n.status_type.type }}
                                                      </p>
                                                      <p class="italic text-sm">
                                                        {{ n.created_at }}
                                                      </p>
                                                    </div>
                                                  </div>

                                                  <div class="ml-auto pl-3"></div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </li>
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </dl>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 7">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-2 xl:gap-x-8"
                      role="list">
                      <li
                        v-if="selected_transaction.contract_type_id === 4"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Linked Contracts
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div>
                            <div class="">
                              <div class="">
                                <div v-if="selected_transaction.contract_type_id === 1">
                                  Unallocated
                                </div>

                                <div v-if="selected_transaction.contract_type_id === 4">
                                  <div class="text-indigo-400 font-bold">MQ</div>

                                  <SecondaryButton
                                    class="m-1 mt-3"
                                    @click="viewContractLink">
                                    Link MQ to PC
                                  </SecondaryButton>

                                  <ContractLinkModal
                                    :link_type_id="3"
                                    :mq_trans_id="selected_transaction.id"
                                    :show="viewContractLinkModal"
                                    @close="closeContractLink" />

                                  <div class="mt-3">
                                    <div>PC linked to this MQ:</div>

                                    <div>
                                      <form class="mt-5">
                                        <div class="px-4 sm:px-6 lg:px-8">
                                          <div class="-mx-4 mt-8 flow-root sm:mx-0">
                                            <table class="min-w-full">
                                              <colgroup>
                                                <col class="w-full sm:w-1/2" />
                                                <col class="sm:w-1/6" />
                                                <col class="sm:w-1/6" />
                                                <col class="sm:w-1/6" />
                                              </colgroup>
                                              <thead
                                                class="border-b border-gray-300 text-gray-900">
                                                <tr>
                                                  <th
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                    scope="col">
                                                    Parties
                                                  </th>
                                                  <th
                                                    class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                                    scope="col">
                                                    Product
                                                  </th>
                                                  <th
                                                    class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                                    scope="col">
                                                    GP
                                                  </th>
                                                  <th
                                                    class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0"
                                                    scope="col">
                                                    Action
                                                  </th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <tr
                                                  v-for="contract in filteredLinkedContractsPc"
                                                  :key="contract.id"
                                                  class="border-b border-gray-200">
                                                  <td
                                                    class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                    <div
                                                      class="font-medium text-gray-900">
                                                      PC{{
                                                        contract.transport_transaction_pc
                                                          .id
                                                      }}
                                                    </div>
                                                    <div
                                                      class="font-medium text-gray-900">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .supplier.last_legal_name
                                                      }}
                                                    </div>
                                                    <div
                                                      class="mt-1 truncate text-gray-500">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .customer.last_legal_name
                                                      }}
                                                    </div>
                                                    <div
                                                      class="mt-1 truncate text-gray-500">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .transporter.last_legal_name
                                                      }}
                                                    </div>
                                                  </td>
                                                  <td
                                                    class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                                    {{
                                                      contract.transport_transaction_pc
                                                        .product.name
                                                    }}
                                                  </td>
                                                  <td
                                                    class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                                    {{
                                                      NiceNumber(
                                                        contract.transport_transaction_pc
                                                          .transport_finance.gross_profit
                                                      )
                                                    }}
                                                  </td>
                                                  <td
                                                    class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                                    <Link
                                                      :data="{
                                                        selected_trans_id:
                                                          contract
                                                            .transport_transaction_pc.id,
                                                      }"
                                                      class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                      href="/transaction_summary"
                                                      method="get"
                                                      target="_blank">
                                                      Summary
                                                    </Link>

                                                    <Link
                                                      :data="{
                                                        selected_trans_id:
                                                          contract
                                                            .transport_transaction_pc.id,
                                                      }"
                                                      class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                      href="/pc_overview"
                                                      method="get"
                                                      target="_blank">
                                                      Overview
                                                    </Link>
                                                  </td>
                                                </tr>
                                              </tbody>
                                              <tfoot></tfoot>
                                            </table>
                                          </div>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </dl>
                      </li>

                      <li
                        v-if="selected_transaction.contract_type_id === 4"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Linked Contracts
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div>
                            <div class="">
                              <div class="">
                                <div v-if="selected_transaction.contract_type_id === 1">
                                  Unallocated
                                </div>
                                <div v-if="selected_transaction.contract_type_id === 3">
                                  SC
                                </div>

                                <div v-if="selected_transaction.contract_type_id === 4">
                                  <div class="text-indigo-400 font-bold">MQ</div>

                                  <SecondaryButton
                                    class="m-1 mt-3"
                                    @click="viewContractLinkSc">
                                    Link MQ to SC
                                  </SecondaryButton>

                                  <ContractLinkModalSc
                                    :link_type_id="4"
                                    :mq_trans_id="selected_transaction.id"
                                    :show="viewContractLinkModalSc"
                                    @close="closeContractLinkSc" />

                                  <div class="mt-3">
                                    <div>SC linked to this MQ:</div>

                                    <div>
                                      <form class="mt-5">
                                        <div class="px-4 sm:px-6 lg:px-8">
                                          <div class="-mx-4 mt-8 flow-root sm:mx-0">
                                            <table class="min-w-full">
                                              <colgroup>
                                                <col class="w-full sm:w-1/2" />
                                                <col class="sm:w-1/6" />
                                                <col class="sm:w-1/6" />
                                                <col class="sm:w-1/6" />
                                              </colgroup>
                                              <thead
                                                class="border-b border-gray-300 text-gray-900">
                                                <tr>
                                                  <th
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                    scope="col">
                                                    Parties
                                                  </th>
                                                  <th
                                                    class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                                    scope="col">
                                                    Product
                                                  </th>
                                                  <th
                                                    class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                                    scope="col">
                                                    GP
                                                  </th>
                                                  <th
                                                    class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0"
                                                    scope="col">
                                                    Action
                                                  </th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <tr
                                                  v-for="contract in filteredLinkedContractsSc"
                                                  :key="contract.id"
                                                  class="border-b border-gray-200">
                                                  <td
                                                    class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                    <div
                                                      class="font-medium text-gray-900">
                                                      SC{{
                                                        contract.transport_transaction_pc
                                                          .id
                                                      }}
                                                    </div>
                                                    <div
                                                      class="font-medium text-gray-900">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .supplier.last_legal_name
                                                      }}
                                                    </div>
                                                    <div
                                                      class="mt-1 truncate text-gray-500">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .customer.last_legal_name
                                                      }}
                                                    </div>
                                                    <div
                                                      class="mt-1 truncate text-gray-500">
                                                      {{
                                                        contract.transport_transaction_pc
                                                          .transporter.last_legal_name
                                                      }}
                                                    </div>
                                                  </td>
                                                  <td
                                                    class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                                    {{
                                                      contract.transport_transaction_pc
                                                        .product.name
                                                    }}
                                                  </td>
                                                  <td
                                                    class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                                    {{
                                                      NiceNumber(
                                                        contract.transport_transaction_pc
                                                          .transport_finance.gross_profit
                                                      )
                                                    }}
                                                  </td>
                                                  <td
                                                    class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                                    <Link
                                                      :data="{
                                                        selected_trans_id:
                                                          contract
                                                            .transport_transaction_pc.id,
                                                      }"
                                                      class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                      href="/transaction_summary"
                                                      method="get"
                                                      target="_blank">
                                                      Summary
                                                    </Link>

                                                    <Link
                                                      :data="{
                                                        selected_trans_id:
                                                          contract
                                                            .transport_transaction_pc.id,
                                                      }"
                                                      class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                      href="/sc_overview"
                                                      method="get"
                                                      target="_blank">
                                                      Overview
                                                    </Link>
                                                  </td>
                                                </tr>
                                              </tbody>
                                              <tfoot></tfoot>
                                            </table>
                                          </div>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </dl>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 8">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <li
                        v-if="selected_transaction.contract_type_id === 4"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Deal Ticket
                          </div>
                          <div lass="text-sm font-medium leading-6 text-gray-900">
                            <span>Approved MQ:</span>
                            <span v-if="selected_transaction.a_mq">
                              {{ selected_transaction.a_mq }}
                            </span>
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="deal_ticket.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/deal_ticket_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Generate Final</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="createFinalDealTicket">
                                  Generate
                                </SecondaryButton>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Download Final</dt>

                              <dd class="flex items-start gap-x-2">
                                <div v-if="deal_ticket.report_path">
                                  <a
                                    :href="
                                      route(
                                        'pdf_report.deal_ticket_final.download',
                                        deal_ticket.report_path
                                      )
                                    "
                                    class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    target="_blank">
                                    Download
                                  </a>
                                </div>

                                <div v-else>
                                  <p>Not generated</p>
                                </div>
                              </dd>
                            </div>
                          </div>

                          <div
                            v-else
                            class="text-red-400">
                            <p class="font-bold">Deal Ticket Not Active</p>
                            <p>(Activate via process control)</p>
                          </div>
                        </dl>
                      </li>
                      <li
                        v-if="selected_transaction.contract_type_id === 4"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Transport Order Confirmation
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="transport_order.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="transport_order.is_to_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendTransportOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Received</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="transport_order.is_to_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receiveTransportOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/transport_order_confirmation_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                          </div>

                          <div v-else>
                            <p class="text-red-400 font-bold">
                              Transport order Not Active
                            </p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Transport Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activateTransportOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>
                        </dl>
                      </li>
                      <li
                        v-if="selected_transaction.contract_type_id === 2"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Purchase Order
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="purchase_order.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="purchase_order.is_po_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendPurchaseOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Received</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="purchase_order.is_po_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receivePurchaseOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/purchase_order_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                          </div>

                          <div v-else>
                            <p class="text-red-400 font-bold">
                              Purchase order Not Active
                            </p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Purchase Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activatePurchaseOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>
                        </dl>
                      </li>
                      <li
                        v-if="
                          selected_transaction.contract_type_id === 2 ||
                          selected_transaction.contract_type_id === 4
                        "
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Purchase Order Confirmation
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="purchase_order.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/purchase_order_confirmation_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                          </div>

                          <div v-else>Sales order Not Active</div>
                        </dl>
                      </li>
                      <li
                        v-if="selected_transaction.contract_type_id === 3"
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Sales Order
                          </div>
                        </div>
                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="sales_order.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Confirmation Sent</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="sales_order.is_sa_conf_sent">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="sendSalesOrder">
                                      Sent
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Received</dt>
                              <dd class="flex items-start gap-x-2">
                                <div>
                                  <div v-if="sales_order.is_sa_conf_received">
                                    <p>
                                      <check-icon class="h-5 h-5" />
                                    </p>
                                  </div>
                                  <div v-else>
                                    <SecondaryButton @click="receiveSalesOrder">
                                      Received
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </dd>
                            </div>

                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/sales_order_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                          </div>

                          <div v-else>
                            <p class="text-red-400 font-bold">Sales order Not Active</p>
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Activate Sales Order</dt>
                              <dd class="flex items-start gap-x-2">
                                <SecondaryButton @click="activateSalesOrder">
                                  Activate
                                </SecondaryButton>
                              </dd>
                            </div>
                          </div>
                        </dl>
                      </li>
                      <li
                        v-if="
                          selected_transaction.contract_type_id === 3 ||
                          selected_transaction.contract_type_id === 4
                        "
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            Sales Order Confirmation
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div v-if="sales_order.is_active">
                            <div class="flex justify-between gap-x-4 py-3">
                              <dt class="text-gray-500">Working Document</dt>
                              <dd class="flex items-start gap-x-2">
                                <a
                                  :href="
                                    '/pdf_report/sales_order_confirmation_view/' +
                                    props.selected_transaction.id
                                  "
                                  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                  target="_blank">
                                  View
                                </a>
                              </dd>
                            </div>
                          </div>

                          <div v-else>Sales order Not Active</div>
                        </dl>
                      </li>
                    </ul>
                  </div>

                  <div v-if="selectedTabId === 9">
                    <div>
                      <div class="font-bold text-indigo-500">Model Activity</div>
                      <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                          <tr>
                            <th
                              class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Id
                            </th>
                            <th
                              class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Date
                            </th>
                            <th
                              class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Event
                            </th>
                            <th
                              class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Subject ID
                            </th>
                            <th
                              class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Causer Type
                            </th>
                            <th
                              class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Causer ID
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                          <tr
                            v-for="(activity, index) in model_activity"
                            :key="activity.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              {{ activity.id }}
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              {{ activity.created_at }}
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              {{ activity.event }}
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              {{ activity.subject_id }}
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              {{ activity.causer_type }}
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              {{ activity.causer_id }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div v-if="selectedTabId === 12">
                    <div>
                      <div class="font-bold text-indigo-500">
                        Staff Commission allocation
                      </div>
                      <div class="">
                        <form class="">
                          <div
                            class="mt-5 grid grid-cols-1 gap-x-6 gap-y-2 sm:grid-cols-6">
                            <div class="col-span-4">
                              <SecondaryButton
                                class=""
                                @click="viewAssignedNewComm">
                                Add Comm User (+)
                              </SecondaryButton>
                              <div v-if="viewAssignedCommNewModal">
                                <assigned-comm-modal
                                  :all_staff="props.all_staff"
                                  :assigned_user_comm="null"
                                  :show="viewAssignedCommNewModal"
                                  :transport_finance_id="
                                    selected_transaction.transport_finance.id
                                  "
                                  :transport_trans_id="selected_transaction.id"
                                  @close="closeAssignedNewComm" />
                              </div>
                            </div>

                            <div class="col-span-4">
                              <div
                                v-for="(
                                  user_comm, index
                                ) in selected_transaction.assigned_user_comm"
                                :key="user_comm.id">
                                <div class="ml-5 border-solid p-1 m-1 rounded shadow-xl">
                                  <div class="px-2 sm:px-0">
                                    <h3
                                      class="text-base font-semibold leading-2 text-indigo-400">
                                      User Comm
                                      {{ index + 1 }}
                                    </h3>
                                    <h3
                                      class="text-base font-semibold leading-2 text-sm text-gray-400">
                                      Reference
                                      {{ user_comm.id }}
                                    </h3>
                                  </div>
                                  <div class="mt-1 border-t border-gray-100">
                                    <dl class="divide-y divide-gray-100">
                                      <div
                                        class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          assigned_user_supplier_id
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{
                                            user_comm.assigned_user_supplier.first_name
                                          }}
                                          {{
                                            user_comm.assigned_user_supplier
                                              .last_legal_name
                                          }}
                                        </dd>
                                      </div>

                                      <div
                                        class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          assigned_user_customer_id
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{
                                            user_comm.assigned_user_customer.first_name
                                          }}
                                          {{
                                            user_comm.assigned_user_customer
                                              .last_legal_name
                                          }}
                                        </dd>
                                      </div>

                                      <div
                                        class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          supplier_comm
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{ NiceNumber(user_comm.supplier_comm) }}
                                        </dd>
                                      </div>

                                      <div
                                        class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          customer_comm
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{ NiceNumber(user_comm.customer_comm) }}
                                        </dd>
                                      </div>
                                    </dl>
                                  </div>

                                  <div class="mt-1 col-span-4">
                                    <div v-if="viewAssignedCommModal">
                                      <assigned-comm-modal
                                        :all_staff="props.all_staff"
                                        :assigned_user_comm="currentAssignedComm"
                                        :show="viewAssignedCommModal"
                                        :transport_finance_id="
                                          selected_transaction.transport_finance.id
                                        "
                                        :transport_trans_id="selected_transaction.id"
                                        @close="closeAssignedComm" />
                                    </div>

                                    <SecondaryButton
                                      class="m-1"
                                      @click="deleteAssignedComm(user_comm.id)">
                                      Delete
                                    </SecondaryButton>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                  <div v-if="selectedTabId === 13">
                    <div>
                      <table
                        v-if="linked_trans_split != null"
                        class="min-w-full divide-y divide-gray-300">
                        <thead>
                          <tr>
                            <th
                              class="whitespace py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              ID
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              MQ
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Customer
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Transporter
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Supplier
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Product
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              BU Incoming
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              BU Outgoing
                            </th>
                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Units in
                            </th>

                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Units Out
                            </th>

                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Plan Tons in
                            </th>

                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Plan Tons out
                            </th>

                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              GP/TON
                            </th>

                            <th
                              class="whitespace px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              GP %
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                          <tr
                            v-for="n in linked_trans_split"
                            :key="n.id"
                            :value="n"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              <div
                                v-if="n.transport_transaction.is_split_load_primary"
                                class="font-bold text-indigo-400">
                                <Link
                                  :data="{
                                    selected_trans_id: n.transport_transaction.id,
                                  }"
                                  href="/transaction_summary"
                                  method="get"
                                  target="_blank">
                                  {{ n.transport_transaction.id }}
                                  Primary
                                </Link>
                              </div>

                              <div v-else>
                                <Link
                                  :data="{
                                    selected_trans_id: n.transport_transaction.id,
                                  }"
                                  href="/transaction_summary"
                                  method="get"
                                  target="_blank">
                                  {{ n.transport_transaction.id }}
                                </Link>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div v-if="n.transport_transaction.a_mq">
                                MQ
                                {{ n.transport_transaction.a_mq }}
                              </div>
                              <div v-else>No MQ</div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                {{ n.transport_transaction.customer.last_legal_name }}
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                {{ n.transport_transaction.transporter.last_legal_name }}
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                {{ n.transport_transaction.supplier.last_legal_name }}
                              </div>
                            </td>
                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                {{ n.transport_transaction.product.name }}
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                {{
                                  n.transport_transaction.transport_load
                                    .billing_units_incoming.name
                                }}
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_load
                                      .billing_units_incoming.name
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_load
                                      .no_units_incoming
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_load
                                      .no_units_outgoing
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_finance
                                      .weight_ton_incoming
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_finance
                                      .weight_ton_outgoing
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_finance
                                      .gross_profit_per_ton
                                  }}
                                </div>
                              </div>
                            </td>

                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-medium text-gray-900">
                              <div>
                                <div>
                                  {{
                                    n.transport_transaction.transport_finance
                                      .gross_profit_percent
                                  }}%
                                </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <tfoot>
                          <tr class="bg-gray-100">
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900 text-right"
                              colspan="8">
                              Total
                            </td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900">
                              {{ split_totals['transport_load_no_units_incoming'] }}
                            </td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900">
                              {{ split_totals['transport_load_no_units_outgoing'] }}
                            </td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900">
                              {{
                                split_totals[
                                  'transport_finance_weight_ton_incoming_planned'
                                ]
                              }}
                            </td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900">
                              {{
                                split_totals[
                                  'transport_finance_weight_ton_outgoing_planned'
                                ]
                              }}
                            </td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900"></td>
                            <td
                              class="whitespace-nowrap text-right px-2 py-2 text-sm font-semibold text-gray-900"></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
