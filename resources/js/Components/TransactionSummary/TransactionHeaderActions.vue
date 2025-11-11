<script setup>
import { defineEmits, defineProps } from 'vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
  isUpdating: { type: Boolean, default: false },
});

const emit = defineEmits(['clone', 'update']);
</script>
<template>
  <div class="mt-4 flex md:absolute md:right-0 md:top-3 md:mt-0">
    <button
      @click="emit('clone')"
      class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
      type="button"
    >
      Clone
    </button>
    <button
      @click.prevent="emit('update')"
      class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
      type="button"
    >
      <ExclamationTriangleIcon
        v-if="isUpdating"
        class="w-3 h-3 animate-spin mr-2 fill-white"
      />
      Update
    </button>
  </div>
</template>
