<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, inject, onBeforeMount, ref, watch } from 'vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import { debounce } from 'lodash';
  import PaginationModified from '@/Components/UI/PaginationModified.vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';

  const swal = inject('$swal');

  let NiceTDate = (date) => {
    const _date = new Date(date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const dayString = _date
      .toLocaleString('en', { weekday: 'long', timeZone: 'Africa/Johannesburg' })
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
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatStart = () => {
    const _date = new Date(filterForm.start_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
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
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatLate = () => {
    const _date = new Date(transport_trans_Form.transport_date_latest);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoicePdDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_paid_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoicePayByDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_pay_by_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const formatInvoiceDate = () => {
    const _date = new Date(transport_invoice_Form.invoice_date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
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
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? 'asc',
    show: props.filters.show ?? 5,
    supplier_name: props.filters.supplier_name ?? null,
    customer_name: props.filters.customer_name ?? null,
    transporter_name: props.filters.transporter_name ?? null,
    product_name: props.filters.product_name ?? null,
    start_date: props.filters.start_date ?? null,
    end_date: props.filters.end_date ?? null,
    id: props.filters.id ?? null,
    selected_trans_id: props.selected_transaction.id ?? null,
    new_trade_added: false,
    old_id: null,
    a_pc: props.filters.a_pc ?? null,
  });

  let filter = debounce(() => {
    isLoading.value = true;
    filterForm.get(route('pc_overview.index'), {
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

  watch(
    () => filterForm.a_pc,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  let filteredTrans = computed(() => props.transactions.data);

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

    return (pc_weight - mq_weight).toFixed(2);
  };

  let weightDelivered = () => {
    let mq_weight = 0;

    if (props.linked_trans_other != null) {
      for (let linked of props.linked_trans_other) {
        if (linked.transport_transaction.transport_finance.weight_ton_outgoing != null) {
          mq_weight += linked.transport_transaction.transport_finance.weight_ton_outgoing;
        }
      }
    }

    return mq_weight.toFixed(2);
  };

  let weightTotal = () => {
    let pc_weight = 0;

    if (props.selected_transaction.transport_finance.weight_ton_outgoing != null) {
      pc_weight = props.selected_transaction.transport_finance.weight_ton_outgoing;
    }

    return pc_weight.toFixed(2);
  };

  const getWeightForTransaction = (transaction) => {
    // Check if customer has invoice basis set to 'Offload Weight'
    const useOffloadWeight =
      transaction.customer?.invoice_basis?.value === 'Offload Weight';

    // Get weighbridge values
    let weighbridgeUpload = 0;
    let weighbridgeOffload = 0;

    if (
      transaction.transport_driver_vehicle &&
      transaction.transport_driver_vehicle.length > 0
    ) {
      transaction.transport_driver_vehicle.forEach((dv) => {
        weighbridgeUpload += dv.weighbridge_upload_weight || 0;
        weighbridgeOffload += dv.weighbridge_offload_weight || 0;
      });
    }

    // Return weighbridge values based on invoice basis, or 0 if not captured
    return useOffloadWeight ? weighbridgeOffload : weighbridgeUpload;
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
</script>

<template>
  <AppLayout :title="getTitle">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <div v-if="selected_transaction != null">PC Overview: {{ getTitle }}</div>
        <div v-else>PC Overview</div>
      </h2>
    </template>

    <div class="py-2">
      <div class="bg-white m-2 p-2 shadow-xl sm:rounded-lg">
        <div>
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="mt-3 flow-root">
              <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                  <div class="ml-4 mb-3">
                    <!-- Single compact filter row -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                      <div class="w-28 text-xs">
                        <VueDatePicker
                          v-model="filterForm.start_date"
                          :format="formatStart"
                          :input-class-name="'text-xs h-9'"
                          :teleport="true"
                          placeholder="Start"></VueDatePicker>
                      </div>
                      <div class="w-28 text-xs">
                        <VueDatePicker
                          v-model="filterForm.end_date"
                          :format="format"
                          :input-class-name="'text-xs h-9'"
                          :teleport="true"
                          placeholder="End"></VueDatePicker>
                      </div>

                      <select
                        v-model="filterForm.show"
                        class="h-9 w-16 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="100">100</option>
                        <option :value="200">200</option>
                        <option :value="500">500</option>
                      </select>

                      <input
                        v-model.number="filterForm.a_pc"
                        class="w-20 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="PC#..."
                        type="search" />

                      <input
                        v-model.number="filterForm.supplier_name"
                        class="w-28 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="supplier..."
                        type="search" />

                      <input
                        v-model.number="filterForm.customer_name"
                        class="w-28 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="customer..."
                        type="search" />

                      <input
                        v-model.number="filterForm.transporter_name"
                        class="w-28 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="transporter..."
                        type="search" />

                      <input
                        v-model.number="filterForm.product_name"
                        class="w-24 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="product..."
                        type="search" />

                      <input
                        v-model.number="filterForm.id"
                        class="w-24 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="contract#..."
                        type="search" />

                      <input
                        v-model.number="filterForm.old_id"
                        class="w-24 h-9 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                        placeholder="old#..."
                        type="search" />

                      <secondary-button
                        class="h-9 px-3 text-xs"
                        @click="filter">
                        Search
                      </secondary-button>
                      <secondary-button
                        class="h-9 px-3 text-xs"
                        @click="clear">
                        Clear
                      </secondary-button>
                      <secondary-button
                        class="h-9 px-3 text-xs"
                        @click="toggleDetails">
                        Toggle
                      </secondary-button>
                    </div>
                  </div>

                  <div></div>
                  <div class="">
                    <div class="">
                      <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                          <tr>
                            <th
                              class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                              scope="col">
                              ID
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
                              PRODUCT
                            </th>

                            <th
                              v-if="showDetails"
                              :class="header_styler"
                              scope="col">
                              Units Incoming
                            </th>
                            <th
                              v-if="showDetails"
                              :class="header_styler"
                              scope="col">
                              Cost Price
                            </th>
                            <th
                              v-if="showDetails"
                              :class="header_styler"
                              scope="col">
                              Selling Price
                            </th>
                            <th
                              v-if="showDetails"
                              :class="header_styler"
                              scope="col">
                              Gross Profit
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
                            ]"
                            @click="updateSelectedTrans(transaction.id)">
                            <td :class="row_styler">
                              <span
                                v-if="transaction.a_pc"
                                class="text-indigo-500 font-bold">
                                PC:{{ transaction.a_pc }}
                              </span>
                              <span v-if="transaction.a_pc"></span>
                              <span class="font-bold">ID:{{ transaction.id }}</span>
                            </td>
                            <td :class="row_styler">
                              {{ transaction.contract_type.name }}
                            </td>

                            <td :class="row_styler">
                              {{ NiceTDate(transaction.transport_date_earliest) }}
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
                              {{ transaction.product.name }}
                            </td>

                            <td
                              v-if="showDetails"
                              :class="row_styler">
                              {{ transaction.transport_load.no_units_incoming }}
                            </td>

                            <td
                              v-if="showDetails"
                              :class="row_styler">
                              {{ NiceNumber(transaction.transport_finance.cost_price) }}
                            </td>

                            <td
                              v-if="showDetails"
                              :class="row_styler">
                              {{
                                NiceNumber(transaction.transport_finance.selling_price)
                              }}
                            </td>

                            <td
                              v-if="showDetails"
                              :class="row_styler">
                              {{ NiceNumber(transaction.transport_finance.gross_profit) }}
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <div
                        v-if="transactions.data.length"
                        class="w-full flex justify-center mt-5 mb-4">
                        <PaginationModified :links="transactions.links" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="sticky bg-white m-2 p-2 shadow-xl sm:rounded-lg">
        <div>
          <div class="px-4 sm:px-6 lg:px-8">
            <div>
              <div class="relative border-b border-gray-200 pb-2 sm:pb-0">
                <div class="md:flex md:items-center md:justify-between">
                  <h3 class="text-base font-semibold leading-5 text-gray-900">
                    Linked Transactions
                  </h3>
                  <div class="mt-1 flex md:absolute md:right-0 md:top-1 md:mt-0"></div>
                </div>
              </div>
              <div class="m-1 p-1">
                <div class="mb-2 overflow-x-auto max-h-96 overflow-y-auto">
                  <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead>
                        <tr>
                          <th
                            class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                            scope="col">
                            Deal Ticket
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Supplier
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Customer
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Transporter
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Product
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Weight
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Cost
                          </th>
                          <th
                            class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                            scope="col">
                            Selling Price
                          </th>

                          <th
                            class="relative py-2 pl-3 pr-4 sm:pr-0"
                            scope="col">
                            <span class="sr-only">Edit</span>
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200">
                        <tr
                          v-for="contract in props.linked_trans_other"
                          :key="contract.id">
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                            <Link
                              :data="{
                                selected_trans_id: contract.transport_transaction.id,
                              }"
                              href="/transaction_summary"
                              method="get"
                              target="_blank">
                              MQ:{{ contract.transport_transaction.a_mq }} ID:{{
                                contract.transport_transaction.id
                              }}
                            </Link>
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ contract.transport_transaction.supplier.last_legal_name }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ contract.transport_transaction.customer.last_legal_name }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{
                              contract.transport_transaction.transporter.last_legal_name
                            }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ contract.transport_transaction.product.name }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{
                              getWeightForTransaction(
                                contract.transport_transaction
                              ).toFixed(2)
                            }}
                            tons
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{
                              NiceNumber(
                                contract.transport_transaction.transport_finance
                                  .cost_price
                              )
                            }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{
                              NiceNumber(
                                contract.transport_transaction.transport_finance
                                  .selling_price
                              )
                            }}
                          </td>
                          <td
                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                            <Link
                              :data="{
                                selected_trans_id: contract.transport_transaction.id,
                              }"
                              class="text-indigo-600 hover:text-indigo-900"
                              href="/transaction_summary"
                              method="get"
                              target="_blank">
                              Edit
                              <span class="sr-only">
                                , {{ contract.transport_transaction.id }}
                              </span>
                            </Link>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <div>
                      <div class="relative mt-2">
                        <div
                          aria-hidden="true"
                          class="absolute inset-0 flex items-center">
                          <div class="w-full border-t border-gray-300" />
                        </div>
                        <div class="relative flex justify-center">
                          <span class="bg-white px-2 text-xs text-gray-500">
                            PC Details
                          </span>
                        </div>
                      </div>

                      <table class="mt-1 min-w-full divide-y divide-gray-300">
                        <thead>
                          <tr>
                            <th
                              class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Tons
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Cost Price
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Transport Costs
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Other Costs
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Selling Price
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              GP
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Tons Del
                            </th>
                            <th
                              class="px-3 py-2 text-left text-sm font-semibold text-gray-900"
                              scope="col">
                              Tons Outstanding
                            </th>

                            <th
                              class="relative py-2 pl-3 pr-4 sm:pr-0"
                              scope="col">
                              <span class="sr-only">Edit</span>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                          <tr>
                            <td
                              class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                              {{ weightTotal() }} tons
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{
                                NiceNumberInt(
                                  props.selected_transaction.transport_finance.cost_price
                                )
                              }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{
                                NiceNumberInt(
                                  props.selected_transaction.transport_finance
                                    .transport_cost
                                )
                              }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              <div
                                v-if="
                                  props.selected_transaction.transport_finance.other_costs
                                ">
                                {{
                                  NiceNumberInt(
                                    props.selected_transaction.transport_finance
                                      .other_costs
                                  )
                                }}
                              </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{
                                NiceNumberInt(
                                  props.selected_transaction.transport_finance
                                    .selling_price
                                )
                              }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{
                                NiceNumberInt(
                                  props.selected_transaction.transport_finance
                                    .gross_profit
                                )
                              }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{ weightDelivered() }} tons
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                              {{ weightRemaining() }} tons
                            </td>
                            <td
                              class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                              <Link
                                :data="{
                                  selected_trans_id: props.selected_transaction.id,
                                }"
                                class="text-indigo-600 hover:text-indigo-900"
                                href="/transaction_summary"
                                method="get"
                                target="_blank">
                                Edit
                                <span class="sr-only">
                                  , {{ props.selected_transaction.id }}
                                </span>
                              </Link>
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
      </div>
    </div>
  </AppLayout>
</template>
