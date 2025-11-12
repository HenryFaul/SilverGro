<template>
  <div class="flex justify-between gap-x-4 py-3">
    <dd class="flex items-start gap-x-2">
      <div>
        <p class="text-gray-500">{{ label }}</p>
        <div class="relative w-70">
          <!-- Search Input -->
          <input
            ref="searchInput"
            v-model="localSearchQuery"
            :placeholder="getPlaceholderText()"
            class="w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            type="text"
            @blur="handleBlur"
            @focus="showDropdown = true"
            @keydown.escape="closeDropdown"
            @keydown.enter.prevent="selectHighlighted"
            @keydown.down.prevent="highlightNext"
            @keydown.up.prevent="highlightPrevious" />

          <!-- Dropdown Button -->
          <button
            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
            type="button"
            @click="toggleDropdown">
            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
          </button>

          <!-- Dropdown List -->
          <div
            v-show="showDropdown && getFilteredCustomers().length > 0"
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
            <div
              v-for="(customer, index) in getFilteredCustomers()"
              :key="customer.id"
              :class="[
                'relative cursor-pointer select-none py-2 pl-3 pr-9',
                index === highlightedIndex
                  ? 'bg-indigo-600 text-white'
                  : 'text-gray-900 hover:bg-gray-100',
                isSelected(customer) ? 'font-semibold' : '',
              ]"
              @mouseenter="highlightedIndex = index"
              @mousedown.prevent="selectCustomer(customer)">
              <span class="block truncate">
                {{ customer.last_legal_name }}
              </span>
              <span
                v-if="isSelected(customer)"
                :class="[
                  'absolute inset-y-0 right-0 flex items-center pr-4',
                  index === highlightedIndex ? 'text-white' : 'text-indigo-600',
                ]">
                <CheckIcon class="h-5 w-5" />
              </span>
            </div>
          </div>
        </div>
      </div>
    </dd>
  </div>
</template>

<script setup>
  import { ref } from 'vue';
  import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

  const props = defineProps({
    modelValue: {
      type: Object,
      default: null,
    },
    customers: {
      type: Array,
      required: true,
    },
    label: {
      type: String,
      default: 'Customer',
    },
  });

  const emit = defineEmits(['update:modelValue']);

  // Local UI state only - no form data
  const showDropdown = ref(false);
  const localSearchQuery = ref('');
  const highlightedIndex = ref(0);
  const searchInput = ref(null);

  // Helper functions - pure, no reactivity
  const getId = (value) => {
    if (!value) return null;
    return typeof value === 'object' ? value.id : value;
  };

  const getCurrentCustomerId = () => {
    return getId(props.modelValue);
  };

  const getCurrentCustomer = () => {
    const id = getCurrentCustomerId();
    return props.customers.find((c) => c.id === id) || null;
  };

  const getPlaceholderText = () => {
    const current = getCurrentCustomer();
    return current ? current.last_legal_name : 'Search customers...';
  };

  // Filter customers based on search query
  const getFilteredCustomers = () => {
    if (!localSearchQuery.value) {
      return props.customers;
    }

    const query = localSearchQuery.value.toLowerCase();
    return props.customers.filter((customer) =>
      customer.last_legal_name.toLowerCase().includes(query)
    );
  };

  const isSelected = (customer) => {
    return customer.id === getCurrentCustomerId();
  };

  // Dropdown controls
  const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) {
      localSearchQuery.value = '';
      highlightedIndex.value = 0;
      setTimeout(() => searchInput.value?.focus(), 0);
    }
  };

  const closeDropdown = () => {
    showDropdown.value = false;
    localSearchQuery.value = '';
    highlightedIndex.value = 0;
  };

  const handleBlur = () => {
    // Small delay to allow click events to fire
    setTimeout(() => {
      closeDropdown();
    }, 150);
  };

  // Keyboard navigation
  const highlightNext = () => {
    const filtered = getFilteredCustomers();
    if (highlightedIndex.value < filtered.length - 1) {
      highlightedIndex.value++;
    }
  };

  const highlightPrevious = () => {
    if (highlightedIndex.value > 0) {
      highlightedIndex.value--;
    }
  };

  const selectHighlighted = () => {
    const filtered = getFilteredCustomers();
    if (filtered[highlightedIndex.value]) {
      selectCustomer(filtered[highlightedIndex.value]);
    }
  };

  // Selection handler - debounced to prevent rapid updates
  let updatePending = false;

  const selectCustomer = (customer) => {
    if (updatePending) return;
    if (!customer || customer.id === getCurrentCustomerId()) {
      closeDropdown();
      return;
    }

    updatePending = true;
    closeDropdown();

    // Emit asynchronously to prevent any reactive loops
    setTimeout(() => {
      emit('update:modelValue', customer);

      setTimeout(() => {
        updatePending = false;
      }, 100);
    }, 0);
  };
</script>
