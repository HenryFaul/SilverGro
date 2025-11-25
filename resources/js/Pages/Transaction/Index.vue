<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import PaginationModified from '@/Components/UI/PaginationModified.vue';
import TradeSlideOver from '@/Components/UI/TradeSlideOver.vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

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

  const open = ref(true);

  const props = defineProps({
    transactions: Object,
    filters: Object,
    start_date: String,
    end_date: String,
    contract_types: Object,
    download_url: String,
    custom_reports: Object,
  });
  const roles_permissions = computed(() => usePage().props.roles_permissions);

  // Watch for flash messages
  const page = usePage();
  const downloadUrl = computed(
    () => page.props.flash?.download_url || props.download_url
  );
  const flashError = computed(() => page.props.flash?.error);

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

  const filterForm = useForm({
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? 'asc',
    show: props.filters.show ?? 25,
    supplier_name: props.filters.supplier_name ?? null,
    customer_name: props.filters.customer_name ?? null,
    transporter_name: props.filters.transporter_name ?? null,
    product_name: props.filters.product_name ?? null,
    start_date: props.filters.start_date ?? null,
    end_date: props.filters.end_date ?? null,
    contract_type_id: props.filters.contract_type_id ?? null,
    id: props.filters.id ?? null,
    custom_report_id: 1,
  });

  let curClient = ref(null);

  // Export state management
  const isExporting = ref(false);
  const showSuccessNotification = ref(false);
  const showErrorNotification = ref(false);
  const notificationMessage = ref('');

  //let tableStats = ref("Showing page " + props.transactions.current_page + "  of " + props.transactions.total + " entries.");

  let filter = debounce(() => {
    filterForm.get(route('transport_transaction.index'), {
      preserveState: true,
      preserveScroll: true,
    });
  }, 150);

  let sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
  };

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

  const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;
    filterForm.id = null;

    mon.value = true;
    tue.value = true;
    wed.value = true;
    thu.value = true;
    fri.value = true;
    sat.value = true;
    sun.value = true;

    /*  ua.value=true;
    pc.value=true;
    sc.value=true;
    mq.value=true;*/

    filter();
  };

  const edit = (id) => {
    // router.get('transport_transaction/'+id);
  };

  const viewTradeSlideOver = ref(false);

  const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
  };

  const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
  };

  let mon = ref(true);
  let tue = ref(true);
  let wed = ref(true);
  let thu = ref(true);
  let fri = ref(true);
  let sat = ref(true);
  let sun = ref(true);

  //Unallocated
  //'PC'
  //'SC'
  //'MQ'

  /*let ua = ref(true);
let pc = ref(true);
let sc = ref(true);
//let mq = ref(true);*/

  let NiceDay = (_date) => {
    return new Date(_date).getDay();
  };

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

  /*let typeIncluded = (typeId) => {

    switch(typeId) {
        case 1:
            return ua.value;
        case 2:
            return pc.value;
        case 3:
            return sc.value;
        case 4:

         return  filterForm.mq.value;
            //return mq.value;
        default:
            return false;
    }
};*/

  let filteredTrans = computed(() =>
    mon.value &&
    tue.value &&
    wed.value &&
    thu.value &&
    fri.value &&
    sat.value &&
    sun.value
      ? props.transactions.data
      : props.transactions.data.filter((trans) => {
          return dayIncluded(trans.transport_date_earliest);
        })
  );

  /*const tableStats = computed(() => {
    return "Showing page " + props.transactions.current_page + "  of " + filteredTrans.length+ " entries.";
})*/

  const generateExcel = () => {
    isExporting.value = true;
    showSuccessNotification.value = false;
    showErrorNotification.value = false;

    router.get(
      route('excel_report.transactions.generate'),
      {
        supplier_name: filterForm.supplier_name,
        customer_name: filterForm.customer_name,
        transporter_name: filterForm.transporter_name,
        product_name: filterForm.product_name,
        start_date: filterForm.start_date,
        end_date: filterForm.end_date,
        contract_type_id: filterForm.contract_type_id,
        id: filterForm.id,
        custom_report_id: filterForm.custom_report_id,
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          isExporting.value = false;
          if (downloadUrl.value) {
            notificationMessage.value = 'Report generated successfully!';
            showSuccessNotification.value = true;
            setTimeout(() => {
              showSuccessNotification.value = false;
            }, 5000);
          }
        },
        onError: (errors) => {
          isExporting.value = false;
          notificationMessage.value = 'Export failed. Please try again.';
          showErrorNotification.value = true;
          setTimeout(() => {
            showErrorNotification.value = false;
          }, 5000);
        },
        onFinish: () => {
          isExporting.value = false;
        },
      }
    );
  };
