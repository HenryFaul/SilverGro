<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, inject, ref, watch } from 'vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { router, useForm, usePage, Link } from '@inertiajs/vue3';
  import { debounce, throttle } from 'lodash';
  import PaginationModified from '@/Components/UI/PaginationModified.vue';
  import Icon from '@/Components/Icon.vue';
  import CustomerSlideOver from '@/Components/UI/CustomerSlideOver.vue';
  import { CalculatorIcon, FlagIcon } from '@heroicons/vue/20/solid';

  const swal = inject('$swal');

  const props = defineProps({
    supplierCommissions: Object,
  });

  let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace('.', '.');
    return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  const permissions = computed(() => usePage().props.permissions);
  const roles_permissions = computed(() => usePage().props.roles_permissions);
  const can_see = computed(() =>
    usePage().props.roles_permissions.permissions.includes('edit_adjusted_gp')
  );
</script>

<template>
  <AppLayout title="Debtor Standing">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Comm</h2>
    </template>

    <div class="py-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
              <div class="flex col-span-6">
                <div>
                  <h1 class="text-xl font-bold mb-4">
                    Staff Commissions (Approved trades only)
                  </h1>

                  <table class="min-w-full border-separate border-spacing-0">
                    <thead>
                      <tr>
                        <th
                          class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">
                          Staff Name
                        </th>
                        <th
                          class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                          Total Supplier Commission
                        </th>

                        <th
                          class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                          Total Customer Commission
                        </th>
                      </tr>
                    </thead>
                    <tbody v-if="can_see">
                      <tr
                        v-for="(commission, index) in supplierCommissions"
                        :key="index"
                        class="hover:bg-gray-100 text-sm focus-within:bg-gray-100">
                        <td
                          class="border-b border-gray-200 whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">
                          {{ commission['Staff Name'] }}
                        </td>
                        <td class="border-b border-gray-200 px-3 py-2">
                          {{ NiceNumber(commission.total_supplier_comm) }}
                        </td>

                        <td class="border-b border-gray-200 px-3 py-2">
                          {{ NiceNumber(commission.total_supplier_comm) }}
                        </td>
                      </tr>
                    </tbody>

                    <tbody v-else>
                      <tr>
                        <td>Not allowed to see this</td>
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
  </AppLayout>
</template>
