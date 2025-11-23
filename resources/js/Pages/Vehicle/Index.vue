<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref, watch } from 'vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import { debounce } from 'lodash';
  import PaginationModified from '@/Components/UI/PaginationModified.vue';
  import VehicleSlideOver from '@/Components/UI/VehicleSlideOver.vue';

  const props = defineProps({
    vehicles: Object,
    filters: Object,
  });
  const permissions = computed(() => usePage().props.permissions);

  const filterForm = useForm({
    searchName: props.filters.searchName ?? null,
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? 'asc',
    show: props.filters.show ?? 10,
  });

  const viewVehicleSlideOver = ref(false);

  const showVehicleSlideOver = () => {
    viewVehicleSlideOver.value = true;
  };

  const closeVehicleSlideOver = () => {
    viewVehicleSlideOver.value = false;
  };

  let curClient = ref(null);
  let showModel = ref(false);

  let tableStats = ref(
    'Showing page ' +
      props.vehicles.current_page +
      '  of ' +
      props.vehicles.total +
      ' entries.'
  );

  let filter = debounce(() => {
    filterForm.get(route('regular_vehicle.index'), {
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
    () => filterForm.searchName,
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

  const clear = () => {
    filterForm.searchName = null;
    filterForm.isActive = null;
    filter();
  };

  /*const edit = (id) => {
    router.get('driver/'+id);
}*/
</script>

<template>
  <AppLayout title="Vehicles">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vehicles</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              Vehicle Data
            </h2>

            <div class="mb-4 mt-5">
              <input
                v-model.number="filterForm.searchName"
                aria-label="Search"
                class="block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Search reg no, transporter, or driver..."
                type="search" />

              <div class="mt-2">
                <select
                  v-model="filterForm.isActive"
                  class="input-filter-l block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option :value="null">All</option>
                  <option value="active">Active Only</option>
                  <option value="inactive">Inactive Only</option>
                </select>
              </div>
              <div class="mt-2">
                <select
                  v-model="filterForm.show"
                  class="input-filter-l block w-1/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option :value="5">5</option>
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="100">100</option>
                </select>
              </div>
              <secondary-button
                class="mt-3"
                @click="filter">
                Search
              </secondary-button>
              <secondary-button
                class="mt-3 ml-1"
                @click="clear">
                Clear
              </secondary-button>
              <secondary-button
                class="mt-3 ml-1"
                @click="showVehicleSlideOver">
                Add (+)
              </secondary-button>
            </div>
            <div>
              <div>
                <vehicle-slide-over
                  :show="viewVehicleSlideOver"
                  @close="closeVehicleSlideOver" />
              </div>

              <div class="bg-white rounded-md shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                  <thead class="bg-indigo-400">
                    <tr class="text-left font-bold">
                      <th
                        class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase"
                        scope="col">
                        <span
                          class="inline-flex py-3 px-6 w-full justify-between"
                          @click="sort('name')">
                          Reg No

                          <svg
                            v-if="
                              filterForm.field === 'name' &&
                              filterForm.direction === 'asc'
                            "
                            class="h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z" />
                          </svg>

                          <svg
                            v-if="
                              filterForm.field === 'name' &&
                              filterForm.direction === 'desc'
                            "
                            class="h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                          </svg>
                        </span>
                      </th>

                      <th
                        class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase"
                        scope="col">
                        Active
                      </th>
                      <th
                        class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase"
                        scope="col">
                        Transporter(s)
                      </th>
                      <th
                        class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase"
                        scope="col">
                        Last Driver
                      </th>
                      <th
                        class="w-1/12 text-xs font-semibold tracking-wider text-left text-white uppercase"
                        scope="col">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="(vehicle, index) in vehicles.data"
                      :key="vehicle.id"
                      class="hover:bg-gray-100 focus-within:bg-gray-100">
                      <td class="py-4 px-6 whitespace-nowrap">
                        {{ vehicle.reg_no }}
                      </td>

                      <td class="py-4 px-6 whitespace-nowrap">
                        <div v-if="vehicle.is_active === 1">
                          <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M4.5 12.75l6 6 9-13.5"
                              stroke-linecap="round"
                              stroke-linejoin="round" />
                          </svg>
                        </div>
                        <div v-else>
                          <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M6 18L18 6M6 6l12 12"
                              stroke-linecap="round"
                              stroke-linejoin="round" />
                          </svg>
                        </div>
                      </td>

                      <td class="py-4 px-6">
                        <div
                          v-if="vehicle.transporters && vehicle.transporters.length > 0">
                          <div
                            v-for="(transporter, idx) in vehicle.transporters.slice(0, 3)"
                            :key="transporter.id"
                            class="text-sm">
                            <Link
                              :href="route('transporter.show', transporter.id)"
                              class="text-indigo-600 hover:text-indigo-900 underline">
                              {{ transporter.first_name }}
                              {{ transporter.last_legal_name }}
                            </Link>
                            <span
                              v-if="idx < Math.min(vehicle.transporters.length, 3) - 1">
                              ,
                            </span>
                          </div>
                          <div
                            v-if="vehicle.transporters.length > 3"
                            class="text-xs text-indigo-600 italic mt-1">
                            (Has {{ vehicle.transporters.length }} total)
                          </div>
                        </div>
                        <div
                          v-else
                          class="text-sm text-gray-400 italic">
                          No transporter
                        </div>
                      </td>

                      <td class="py-4 px-6 whitespace-nowrap">
                        <div
                          v-if="vehicle.last_driver"
                          class="text-sm">
                          <Link
                            :href="route('regular_driver.show', vehicle.last_driver.id)"
                            class="text-indigo-600 hover:text-indigo-900 underline">
                            {{ vehicle.last_driver.first_name }}
                            {{ vehicle.last_driver.last_name }}
                          </Link>
                        </div>
                        <div
                          v-else
                          class="text-sm text-gray-400 italic">
                          No driver
                        </div>
                      </td>

                      <td class="py-4 px-6 whitespace-nowrap">
                        <Link
                          :href="route('regular_vehicle.show', vehicle.id)"
                          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                          View
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="ml-3 mt-2">
                {{ tableStats }}
              </div>
            </div>
            <div
              v-if="vehicles.data.length"
              class="w-full flex justify-center mt-5 mb-4">
              <PaginationModified :links="vehicles.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