</script>

<template>
  <AppLayout title="Transactions">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report Exporter</h2>
    </template>

    <div class="py-12">
      <div class="max-w-full mx-auto sm:px-4 lg:px-6">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <!-- Single Row Filter Layout -->
            <div class="ml-4 flex flex-wrap items-end gap-2">
              <!-- Date Filters -->
              <div>
                <div class="ml-3 text-indigo-400 text-sm font-bold">Start Date</div>
                <div class="w-40">
                  <VueDatePicker
                    v-model="filterForm.start_date"
                    :format="formatStart"
                    :teleport="true"></VueDatePicker>
                </div>
              </div>
              <div>
                <div class="ml-3 text-indigo-400 text-sm font-bold">End Date</div>
                <div class="w-40">
                  <VueDatePicker
                    v-model="filterForm.end_date"
                    :format="format"
                    :teleport="true"></VueDatePicker>
                </div>
              </div>

              <!-- Contract Type Dropdown -->
              <div>
                <select
                  v-model="filterForm.contract_type_id"
                  class="input-filter-l w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option :value="null">All contracts</option>
                  <option
                    v-for="n in contract_types"
                    :key="n.id"
                    :value="n.id">
                    {{ n.name }}
                  </option>
                </select>
              </div>

              <!-- Search Inputs -->
              <input
                v-model.number="filterForm.supplier_name"
                aria-label="Search"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Supplier..."
                type="search" />

              <input
                v-model.number="filterForm.customer_name"
                aria-label="Search"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Customer..."
                type="search" />

              <input
                v-model.number="filterForm.transporter_name"
                aria-label="Search"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Transporter..."
                type="search" />

              <input
                v-model.number="filterForm.product_name"
                aria-label="Search"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Product..."
                type="search" />

              <input
                v-model.number="filterForm.id"
                aria-label="Search"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Contract No..."
                type="search" />

              <!-- Items Per Page -->
              <div>
                <select
                  v-model="filterForm.show"
                  class="input-filter-l w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option :value="5">5</option>
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="100">100</option>
                  <option :value="200">200</option>
                  <option :value="500">500</option>
                </select>
              </div>

              <!-- Action Buttons -->
              <secondary-button @click="filter">Search</secondary-button>
              <secondary-button @click="clear">Clear</secondary-button>
              <secondary-button @click="showTradeSlideOver">Add (+)</secondary-button>
            </div>

            <!-- Second Row: Report Selector and Export Button -->
            <div class="ml-4 mt-4 flex flex-wrap items-center gap-4">
              <!-- Report Selector -->
              <div
                v-if="custom_reports != null"
                class="flex items-end gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Select Report
                  </label>
                  <select
                    v-model="filterForm.custom_report_id"
                    class="block w-64 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option
                      v-for="n in custom_reports"
                      :key="n.id"
                      :value="n.id">
                      {{ n.name }}
                    </option>
                  </select>
                </div>

                <!-- Export Button with Loading Spinner -->
                <button
                  :class="
                    isExporting
                      ? 'bg-indigo-400 cursor-not-allowed'
                      : 'bg-indigo-600 hover:bg-indigo-500 focus-visible:outline-indigo-600'
                  "
                  :disabled="isExporting"
                  class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                  @click="generateExcel">
                  <svg
                    v-if="isExporting"
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"></circle>
                    <path
                      class="opacity-75"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      fill="currentColor"></path>
                  </svg>
                  <span>{{ isExporting ? 'Generating...' : 'Generate Export' }}</span>
                </button>
              </div>

              <!-- Download button -->
              <div v-if="downloadUrl && !isExporting">
                <a
                  :href="route('excel_report.transactions.download', downloadUrl)"
                  class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                  target="_blank">
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2" />
                  </svg>
                  Download Excel
                </a>
              </div>
            </div>

            <!-- Notifications -->
            <div class="ml-4 mt-4 space-y-2">
              <!-- Success Notification -->
              <div
                v-if="showSuccessNotification"
                class="rounded-md bg-green-50 p-4 max-w-md animate-fade-in">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-5 w-5 text-green-400"
                      fill="currentColor"
                      viewBox="0 0 20 20">
                      <path
                        clip-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                        fill-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                      {{ notificationMessage }}
                    </p>
                  </div>
                  <div class="ml-auto pl-3">
                    <button
                      class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100"
                      @click="showSuccessNotification = false">
                      <span class="sr-only">Dismiss</span>
                      <svg
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                          d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Error Notification -->
              <div
                v-if="showErrorNotification"
                class="rounded-md bg-red-50 p-4 max-w-md animate-fade-in">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-5 w-5 text-red-400"
                      fill="currentColor"
                      viewBox="0 0 20 20">
                      <path
                        clip-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        fill-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                      {{ notificationMessage }}
                    </p>
                  </div>
                  <div class="ml-auto pl-3">
                    <button
                      class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100"
                      @click="showErrorNotification = false">
                      <span class="sr-only">Dismiss</span>
                      <svg
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                          d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Flash Error (from backend) -->
              <div
                v-if="flashError"
                class="rounded-md bg-red-50 p-4 max-w-md">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-5 w-5 text-red-400"
                      fill="currentColor"
                      viewBox="0 0 20 20">
                      <path
                        clip-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        fill-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Export Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                      <p>{{ flashError }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <trade-slide-over
                :show="viewTradeSlideOver"
                @close="closeTradeSlideOver" />
            </div>

            <div>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-400 text-right">
                  <tr class="font-bold">
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      ID
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      Type
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      DATE
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      Supplier
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      Customer
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      Transporter
                    </th>
                    <th
                      class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase"
                      scope="col">
                      Product
                    </th>

                    <!--
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
-->
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <!--                                    'id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                    'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'-->

                  <tr
                    v-for="(transaction, index) in filteredTrans"
                    :key="transaction.id"
                    class="hover:bg-gray-100 text-sm focus-within:bg-gray-100">
                    <td class="py-1 px-6 whitespace-nowrap">
                      {{ transaction.id }}
                    </td>
                    <td class="py-1 px-6">
                      {{ transaction.contract_type.name }}
                    </td>

                    <td class="py-1 px-6 whitespace-nowrap">
                      {{ NiceTDate(transaction.transport_date_earliest) }}
                    </td>

                    <td class="py-1 px-6">
                      {{ transaction.supplier.last_legal_name }}
                    </td>
                    <td class="py-1 px-6">
                      {{ transaction.customer.last_legal_name }}
                    </td>
                    <td class="py-1 px-6">
                      {{ transaction.transporter.last_legal_name }}
                    </td>

                    <td class="py-1 px-6 whitespace-nowrap">
                      {{ transaction.product.name }}
                    </td>

                    <!--                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',transaction.id)" >View trans</Link>
                                        </td>-->
                  </tr>
                </tbody>
              </table>

              <div class="ml-3 mt-2">
                {{
                  'Showing page ' +
                  props.transactions.current_page +
                  '  of ' +
                  props.transactions.total +
                  ' entries.'
                }}
              </div>
            </div>
            <div
              v-if="transactions.data.length"
              class="w-full flex justify-center mt-5 mb-4">
              <PaginationModified :links="transactions.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-fade-in {
    animation: fade-in 0.3s ease-out;
  }
</style>
