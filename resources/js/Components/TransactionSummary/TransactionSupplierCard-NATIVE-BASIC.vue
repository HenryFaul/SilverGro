<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">Supplier Details</div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <!-- Supplier Selection -->
      <div class="flex justify-between gap-x-4 py-3">
        <dd class="flex items-start gap-x-2">
          <div>
            <!-- Use native select instead of HeadlessUI Combobox -->
            <select
              :value="getCurrentSupplierId()"
              @change="handleSupplierChange($event.target.value)"
              class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
              <option value="">Select a supplier...</option>
              <option
                v-for="supplier in filteredSuppliers"
                :key="supplier.id"
                :value="supplier.id">
                {{ supplier.last_legal_name }}
              </option>
            </select>
          </div>
        </dd>
      </div>

      <!-- Supplier Loading Number -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Supplier loading number</dt>
        <dd class="flex items-start gap-x-2">
          <input
            v-model="combinedForm.supplier_loading_number"
            type="text"
            class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
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
                filteredLinkedContractsPc[0].transport_transaction_pc.customer
                  .last_legal_name
              }}
            </div>
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
              :show="showContractLinkModal"
              @close="emit('close-contract-link')"
              :mq_trans_id="selectedTransaction.id"
              :link_type_id="3" />
          </div>
        </dd>
      </div>
    </dl>
  </li>
</template>

<script setup>
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

// Helper functions - no reactive state whatsoever
const getId = (value) => {
  if (!value) return null;
  return typeof value === 'object' ? value.id : value;
};

const findSupplierById = (id) => {
  if (!id) return null;
  return props.filteredSuppliers.find(s => s.id == id) || null; // Use == not === for type coercion
};

// Get current supplier ID directly from props (no local state)
const getCurrentSupplierId = () => {
  return getId(props.combinedForm.supplier_id);
};

// Simple debounced handler - no state mutations at all
let debounceTimeout = null;
const handleSupplierChange = (newIdString) => {
  // Clear any pending debounced calls
  if (debounceTimeout) {
    clearTimeout(debounceTimeout);
  }

  // Debounce to prevent rapid fire
  debounceTimeout = setTimeout(() => {
    const newId = parseInt(newIdString);
    const currentId = getCurrentSupplierId();

    if (newId === currentId) return; // No change

    const supplier = findSupplierById(newId);
    if (supplier) {
      emit('update:supplier', supplier);
    }

    debounceTimeout = null;
  }, 50); // Short debounce
};
</script>
