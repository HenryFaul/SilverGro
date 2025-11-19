<script setup>
  import { computed } from 'vue';
  import { ExclamationTriangleIcon } from '@heroicons/vue/20/solid';
  import BaseTooltip from '@/Components/UI/BaseTooltip.vue';

  const props = defineProps({
    dealTicket: Object,
    purchaseOrder: Object,
    salesOrder: Object,
    transportOrder: Object,
    transportLoad: Object,
    transportInvoice: Object,
    alerts: {
      type: Array,
      default: () => [],
    },
  });

  const hasAlerts = computed(() => props.alerts && props.alerts.length > 0);

  const alertTooltipContent = computed(() => {
    if (!hasAlerts.value) return '';
    return (
      '<ul class="list-disc pl-4">' +
      props.alerts
        .map((a) => {
          const entity = a.status_entity?.entity ?? '';
          const type = a.status_type?.type ?? '';
          const created = a.created_at ?? '';
          const line = [entity, type].filter(Boolean).join(' - ');
          return `<li>${line}${created ? ' (' + created + ')' : ''}</li>`;
        })
        .join('') +
      '</ul>'
    );
  });
</script>

<template>
  <div class="flex items-center space-x-1">
    <!-- Existing status icons (if you want to reuse them here later) -->
    <slot />

    <!-- Operational alerts icon -->
    <div v-if="hasAlerts">
      <BaseTooltip
        :content="alertTooltipContent"
        as-html>
        <ExclamationTriangleIcon class="h-5 w-5 text-yellow-500 hover:text-yellow-600" />
      </BaseTooltip>
    </div>
  </div>
</template>
