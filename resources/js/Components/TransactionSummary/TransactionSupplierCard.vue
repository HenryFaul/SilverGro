<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">Supplier Details</div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <!-- Supplier Selection with Custom Searchable Select -->
      <div class="flex justify-between gap-x-4 py-3">
        <dd class="flex items-start gap-x-2">
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
              v-show="showDropdown && getFilteredSuppliers().length > 0"
              class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
              <div
                v-for="(supplier, index) in getFilteredSuppliers()"
                :key="supplier.id"
                :class="[
                  'relative cursor-pointer select-none py-2 pl-3 pr-9',
                  index === highlightedIndex
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-900 hover:bg-gray-100',
                  isSelected(supplier) ? 'font-semibold' : '',
                ]"
                @mouseenter="highlightedIndex = index"
                @mousedown.prevent="selectSupplier(supplier)">
                <span class="block truncate">
                  {{ supplier.last_legal_name }}
                </span>
                <span
                  v-if="isSelected(supplier)"
                  :class="[
                    'absolute inset-y-0 right-0 flex items-center pr-4',
                    index === highlightedIndex ? 'text-white' : 'text-indigo-600',
                  ]">
                  <CheckIcon class="h-5 w-5" />
                </span>
              </div>
            </div>
          </div>
        </dd>
      </div>

      <!-- Supplier Loading Number -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Supplier loading number</dt>
        <dd class="flex items-start gap-x-2">
          <input
            v-model="combinedForm.supplier_loading_number"
            class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            type="text" />
        </dd>
      </div>

      <!-- PC Linked (only for MQ contracts) -->
      <div
        v-if="selectedTransaction.contract_type_id === 4"
        class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">PC Linked</dt>
        <dd class="flex items-start gap-x-2">
          <div v-if="filteredLinkedContractsPc[0]">
            <div>PC:{{ filteredLinkedContractsPc[0].transport_trans_id }}</div>
            <div>
              {{
                filteredLinkedContractsPc[0].transport_transaction_pc.supplier
                  .last_legal_name
              }}
            </div>
            <div>
              {{ filteredLinkedContractsPc[0].transport_transaction_pc.product.name }}
            </div>
            <div>
              {{
                filteredLinkedContractsPc[0].transport_transaction_pc.transport_load
                  .no_units_incoming
              }}
            </div>
          </div>
          <div v-else>Nothing linked..</div>
        </dd>
      </div>

      <!-- Link MQ to PC Button (only for MQ contracts) -->
      <div class="flex justify-between gap-x-4 py-3">
        <dd class="text-gray-700">
          <div v-if="selectedTransaction.contract_type_id === 4">
            <SecondaryButton
              class="m-1 mt-3"
              @click="emit('view-contract-link')">
              Link MQ to PC
            </SecondaryButton>

            <ContractLinkModal
              :link_type_id="3"
              :mq_trans_id="selectedTransaction.id"
              :show="showContractLinkModal"
              @close="emit('close-contract-link')" />
          </div>
        </dd>
      </div>
    </dl>
  </li>
</template>

<script setup>
import { ref } from 'vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ContractLinkModal from '@/Components/UI/ContractLinkModal.vue';

const props = defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    selectedTransaction: {
      type: Object,
      required: true,
    },
    filteredSuppliers: {
      type: Array,
      required: true,
    },
    filteredLinkedContractsPc: {
      type: Array,
      default: () => [],
    },
    showContractLinkModal: {
      type: Boolean,
      default: false,
    },
    supplierQuery: {
      type: String,
      default: '',
    },
  });

  const emit = defineEmits([
    'update:supplierQuery',
    'update:supplier',
    'view-contract-link',
    'close-contract-link',
  ]);

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

  const getCurrentSupplierId = () => {
    return getId(props.combinedForm.supplier_id);
  };

  const getCurrentSupplier = () => {
    const id = getCurrentSupplierId();
    return props.filteredSuppliers.find((s) => s.id === id) || null;
  };

  const getPlaceholderText = () => {
    const current = getCurrentSupplier();
    return current ? current.last_legal_name : 'Search suppliers...';
  };

  // Filter suppliers based on search query
  const getFilteredSuppliers = () => {
    if (!localSearchQuery.value) {
      return props.filteredSuppliers;
    }

    const query = localSearchQuery.value.toLowerCase();
    return props.filteredSuppliers.filter((supplier) =>
      supplier.last_legal_name.toLowerCase().includes(query)
    );
  };

  const isSelected = (supplier) => {
    return supplier.id === getCurrentSupplierId();
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
    const filtered = getFilteredSuppliers();
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
    const filtered = getFilteredSuppliers();
    if (filtered[highlightedIndex.value]) {
      selectSupplier(filtered[highlightedIndex.value]);
    }
  };

  // Selection handler - debounced to prevent rapid updates
  let updatePending = false;

  const selectSupplier = (supplier) => {
    if (updatePending) return;
    if (!supplier || supplier.id === getCurrentSupplierId()) {
      closeDropdown();
      return;
    }

    updatePending = true;
    closeDropdown();

    // Emit asynchronously to prevent any reactive loops
    setTimeout(() => {
      emit('update:supplier', supplier);
      emit('update:supplierQuery', '');

      setTimeout(() => {
        updatePending = false;
      }, 100);
    }, 0);
  };
</script>
