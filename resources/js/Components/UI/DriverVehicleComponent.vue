<script setup>
  import InputError from '@/Components/InputError.vue';
  import TextInput from '@/Components/TextInput.vue';
  import AreaInput from '@/Components/AreaInput.vue';
  import { computed, onBeforeMount, ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import SecondaryButton from '@/Components/SecondaryButton.vue'; //let addressApi = ref();

  //let addressApi = ref();
  const emit = defineEmits(['close']);

  let props = defineProps({
    transport_trans_id: Number,
    transport_job_id: Number,
    driver_vehicle: Object,
    all_drivers: Object,
    all_vehicles: Object,
    show: false,
    closeable: true,
  });

  const form = useForm({
    transport_trans_id:
      props.transport_trans_id == null ? null : props.transport_trans_id,
    transport_job_id: props.transport_job_id == null ? null : props.transport_job_id,
    regular_driver_id:
      props.driver_vehicle == null ? 1 : props.driver_vehicle.regular_driver_id,
    regular_vehicle_id:
      props.driver_vehicle == null ? 1 : props.driver_vehicle.regular_vehicle_id,
    weighbridge_upload_weight:
      props.driver_vehicle == null ? 0 : props.driver_vehicle.weighbridge_upload_weight,
    weighbridge_offload_weight:
      props.driver_vehicle == null ? 0 : props.driver_vehicle.weighbridge_offload_weight,
    is_weighbridge_variance:
      props.driver_vehicle == null ? false : props.driver_vehicle.is_weighbridge_variance,
    is_cancelled:
      props.driver_vehicle == null ? false : props.driver_vehicle.is_cancelled,
    date_cancelled:
      props.driver_vehicle == null ? null : props.driver_vehicle.date_cancelled,
    is_loaded: props.driver_vehicle == null ? false : props.driver_vehicle.is_loaded,
    date_loaded: props.driver_vehicle == null ? null : props.driver_vehicle.date_loaded,
    is_onroad: props.driver_vehicle == null ? false : props.driver_vehicle.is_onroad,
    date_onroad: props.driver_vehicle == null ? null : props.driver_vehicle.date_onroad,
    is_delivered:
      props.driver_vehicle == null ? false : props.driver_vehicle.is_delivered,
    date_delivered:
      props.driver_vehicle == null ? null : props.driver_vehicle.date_delivered,
    is_transport_scheduled:
      props.driver_vehicle == null ? false : props.driver_vehicle.is_transport_scheduled,
    date_scheduled:
      props.driver_vehicle == null ? null : props.driver_vehicle.date_scheduled,
    is_paid: props.driver_vehicle == null ? false : props.driver_vehicle.is_paid,
    date_paid: props.driver_vehicle == null ? null : props.driver_vehicle.date_paid,
    is_payment_overdue:
      props.driver_vehicle == null ? false : props.driver_vehicle.is_payment_overdue,
    traders_notes:
      props.driver_vehicle == null ? null : props.driver_vehicle.traders_notes,
    operations_alert_notes:
      props.driver_vehicle == null ? null : props.driver_vehicle.operations_alert_notes,
    driver_vehicle_loading_number:
      props.driver_vehicle == null
        ? null
        : props.driver_vehicle.driver_vehicle_loading_number,
  });

  let showDriver = ref(false);
  let showVehicle = ref(false);

  const toggleShowDriver = () => {
    showDriver.value = !showDriver.value;
  };

  const toggleShowVehicle = () => {
    showVehicle.value = !showVehicle.value;
  };

  let driverForm = useForm({
    first_name: null,
    last_name: null,
    cell_no: null,
    comment: null,
  });

  let vehicleForm = useForm({
    vehicle_type_id: 1,
    comment: null,
    reg_no: null,
  });

  let vehicleSlideProps = ref(null);

  const getComponentProps = () => {
    axios.get(route('props.vehicle_slide_over')).then((res) => {
      vehicleSlideProps.value = res.data['vehicle_types'];
    });
  };

  const createProduct = () => {
    driverForm.post(route('regular_driver.store'), {
      preserveScroll: true,
      onSuccess: () => {
        driverForm.first_name = null;
        driverForm.last_name = null;
        driverForm.cell_no = null;
        driverForm.comment = null;
        toggleShowDriver();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  const createProductVehicle = () => {
    vehicleForm.post(route('regular_vehicle.store'), {
      preserveScroll: true,
      onSuccess: () => {
        toggleShowVehicle();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  const deleteDriverVehicle = () => {
    if (confirm('Sure you want to delete?')) {
      form.delete(route('transport_driver_vehicle.destroy', props.driver_vehicle.id), {
        preserveScroll: true,
        onSuccess: () => {
          close();
        },
        onError: (e) => {
          close();
          console.log(e);
        },
      });

      close();
    }
  };

  const updateDriverVehicle = () => {
    form.put(route('transport_driver_vehicle.update.driver', props.driver_vehicle.id), {
      preserveScroll: true,
      onSuccess: () => {
        close();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  const createDriverVehicle = () => {
    form.post(route('transport_driver_vehicle.store'), {
      preserveScroll: true,
      onSuccess: () => {
        close();
      },
      onError: (e) => {
        console.log(e);
      },
    });
  };

  onBeforeMount(async () => {
    await getComponentProps();
  });

  let emptyErrors = computed(
    () => Object.keys(form.errors).length === 0 && form.errors.constructor === Object
  );
  let borderClass = computed(() =>
    !emptyErrors
      ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-red-500'
      : 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray'
  );
</script>

<template>
  <div>
    <div>
      <div>
        <div class="">
          <form class="w-full m-1 p-1">
            <!--                            'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
                                                        'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
                                                        'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
                                                        'traders_notes','operations_alert_notes'-->

            <div>
              <div class="text-lg mb-2 text-indigo-400">
                Driver Vehicle

                <span v-if="driver_vehicle != null">({{ driver_vehicle.id }})</span>
                <span v-else>(New)</span>
              </div>

              <div class="mt-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">
                  Driver/Vehicle loading no
                </label>
                <TextInput
                  id="loading_no"
                  v-model="form.driver_vehicle_loading_number"
                  class="mt-1 block w-full"
                  type="text" />
                <!--                                    <InputError class="mt-2" :message="form.errors.line_1"/>-->
              </div>

              <div class="mt-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">
                  Driver
                </label>

                <select
                  v-model="form.regular_driver_id"
                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  <option
                    v-for="n in props.all_drivers"
                    :key="n.id"
                    :value="n.id">
                    {{ n.first_name }} {{ n.last_name }}
                    <template v-if="n.transporter">
                      ({{ n.transporter.last_legal_name }})
                    </template>
                  </option>
                </select>

                <InputError
                  :message="form.errors.regular_driver_id"
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
                      <div>
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                          Comments
                        </label>
                      </div>
                      <div class="sm:col-span-2">
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
                    <div class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                      <div class="flex justify-end space-x-3">
                        <button
                          class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                          type="button"
                          @click="toggleShowDriverVehicle">
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

              <div class="mt-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">
                  Vehicle
                </label>
                <select
                  v-model="form.regular_vehicle_id"
                  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  <option
                    v-for="n in props.all_vehicles"
                    :key="n.id"
                    :value="n.id">
                    {{ n.reg_no }} - {{ n.vehicle_type.name }}
                    <template v-if="n.transporter">
                      ({{ n.transporter.last_legal_name }})
                    </template>
                  </option>
                </select>

                <InputError
                  :message="form.errors.regular_driver_id"
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
                              v-for="n in vehicleSlideProps"
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
                        <div>
                          <label
                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">
                            Comments
                          </label>
                        </div>
                        <div class="sm:col-span-2">
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
                    <div class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
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
          </form>
        </div>
      </div>
    </div>

    <div>
      <div v-if="props.driver_vehicle == null">
        <div>Driver vehicle not allocated. Contact support.</div>
      </div>
      <div v-else>
        <SecondaryButton
          class="ml-1 mt-2 bg-green"
          @click="updateDriverVehicle">
          Update
        </SecondaryButton>
      </div>
    </div>
  </div>
</template>
