<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, inject, onBeforeMount, ref, watch } from 'vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import { debounce } from 'lodash';
  import PaginationModified from '@/Components/UI/PaginationModified.vue';
  import Icon from '@/Components/Icon.vue';
  import BaseTooltip from '@/Components/UI/BaseTooltip.vue';
  import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';
  import TransactionFilters from '@/Components/TransactionSummary/TransactionFilters.vue';

  const swal = inject('$swal');

  let dayIncluded = (_date) => {
    let _day = NiceDay(_date);
    switch (_day) {
      case 1:
        return mon.value;
      case 2:
        return tue.value;
      case 3:
        return wed.value;
      case 4:
        return thu.value;
      case 5:
        return fri.value;
      case 6:
        return sat.value;
      case 7:
        return sun.value;
      default:
        return false;
    }
  };

  let NiceDay = (_date) => {
    return new Date(_date).getDay();
  };

  let NiceTDate = (date) => {
    const _date = new Date(date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const dayString = _date
      .toLocaleString('en', { weekday: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
  };

  let TrunkCateText = (_text) => {
    return _text.length > 40 ? _text.slice(0, 40) + '...' : _text;
  };

  const format = () => {
    const _date = new Date(filterForm.end_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatStart = () => {
    const _date = new Date(filterForm.start_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  let NiceNumber = (_number) => {
    if (_number === null) {
      return 0;
    } else {
      let val = (_number / 1).toFixed(2).replace('.', '.');
      return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  };

  let NiceNumberInt = (_number) => {
    if (_number === null) {
      return 0;
    } else {
      let val = Math.round(_number);
      return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  };

  const formatEarly = () => {
    const _date = new Date(transport_trans_Form.transport_date_earliest);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatLate = () => {
    const _date = new Date(transport_trans_Form.transport_date_latest);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoicePdDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_paid_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoicePayByDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_pay_by_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoiceDate = () => {
    const _date = new Date(transport_invoice_Form.invoice_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'short', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const props = defineProps({
    transactions: Object,
    selected_transaction: Object,
    filters: Object,
    start_date: String,
    end_date: String,
    rules_with_approvals: Object,
    deal_ticket: Object,
    transport_order: Object,
    purchase_order: Object,
    sales_order: Object,
    linked_trans_other: Object,
    planned_tons_in: Number,
    planned_tons_out: Number,
    weight_uploaded: Number,
    weight_offloaded: Number,
    cost_price: Number,
    trans_cost: Number,
    other_costs: Number,
    selling_price: Number,
    gp: Number,
    gp_perc: Number,
    no_trades: Number,
    contract_types: Object,
  });

  onBeforeMount(async () => {});

  const permissions = computed(() => usePage().props.permissions);
  const roles_permissions = computed(() => usePage().props.roles_permissions);
  const can_adjust_gp = computed(() =>
    usePage().props.roles_permissions.permissions.includes('edit_adjusted_gp')
  );

  const selectedSplitCustomer = ref(2);

  let isLoading = ref(false);
  let isUpdating = ref(false);

  const selectedTabId = ref(0);
  const selectTab = (id) => {
    selectedTabId.value = id;
  };

  const newTradeAdded = () => {
    filterForm.new_trade_added = true;
  };

  const filterForm = useForm({
    isActive: props.filters?.isActive ?? null,
    field: props.filters?.field ?? null,
    direction: props.filters?.direction ?? 'asc',
    show: props.filters?.show ?? 25,
    supplier_name: props.filters?.supplier_name ?? null,
    customer_name: props.filters?.customer_name ?? null,
    transporter_name: props.filters?.transporter_name ?? null,
    product_name: props.filters?.product_name ?? null,
    start_date: props.filters?.start_date ?? null,
    end_date: props.filters?.end_date ?? null,
    id: props.filters?.id ?? null,
    selected_trans_id: props.selected_transaction?.id ?? null,
    new_trade_added: false,
    old_id: null,
    a_mq: props.filters?.a_mq ?? null,
    a_pc: props.filters?.a_pc ?? null,
    a_sc: props.filters?.a_sc ?? null,
    contract_type_id: props.filters.contract_type_id ?? null,
  });

  let filter = debounce(() => {
    isLoading.value = true;
    filterForm.get(route('home_overview.index'), {
      preserveState: true,
      preserveScroll: true,
      onFinish: (visit) => {
        isLoading.value = false;
      },
    });
  }, 150);

  let sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
  };

  watch(
    () => filterForm.a_mq,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.a_pc,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.a_sc,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.supplier_name,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.customer_name,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.transporter_name,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.product_name,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.show,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.start_date,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.end_date,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.contract_type_id,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.id,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => filterForm.old_id,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  let mon = ref(true);
  let tue = ref(true);
  let wed = ref(true);
  let thu = ref(true);
  let fri = ref(true);
  let sat = ref(true);
  let sun = ref(true);

  let filteredTrans = computed(() => {
    // Ensure props.transactions and props.transactions.data are defined
    if (!props.transactions?.data) {
      return []; // Return an empty array if transactions or data is null/undefined
    }

    // Check if all day values are true
    if (
      mon.value &&
      tue.value &&
      wed.value &&
      thu.value &&
      fri.value &&
      sat.value &&
      sun.value
    ) {
      return props.transactions.data; // Return all transactions if all days are selected
    }

    // Otherwise, filter transactions based on the dayIncluded function
    return props.transactions.data.filter((trans) => {
      return dayIncluded(trans.transport_date_earliest);
    });
  });
  let updateSelectedTrans = async (_id) => {
    filterForm.selected_trans_id = _id;
    filter();
  };

  const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;
    filterForm.id = null;
    filterForm.old_id = null;
    filterForm.a_mq = null;
    filterForm.a_pc = null;
    filterForm.a_sc = null;

    mon.value = true;
    tue.value = true;
    wed.value = true;
    thu.value = true;
    fri.value = true;
    sat.value = true;
    sun.value = true;

    filter();
  };

  let showDetails = ref(false);

  const toggleDetails = () => {
    showDetails.value === true ? (showDetails.value = false) : (showDetails.value = true);
  };

  let weightRemaining = () => {
    let pc_weight = 0;
    let mq_weight = 0;

    if (props.linked_trans_other != null) {
      for (let linked of props.linked_trans_other) {
        if (linked.transport_transaction.transport_finance.weight_ton_outgoing != null) {
          mq_weight += linked.transport_transaction.transport_finance.weight_ton_outgoing;
        }
      }
    }

    //props.selected_transaction.transport_load.no_units_incoming
    if (props.selected_transaction.transport_finance.weight_ton_outgoing != null) {
      pc_weight = props.selected_transaction.transport_finance.weight_ton_outgoing;
    }

    return (pc_weight - mq_weight).toFixed(4);
  };

  const getTitle = computed(() => {
    return props.selected_transaction != null
      ? props.selected_transaction.contract_type.name + props.selected_transaction.id
      : 'PC Overview';
  });

  const header_styler = computed(
    () =>
      'sticky top-0 z-10 hidden border-b border-gray-300 bg-gray-200 px-1 py-2 text-left text-sm font-semibold text-gray-900 sm:table-cell'
  );
  const row_styler = computed(
    () => 'whitespace-nowrap border-b px-1 py-1 text-sm text-gray-500 lg:table-cell'
  );

  const isPaymentOverdue = (invoice_details) => {
    return invoice_details && invoice_details.overdue > 0;
  };

  const hasWeighbridgeVariance = (driver_vehicles) => {
    if (!driver_vehicles || driver_vehicles.length === 0) {
      return false;
    }
    for (let i = 0; i < driver_vehicles.length; i++) {
      if (driver_vehicles[i].is_weighbridge_variance) {
        return true;
      }
    }
    return false;
  };

  const warningLister = (trans) => {
    var the_list = '';

    // Add transport status alerts
    if (trans.transport_status && trans.transport_status.length > 0) {
      trans.transport_status.forEach(function (arrayItem) {
        var entity = arrayItem.status_entity.entity;
        var type = arrayItem.status_type.type;
        the_list += '  ' + entity + '_' + type + '.';
      });
    }

    // Add payment overdue alert
    if (
      trans.transport_invoice_details &&
      isPaymentOverdue(trans.transport_invoice_details)
    ) {
      the_list += '  payment_overdue.';
    }

    // Add weighbridge variance alert
    if (
      trans.transport_driver_vehicle &&
      hasWeighbridgeVariance(trans.transport_driver_vehicle)
    ) {
      the_list += '  weighbridge_variance.';
    }

    return the_list;
  };

  const hasAnyAlerts = (trans) => {
    // Check if there are any transport status alerts
    if (trans.transport_status && trans.transport_status.length > 0) {
      return true;
    }
    // Check if payment is overdue
    if (
      trans.transport_invoice_details &&
      isPaymentOverdue(trans.transport_invoice_details)
    ) {
      return true;
    }
    // Check if there's a weighbridge variance
    if (
      trans.transport_driver_vehicle &&
      hasWeighbridgeVariance(trans.transport_driver_vehicle)
    ) {
      return true;
    }
    return false;
  };
</script>

<template>
  <AppLayout :title="getTitle">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Home Overview</h2>
    </template>

    <div class="p-1 h-screen flex flex-col">
      <div
        class="bg-white m-2 shadow-xl sm:rounded-lg flex flex-col overflow-hidden"
        style="height: calc(75vh)">
        <div class="flex-shrink-0 bg-white border-b border-gray-200">
          <transaction-filters
            :contract-types="contract_types"
            :filter-form="filterForm"
            :format-end="format"
            :format-start="formatStart"
            :fri="fri"
            :mon="mon"
            :sat="sat"
            :show-add="false"
            :sun="sun"
            :thu="thu"
            :tue="tue"
            :wed="wed"
            @clear="clear"
            @search="filter"
            @toggle-details="toggleDetails"
            @update:fri="fri = $event"
            @update:mon="mon = $event"
            @update:sat="sat = $event"
            @update:sun="sun = $event"
            @update:thu="thu = $event"
            @update:tue="tue = $event"
            @update:wed="wed = $event" />
        </div>

        <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8">
          <div class="inline-block min-w-full py-2 align-middle">
            <table class="min-w-full border-separate border-spacing-0">
              <thead>
                <tr>
                  <th
                    class="sticky top-0 z-10 border-b border-gray-300 bg-gray-200 py-1 pl-1 pr-1 text-left text-sm font-semibold text-gray-900"
                    scope="col">
                    ID
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    STATUS
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    TYPE
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    DATE
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    C_ORDER
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    SUPPLIER
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    CUSTOMER
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    TRANSPORTER
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    REG#
                  </th>
                  <th
                    :class="header_styler"
                    scope="col">
                    PRODUCT
                  </th>

                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    Trade Status
                  </th>

                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    Units
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    Cost/Ton
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    TC/Ton
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    Price/Ton
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    GP/Ton
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    GP%
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    Process Notes
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(transaction, index) in filteredTrans"
                  :key="transaction.id"
                  :class="[
                    transaction.id === props.selected_transaction.id ? 'bg-gray-200' : '',
                    'hover:bg-gray-100 text-sm focus-within:bg-gray-100',
                  ]">
                  <td :class="row_styler">
                    <Link
                      :data="{ selected_trans_id: transaction.id }"
                      href="/transaction_summary"
                      method="get"
                      target="_blank">
                      <span
                        v-if="transaction.a_mq"
                        class="font-bold">
                        (MQ:{{ transaction.a_mq }})
                      </span>
                      <span
                        v-if="transaction.a_pc"
                        class="font-bold">
                        (PC:{{ transaction.a_pc }})
                      </span>
                      <span
                        v-if="transaction.a_sc"
                        class="font-bold">
                        (SC:{{ transaction.a_sc }})
                      </span>
                      <span>(ID:{{ transaction.id }})</span>
                      <span></span>
                      <span>(Old:{{ transaction.old_id }})</span>
                    </Link>
                  </td>
                  <td :class="row_styler">
                    <div v-if="transaction.supplier.terms_of_payment_id == 1">
                      <base-tooltip content="Supplier C.O.D account.">
                        <icon
                          class="mr-3 w-6 h-6 fill-red-500"
                          name="bell-solid" />
                      </base-tooltip>
                    </div>

                    <div v-if="hasAnyAlerts(transaction)">
                      <base-tooltip :content="warningLister(transaction)">
                        <icon
                          class="mr-3 w-3 h-3 animate-pulse fill-red-500"
                          name="triangle" />
                      </base-tooltip>
                    </div>

                    <div v-if="!transaction.include_in_calculations">
                      <base-tooltip content="Exclude from calculations.">
                        <icon
                          class="mr-3 w-3 h-3 fill-gray-500"
                          name="info" />
                      </base-tooltip>
                    </div>
                  </td>
                  <td :class="row_styler">
                    {{ transaction.contract_type.name }}
                  </td>

                  <td :class="row_styler">
                    {{ NiceTDate(transaction.transport_date_earliest) }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <div v-if="transaction.transport_job?.customer_order_number">
                      {{ transaction.transport_job.customer_order_number }}
                    </div>
                    <div v-else>N/A</div>
                  </td>

                  <td :class="row_styler">
                    {{ transaction.supplier.last_legal_name }}
                  </td>
                  <td :class="row_styler">
                    {{ transaction.customer.last_legal_name }}
                  </td>
                  <td :class="row_styler">
                    {{ transaction.transporter.last_legal_name }}
                  </td>

                  <td :class="row_styler">
                    <div
                      v-if="
                        transaction.transport_driver_vehicle &&
                        transaction.transport_driver_vehicle.length > 0
                      ">
                      <div
                        v-for="driver_vehicle of transaction.transport_driver_vehicle"
                        :key="driver_vehicle.id">
                        {{ driver_vehicle.vehicle.reg_no }}
                      </div>
                    </div>
                    <div v-else>N/A</div>
                  </td>

                  <td :class="row_styler">
                    {{ transaction.product.name }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <div class="flex items-center gap-0.5">
                      <base-tooltip content="Deal Ticket">
                        <check-icon v-if="transaction.deal_ticket?.is_active" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip content="Purchase Order">
                        <check-icon v-if="transaction.purchase_order?.is_active" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip content="Sales Order">
                        <check-icon v-if="transaction.sales_order?.is_active" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip content="Transport Order">
                        <check-icon v-if="transaction.transport_order?.is_active" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip content="Weighbridge Certificate">
                        <check-icon v-if="transaction.transport_load?.is_weighbridge_certificate_received" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip content="Invoice">
                        <check-icon v-if="transaction.transport_invoice?.is_active" class="w-4 h-4 fill-green-300" />
                        <x-mark-icon v-else class="w-4 h-4 fill-red-400" />
                      </base-tooltip>
                      <base-tooltip
                        v-if="isPaymentOverdue(transaction.transport_invoice_details)"
                        content="Payment overdue">
                        <icon
                          class="w-4 h-4 fill-red-500"
                          name="dollar" />
                      </base-tooltip>
                    </div>
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <span
                      v-if="transaction.transport_finance?.weight_ton_incoming_actual"
                      class="text-green-600 font-medium">
                      {{ transaction.transport_finance.weight_ton_incoming_actual }}
                    </span>
                    <span v-else>
                      {{ transaction.transport_load.no_units_incoming }}
                    </span>
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{ NiceNumberInt(transaction.transport_finance.cost_price_per_ton) }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{
                      NiceNumberInt(transaction.transport_finance.transport_rate_per_ton)
                    }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{
                      NiceNumberInt(transaction.transport_finance.selling_price_per_ton)
                    }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{
                      NiceNumberInt(transaction.transport_finance.gross_profit_per_ton)
                    }}
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{ transaction.transport_finance.gross_profit_percent?.toFixed(2) }}%
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <div v-if="transaction.process_notes">
                      <base-tooltip :content="transaction.process_notes">
                        {{ TrunkCateText(transaction.process_notes) }}
                      </base-tooltip>
                    </div>
                    <div v-else>None...</div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div v-if="transactions">
              <div
                v-if="transactions.data.length"
                class="w-full flex justify-center mt-5 mb-4">
                <PaginationModified :links="transactions.links" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="sticky bg-white m-2 p-2 shadow-xl sm:rounded-lg">
        <div>
          <div class="px-4 sm:px-6 lg:px-8">
            <div>
              <div class="relative border-b border-gray-200 pb-1 sm:pb-0">
                <div class="md:flex md:items-center md:justify-between">
                  <h3
                    v-if="filterForm.end_date"
                    class="text-base font-semibold leading-6 text-gray-900">
                    {{ NiceTDate(filters['start_date']) }} to
                    {{ NiceTDate(filters['end_date']) }} (filtered)
                  </h3>
                  <div class="mt-1 flex md:absolute md:right-0 md:top-3 md:mt-0"></div>
                </div>
              </div>
              <div class="m-2 p-1">
                <div class="mb-2">
                  <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead class="bg-gray-200">
                        <tr>
                          <th
                            class="py-1 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                            scope="col">
                            No Trades
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Planned Tons In
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Planned Tons Out
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Weight Uploaded
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Weight Offloaded
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Cost Price
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Trans Cost
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Other Cost
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Selling Price
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            GP
                          </th>
                          <th
                            class="px-3 py-1 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            GP %
                          </th>

                          <th
                            class="relative py-1 pl-3 pr-4 sm:pr-0"
                            scope="col">
                            <span class="sr-only">Edit</span>
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200">
                        <tr>
                          <td
                            class="whitespace-nowrap py-1 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                            {{ no_trades }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ planned_tons_in }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ planned_tons_out }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ weight_uploaded }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ weight_offloaded }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumberInt(cost_price) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumberInt(trans_cost) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumberInt(other_costs) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumberInt(selling_price) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumberInt(gp) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ gp_perc?.toFixed(2) }}%
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <div></div>
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
