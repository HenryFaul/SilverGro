<script setup>
  import { defineProps, defineEmits } from 'vue';

  const props = defineProps({
    tabs: {
      type: Array,
      required: true,
      // Expected shape: [{ id: Number|String, name: String }]
    },
    selectedTabId: {
      type: [Number, String],
      required: true,
    },
  });

  const emit = defineEmits(['select']);

  const handleSelect = (id) => emit('select', id);
</script>

<template>
  <nav class="-mb-px flex space-x-8">
    <button
      v-for="tab in tabs"
      :key="tab.id"
      @click="handleSelect(tab.id)"
      :class="[
        tab.id === selectedTabId
          ? 'border-indigo-500 text-indigo-600'
          : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
        'whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium',
      ]"
      type="button">
      {{ tab.name }}
    </button>
  </nav>
</template>
