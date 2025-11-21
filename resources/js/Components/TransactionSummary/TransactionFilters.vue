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
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="old no..."
          type="search" />
      </div>

      <!-- ID Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.id"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="ID no..."
          type="search" />
      </div>

      <!-- MQ Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.a_mq"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="MQ no..."
          type="search" />
      </div>

      <!-- PC Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.a_pc"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="PC no..."
          type="search" />
      </div>

      <!-- SC Search -->
      <div class="mt-5 ml-1">
        <input
          v-model.number="filterForm.a_sc"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="SC no..."
          type="search" />
      </div>

      <!-- Supplier Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.supplier_name"
          aria-label="Search"
          class="block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="supplier..."
          type="search" />
      </div>

      <!-- Customer Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.customer_name"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="customer..."
          type="search" />
      </div>

      <!-- Transporter Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.transporter_name"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="transporter..."
          type="search" />
      </div>

      <!-- Product Search -->
      <div class="mt-5 ml-1">
        <input
          v-model="filterForm.product_name"
          aria-label="Search"
          class="block ml-1 w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="product..."
          type="search" />
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
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="mon"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="mon">
              Mon
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="tue"
              v-model="tueValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="tue"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="tue">
              Tue
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="wed"
              v-model="wedValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="wed"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="wed">
              Wed
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="thu"
              v-model="thuValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="thu"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="thu">
              Thu
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="fri"
              v-model="friValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="fri"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="fri">
              Fri
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="sat"
              v-model="satValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="sat"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="sat">
              Sat
            </label>
          </div>
        </div>

        <div class="relative ml-2 flex items-start">
          <div class="flex h-6 items-center">
            <input
              id="sun"
              v-model="sunValue"
              aria-describedby="candidates-description"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              name="sun"
              type="checkbox" />
          </div>
          <div class="ml-3 text-sm leading-6">
            <label
              class="font-medium text-gray-900"
              for="sun">
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
