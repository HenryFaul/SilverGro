<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, inject, ref } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';

const swal = inject('$swal');

  const props = defineProps({
    driver: Object,
    transporters: Array,
    last_vehicle: Object,
  });
  const permissions = computed(() => usePage().props.permissions);
  const emptyErrors = computed(
    () =>
      Object.keys(driverForm.errors).length === 0 &&
      driverForm.errors.constructor === Object
  );

  /*    ['first_name','last_name','cell_no','comment','is_active'];*/

  let driverForm = useForm({
    first_name: props.driver.first_name ?? null,
    last_name: props.driver.last_name ?? null,
    cell_no: props.driver.cell_no ?? null,
    is_active: props.driver.is_active ?? 1,
    comment: props.driver.comment ?? null,
  });

  const updateDriver = () => {
    driverForm.put(route('regular_driver.update', props.driver.id), {
      preserveScroll: true,
      onSuccess: () => {
        swal(usePage().props.jetstream.flash?.banner || '');
        toggleEdit();
      },
    });
  };

  const editDisabled = ref(true);

  const toggleEdit = () => {
    editDisabled.value = !editDisabled.value;
  };

  const roles_permissions = computed(() => usePage().props.roles_permissions);
  const can_update_product = computed(() =>
    usePage().props.roles_permissions.permissions.includes('update_product')
  );
</script>

<template>
  <AppLayout title="Driver">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Driver /
        <span class="text-indigo-400">{{ driverForm.first_name }}</span>
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div
            :class="
              !emptyErrors
                ? 'm-2 p-2 rounded-md rounded-md shadow-sm border border-red-500'
                : editDisabled
                  ? 'm-2 p-2'
                  : 'm-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500'
            ">
            <div class="">
              <form>
                <div class="text-lg mb-4 text-indigo-400">General details</div>
                <div class="space-y-12">
                  <div
                    class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                      <h2 class="text-base font-semibold leading-7 text-gray-900">
                        Static Information
                      </h2>
                      <p class="mt-1 text-sm leading-6 text-gray-600">
                        Static Driver information.
                      </p>
                    </div>

                    <div
                      class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="name">
                          First Name
                        </label>
                        <div class="mt-2">
                          <input
                            id="name"
                            v-model="driverForm.first_name"
                            :disabled="editDisabled"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="name"
                            type="text" />
                        </div>
                        <InputError
                          :message="driverForm.errors.first_name"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="name">
                          Last Name
                        </label>
                        <div class="mt-2">
                          <input
                            id="last_name"
                            v-model="driverForm.last_name"
                            :disabled="editDisabled"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="last_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="driverForm.errors.last_name"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="name">
                          Cell no
                        </label>
                        <div class="mt-2">
                          <input
                            id="cell_no"
                            v-model="driverForm.cell_no"
                            :disabled="editDisabled"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="last_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="driverForm.errors.cell_no"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label class="block text-sm font-medium leading-6 text-gray-900">
                          Driver status
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="driverForm.is_active"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              key="1"
                              value="1">
                              Active
                            </option>

                            <option
                              key="0"
                              value="0">
                              Suspended
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="driverForm.errors.is_active"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-6">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="comments">
                          Comments
                        </label>
                        <AreaInput
                          id="comments"
                          v-model="driverForm.comment"
                          :disabled="editDisabled"
                          :rows="6"
                          class="mt-1 block w-full"
                          placeholder="Optional comments..."
                          type="text" />
                        <InputError
                          :message="driverForm.errors.comment"
                          class="mt-2" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                  <SecondaryButton
                    v-if="can_update_product"
                    class="m-1"
                    @click="toggleEdit">
                    Edit
                  </SecondaryButton>

                  <SecondaryButton
                    v-if="!editDisabled && can_update_product"
                    class="m-1"
                    @click="updateDriver">
                    Save
                  </SecondaryButton>
                </div>
              </form>
            </div>
          </div>

          <!-- Last Vehicle Section -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
            <div class="m-2 p-2">
              <div class="text-lg mb-4 text-indigo-400">Last Vehicle</div>
              <div
                v-if="last_vehicle"
                class="space-y-2">
                <div class="text-sm text-gray-600 mb-2">
                  Most recent vehicle driven by this driver:
                </div>
                <Link
                  :href="route('regular_vehicle.show', last_vehicle.id)"
                  class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-400 max-w-sm block transition-colors">
                  <div class="flex items-center space-x-3">
                    <div class="flex-1 min-w-0">
                      <p
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                        {{ last_vehicle.reg_no }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        Click to view vehicle details
                      </p>
                    </div>
                    <svg
                      class="h-5 w-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M9 5l7 7-7 7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2" />
                    </svg>
                  </div>
                </Link>
              </div>
              <div
                v-else
                class="text-sm text-gray-500 italic">
                No vehicle has been associated with this driver yet.
              </div>
            </div>
          </div>

          <!-- Associated Transporters Section -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
            <div class="m-2 p-2">
              <div class="text-lg mb-4 text-indigo-400">Associated Transporters</div>
              <div
                v-if="transporters && transporters.length > 0"
                class="space-y-2">
                <div class="text-sm text-gray-600 mb-2">
                  This driver has worked for the following transporters:
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <Link
                    v-for="transporter in transporters"
                    :key="transporter.id"
                    :href="route('transporter.show', transporter.id)"
                    class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-400 transition-colors">
                    <div class="flex items-center space-x-3">
                      <div class="flex-1 min-w-0">
                        <p
                          class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                          {{ transporter.first_name }} {{ transporter.last_legal_name }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                          Click to view transporter details
                        </p>
                      </div>
                      <svg
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M9 5l7 7-7 7"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2" />
                      </svg>
                    </div>
                  </Link>
                </div>
              </div>
              <div
                v-else
                class="text-sm text-gray-500 italic">
                This driver has not been associated with any transporters yet.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
