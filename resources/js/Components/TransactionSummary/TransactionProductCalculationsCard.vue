<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">Calculations</div>
      <div class="text-sm font-light text-gray-900">(Weight & variances)</div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <!-- Incoming Weight -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Incoming Weight (Tons)</dt>
        <dd class="flex items-start gap-x-2">
          <div class="font-medium text-gray-900">
            {{ selectedTransaction.transport_finance.weight_ton_incoming }}
          </div>
        </dd>
      </div>

      <!-- Outgoing Weight -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Outgoing Weight (Tons)</dt>
        <dd class="flex items-start gap-x-2">
          <div class="font-medium text-gray-900">
            {{ selectedTransaction.transport_finance.weight_ton_outgoing }}
          </div>
        </dd>
      </div>

      <!-- Weight Variance -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Weight Variance (Tons)</dt>
        <dd class="flex items-start gap-x-2">
          <div class="font-medium text-gray-900">
            {{
              selectedTransaction.transport_finance.weight_ton_incoming -
              selectedTransaction.transport_finance.weight_ton_outgoing
            }}
          </div>
        </dd>
      </div>
    </dl>
  </li>
</template>

<script setup>
  defineProps({
    selectedTransaction: {
      type: Object,
      required: true,
    },
  });
</script>
