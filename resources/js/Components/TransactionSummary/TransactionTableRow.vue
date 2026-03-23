<script setup>
import { computed } from 'vue';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import Icon from '@/Components/Icon.vue';
import BaseTooltip from '@/Components/UI/BaseTooltip.vue';
import { formatNiceDate } from '@/Composables/useDateFormatters';
import { truncateText } from '@/Composables/useTextFormatters';

// Helper function for formatting numbers as currency
  const formatNiceNumberInt = (_number) => {
    if (_number === null) {
      return 0;
    } else {
      let val = Math.round(_number);
      return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  };

  // Helper function to check if payment is overdue
  const isPaymentOverdue = (invoice_details) => {
    return invoice_details && invoice_details.overdue > 10;
  };

  // Helper function to check if there's a weighbridge variance
  const hasWeighbridgeVariance = (driver_vehicles) => {
    if (!driver_vehicles || driver_vehicles.length === 0) {
      return false;
    }
    for (let i = 0; i < driver_vehicles.length; i++) {
      if (driver_vehicles[i].is_weighbridge_variance) {
        return true;
      }
    }
    return false;
  };

  // Helper function to check if transaction has any alerts
  const hasAnyAlerts = (trans) => {
    // Check if there are any transport status alerts
    if (trans.transport_status && trans.transport_status.length > 0) {
      return true;
    }
    // Check if payment is overdue
    if (
      trans.transport_invoice_details &&
      isPaymentOverdue(trans.transport_invoice_details)
    ) {
      return true;
    }
    // Check if there's a weighbridge variance
    if (
      trans.transport_driver_vehicle &&
      hasWeighbridgeVariance(trans.transport_driver_vehicle)
    ) {
      return true;
    }
    return false;
  };

  // Helper function to list all warnings
  const warningLister = (trans) => {
    var the_list = '';

    // Add transport status alerts
    if (trans.transport_status && trans.transport_status.length > 0) {
      trans.transport_status.forEach(function (arrayItem) {
        var entity = arrayItem.status_entity.entity;
        var type = arrayItem.status_type.type;
        the_list += '  ' + entity + '_' + type + '.';
      });
    }

    // Add payment overdue alert
    if (
      trans.transport_invoice_details &&
      isPaymentOverdue(trans.transport_invoice_details)
    ) {
      the_list += '  payment_overdue.';
    }

    // Add weighbridge variance alert
    if (
      trans.transport_driver_vehicle &&
      hasWeighbridgeVariance(trans.transport_driver_vehicle)
    ) {
      the_list += '  weighbridge_variance.';
    }

    return the_list;
  };

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
        v-if="transaction.is_split_load"
        class="font-light text-sm italic text-indigo-400">
        <span class="flex items-center">
          <icon
            class="w-3 h-3"
            name="arrow-split" />
          <span
            v-if="transaction.a_mq"
            class="font-bold">
            (MQ:{{ transaction.a_mq }})
          </span>
          <span
            v-if="transaction.a_pc"
            class="font-bold">
            (PC:{{ transaction.a_pc }})
          </span>
          <span
            v-if="transaction.a_sc"
            class="font-bold">
            (SC:{{ transaction.a_sc }})
          </span>
          (ID: {{ transaction.id }})
        </span>
      </div>
      <div
        v-else
        class="font-light text-sm italic">
        <span
          v-if="transaction.a_mq"
          class="font-bold">
          (MQ:{{ transaction.a_mq }})
        </span>
        <span
          v-if="transaction.a_pc"
          class="font-bold">
          (PC:{{ transaction.a_pc }})
        </span>
        <span
          v-if="transaction.a_sc"
          class="font-bold">
          (SC:{{ transaction.a_sc }})
        </span>
        (ID:{{ transaction.id }})
      </div>

      <div
        v-if="transaction.old_id"
        class="font-light text-sm italic">
        (OLD:{{ transaction.old_id }})
      </div>
    </td>

    <!-- STATUS Column -->
    <td :class="cellClass">
      <div class="flex items-center gap-1">
        <base-tooltip v-if="transaction.supplier?.terms_of_payment_id === 1" content="Supplier C.O.D account.">
          <icon class="w-5 h-5 fill-red-500" name="bell-solid" />
        </base-tooltip>
        <base-tooltip v-if="transaction.transport_invoice_details && isPaymentOverdue(transaction.transport_invoice_details)" content="Payment overdue.">
          <icon class="w-4 h-4 fill-red-500" name="dollar" />
        </base-tooltip>
        <base-tooltip v-if="hasAnyAlerts(transaction)" :content="warningLister(transaction)">
          <icon class="w-4 h-4 animate-pulse fill-red-500" name="triangle" />
        </base-tooltip>
        <base-tooltip v-if="!transaction.include_in_calculations" content="Exclude from calculations.">
          <icon class="w-4 h-4 fill-gray-500" name="info" />
        </base-tooltip>
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

    <!-- C_ORDER (Customer Order Number) -->
    <td
      v-if="showDetails"
      :class="cellClass">
      <div v-if="transaction.transport_job?.customer_order_number">
        {{ transaction.transport_job.customer_order_number }}
      </div>
      <div v-else>N/A</div>
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

    <!-- REG# (Vehicle Registration) -->
    <td :class="cellClass">
      <div
        v-if="
          transaction.transport_driver_vehicle &&
          transaction.transport_driver_vehicle.length > 0
        ">
        <div
          v-for="driver_vehicle of transaction.transport_driver_vehicle"
          :key="driver_vehicle.id">
          {{ driver_vehicle.vehicle?.reg_no }}
        </div>
      </div>
      <div v-else>N/A</div>
    </td>

    <!-- Product -->
    <td :class="cellClass">
      {{ transaction.product?.name || '' }}
    </td>

    <!-- D/T -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.deal_ticket?.is_active" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>
    <!-- P/O -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.purchase_order?.is_active" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>
    <!-- S/O -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.sales_order?.is_active" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>
    <!-- T/O -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.transport_order?.is_active" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>
    <!-- WB -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.transport_load?.is_weighbridge_certificate_received" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>
    <!-- INV -->
    <td v-if="showDetails" :class="cellClass">
      <CheckIcon v-if="transaction.transport_invoice?.is_active" class="w-4 h-4 fill-green-300" />
      <XMarkIcon v-else class="w-4 h-4 fill-red-400" />
    </td>

    <!-- Units -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ transaction.transport_load?.no_units_incoming }}
    </td>

    <!-- Cost/Ton -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ formatNiceNumberInt(transaction.transport_finance?.cost_price_per_ton) }}
    </td>

    <!-- TC/Ton (Transport Cost per Ton) -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ formatNiceNumberInt(transaction.transport_finance?.transport_rate_per_ton) }}
    </td>

    <!-- Price/Ton -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ formatNiceNumberInt(transaction.transport_finance?.selling_price_per_ton) }}
    </td>

    <!-- GP/Ton (Gross Profit per Ton) -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ formatNiceNumberInt(transaction.transport_finance?.gross_profit_per_ton) }}
    </td>

    <!-- GP% (Gross Profit Percentage) -->
    <td
      v-if="showDetails"
      :class="cellClass">
      {{ transaction.transport_finance?.gross_profit_percent?.toFixed(2) }}%
    </td>

    <!-- Process Notes -->
    <td
      v-if="showDetails"
      :class="cellClass">
      <div v-if="transaction.process_notes">
        <base-tooltip :content="transaction.process_notes">
          {{ truncateText(transaction.process_notes, 40) }}
        </base-tooltip>
      </div>
      <div v-else>None...</div>
    </td>
  </tr>
</template>
