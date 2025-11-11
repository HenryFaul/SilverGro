<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">Supplier Notes</div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <div class="flex justify-between gap-x-4 py-3">
        <AreaInput
          id="supplier_notes"
          v-model="combinedForm.suppliers_notes"
          :rows="12"
          placeholder="Optional notes..."
          type="text"
          class="mt-1 block w-1/3" />

        <InputError
          class="mt-2"
          :message="combinedForm.errors.suppliers_notes" />
      </div>
    </dl>
  </li>
</template>

<script setup>
  import AreaInput from '@/Components/AreaInput.vue';
  import InputError from '@/Components/InputError.vue';

  defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
  });
</script>
