<script setup>
  import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue';
  import { Link, router, useForm, usePage } from '@inertiajs/vue3';
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
  import { useTransactionCombinedForm } from '@/Composables/TransactionSummary/useTransactionCombinedForm.js';
  import { useTransactionComputedValues } from '@/Composables/TransactionSummary/useTransactionComputedValues.js';
  import { useTransactionDateFormatters } from '@/Composables/TransactionSummary/useTransactionDateFormatters.js';
  import { useAddressFilters } from '@/Composables/TransactionSummary/useAddressFilters.js';
  import { useAddressClearing } from '@/Composables/TransactionSummary/useAddressClearing.js';
  import { useTransactionActions } from '@/Composables/TransactionSummary/useTransactionActions.js';
  import { formatNiceNumber } from '@/Composables/useNumberFormatters.js';
  import Swal from 'sweetalert2';
  import TransactionFilters from '@/Components/TransactionSummary/TransactionFilters.vue';
  import TransactionTable from '@/Components/TransactionSummary/TransactionTable.vue';
  import TradeSlideOver from '@/Components/UI/TradeSlideOver.vue';
  import TransactionTabNav from '@/Components/TransactionSummary/TransactionTabNav.vue';
  import TransactionSupplierAccountCard from '@/Components/TransactionSummary/TransactionSupplierAccountCard.vue';
  import TransactionSupplierCard from '@/Components/TransactionSummary/TransactionSupplierCard.vue';
  import TransactionCustomerSelect from '@/Components/TransactionSummary/TransactionCustomerSelect.vue';
  import TransactionCustomerTab from '@/Components/TransactionSummary/TransactionCustomerTab.vue';
  import TransactionProductCard from '@/Components/TransactionSummary/TransactionProductCard.vue';
  import TransactionSupplierNotesCard from '@/Components/TransactionSummary/TransactionSupplierNotesCard.vue';
  import TransactionProductIncomingCard from '@/Components/TransactionSummary/TransactionProductIncomingCard.vue';
  import TransactionProductOutgoingCard from '@/Components/TransactionSummary/TransactionProductOutgoingCard.vue';
  import TransactionProductCalculationsCard from '@/Components/TransactionSummary/TransactionProductCalculationsCard.vue';
  import TransactionProductNotesCard from '@/Components/TransactionSummary/TransactionProductNotesCard.vue';
  import TransactionLogTab from '@/Components/TransactionSummary/TransactionLogTab.vue';
  import TransactionInvoiceTab from '@/Components/TransactionSummary/TransactionInvoiceTab.vue';
  import TransactionPricingTab from '@/Components/TransactionSummary/TransactionPricingTab.vue';
  import AssignedCommModal from '@/Components/UI/AssignedCommModal.vue'; // Expose Swal globally for legacy code

  // Expose Swal globally for legacy code
  if (typeof window !== 'undefined') {
    window.Swal = Swal;
    window.swal = Swal.fire.bind(Swal);
  }

  const NiceNumber = formatNiceNumber;

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

  const reloadTradeData = () => {
    router.reload({
      only: ['selected_transaction', 'transactions'],
      preserveScroll: true,
    });
  };

  const handleVisibilityChange = () => {
    if (!document.hidden) {
      reloadTradeData();
    }
  };

  let pollInterval = null;

  onMounted(() => {
    document.addEventListener('visibilitychange', handleVisibilityChange);
    pollInterval = setInterval(() => {
      if (!document.hidden) {
        reloadTradeData();
      }
    }, 30000);
  });

  onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    clearInterval(pollInterval);
  });

  // tabs and tab selection moved to composable for cleaner code
  const isAdmin = computed(() => usePage().props.roles_permissions.roles?.includes('AdminRole') ?? false);

  const { tabs, selectedTabId, selectTab } = useTransactionTabs(
    computed(() => props.selected_transaction.is_split_load),
    isAdmin
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

  const permissions = computed(() => usePage().props.permissions);
  const roles_permissions = computed(() => usePage().props.roles_permissions);
  computed(() =>
    usePage().props.roles_permissions.permissions.includes('edit_adjusted_gp')
  );

  // isLoading now comes from useTransactionFilters composable
  let isUpdating = ref(false);
  const selectedSplitMqId = ref(null);

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
    pcScApprovalForm,
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
  // Combined form initialization moved to composable
  const { combined_Form, updateSelectValuesInternal } = useTransactionCombinedForm(props);

  // Initialize transport tab filters (drivers and vehicles filtered by selected transporter)
  const {
    transporterQuery,
    filteredTransporters,
    selectedVehicleQuery,
    filteredSelectedVehicles,
    selectedDriverQuery,
    filteredSelectedDrivers,
  } = useTransportTab(props, combined_Form);

  // Wrapper for updateSelectValues to pass temp_form update
  let updateSelectValues;

  // Declare updateSelectValues first (defined below)

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

  // Date formatters moved to composable
  const {
    format,
    formatStart,
    formatEarly,
    formatLate,
    formatInvoicePdDay,
    formatInvoicePayByDay,
    formatInvoiceDate,
  } = useTransactionDateFormatters(filterForm, combined_Form);

  // Define updateSelectValues
  updateSelectValues = (passedStatusForms) => {
    temp_form.transport_trans_id = props.selected_transaction.id;

    // Always pass the status forms to ensure they're updated
    const formsToUpdate = passedStatusForms || {
      transportApprovalForm,
      pcScApprovalForm,
      statusForm,
      salesOrderForm,
      purchaseOrderForm,
      transportOrderForm,
    };

    updateSelectValuesInternal(formsToUpdate);
  };

  // Computed values moved to composable
  const { no_units_to_allocate, selling_price_to_allocate, transport_cost_to_allocate } =
    useTransactionComputedValues(combined_Form, props);
  props.selected_transaction.transport_finance.transport_cost -
    combined_Form.transport_cost_2 -
    combined_Form.transport_cost_3 -
    combined_Form.transport_cost_4 -
    combined_Form.transport_cost_5;

  // Status and order forms now provided by useTransactionStatusForms composable

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

          // Force refresh to get updated data from backend (e.g., calculated invoice_pay_by_date)
          filter();

          //endTime = performance.now()
          //console.log(`Call to transportTrans took ${(endTime - startTime)/1000} seconds`);
          startTime = 0;
          endTime = 0;
        },
        onError: (errors) => {
          // Show detailed validation errors
          const errorMessages = Object.entries(errors)
            .map(([field, messages]) => {
              const fieldName = field.replace(/_/g, ' ').replace(/\./g, ' ');
              return `${fieldName}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
            })
            .join('\n');

          alert('Validation Errors:\n\n' + errorMessages);
          console.error('Validation errors:', errors);
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

  // Address clearing watchers for customers (prevents invalid addresses when customer changes)
  useAddressClearing(combined_Form);

  // Order activation, send, and receive handlers now provided by useTransactionStatusForms composable

  const temp_form = useForm({
    transport_trans_id: props.selected_transaction.id,
  });

  // CRUD actions from useTransactionActions composable
  const {
    cloneTransportTrans,
    deleteDriverVehicle,
    createApproval,
    createActivation,
    createPcScApproval,
    createFinalDealTicket,
    downloadDealTicket,
  } = useTransactionActions(
    temp_form,
    transportApprovalForm,
    pcScApprovalForm,
    filter,
    isUpdating,
    props
  );

  // Helper functions from useTransactionHelpers composable
  const { vehicleSlideProps, getComponentProps, doCreatedTrade, deleteAssignedComm } =
    useTransactionHelpers(filterForm, filter, temp_form);

  // NOTE: All filter queries (customer, product, transport, etc.) are now provided by composables
  // See: useCustomerTab, useProductTab, useTransportTab, useSupplierTab
  // This removed ~250 lines of duplicated filter logic

  // Address filters for specific form fields (these filter addressable relationships)
  const {
    collectionAddressQuery,
    filteredCollectionAddress,
    deliveryAddressQuery,
    deliveryAddressQuery2,
    deliveryAddressQuery3,
    deliveryAddressQuery4,
    deliveryAddressQuery5,
    filteredDeliveryAddress,
    filteredDeliveryAddress2,
    filteredDeliveryAddress3,
    filteredDeliveryAddress4,
    filteredDeliveryAddress5,
  } = useAddressFilters(combined_Form);

  // Modal state removed - now provided by useTransactionModals composable
  // filteredLinkedContracts and sumLinkedContracts now provided by useTransactionComputed composable

  // showDetails and toggleDetails now provided by useTransactionToggles composable
  // createStatus and deleteStatus now provided by useTransactionStatusForms composable

  // getTitle now provided by useTransactionComputed composable

  // showDriver, showVehicle, toggleShowDriver, toggleShowVehicle now provided by useTransactionToggles composable
  // driverForm, vehicleForm now provided by useTransactionForms composable
  // vehicleSlideProps, getComponentProps now provided by useTransactionHelpers composable

  // createDriver, createVehicle, deleteTransLink now provided by useTransactionForms composable
  // Reload all_suppliers and all_customers after a new address is added inline
  const reloadAddresses = () => {
    router.reload({ only: ['all_suppliers', 'all_customers'] });
  };

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
      <div class="bg-white m-1 p-1 shadow-xl sm:rounded-lg">
        <div>
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

          <div class="px-4 sm:px-6 lg:px-8">
            <div class="mt-1 flow-root">
              <div class="-mx-4 -my-4 sm:-mx-6 lg:-mx-8">
                <div class="overflow-x-auto">
                  <div class="inline-block min-w-full py-2 align-middle">
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
                        v-if="selected_transaction.a_mq || (selected_transaction.contract_type?.name === 'MQ' && selected_transaction.old_deal_ticket)"
                        class="py-2 inline-flex text-xl font-bold text-black">
                        MQ{{ selected_transaction.a_mq ?? selected_transaction.old_deal_ticket }}
                      </div>
                      <div
                        v-if="selected_transaction.a_pc || (selected_transaction.contract_type?.name === 'PC' && selected_transaction.contract_no)"
                        class="py-2 inline-flex text-xl font-bold text-black">
                        PC{{ selected_transaction.a_pc ?? selected_transaction.contract_no }}
                      </div>
                      <div
                        v-if="selected_transaction.a_sc || (selected_transaction.contract_type?.name === 'SC' && selected_transaction.contract_no)"
                        class="py-2 inline-flex text-xl font-bold text-black">
                        SC{{ selected_transaction.a_sc ?? selected_transaction.contract_no }}
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
                        @update:collectionAddressQuery="(v) => (collectionAddressQuery = v)"
                        @address-created="reloadAddresses" />

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
                      :filtered-billing-units-outgoing="filteredBillingUnitsOutgoing"
                      :filtered-customers="filteredCustomers"
                      :filtered-delivery-address="filteredDeliveryAddress"
                      :filtered-linked-contracts-sc="filteredLinkedContractsSc"
                      :filtered-package-outgoing="filteredPackageOutgoing"
                      :primary-linked-trans-split="primary_linked_trans_split"
                      :selected-transaction="selected_transaction"
                      :view-contract-link-modal-sc="viewContractLinkModalSc"
                      :view-split-link-modal="viewSplitLinkModal"
                      @update:delivery-address-query="deliveryAddressQuery = $event"
                      @update:billing-units-outgoing-query="
                        billingUnitsOutgoingQuery = $event
                      "
                      @update:package-outgoing-query="packageOutgoingQuery = $event"
                      @view-split-link="viewSplitLink"
                      @close-split-link="closeSplitLink"
                      @delete-trans-link="deleteTransLink"
                      @view-contract-link-sc="viewContractLinkSc"
                      @close-contract-link-sc="closeContractLinkSc"
                      @address-created="reloadAddresses" />
                  </div>

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
                                <div
                                  v-if="selected_transaction.a_pc"
                                  class="font-bold text-indigo-400">
                                  Approved PC:
                                  {{ selected_transaction.a_pc }}_b
                                </div>
                                <div
                                  v-if="selected_transaction.a_sc"
                                  class="font-bold text-indigo-400">
                                  Approved SC:
                                  {{ selected_transaction.a_sc }}_b
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
                                <div
                                  v-if="selected_transaction.a_pc"
                                  class="font-bold text-indigo-400">
                                  Approved PC:
                                  {{ selected_transaction.a_pc }}_c
                                </div>
                                <div
                                  v-if="selected_transaction.a_sc"
                                  class="font-bold text-indigo-400">
                                  Approved SC:
                                  {{ selected_transaction.a_sc }}_c
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
                                <div
                                  v-if="selected_transaction.a_pc"
                                  class="font-bold text-indigo-400">
                                  Approved PC:
                                  {{ selected_transaction.a_pc }}_d
                                </div>
                                <div
                                  v-if="selected_transaction.a_sc"
                                  class="font-bold text-indigo-400">
                                  Approved SC:
                                  {{ selected_transaction.a_sc }}_d
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
                                <div
                                  v-if="selected_transaction.a_pc"
                                  class="font-bold text-indigo-400">
                                  Approved PC:
                                  {{ selected_transaction.a_pc }}_e
                                </div>
                                <div
                                  v-if="selected_transaction.a_sc"
                                  class="font-bold text-indigo-400">
                                  Approved SC:
                                  {{ selected_transaction.a_sc }}_e
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
                                        (driver) => {
                                          if (!driver) return '';
                                          const driverName =
                                            driver.first_name + ' ' + driver.last_name;
                                          if (driver.transporter) {
                                            return (
                                              driverName +
                                              ' - ' +
                                              driver.transporter.last_legal_name
                                            );
                                          }
                                          return driverName;
                                        }
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
                                              <span
                                                v-if="driver.transporter"
                                                class="text-gray-500 font-normal">
                                                - {{ driver.transporter.last_legal_name }}
                                              </span>
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
                                        (vehicle) => {
                                          if (!vehicle) return '';
                                          if (vehicle.reg_no === 'N/A' || vehicle.reg_no === 'n/a') return 'Unallocated';
                                          const vehicleInfo =
                                            vehicle.reg_no +
                                            ' ' +
                                            vehicle.vehicle_type.name;
                                          if (vehicle.transporter) {
                                            return (
                                              vehicleInfo +
                                              ' - ' +
                                              vehicle.transporter.last_legal_name
                                            );
                                          }
                                          return vehicleInfo;
                                        }
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
                                              <template v-if="vehicle.reg_no === 'N/A' || vehicle.reg_no === 'n/a'">
                                                Unallocated
                                              </template>
                                              <template v-else>
                                                {{ vehicle.reg_no }}
                                                {{ vehicle.vehicle_type.name }}
                                                <span
                                                  v-if="vehicle.transporter"
                                                  class="text-gray-500 font-normal">
                                                  -
                                                  {{ vehicle.transporter.last_legal_name }}
                                                </span>
                                              </template>
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

                  <!-- Tab 4: Pricing -->
                  <div v-if="selectedTabId === 4">
                    <TransactionPricingTab
                      :all-transport-rates="all_transport_rates"
                      :combined-form="combined_Form"
                      :filtered-billing-units-incoming="filteredBillingUnitsIncoming"
                      :filtered-billing-units-outgoing="filteredBillingUnitsOutgoing"
                      :filtered-package-incoming="filteredPackageIncoming"
                      :filtered-package-outgoing="filteredPackageOutgoing"
                      :loading-hour-options="loading_hour_options"
                      :selected-transaction="selected_transaction" />
                  </div>

                  <!-- Tab 5: Invoice -->
                  <div v-if="selectedTabId === 5">
                    <TransactionInvoiceTab
                      :all-invoice-statuses="all_invoice_statuses"
                      :combined-form="combined_Form"
                      :format-invoice-date="formatInvoiceDate"
                      :format-invoice-pay-by-day="formatInvoicePayByDay"
                      :format-invoice-pd-day="formatInvoicePdDay"
                      :payment-terms="paymentTerms"
                      :selected-transaction="selected_transaction" />
                  </div>

                  <div v-if="selectedTabId === 6">
                    <ul
                      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
                      role="list">
                      <li
                        v-if="selected_transaction.contract_type_id === 4"
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

                      <!-- PC/SC Approvals Section -->
                      <li
                        v-if="
                          selected_transaction.contract_type_id === 2 ||
                          selected_transaction.contract_type_id === 3
                        "
                        class="overflow-hidden rounded-xl border border-gray-200">
                        <div
                          class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            {{
                              selected_transaction.contract_type_id === 2 ? 'PC' : 'SC'
                            }}
                            Approvals
                          </div>
                        </div>

                        <dl
                          class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                          <div class="mb-2">
                            <div class="text-indigo-400">
                              ID: {{ selected_transaction.id }}
                            </div>
                            <div
                              v-if="selected_transaction.old_id"
                              class="mb-2 text-gray-400">
                              (OLD: {{ selected_transaction.old_id }})
                            </div>

                            <div
                              v-if="
                                selected_transaction.contract_type_id === 2 &&
                                selected_transaction.a_pc
                              "
                              class="font-bold text-indigo-400 mt-3">
                              PC#: {{ selected_transaction.a_pc }}
                            </div>

                            <div
                              v-if="
                                selected_transaction.contract_type_id === 3 &&
                                selected_transaction.a_sc
                              "
                              class="font-bold text-indigo-400 mt-3">
                              SC#: {{ selected_transaction.a_sc }}
                            </div>

                            <div
                              v-if="
                                (selected_transaction.contract_type_id === 2 &&
                                  !selected_transaction.a_pc) ||
                                (selected_transaction.contract_type_id === 3 &&
                                  !selected_transaction.a_sc)
                              "
                              class="text-red-400 mt-3">
                              Not Yet Approved
                            </div>
                          </div>

                          <div class="mt-2">
                            <SecondaryButton
                              class="m-1"
                              @click="createPcScApproval">
                              Approve
                            </SecondaryButton>
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
                                <input
                                  v-if="combined_Form.is_loaded"
                                  v-model="combined_Form.date_loaded"
                                  type="date"
                                  class="mt-1 block w-full rounded-md border-0 py-1 px-2 text-xs text-gray-700 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600" />
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
                                <input
                                  v-if="combined_Form.is_delivered"
                                  v-model="combined_Form.date_delivered"
                                  type="date"
                                  class="mt-1 block w-full rounded-md border-0 py-1 px-2 text-xs text-gray-700 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600" />
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
                                <input
                                  v-if="combined_Form.is_onroad"
                                  v-model="combined_Form.date_onroad"
                                  type="date"
                                  class="mt-1 block w-full rounded-md border-0 py-1 px-2 text-xs text-gray-700 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600" />
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

                                  <ContractLinkModal
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
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            <span>Approved PC:</span>
                            <span v-if="selected_transaction.a_pc">
                              {{ selected_transaction.a_pc }}
                            </span>
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
                          <div class="text-sm font-medium leading-6 text-gray-900">
                            <span>Approved SC:</span>
                            <span v-if="selected_transaction.a_sc">
                              {{ selected_transaction.a_sc }}
                            </span>
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

                            <div
                              v-if="props.selected_transaction.is_split_load && linked_trans_split"
                              class="flex flex-col gap-y-2 py-3">
                              <dt class="text-gray-500">Individual Split</dt>
                              <dd class="flex items-center gap-x-2">
                                <select
                                  v-model="selectedSplitMqId"
                                  class="block rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 text-sm">
                                  <option :value="null" disabled>Select MQ...</option>
                                  <option
                                    v-for="n in linked_trans_split"
                                    :key="n.transport_transaction.id"
                                    :value="n.transport_transaction.id">
                                    {{ n.transport_transaction.a_mq ? 'MQ ' + n.transport_transaction.a_mq : 'ID ' + n.transport_transaction.id }}
                                    — {{ n.transport_transaction.customer?.last_legal_name }}
                                  </option>
                                </select>
                                <a
                                  v-if="selectedSplitMqId"
                                  :href="'/pdf_report/sales_order_confirmation_view_individual/' + selectedSplitMqId"
                                  class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
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
                    <TransactionLogTab :model-activity="model_activity" />
                  </div>

                  <div v-if="selectedTabId === 12">
                    <div>
                      <div class="font-bold text-indigo-500">
                        Staff allocation
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
                                <div class="ml-5 border border-gray-200 p-6 mb-5 rounded-lg shadow-md bg-white">
                                  <div class="px-2 pt-3 pb-3 sm:px-0">
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
                                  <div class="mt-2 border-t border-gray-100">
                                    <dl class="divide-y divide-gray-100">
                                      <div
                                        class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          Supplier User
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
                                        class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          Customer User
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
                                        class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          Supplier
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{ NiceNumber(user_comm.supplier_comm) }}
                                        </dd>
                                      </div>

                                      <div
                                        class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt
                                          class="text-sm font-medium leading-6 text-gray-900">
                                          Customer
                                        </dt>
                                        <dd
                                          class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                          {{ NiceNumber(user_comm.customer_comm) }}
                                        </dd>
                                      </div>
                                    </dl>
                                  </div>

                                  <div class="mt-3 col-span-4">
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
                                      class="mt-4"
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
                              <div v-else-if="n.transport_transaction.a_pc">
                                PC
                                {{ n.transport_transaction.a_pc }}
                              </div>
                              <div v-else-if="n.transport_transaction.a_sc">
                                SC
                                {{ n.transport_transaction.a_sc }}
                              </div>
                              <div v-else>Not Approved</div>
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
  </AppLayout>
</template>
