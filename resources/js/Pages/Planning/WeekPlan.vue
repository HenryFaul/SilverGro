<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import PaginationModified from '@/Components/UI/PaginationModified.vue';
  import Icon from '@/Components/Icon.vue';
  import { Link, useForm } from '@inertiajs/vue3';
  import { computed, onMounted, ref, watch } from 'vue';
  import { debounce } from 'lodash';
  import BaseTooltip from '@/Components/UI/BaseTooltip.vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  import TradeSlideOver from '@/Components/UI/TradeSlideOver.vue';

  const props = defineProps({
    transport_trans: Object,
    filters: Object,
    end_of_week: Object,
    start_of_week: Object,
    no_loads: Number,
    weight_uploaded: Number,
    planned_tons: Number,
    weight_offloaded: Number,
    cost_price: Number,
    trans_cost: Number,
    other_costs: Number,
    selling_price: Number,
    gp: Number,
    gp_perc: Number,
    contract_types: Object,
  });

  const Form = useForm({
    date: props.filters.date ?? new Date().toISOString().substr(0, 10),
    show: props.filters.show ?? 25,
    contract_type_id: props.filters.contract_type_id ?? null,
  });

  onMounted(() => {});

  let addDay = () => {
    const result = new Date(Form.date);
    result.setDate(result.getDate() + 7);
    Form.date = result.toISOString().substr(0, 10);
  };

  let lessDay = () => {
    const result = new Date(Form.date);
    result.setDate(result.getDate() - 7);
    Form.date = result.toISOString().substr(0, 10);
  };

  let filter = debounce(() => {
    Form.get(route('planning.week'), {
      preserveState: true,
      preserveScroll: true,
    });
  }, 150);

  watch(
    () => Form.date,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => Form.show,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  watch(
    () => Form.contract_type_id,
    (exampleField, prevExampleField) => {
      filter();
    }
  );

  // col_class only controls nowrap, no font size or color so headers can match summary table
  const col_class = 'whitespace-nowrap';

  let NiceDate = (_date) => {
    return new Date(_date).toDateString();
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

  let NiceDay = (_date) => {
    return new Date(_date).getDay();
  };

  //Monday sky-200
  //Tuesday lime-200
  //Wednesday orange-200
  //Thursday fuchsia-200
  //Friday rose-200
  //Saturday zinc-200
  //Sunday emerald-200

  let DayStyle = (_date) => {
    let _day = NiceDay(_date);
    return _day === 1
      ? 'bg-sky-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100'
      : _day === 2
        ? 'bg-lime-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100'
        : _day === 3
          ? 'bg-orange-200  hover:bg-gray-100 text-xs focus-within:bg-gray-100'
          : _day === 4
            ? 'bg-fuchsia-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100'
            : _day === 5
              ? 'bg-rose-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100'
              : _day === 6
                ? 'bg-zinc-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100'
                : 'emerald-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100';
  };

  let TrunkCateText = (_text) => {
    return _text.length > 30 ? _text.slice(0, 30) + '...' : _text;
  };

  let NiceNumber = (_number) => {
    let val = isNaN(_number) ? 0.0 : (_number / 1).toFixed(2).replace('.', '.');
    return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  // helper to format currency with zero decimal places
  let NiceNumber0 = (_number) => {
    let val = isNaN(_number) ? 0.0 : Math.round(_number);
    return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  // helper to round numeric values to 2 decimal places without currency formatting
  let NiceNumberPlain = (_number) => {
    let val = isNaN(_number) ? 0.0 : (_number / 1).toFixed(2).replace('.', '.');
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  let shortDate = () => {
    return Form.date.toISOString().substr(0, 10);
  };

  let calculatedCostPrice = (trans) => {
    let offload = totalWeighBridgeOffload(trans.transport_driver_vehicle);
    let outgoing = trans;
  };

  //const isNamePresent = computed(() => name.value.length > 0)

  const format = () => {
    const _date = new Date(Form.date);
    const day = _date.getDate();
    const month = _date
      .toLocaleString('en', { month: 'long', timeZone: 'Africa/Johannesburg' })
      .toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const isPaymentOverdue = (invoice_details) => {
    return invoice_details.overdue > 0;
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

  const toolTipGen = (Message, Truck) => {
    return Message + ' truck: ' + (Truck + 1) + '.';
  };

  const totalWeighBridgeUpload = (driver_vehicles) => {
    var total = 0.0;

    if (Array.isArray(driver_vehicles)) {
      return total;
    }
    driver_vehicles.forEach(function (arrayItem) {
      var uploadWeight = arrayItem.weighbridge_upload_weight;
      total += uploadWeight;
    });

    return total;
  };

  const totalWeighBridgeOffload = (driver_vehicles) => {
    var total = 0.0;

    if (Array.isArray(driver_vehicles)) {
      return total;
    }

    driver_vehicles.forEach(function (arrayItem) {
      var uploadWeight = arrayItem.weighbridge_upload_weight;
      total += uploadWeight;
    });

    return total;
  };

  let mon = ref(true);
  let tue = ref(true);
  let wed = ref(true);
  let thu = ref(true);
  let fri = ref(true);
  let sat = ref(true);
  let sun = ref(true);

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

  let filteredTrans = computed(() =>
    mon.value &&
    tue.value &&
    wed.value &&
    thu.value &&
    fri.value &&
    sat.value &&
    sun.value
      ? props.transport_trans.data
      : props.transport_trans.data.filter((trans) => {
          return dayIncluded(trans.transport_date_earliest);
        })
  );

  const viewTradeSlideOver = ref(false);

  const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
  };

  const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
  };

  const contractName = (trans) => {
    if (trans != null) {
      return trans.contract_type.name + ':' + trans.id;
    } else return 'N/A';
  };

  const contractNameOld = (trans) => {
    if (trans != null) {
      if (trans.deal_ticket != null) {
        return trans.deal_ticket.old_id == null
          ? trans.contract_type.name + ':' + trans.old_id
          : trans.contract_type.name + ':' + trans.deal_ticket.old_id;
      }

      return trans.contract_type.name + ':' + trans.old_id;
    } else return 'N/A';
  };
</script>

<template>
  <AppLayout title="Weekly View">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Weekly View</h2>
    </template>

    <div class="py-1">
      <div class="max-w-10xl mx-auto sm:px-3 lg:px-4">
        <div class="bg-gray-50 overflow-hidden shadow-xl sm:rounded-lg min-h-600">
          <!-- filters (non-sticky) -->
          <div class="flex flex-row px-2 pt-2 pb-0 items-start">
            <div class="flex-1">
              <div class="flex flex-col">
                <!-- Filters row: calendar +/- and weekday filters all in one row -->
                <div class="flex items-center min-h-fit">
                  <button
                    class="block mr-1 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    type="button"
                    @click="lessDay()">
                    -
                  </button>

                  <div>
                    <VueDatePicker
                      v-model="Form.date"
                      :format="format"
                      :teleport="true"
                      style="width: 250px"></VueDatePicker>
                  </div>

                  <button
                    class="block ml-1 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    type="button"
                    @click="addDay()">
                    +
                  </button>

                  <!-- weekday filters to the right of the calendar -->
                  <div class="flex ml-4 space-x-2">
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="mon"
                          v-model="mon"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="mon"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="mon">
                          Mon
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="tue"
                          v-model="tue"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="tue"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="tue">
                          Tue
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="wed"
                          v-model="wed"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="wed"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="wed">
                          Wed
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="thu"
                          v-model="thu"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="thu"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="thu">
                          Thu
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="fri"
                          v-model="fri"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="fri"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="fri">
                          Fri
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="sat"
                          v-model="sat"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="sat"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="sat">
                          Sat
                        </label>
                      </div>
                    </div>
                    <div class="relative flex items-start">
                      <div class="flex h-6 items-center">
                        <input
                          id="sun"
                          v-model="sun"
                          aria-describedby="candidates-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                          name="sun"
                          type="checkbox" />
                      </div>
                      <div class="ml-1 text-sm leading-6">
                        <label
                          class="font-medium text-gray-900"
                          for="sun">
                          Sun
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Range row: directly under the calendar/filters row -->
                <div class="text-xs mt-1 ml-1 whitespace-nowrap">
                  ({{ NiceTDate(start_of_week) }} to {{ NiceTDate(end_of_week) }})
                </div>
              </div>
            </div>

            <!-- Right-aligned filters and actions -->
            <div class="flex items-start ml-auto space-x-4">
              <div class="mt-2">
                <div class="flex items-center">
                  <div class="mr-2 text-sm">Per page:</div>
                  <select
                    v-model="Form.show"
                    class="input-filter-l block w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option :value="1">1</option>
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="100">100</option>
                    <option :value="200">200</option>
                  </select>
                </div>
              </div>

              <div class="mt-2">
                <select
                  v-model="Form.contract_type_id"
                  class="input-filter-l rounded-md shadow-sm border border-gray-300 text-gray-500">
                  <option
                    :value="null"
                    selected>
                    All contracts
                  </option>

                  <option
                    v-for="n in contract_types"
                    :key="n.id"
                    :value="n.id">
                    {{ n.name }}
                  </option>
                </select>
              </div>

              <div class="mt-2">
                <trade-slide-over
                  :show="viewTradeSlideOver"
                  @close="closeTradeSlideOver" />

                <button
                  class="ml-2 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  type="button"
                  @click="showTradeSlideOver">
                  Add trans
                </button>
              </div>
            </div>
          </div>

          <!-- table section with its own scroll container and sticky header -->
          <div class="px-2 pb-2 mt-1">
            <div class="px-4 sm:px-6 lg:px-8">
              <div class="mt-1 flow-root">
                <!-- outer wrapper: horizontal scroll only -->
                <div class="-mx-4 -my-1 sm:-mx-6 lg:-mx-8 overflow-x-auto">
                  <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8">
                    <div v-if="filteredTrans.length > 0">
                      <!-- inner wrapper: vertical scroll + sticky header context -->
                      <div
                        class="bg-white rounded-md shadow max-h-[65vh] overflow-y-auto">
                        <table class="min-w-max table-auto">
                          <!-- header: light grey background, same font size as summary table header -->
                          <thead class="bg-gray-100 sticky top-0 z-10">
                            <tr>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Status
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Date
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Contract
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Product
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Tons
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Loaded
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Supplier
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Customer
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Del?
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Transporter
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Tr/Reg.
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Cost/Ton
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Price/Ton
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                TC/Ton
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                GP
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Traders Notes
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                Process Notes
                              </th>
                              <!-- Removed Cost, T/Cost, Selling headers -->
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                GP%
                              </th>
                              <th
                                :class="col_class"
                                class="px-2 py-2 text-sm font-semibold text-black"
                                scope="col">
                                GP/Ton
                              </th>
                            </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                              v-for="(trans, index) in filteredTrans"
                              :key="trans.id"
                              :class="DayStyle(trans.transport_date_earliest)">
                              <td class="px-2 py-1">
                                <div class="flex items-center gap-1">
                                  <base-tooltip
                                    v-if="trans.supplier.terms_of_payment_id == 1"
                                    content="Supplier C.O.D account.">
                                    <icon
                                      class="w-6 h-6 fill-red-500"
                                      name="bell-solid" />
                                  </base-tooltip>

                                  <base-tooltip
                                    v-if="trans.transport_driver_vehicle.is_payment_overdue"
                                    content="Payment overdue.">
                                    <icon
                                      class="w-3 h-3 fill-red-500"
                                      name="dollar" />
                                  </base-tooltip>

                                  <base-tooltip
                                    v-if="trans.transport_invoice_details && isPaymentOverdue(trans.transport_invoice_details)"
                                    content="Invoice payment overdue.">
                                    <icon
                                      class="w-3 h-3 fill-orange-500"
                                      name="dollar" />
                                  </base-tooltip>

                                  <base-tooltip
                                    v-if="hasAnyAlerts(trans)"
                                    :content="warningLister(trans)">
                                    <icon
                                      class="w-3 h-3 animate-pulse fill-red-500"
                                      name="triangle" />
                                  </base-tooltip>

                                  <base-tooltip
                                    v-if="!trans.include_in_calculations"
                                    content="Exclude from calculations.">
                                    <icon
                                      class="w-3 h-3 fill-gray-500"
                                      name="info" />
                                  </base-tooltip>
                                </div>
                              </td>
                              <td class="px-2 py-1">
                                {{ NiceTDate(trans.transport_date_earliest) }}
                              </td>
                              <td class="px-2 py-1">
                                <div class="font-bold">
                                  <Link
                                    :data="{ selected_trans_id: trans.id }"
                                    href="/transaction_summary"
                                    method="get"
                                    target="_blank">
                                    <span v-if="trans.a_mq">MQ{{ trans.a_mq }}</span>
                                    <span v-else>ID:{{ trans.id }}</span>
                                  </Link>
                                </div>
                                <div
                                  v-if="trans.old_id"
                                  class="italic">
                                  (OLD:{{ trans.old_id }})
                                </div>
                              </td>
                              <td class="px-2 py-1">
                                {{ trans.product.name }}
                              </td>
                              <td class="px-2 py-1">
                                <div
                                  v-if="
                                    trans.transport_finance.weight_ton_incoming_actual !=
                                    0
                                  ">
                                  {{ trans.transport_finance.weight_ton_incoming_actual }}
                                </div>
                                <div v-else>
                                  {{ trans.transport_finance.weight_ton_incoming }}
                                </div>
                                <!--                                                            <div v-if="totalWeighBridgeUpload(trans.transport_driver_vehicle)>0">
                                                                                  {{ totalWeighBridgeUpload(trans.transport_driver_vehicle)}}
                                                                              </div>

                                                                              <div v-else>
                                                                                  {{ trans.transport_finance.weight_ton_incoming }}
                                                                              </div>-->
                              </td>
                              <td class="px-2 py-1">
                                <div v-if="trans.transport_driver_vehicle">
                                  <div
                                    v-for="(
                                      item, index
                                    ) of trans.transport_driver_vehicle">
                                    <div v-if="item.is_loaded">
                                      {{ NiceTDate(item.date_loaded) }}
                                    </div>
                                    <div v-else>
                                      <icon
                                        class="mr-2 w-3 h-3"
                                        name="cross-solid" />
                                    </div>
                                  </div>
                                </div>
                              </td>
                              <td class="px-2 py-1">
                                {{ trans.supplier.last_legal_name }}
                              </td>
                              <td class="px-2 py-1">
                                {{ trans.customer.last_legal_name }}
                              </td>
                              <td class="px-2 py-1">
                                <div v-if="trans.transport_driver_vehicle">
                                  <div
                                    v-for="(
                                      item, index
                                    ) of trans.transport_driver_vehicle">
                                    <base-tooltip
                                      v-if="item.is_delivered"
                                      :content="toolTipGen('Delivered', index)">
                                      <icon
                                        class="mr-3 w-6 h-6 fill-green-300 animate-pulse"
                                        name="truck" />
                                    </base-tooltip>

                                    <base-tooltip
                                      v-else-if="item.is_loaded"
                                      :content="toolTipGen('Loaded', index)">
                                      <icon
                                        class="mr-3 w-6 h-6 fill-yellow-300 animate-pulse"
                                        name="truck" />
                                    </base-tooltip>
                                  </div>
                                </div>
                              </td>
                              <td class="px-2 py-1">
                                {{ trans.transporter.last_legal_name }}
                              </td>
                              <td class="px-2 py-1">
                                <div v-if="trans.transport_driver_vehicle">
                                  <div
                                    v-for="driver_vehicle of trans.transport_driver_vehicle">
                                    {{ driver_vehicle.vehicle.reg_no }}
                                  </div>
                                </div>
                                <div v-else>No vehicle</div>
                              </td>
                              <td class="px-2 py-1 whitespace-nowrap">
                                {{
                                  NiceNumber0(trans.transport_finance.cost_price_per_ton)
                                }}
                              </td>
                              <td class="px-2 py-1 whitespace-nowrap">
                                {{
                                  NiceNumber0(
                                    trans.transport_finance.selling_price_per_ton
                                  )
                                }}
                              </td>
                              <td class="px-2 py-1 whitespace-nowrap">
                                {{
                                  NiceNumber0(
                                    trans.transport_finance.transport_rate_per_ton
                                  )
                                }}
                              </td>
                              <td class="px-2 py-1 whitespace-nowrap">
                                {{ NiceNumber0(trans.transport_finance.gross_profit) }}
                              </td>
                              <td class="px-2 py-1 align-top">
                                <div v-if="trans.traders_notes">
                                  <base-tooltip :content="trans.traders_notes">
                                    <div
                                      class="max-w-[32ch] text-xs leading-snug line-clamp-2 break-words">
                                      {{ trans.traders_notes }}
                                    </div>
                                  </base-tooltip>
                                </div>
                                <div v-else>None...</div>
                              </td>
                              <td class="px-2 py-1 align-top">
                                <div v-if="trans.process_notes">
                                  <base-tooltip :content="trans.process_notes">
                                    <div
                                      class="max-w-[32ch] text-xs leading-snug line-clamp-2 break-words">
                                      {{ trans.process_notes }}
                                    </div>
                                  </base-tooltip>
                                </div>
                                <div v-else>None...</div>
                              </td>
                              <!-- Removed Cost, T/Cost, Selling body cells -->
                              <td class="px-2 py-1 whitespace-nowrap">
                                {{
                                  NiceNumberPlain(
                                    trans.transport_finance.gross_profit_percent
                                  )
                                }}
                                %
                              </td>

                              <td class="px-2 py-1 whitespace-nowrap">
                                {{
                                  NiceNumber(trans.transport_finance.gross_profit_per_ton)
                                }}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <!-- summary table remains below the scrollable card -->
                      <table class="ml-3 w-2/3 mt-5 divide-y divide-gray-300">
                        <thead>
                          <tr>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              No Loads
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Planned Tons
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Weight Uploaded
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Weight Offloaded
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Cost Price
                            </th>

                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Transport Cost
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Other Costs
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Selling Price
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              Gross Profit
                            </th>
                            <th
                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                              scope="col">
                              GP %
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                          <tr class="hover:bg-indigo-200 focus-within:bg-indigo-200">
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ no_loads }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ planned_tons }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ weight_uploaded }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ weight_offloaded }}
                            </td>
                            <!-- apply zero-decimal rounding to monetary summary fields -->
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ NiceNumber0(cost_price) }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ NiceNumber0(trans_cost) }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ NiceNumber0(other_costs) }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ NiceNumber0(selling_price) }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ NiceNumber0(gp) }}
                            </td>
                            <td
                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                              {{ gp_perc }} %
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div v-else>No data over the period...</div>
                  </div>
                </div>
              </div>

              <div class="mt-3 flex items-center justify-center">
                <PaginationModified :links="transport_trans.links" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
