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
    showAdd: {
      type: Boolean,
      default: true,
    },
    // Day flags
    mon: { type: Boolean, default: true },
    tue: { type: Boolean, default: true },
    wed: { type: Boolean, default: true },
    thu: { type: Boolean, default: true },
    fri: { type: Boolean, default: true },
    sat: { type: Boolean, default: true },
    sun: { type: Boolean, default: true },
    // Format functions
    formatStart: { type: Function, required: true },
    formatEnd:   { type: Function, required: true },
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

  const contractTypesArray = computed(() =>
    Array.isArray(props.contractTypes)
      ? props.contractTypes
      : Object.values(props.contractTypes || {})
  );

  const monValue = computed({ get: () => props.mon, set: (v) => emit('update:mon', v) });
  const tueValue = computed({ get: () => props.tue, set: (v) => emit('update:tue', v) });
  const wedValue = computed({ get: () => props.wed, set: (v) => emit('update:wed', v) });
  const thuValue = computed({ get: () => props.thu, set: (v) => emit('update:thu', v) });
  const friValue = computed({ get: () => props.fri, set: (v) => emit('update:fri', v) });
  const satValue = computed({ get: () => props.sat, set: (v) => emit('update:sat', v) });
  const sunValue = computed({ get: () => props.sun, set: (v) => emit('update:sun', v) });

  const inputClass =
    'rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm';
</script>

<template>
  <div class="flex flex-wrap items-end gap-2 px-2 py-2">

    <!-- Start Date -->
    <div>
      <div class="text-xs font-medium text-indigo-500 mb-0.5">Start</div>
      <div class="w-32">
        <vue-date-picker
          v-model="filterForm.start_date"
          :format="formatStart"
          :teleport="true" />
      </div>
    </div>

    <!-- End Date -->
    <div>
      <div class="text-xs font-medium text-indigo-500 mb-0.5">End</div>
      <div class="w-32">
        <vue-date-picker
          v-model="filterForm.end_date"
          :format="formatEnd"
          :teleport="true" />
      </div>
    </div>

    <!-- Contract Type -->
    <select
      v-model="filterForm.contract_type_id"
      :class="inputClass"
      class="w-32">
      <option :value="null">All contracts</option>
      <option
        v-for="n in contractTypesArray"
        :key="n.id"
        :value="n.id">
        {{ n.name }}
      </option>
    </select>

    <!-- Show Per Page -->
    <select
      v-model="filterForm.show"
      :class="inputClass"
      class="w-16">
      <option :value="5">5</option>
      <option :value="10">10</option>
      <option :value="25">25</option>
      <option :value="100">100</option>
      <option :value="200">200</option>
      <option :value="500">500</option>
    </select>

    <!-- Search inputs -->
    <input v-model.number="filterForm.old_id"         :class="inputClass" class="w-20" placeholder="old no..."    type="search" />
    <input v-model.number="filterForm.id"             :class="inputClass" class="w-20" placeholder="ID no..."     type="search" />
    <input v-model.number="filterForm.a_mq"           :class="inputClass" class="w-20" placeholder="MQ no..."     type="search" />
    <input v-model.number="filterForm.a_pc"           :class="inputClass" class="w-20" placeholder="PC no..."     type="search" />
    <input v-model.number="filterForm.a_sc"           :class="inputClass" class="w-20" placeholder="SC no..."     type="search" />
    <input v-model="filterForm.supplier_name"         :class="inputClass" class="w-24" placeholder="supplier..."  type="search" />
    <input v-model="filterForm.customer_name"         :class="inputClass" class="w-24" placeholder="customer..."  type="search" />
    <input v-model="filterForm.transporter_name"      :class="inputClass" class="w-28" placeholder="transporter..." type="search" />
    <input v-model="filterForm.product_name"          :class="inputClass" class="w-24" placeholder="product..."   type="search" />

    <!-- Action Buttons -->
    <div class="flex items-center gap-1">
      <secondary-button @click="emit('search')">Search</secondary-button>
      <secondary-button @click="emit('clear')">Clear</secondary-button>
      <secondary-button
        v-if="showAdd"
        @click="emit('add-trade')">
        Add (+)
      </secondary-button>
      <secondary-button @click="emit('toggle-details')">Toggle</secondary-button>
    </div>

    <!-- Day Checkboxes -->
    <div class="flex items-center gap-2 ml-1">
      <div class="flex items-center gap-0.5">
        <input id="f-mon" v-model="monValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-mon">Mon</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-tue" v-model="tueValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-tue">Tue</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-wed" v-model="wedValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-wed">Wed</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-thu" v-model="thuValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-thu">Thu</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-fri" v-model="friValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-fri">Fri</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-sat" v-model="satValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-sat">Sat</label>
      </div>
      <div class="flex items-center gap-0.5">
        <input id="f-sun" v-model="sunValue" class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" />
        <label class="text-xs text-gray-700 cursor-pointer" for="f-sun">Sun</label>
      </div>
    </div>

  </div>
</template>
