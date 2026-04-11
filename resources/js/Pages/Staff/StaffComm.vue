<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  import SecondaryButton from '@/Components/SecondaryButton.vue';

  const props = defineProps({
    commissions: Array,
    staff_list: Array,
    filters: Object,
  });

  const can_see = computed(() =>
    usePage().props.roles_permissions.permissions.includes('edit_adjusted_gp')
  );

  // Filter state — initialise from server-side filters
  const selectedStaff = ref(props.filters?.staff_id ?? '');
  const dateFrom = ref(props.filters?.date_from ?? '');
  const dateTo = ref(props.filters?.date_to ?? '');

  function applyFilters() {
    router.get(route('staff.comm'), {
      staff_id:  selectedStaff.value || undefined,
      date_from: dateFrom.value || undefined,
      date_to:   dateTo.value || undefined,
    }, { preserveScroll: true });
  }

  function clearFilters() {
    selectedStaff.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get(route('staff.comm'), {}, { preserveScroll: true });
  }

  function exportExcel() {
    const params = new URLSearchParams();
    if (selectedStaff.value) params.set('staff_id', selectedStaff.value);
    if (dateFrom.value)      params.set('date_from', dateFrom.value);
    if (dateTo.value)        params.set('date_to', dateTo.value);
    window.location.href = route('staff.comm.export') + (params.toString() ? '?' + params.toString() : '');
  }

  const NiceNumber = (n) => {
    const val = (n / 1).toFixed(2).replace('.', '.');
    return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  const totalSupplier = computed(() =>
    props.commissions.reduce((sum, c) => sum + c.supplier_comm, 0)
  );
  const totalCustomer = computed(() =>
    props.commissions.reduce((sum, c) => sum + c.customer_comm, 0)
  );
  const totalAll = computed(() => totalSupplier.value + totalCustomer.value);
</script>

<template>
  <AppLayout title="Staff Commissions">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Comm</h2>
    </template>

    <div class="py-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white m-2 p-4 overflow-hidden shadow-xl sm:rounded-lg">

          <!-- Filters -->
          <div class="mb-4 flex flex-wrap items-end gap-3">
            <!-- Staff filter -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Staff member</label>
              <select
                v-model="selectedStaff"
                class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All staff</option>
                <option v-for="s in staff_list" :key="s.id" :value="s.id">
                  {{ s.first_name }} {{ s.last_legal_name }}
                </option>
              </select>
            </div>

            <!-- Date from -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Date from</label>
              <input
                type="date"
                v-model="dateFrom"
                class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <!-- Date to -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Date to</label>
              <input
                type="date"
                v-model="dateTo"
                class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div class="flex gap-2">
              <button
                @click="applyFilters"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Apply
              </button>
              <button
                @click="clearFilters"
                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Clear
              </button>
              <button
                @click="exportExcel"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Export Excel
              </button>
            </div>
          </div>

          <!-- Table -->
          <h1 class="text-xl font-bold mb-3">
            Staff Commissions (Approved trades only)
          </h1>

          <table class="min-w-full border-separate border-spacing-0" v-if="can_see">
            <thead>
              <tr>
                <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">
                  Staff Name
                </th>
                <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-right text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                  Supplier Commission
                </th>
                <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-right text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                  Customer Commission
                </th>
                <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-right text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                  Total
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(comm, index) in commissions"
                :key="index"
                class="hover:bg-gray-50 text-sm">
                <td class="border-b border-gray-200 whitespace-nowrap py-2 pl-4 pr-3 font-medium text-gray-900 sm:pl-6 lg:pl-8">
                  {{ comm.name }}
                </td>
                <td class="border-b border-gray-200 px-3 py-2 text-right">
                  {{ NiceNumber(comm.supplier_comm) }}
                </td>
                <td class="border-b border-gray-200 px-3 py-2 text-right">
                  {{ NiceNumber(comm.customer_comm) }}
                </td>
                <td class="border-b border-gray-200 px-3 py-2 text-right font-medium">
                  {{ NiceNumber(comm.supplier_comm + comm.customer_comm) }}
                </td>
              </tr>

              <!-- Totals row -->
              <tr v-if="commissions.length" class="bg-gray-50 font-semibold text-sm">
                <td class="py-2 pl-4 pr-3 sm:pl-6 lg:pl-8">Total</td>
                <td class="px-3 py-2 text-right">{{ NiceNumber(totalSupplier) }}</td>
                <td class="px-3 py-2 text-right">{{ NiceNumber(totalCustomer) }}</td>
                <td class="px-3 py-2 text-right">{{ NiceNumber(totalAll) }}</td>
              </tr>

              <tr v-if="!commissions.length">
                <td colspan="4" class="py-6 text-center text-gray-500 text-sm">No commission records found.</td>
              </tr>
            </tbody>
          </table>

          <div v-else class="text-sm text-red-600 mt-4">Not allowed to see this.</div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>
