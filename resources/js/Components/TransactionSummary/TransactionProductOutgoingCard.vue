<template>
  <li class="overflow-hidden rounded-xl border border-gray-200">
    <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
      <div class="text-sm font-medium leading-6 text-gray-900">Outgoing product</div>
      <div class="text-sm font-light text-gray-900">(To Customer)</div>
    </div>
    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
      <!-- No Units -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">No Units</dt>
        <dd class="text-gray-700">
          <input
            v-model="combinedForm.no_units_outgoing"
            type="number"
            class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
        </dd>
      </div>

      <!-- Billing Units -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Billing units</dt>
        <dd class="flex items-start gap-x-2">
          <Combobox
            as="div"
            v-model="combinedForm.billing_units_outgoing_id">
            <div class="relative mt-2">
              <ComboboxInput
                class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                @change="$emit('update:billingUnitsOutgoingQuery', $event.target.value)"
                :display-value="(units) => units?.name" />
              <ComboboxButton
                class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <ChevronUpDownIcon
                  class="h-5 w-5 text-gray-400"
                  aria-hidden="true" />
              </ComboboxButton>

              <ComboboxOptions
                v-if="filteredBillingUnitsOutgoing.length > 0"
                class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ComboboxOption
                  v-for="units in filteredBillingUnitsOutgoing"
                  :key="units.id"
                  :value="units"
                  as="template"
                  v-slot="{ active, selected }">
                  <ul>
                    <li
                      :class="[
                        'relative cursor-default select-none py-2 pl-3 pr-9',
                        active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      ]">
                      <span :class="['block truncate', selected && 'font-semibold']">
                        {{ units.name }}
                      </span>
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
        </dd>
      </div>

      <!-- Packaging -->
      <div class="flex justify-between gap-x-4 py-3">
        <dt class="text-gray-500">Packaging</dt>
        <dd class="flex items-start gap-x-2">
          <Combobox
            as="div"
            v-model="combinedForm.packaging_outgoing_id">
            <div class="relative mt-2">
              <ComboboxInput
                class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                @change="$emit('update:packageOutgoingQuery', $event.target.value)"
                :display-value="(packaging) => packaging?.name" />
              <ComboboxButton
                class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <ChevronUpDownIcon
                  class="h-5 w-5 text-gray-400"
                  aria-hidden="true" />
              </ComboboxButton>

              <ComboboxOptions
                v-if="filteredPackageOutgoing.length > 0"
                class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ComboboxOption
                  v-for="packaging in filteredPackageOutgoing"
                  :key="packaging.id"
                  :value="packaging"
                  as="template"
                  v-slot="{ active, selected }">
                  <ul>
                    <li
                      :class="[
                        'relative cursor-default select-none py-2 pl-3 pr-9',
                        active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      ]">
                      <span :class="['block truncate', selected && 'font-semibold']">
                        {{ packaging.name }}
                      </span>
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
        </dd>
      </div>
    </dl>
  </li>
</template>

<script setup>
  import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
  } from '@headlessui/vue';
  import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid';

  defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    filteredBillingUnitsOutgoing: {
      type: Array,
      required: true,
    },
    filteredPackageOutgoing: {
      type: Array,
      required: true,
    },
  });

  defineEmits(['update:billingUnitsOutgoingQuery', 'update:packageOutgoingQuery']);
</script>
