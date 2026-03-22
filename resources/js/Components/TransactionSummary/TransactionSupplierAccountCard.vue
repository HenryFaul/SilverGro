<script setup>
  import { ref } from 'vue';
  import InputError from '@/Components/InputError.vue';
  import AddressModal from '@/Components/UI/AddressModal.vue';
  import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
  } from '@headlessui/vue';
  import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid';

  const props = defineProps({
    combinedForm: { type: Object, required: true },
    selectedTransaction: { type: Object, required: true },
    filteredCollectionAddress: { type: Array, required: true },
    collectionAddressQuery: { type: String, default: '' },
  });

  const emit = defineEmits(['update:collectionAddressQuery', 'address-created']);

  const showAddressModal = ref(false);
</script>

<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">
        Supplier Account Details
      </div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Payment Terms</dt>
        <dd class="text-gray-700">
          <div>
            {{ selectedTransaction.supplier.terms_of_payment.value }}
          </div>
        </dd>
      </div>

      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Collection Address</dt>
        <dd class="flex items-start gap-x-2">
          <div>
            <div class="mt-2">
              <Combobox
                as="div"
                v-model="combinedForm.collection_address_id">
                <div class="relative mt-2">
                  <ComboboxInput
                    class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    @change="(e) => emit('update:collectionAddressQuery', e.target.value)"
                    :display-value="(address) => address?.line_1" />
                  <ComboboxButton
                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                    <ChevronUpDownIcon
                      class="h-5 w-5 text-gray-400"
                      aria-hidden="true" />
                  </ComboboxButton>

                  <ComboboxOptions
                    v-if="filteredCollectionAddress.length > 0"
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ComboboxOption
                      v-for="address in filteredCollectionAddress"
                      :key="address.id"
                      :value="address"
                      as="template"
                      v-slot="{ active, selected }">
                      <ul>
                        <li
                          :class="[
                            'relative cursor-default select-none py-2 pl-3 pr-9',
                            active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          ]">
                          <div class="flex items-center">
                            <span
                              :class="[
                                'inline-block h-2 w-2 flex-shrink-0 rounded-full',
                                address.is_primary ? 'bg-green-400' : 'bg-gray-200',
                              ]"
                              aria-hidden="true" />
                            <span :class="['ml-3 truncate', selected && 'font-semibold']">
                              <span>{{ address.line_1 }}</span>
                              <span v-if="address.line_2">, {{ address.line_2 }}</span>
                              <span v-if="address.line_3">, {{ address.line_3 }}</span>
                              <span class="sr-only">
                                is {{ address.is_primary ? 'online' : 'offline' }}
                              </span>
                            </span>
                          </div>
                          <span
                            v-if="selected"
                            :class="[
                              'absolute inset-y-0 right-0 flex items-center pr-4',
                              active ? 'text-white' : 'text-indigo-600',
                            ]">
                            <CheckIcon
                              class="h-5 w-5"
                              aria-hidden="true" />
                          </span>
                        </li>
                      </ul>
                    </ComboboxOption>
                  </ComboboxOptions>
                </div>
              </Combobox>

              <InputError
                class="mt-2"
                :message="combinedForm.errors['collection_address_id.id']" />
            </div>

            <div class="mt-2">
              <button
                type="button"
                class="underline text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="showAddressModal = true">
                + Add supplier address
              </button>
            </div>

            <AddressModal
              :related_id="combinedForm.supplier_id?.id"
              related_class="App\Models\Supplier"
              :show="showAddressModal"
              @close="showAddressModal = false"
              @address-created="emit('address-created')" />
          </div>
        </dd>
      </div>

      <div class="flex justify-between gap-x-4 py-3">
        <div>
          <div v-if="combinedForm.collection_address_id">
            <h3 class="text-base font-semibold leading-7 text-indigo-400">
              Selected Collection Address:
            </h3>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">Line 1</dt>
              <dd class="text-gray-700">
                <div>{{ combinedForm.collection_address_id.line_1 }}</div>
              </dd>
            </div>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">Line 2</dt>
              <dd class="text-gray-700">
                <div>{{ combinedForm.collection_address_id.line_2 }}</div>
              </dd>
            </div>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">Line 3</dt>
              <dd class="text-gray-700">
                <div>{{ combinedForm.collection_address_id.line_3 }}</div>
              </dd>
            </div>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">Country</dt>
              <dd class="text-gray-700">
                <div>{{ combinedForm.collection_address_id.country }}</div>
              </dd>
            </div>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">Code</dt>
              <dd class="text-gray-700">
                <div>{{ combinedForm.collection_address_id.code }}</div>
              </dd>
            </div>
          </div>
          <div v-else>No supplier addresses loaded...</div>
        </div>
      </div>
    </dl>
  </li>
</template>
