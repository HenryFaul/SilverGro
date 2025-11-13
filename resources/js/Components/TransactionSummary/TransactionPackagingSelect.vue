<template>
  <div class="relative">
    <label
      v-if="label"
      class="block text-sm font-medium text-gray-500 mb-1">
      {{ label }}
    </label>
    <div class="relative">
      <input
        v-model="searchQuery"
        :placeholder="selectedPackaging ? selectedPackaging.name : 'Search packaging...'"
        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        type="text"
        @blur="handleBlur"
        @focus="showDropdown = true"
        @keydown.escape="closeDropdown"
        @keydown.enter.prevent="selectFirst"
        @keydown.down.prevent="navigateDown"
        @keydown.up.prevent="navigateUp" />
      <button
        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
        type="button"
        @click="toggleDropdown">
        <svg
          class="h-5 w-5 text-gray-400"
          fill="currentColor"
          viewBox="0 0 20 20">
          <path
            clip-rule="evenodd"
            d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
            fill-rule="evenodd" />
        </svg>
      </button>
    </div>

    <!-- Dropdown -->
    <div
      v-if="showDropdown && filteredPackaging.length > 0"
      class="absolute z-10 mt-1 max-h-60 w-48 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
      <div
        v-for="(pkg, index) in filteredPackaging"
        :key="pkg.id"
        :class="[
          'relative cursor-pointer select-none py-2 pl-3 pr-9',
          index === highlightedIndex ? 'bg-indigo-600 text-white' : 'text-gray-900',
        ]"
        @mouseenter="highlightedIndex = index"
        @mousedown.prevent="selectPackaging(pkg)">
        <span :class="['block truncate', isSelected(pkg) && 'font-semibold']">
          {{ pkg.name }}
        </span>
        <span
          v-if="isSelected(pkg)"
          :class="[
            'absolute inset-y-0 right-0 flex items-center pr-4',
            index === highlightedIndex ? 'text-white' : 'text-indigo-600',
          ]">
          <svg
            class="h-5 w-5"
            fill="currentColor"
            viewBox="0 0 20 20">
            <path
              clip-rule="evenodd"
              d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
              fill-rule="evenodd" />
          </svg>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
      type: [Object, Number],
      default: null,
    },
    packaging: {
      type: Array,
      default: () => [],
    },
    label: {
      type: String,
      default: '',
    },
  });

  const emit = defineEmits(['update:modelValue']);

  const searchQuery = ref('');
  const showDropdown = ref(false);
  const highlightedIndex = ref(0);
  const isUpdating = ref(false);

  const selectedPackaging = computed(() => {
    if (!props.modelValue) return null;
    const id =
      typeof props.modelValue === 'object' ? props.modelValue.id : props.modelValue;
    return props.packaging.find((p) => p.id === id);
  });

  const filteredPackaging = computed(() => {
    if (!searchQuery.value) return props.packaging;
    const query = searchQuery.value.toLowerCase();
    return props.packaging.filter((p) => p.name.toLowerCase().includes(query));
  });

  const isSelected = (pkg) => {
    if (!props.modelValue) return false;
    const currentId =
      typeof props.modelValue === 'object' ? props.modelValue.id : props.modelValue;
    return pkg.id === currentId;
  };

  const selectPackaging = (pkg) => {
    if (isUpdating.value) return;
    isUpdating.value = true;

    setTimeout(() => {
      emit('update:modelValue', pkg);
      searchQuery.value = '';
      showDropdown.value = false;
      highlightedIndex.value = 0;
      isUpdating.value = false;
    }, 0);
  };

  const selectFirst = () => {
    if (filteredPackaging.value.length > 0) {
      selectPackaging(filteredPackaging.value[0]);
    }
  };

  const navigateDown = () => {
    if (highlightedIndex.value < filteredPackaging.value.length - 1) {
      highlightedIndex.value++;
    }
  };

  const navigateUp = () => {
    if (highlightedIndex.value > 0) {
      highlightedIndex.value--;
    }
  };

  const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
  };

  const closeDropdown = () => {
    showDropdown.value = false;
    searchQuery.value = '';
  };

  const handleBlur = () => {
    setTimeout(() => {
      showDropdown.value = false;
      searchQuery.value = '';
    }, 200);
  };

  // Watch for external changes
  watch(
    () => props.modelValue,
    () => {
      if (!showDropdown.value) {
        searchQuery.value = '';
      }
    }
  );
</script>
