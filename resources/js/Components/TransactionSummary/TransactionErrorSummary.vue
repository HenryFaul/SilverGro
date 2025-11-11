<script setup>
import InputError from '@/Components/InputError.vue';
import { defineProps } from 'vue';
import { XCircleIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
  formErrors: { type: Object, required: true },
  show: { type: Boolean, required: true },
});
</script>

<template>
  <div v-if="show && Object.keys(formErrors).length" class="mb-1">
    <div class="rounded-md bg-red-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">
            There were errors when updating. Fix &amp; update again.
          </h3>
          <div class="mt-2 text-lg text-red-700">
            <ul role="list" class="list-disc space-y-1 pl-5">
              <li>Transaction errors (see appropriate tab)</li>
            </ul>
          </div>
          <div class="mt-1 ml-6">
            <InputError
              v-for="(msg, key) in formErrors"
              :key="key"
              class="mt-2"
              :message="formErrors[key]"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
