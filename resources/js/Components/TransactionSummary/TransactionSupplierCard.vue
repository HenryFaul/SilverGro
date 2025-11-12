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
            <Combobox
              v-model="selectedSupplierModel"
              as="div">
              <div class="relative mt-2">
                <ComboboxInput
                  :display-value="displaySupplierName"
                  class="w-70 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  @change="emit('update:supplierQuery', $event.target.value)" />
                <ComboboxButton
                  class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                  <ChevronUpDownIcon
                    aria-hidden="true"
                    class="h-5 w-5 text-gray-400" />
                </ComboboxButton>

                <ComboboxOptions
                  v-if="filteredSuppliers.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                  <ComboboxOption
                    v-for="supplier in filteredSuppliers"
                    :key="supplier.id"
                    v-slot="{ active, selected }"
                    :value="supplier.id"
                    as="template">
                    <ul>
                      <li
                        :class="[
                          'relative cursor-default select-none py-2 pl-3 pr-9',
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                        ]">
                        <span :class="['block truncate', selected && 'font-semibold']">
                          {{ supplier.last_legal_name }}
                        </span>
                        <span
                          v-if="selected"
                          :class="[
                            'absolute inset-y-0 right-0 flex items-center pr-4',
                            active ? 'text-white' : 'text-indigo-600',
                          ]">
                          <CheckIcon
                            aria-hidden="true"
                            class="h-5 w-5" />
                        </span>
                      </li>
                    </ul>
                  </ComboboxOption>
                </ComboboxOptions>
              </div>
            </Combobox>
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
import { computed, nextTick } from 'vue';
import { Combobox, ComboboxButton, ComboboxInput, ComboboxOption, ComboboxOptions, } from '@headlessui/vue';
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

  // Helper to safely get ID from object or primitive
  const getId = (value) => {
    if (value === null || value === undefined) return null;
    return typeof value === 'object' ? value.id : value;
  };

  // Helper to find supplier by ID
  const findSupplierById = (id) => {
    if (!id) return null;
    return props.filteredSuppliers.find((s) => s.id === id) || null;
  };

  // Simple computed for current supplier ID from the form
  const currentSupplierId = computed(() => getId(props.combinedForm.supplier_id));

  // Simple computed for current supplier object
  const currentSupplier = computed(() => findSupplierById(currentSupplierId.value));

  // Display function for the input
  const displaySupplierName = () => currentSupplier.value?.last_legal_name || '';

  // Flag to prevent recursive updates during our own updates
  let isUpdating = false;

  // Simple v-model that breaks all reactive chains
  const selectedSupplierModel = computed({
    get() {
      return currentSupplierId.value;
    },
    set(newId) {
      if (isUpdating) return; // Prevent recursion during our own updates
      if (newId === currentSupplierId.value) return; // No actual change

      isUpdating = true;

      // Use double nextTick to completely break out of current reactive cycle
      nextTick(() => {
        nextTick(() => {
          const supplier = findSupplierById(newId);
          if (supplier) {
            emit('update:supplier', supplier);
          }
          isUpdating = false;
        });
      });
    },
  });
</script>
