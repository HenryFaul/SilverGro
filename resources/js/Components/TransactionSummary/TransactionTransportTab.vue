<template>
  <ul
    class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
    role="list">
    <!-- Transport Details Card -->
    <li class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Transport details</div>
      </div>
      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <!-- Transporter Select -->
        <div class="flex justify-between gap-x-4 py-3">
          <dd class="flex items-start gap-x-2">
            <TransactionTransporterSelect
              v-model="combinedForm.transporter_id"
              :transporters="filteredTransporters"
              label="Transporter" />
          </dd>
        </div>

        <!-- Transport Date Earliest -->
        <div class="flex justify-between gap-x-4 py-3">
          <dd class="flex items-start gap-x-2">
            <div class="w-70">
              <VueDatePicker
                v-model="combinedForm.transport_date_earliest"
                :format="formatEarly"
                :teleport="true"></VueDatePicker>
              <div class="ml-3 text-sm text-indigo-400">Transport date earliest</div>
            </div>
          </dd>
        </div>

        <!-- Transport Date Latest -->
        <div class="flex justify-between gap-x-4 py-3">
          <dd class="flex items-start gap-x-2">
            <div class="w-70">
              <VueDatePicker
                v-model="combinedForm.transport_date_latest"
                :format="formatLatest"
                :teleport="true"></VueDatePicker>
              <div class="ml-3 text-sm text-indigo-400">Transport date latest</div>
            </div>
          </dd>
        </div>
      </dl>
    </li>

    <!-- Additional cards will be added here -->
    <!-- Driver/Vehicle, Transport Load, Notes cards -->
  </ul>
</template>

<script setup>
import TransactionTransporterSelect from '@/Components/TransactionSummary/TransactionTransporterSelect.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// Props
  const props = defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    filteredTransporters: {
      type: Array,
      default: () => [],
    },
    formatEarly: {
      type: Function,
      default: null,
    },
    formatLatest: {
      type: Function,
      default: null,
    },
  });

  // Emits
  const emit = defineEmits(['update:transporterQuery']);
</script>
