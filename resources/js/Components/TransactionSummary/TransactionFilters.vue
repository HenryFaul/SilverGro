<script setup>
  import { computed } from 'vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  import SecondaryButton from '@/Components/SecondaryButton.vue';

  const props = defineProps({
    filterForm: {
      type: Object,
      required: true,
    },
    contractTypes: {
      type: [Array, Object],
      default: () => [],
    },
    // Day flags
    mon: {
      type: Boolean,
      default: true,
    },
    tue: {
      type: Boolean,
      default: true,
    },
    wed: {
      type: Boolean,
      default: true,
    },
    thu: {
      type: Boolean,
      default: true,
    },
    fri: {
      type: Boolean,
      default: true,
    },
    sat: {
      type: Boolean,
      default: true,
    },
    sun: {
      type: Boolean,
      default: true,
    },
    // Format functions
    formatStart: {
      type: Function,
      required: true,
    },
    formatEnd: {
      type: Function,
      required: true,
    },
  });

  const emit = defineEmits([
    'search',
    'clear',
    'add-trade',
    'toggle-details',
    'update:mon',
    'update:tue',
    'update:wed',
    'update:thu',
    'update:fri',
    'update:sat',
    'update:sun',
  ]);

  // Day checkbox models with v-model support
  const monValue = computed({
    get: () => props.mon,
    set: (value) => emit('update:mon', value),
  });

  const tueValue = computed({
    get: () => props.tue,
    set: (value) => emit('update:tue', value),
  });

  const wedValue = computed({
    get: () => props.wed,
    set: (value) => emit('update:wed', value),
  });

  const thuValue = computed({
    get: () => props.thu,
    set: (value) => emit('update:thu', value),
  });

  const friValue = computed({
    get: () => props.fri,
    set: (value) => emit('update:fri', value),
  });

  const satValue = computed({
    get: () => props.sat,
    set: (value) => emit('update:sat', value),
  });

  const sunValue = computed({
    get: () => props.sun,
    set: (value) => emit('update:sun', value),
  });

  const contractTypesArray = computed(() => {
    return Array.isArray(props.contractTypes)
      ? props.contractTypes
      : Object.values(props.contractTypes || {});
  });
</script>

<template>
  <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-1 sm:grid-cols-6">
    <!-- Date and Filter Inputs Row -->
    <div class="flex col-span-6">
      <!-- Start Date -->
      <div>
        <div class="ml-1 text-indigo-400 text-sm font-bold">Start Date</div>
        <div class="w-36">
          <vue-date-picker
            v-model="filterForm.start_date"
            :format="formatStart"
            :teleport="true" />
        </div>
      </div>

      <!-- End Date -->
      <div class="ml-1">
        <div class="ml-3 text-indigo-400 text-sm font-bold">End Date</div>
        <div class="w-36">
          <vue-date-picker
            v-model="filterForm.end_date"
            :format="formatEnd"
            :teleport="true" />
        </div>
      </div>

      <!-- Contract Type -->
      <div class="mt-5 ml-1">
        <select
          v-model="filterForm.contract_type_id"
          class="input-filter-l w-36 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option :value="null">All contracts</option>
          <option
            v-for="n in contractTypesArray"
            :key="n.id"
            :value="n.id">
            {{ n.name }}
          </option>
        </select>
      </div>

      <!-- Show Per Page -->
      <div class="mt-5 ml-1">
        <select
          v-model="filterForm.show"
          class="input-filter-l w-20 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="100">100</option>
          <option :value="200">200</option>
          <option :value="500">500</option>
        </select>
      </div>

      <!-- Old ID Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.old_id"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="old no..." />
      </div>

      <!-- ID Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.id"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="ID no..." />
      </div>

      <!-- MQ Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.a_mq"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="MQ no..." />
      </div>

      <!-- Supplier Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.supplier_name"
          type="search"
          aria-label="Search"
          class="block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="supplier..." />
      </div>

      <!-- Customer Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.customer_name"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="customer..." />
      </div>

      <!-- Transporter Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.transporter_name"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="transporter..." />
      </div>

      <!-- Product Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.product_name"
          type="search"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="product..." />
      </div>
    </div>

    <!-- Action Buttons and Day Checkboxes Row -->
    <div class="col-span-4 flex">
      <!-- Action Buttons -->
      <div>
        <secondary-button @click="emit('search')">Search</secondary-button>
        <secondary-button
          class="ml-1"
          @click="emit('clear')">
          Clear
        </secondary-button>
        <secondary-button
          class="ml-1"
          @click="emit('add-trade')">
          Add (+)
        </secondary-button>
        <secondary-button
          class="ml-1"
          @click="emit('toggle-details')">
          Toggle
        </secondary-button>
      </div>

      <!-- Day Checkboxes -->
      <div class="flex ml-6">
        <div class="relative flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="mon"
              v-model="monValue"
              type="checkbox"
              name="mon"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="mon"
              class="font-medium text-gray-900">
              Mon
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="tue"
              v-model="tueValue"
              type="checkbox"
              name="tue"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="tue"
              class="font-medium text-gray-900">
              Tue
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="wed"
              v-model="wedValue"
              type="checkbox"
              name="wed"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="wed"
              class="font-medium text-gray-900">
              Wed
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="thu"
              v-model="thuValue"
              type="checkbox"
              name="thu"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="thu"
              class="font-medium text-gray-900">
              Thu
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="fri"
              v-model="friValue"
              type="checkbox"
              name="fri"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="fri"
              class="font-medium text-gray-900">
              Fri
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="sat"
              v-model="satValue"
              type="checkbox"
              name="sat"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="sat"
              class="font-medium text-gray-900">
              Sat
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="sun"
              v-model="sunValue"
              type="checkbox"
              name="sun"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              aria-describedby="candidates-description" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              for="sun"
              class="font-medium text-gray-900">
              Sun
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Spacer -->
    <div class="col-span-4 mb-3"></div>
  </div>
</template>
