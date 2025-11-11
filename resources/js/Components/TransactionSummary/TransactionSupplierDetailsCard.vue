<script setup>
import { defineProps, defineEmits } from 'vue';
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
} from '@headlessui/vue';
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ContractLinkModal from '@/Components/UI/ContractLinkModal.vue';

const props = defineProps({
  combinedForm: { type: Object, required: true },
  filteredSuppliers: { type: Array, required: true },
  supplierQuery: { type: String, default: '' },
  selectedTransaction: { type: Object, required: true },
  filteredLinkedContractsPc: { type: Array, default: () => [] },
  viewContractLinkModal: { type: Boolean, default: false },
});

const emit = defineEmits([
  'update:supplierQuery',
  'viewContractLink',
  'closeContractLink',
]);
</script>

<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div
      class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6"
    >
      <div class="text-sm font-medium leading-6 text-gray-900">
        Supplier Details
      </div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <div class="flex justify-between gap-x-4 py-3">
        <dd class="flex items-start gap-x-2">
          <div>
            <Combobox as="div" v-model="combinedForm.supplier_id">
              <div class="relative mt-2">
                <ComboboxInput
                  class="w-70 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  @change="(e) => emit('update:supplierQuery', e.target.value)"
                  :display-value="(supplier) => supplier?.last_legal_name"
                />
                <ComboboxButton
                  class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
                >
                  <ChevronUpDownIcon
                    class="h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                </ComboboxButton>

                <ComboboxOptions
                  v-if="filteredSuppliers.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                  <ComboboxOption
                    v-for="supplier in filteredSuppliers"
                    :key="supplier.id"
                    :value="supplier"
                    as="template"
                    v-slot="{ active, selected }"
                  >
                    <ul>
                      <li
                        :class="[
                          'relative cursor-default select-none py-2 pl-3 pr-9',
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                        ]"
                      >
                        <span
                          :class="[
                            'block truncate',
                            selected && 'font-semibold',
                          ]"
                        >
                          {{ supplier.last_legal_name }}
                        </span>
                        <span
                          v-if="selected"
                          :class="[
                            'absolute inset-y-0 right-0 flex items-center pr-4',
                            active ? 'text-white' : 'text-indigo-600',
                          ]"
                        >
                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
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

      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Supplier loading number</dt>
        <dd class="flex items-start gap-x-2">
          <input
            v-model="combinedForm.supplier_loading_number"
            type="text"
            class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </dd>
      </div>

      <div
        v-if="selectedTransaction.contract_type_id === 4"
        class="flex justify-between gap-x-4 py-3"
      >
        <dt class="text-gray-500">PC Linked</dt>
        <dd class="flex items-start gap-x-2">
          <div v-if="filteredLinkedContractsPc && filteredLinkedContractsPc[0]">
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
              {{
                filteredLinkedContractsPc[0].transport_transaction_pc.product
                  .name
              }}
            </div>
            <div>
              {{
                filteredLinkedContractsPc[0].transport_transaction_pc
                  .transport_load.no_units_incoming
              }}
            </div>
          </div>
          <div v-else>Nothing linked..</div>
        </dd>
      </div>

      <div class="flex justify-between gap-x-4 py-3">
        <dd class="text-gray-700">
          <div>
            <div v-if="selectedTransaction.contract_type_id === 4">
              <SecondaryButton
                class="m-1 mt-3"
                @click="emit('viewContractLink')"
              >
                Link MQ to PC
              </SecondaryButton>

              <ContractLinkModal
                :show="viewContractLinkModal"
                @close="emit('closeContractLink')"
                :mq_trans_id="selectedTransaction.id"
                :link_type_id="3"
              />
            </div>
          </div>
        </dd>
      </div>
    </dl>
  </li>
</template>
