<script setup>
  import { computed } from 'vue';
  import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';
  import Icon from '@/Components/Icon.vue';
  import BaseTooltip from '@/Components/UI/BaseTooltip.vue';
  import { formatNiceDate } from '@/composables/useDateFormatters';
  import { truncateText } from '@/composables/useTextFormatters';

  const props = defineProps({
    transaction: {
      type: Object,
      required: true,
    },
    isSelected: {
      type: Boolean,
      default: false,
    },
    showDetails: {
      type: Boolean,
      default: false,
    },
  });

  const emit = defineEmits(['select']);

  const rowClasses = computed(() => [
    props.isSelected ? 'bg-indigo-300' : '',
    'hover:bg-gray-100 text-sm focus-within:bg-gray-100 cursor-pointer',
  ]);

  const cellClass =
    'whitespace-nowrap border-b px-3 py-1 text-sm text-gray-500 lg:table-cell';

  const handleClick = () => {
    emit('select', props.transaction.id);
  };
</script>

<template>
  <tr
    :class="rowClasses"
    @click="handleClick">
    <!-- ID Column -->
    <td :class="cellClass">
      <div
        v-if="transaction.a_mq"
        class="text-lg font-bold text-black">
        MQ{{ transaction.a_mq }}
      </div>

      <div
        v-if="transaction.is_split_load"
        class="font-light text-sm italic text-indigo-400">
        <span class="flex items-center">
          <icon
            name="arrow-split"
            class="w-3 h-3" />
          (ID: {{ transaction.id }})
        </span>
      </div>
      <div
        v-else
        class="font-light text-sm italic">
        (ID:{{ transaction.id }})
      </div>

      <div
        v-if="transaction.old_id"
        class="font-light text-sm italic">
        (OLD:{{ transaction.old_id }})
      </div>
    </td>

    <!-- Contract Type -->
    <td :class="cellClass">
      {{ transaction.contract_type?.name || '' }}
    </td>

    <!-- Date -->
    <td :class="cellClass">
      {{ formatNiceDate(transaction.transport_date_earliest) }}
    </td>

    <!-- Supplier -->
    <td :class="cellClass">
      {{ transaction.supplier?.last_legal_name || '' }}
    </td>

    <!-- Customer -->
    <td :class="cellClass">
      {{ transaction.customer?.last_legal_name || '' }}
    </td>

    <!-- Transporter -->
    <td :class="cellClass">
      {{ transaction.transporter?.last_legal_name || '' }}
    </td>

    <!-- Product -->
    <td :class="cellClass">
      {{ transaction.product?.name || '' }}
    </td>

    <!-- Deal Ticket (D/T) -->
    <td
      v-if="showDetails && transaction.deal_ticket"
      :class="cellClass">
      <check-icon
        v-if="transaction.deal_ticket.is_active"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Purchase Order (P/O) -->
    <td
      v-if="showDetails && transaction.purchase_order"
      :class="cellClass">
      <check-icon
        v-if="transaction.purchase_order.is_active"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Sales Order (S/O) -->
    <td
      v-if="showDetails && transaction.sales_order"
      :class="cellClass">
      <check-icon
        v-if="transaction.sales_order.is_active"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Transport Order (T/O) -->
    <td
      v-if="showDetails && transaction.transport_order"
      :class="cellClass">
      <check-icon
        v-if="transaction.transport_order.is_active"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Weighbridge (WB) -->
    <td
      v-if="showDetails && transaction.transport_load"
      :class="cellClass">
      <check-icon
        v-if="transaction.transport_load.is_weighbridge_certificate_received"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Invoice (INV) -->
    <td
      v-if="showDetails && transaction.transport_invoice"
      :class="cellClass">
      <check-icon
        v-if="transaction.transport_invoice.is_active"
        class="w-5 h-5 fill-green-300" />
      <x-mark-icon
        v-else
        class="w-5 h-5 fill-red-400" />
    </td>

    <!-- Notes -->
    <td :class="cellClass">
      <div v-if="transaction.process_notes">
        <base-tooltip :content="transaction.process_notes">
          {{ truncateText(transaction.process_notes, 40) }}
        </base-tooltip>
      </div>
      <div v-else>None...</div>
    </td>
  </tr>
</template>
