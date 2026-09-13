<script setup>
  import { computed, ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  import Icon from '@/Components/Icon.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';

  const props = defineProps({
    addresses: { type: Array, default: () => [] },
  });

  const emit = defineEmits(['edit']);

  const showHidden = ref(false);

  const visible = computed(() => props.addresses.filter((a) => !a.is_hidden));
  const hidden = computed(() => props.addresses.filter((a) => a.is_hidden));

  // What the rows actually render: the hidden ones only join the list once the
  // user asks for them, so a party with 140 surplus addresses reads as the two
  // or three real ones until somebody goes looking.
  const rows = computed(() => (showHidden.value ? [...visible.value, ...hidden.value] : visible.value));

  const hide = (address) => {
    router.post(route('address.hide', address.id), {}, { preserveScroll: true });
  };

  const unhide = (address) => {
    router.post(route('address.unhide', address.id), {}, { preserveScroll: true });
  };

  const oneLine = (a) =>
    [a.line_1, a.line_2, a.line_3, a.country, a.code].filter(Boolean).join(' ').trim();
</script>

<template>
  <div>
    <div
      v-if="hidden.length > 0"
      class="mb-2">
      <button
        class="text-sm text-indigo-500 hover:text-indigo-700 underline"
        type="button"
        @click="showHidden = !showHidden">
        {{ showHidden ? 'Hide' : 'Show' }} {{ hidden.length }} hidden
        {{ hidden.length === 1 ? 'address' : 'addresses' }}
      </button>
    </div>

    <ul class="w-3/2">
      <li
        v-for="n in rows"
        :key="n.id"
        :class="n.is_hidden ? 'opacity-50' : ''"
        :value="n.id"
        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
        <div class="flex row mt-1">
          <div class="flex-none w-1/6">
            <icon
              v-if="n.address_type_id === 1"
              class="mr-2 w-6 h-6 fill-green-200"
              name="truck" />
            <icon
              v-if="n.address_type_id === 2"
              class="mr-2 w-6 h-6 fill-green-200"
              name="house" />
            <icon
              v-if="n.address_type_id === 3"
              class="mr-2 w-6 h-6 fill-green-200"
              name="envelope" />
          </div>

          <div class="flex-auto w-3/6">
            {{ oneLine(n) || '(no address captured)' }}
            <span
              v-if="n.is_hidden"
              class="ml-2 text-xs uppercase tracking-wide text-gray-500">
              hidden
            </span>
          </div>

          <div class="flex-auto w-1/6">
            <icon
              v-if="n.is_primary === 1"
              class="mr-2 w-6 h-6 fill-green-200"
              name="tick-circle" />
          </div>

          <div class="flex-auto w-1/6">
            <SecondaryButton
              class="ml-2"
              @click="emit('edit', n)">
              Edit
            </SecondaryButton>

            <SecondaryButton
              v-if="!n.is_hidden"
              class="ml-2"
              @click="hide(n)">
              Hide
            </SecondaryButton>
            <SecondaryButton
              v-else
              class="ml-2"
              @click="unhide(n)">
              Restore
            </SecondaryButton>
          </div>
        </div>
      </li>
    </ul>

    <div
      v-if="rows.length === 0"
      class="text-sm text-gray-500 py-4">
      No addresses captured.
    </div>
  </div>
</template>
