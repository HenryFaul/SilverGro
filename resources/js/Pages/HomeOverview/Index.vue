<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, inject, onBeforeMount, ref, watch } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import PaginationModified from '@/Components/UI/PaginationModified.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Icon from '@/Components/Icon.vue';
import BaseTooltip from '@/Components/UI/BaseTooltip.vue';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';

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

    mon.value = true;
    tue.value = true;
    wed.value = true;
    thu.value = true;
    fri.value = true;
    sat.value = true;
    sun.value = true;

    filter();
  };

  let showDetails = ref(true);

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
      'sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-2 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell'
  );
  const row_styler = computed(
    () => 'whitespace-nowrap border-b px-3 py-1 text-sm text-gray-500 lg:table-cell'
  );

  const warningLister = (Warnings) => {
    var the_list = '';

    Warnings.forEach(function (arrayItem) {
      var entity = arrayItem.status_entity.entity;
      var type = arrayItem.status_type.type;

      the_list += '  ' + entity + '_' + type + '.';
    });
    return the_list;
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
        <div
          class="flex-shrink-0 bg-white px-4 sm:px-6 lg:px-8 py-1.5 border-b border-gray-200">
          <div class="flex flex-wrap items-center gap-1.5">
            <div class="w-36">
              <VueDatePicker
                v-model="filterForm.start_date"
                :format="formatStart"
                :teleport="true"></VueDatePicker>
            </div>
            <div class="w-36">
              <VueDatePicker
                v-model="filterForm.end_date"
                :format="format"
                :teleport="true"></VueDatePicker>
            </div>
            <select
              v-model="filterForm.contract_type_id"
              class="input-filter-l w-36 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              <option :value="null">All contracts</option>
              <option
                v-for="n in contract_types"
                :key="n.id"
                :value="n.id">
                {{ n.name }}
              </option>
            </select>
            <select
              v-model="filterForm.show"
              class="input-filter-l w-20 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
              <option :value="200">200</option>
              <option :value="500">500</option>
            </select>
            <input
              v-model.number="filterForm.old_id"
              aria-label="Search"
              class="block w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="old no..."
              type="search" />
            <input
              v-model.number="filterForm.id"
              aria-label="Search"
              class="block w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="ID no..."
              type="search" />
            <input
              v-model.number="filterForm.a_mq"
              aria-label="Search"
              class="block w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="MQ no..."
              type="search" />
            <input
              v-model.number="filterForm.supplier_name"
              aria-label="Search"
              class="block w-28 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="supplier..."
              type="search" />
            <input
              v-model.number="filterForm.customer_name"
              aria-label="Search"
              class="block w-28 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="customer..."
              type="search" />
            <input
              v-model.number="filterForm.transporter_name"
              aria-label="Search"
              class="block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="transporter..."
              type="search" />
            <input
              v-model.number="filterForm.product_name"
              aria-label="Search"
              class="block w-28 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="product..."
              type="search" />

            <secondary-button @click="filter">Search</secondary-button>
            <secondary-button @click="clear">Clear</secondary-button>
            <secondary-button @click="toggleDetails">Toggle</secondary-button>

            <div class="flex items-center gap-1">
              <input
                id="mon"
                v-model="mon"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="mon">
                Mon
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="tue"
                v-model="tue"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="tue">
                Tue
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="wed"
                v-model="wed"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="wed">
                Wed
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="thu"
                v-model="thu"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="thu">
                Thu
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="fri"
                v-model="fri"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="fri">
                Fri
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="sat"
                v-model="sat"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="sat">
                Sat
              </label>
            </div>
            <div class="flex items-center gap-1">
              <input
                id="sun"
                v-model="sun"
                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600"
                type="checkbox" />
              <label
                class="text-xs text-gray-700"
                for="sun">
                Sun
              </label>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8">
          <div class="inline-block min-w-full py-2 align-middle">
            <table class="min-w-full border-separate border-spacing-0">
              <thead>
                <tr>
                  <th
                    class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-1 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
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
                    D/T
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    P/O
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    S/O
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    T/O
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    WB
                  </th>
                  <th
                    v-if="showDetails"
                    :class="header_styler"
                    scope="col">
                    INV
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
                    transaction.id === props.selected_transaction.id
                      ? 'bg-indigo-300'
                      : '',
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
                      <span>(ID:{{ transaction.id }})</span>
                      <span></span>
                      <span>(Old:{{ transaction.old_id }})</span>
                    </Link>
                  </td>
                  <td :class="row_styler">
                    <div
                      v-if="
                        transaction.transport_status &&
                        transaction.transport_status.length > 0
                      ">
                      <base-tooltip
                        :content="warningLister(transaction.transport_status)">
                        <icon
                          class="w-4 h-4 animate-pulse fill-red-500"
                          name="triangle" />
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
                    <check-icon
                      v-if="transaction.deal_ticket?.is_active"
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <check-icon
                      v-if="transaction.purchase_order?.is_active"
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <check-icon
                      v-if="transaction.sales_order?.is_active"
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <check-icon
                      v-if="transaction.transport_order?.is_active"
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <check-icon
                      v-if="
                        transaction.transport_load?.is_weighbridge_certificate_received
                      "
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    <check-icon
                      v-if="transaction.transport_invoice?.is_active"
                      class="w-5 h-5 fill-green-300" />
                    <x-mark-icon
                      v-else
                      class="w-5 h-5 fill-red-400" />
                  </td>

                  <td
                    v-if="showDetails"
                    :class="row_styler">
                    {{ transaction.transport_load.no_units_incoming }}
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
                      <thead class="bg-indigo-300">
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
                            {{ NiceNumber(cost_price) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumber(trans_cost) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumber(other_costs) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumber(selling_price) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ NiceNumber(gp) }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-1 text-sm text-gray-500">
                            {{ gp_perc }}%
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
