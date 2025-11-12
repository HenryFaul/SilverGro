<template>
  <ul
    class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
    role="list">
    <!-- Customer Details Card -->
    <li class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Customer details</div>
      </div>

      <dl class="-my-3 divide-y divide-gray-100 px-6 py-2 text-sm leading-6">
        <!-- Split Load Primary Toggle -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Split Load Primary</dt>
          <dd class="flex items-start gap-x-2">
            <div>
              <SwitchGroup
                as="div"
                class="flex m-2 items-center">
                <Switch
                  v-model="combinedForm.is_split_load_primary"
                  :class="[
                    combinedForm.is_split_load_primary ? 'bg-indigo-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                  ]">
                  <span
                    :class="[
                      combinedForm.is_split_load_primary
                        ? 'translate-x-5'
                        : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    ]"
                    aria-hidden="true" />
                </Switch>
              </SwitchGroup>
            </div>
          </dd>
        </div>

        <!-- Split Load Primary ID -->
        <div
          v-if="!combinedForm.is_split_load_primary"
          class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Split Load Primary ID</dt>
          <dd class="flex items-start gap-x-2">
            <div v-if="primaryLinkedTransSplit">
              <Link
                :data="{ selected_trans_id: primaryLinkedTransSplit.id }"
                href="/transaction_summary"
                method="get"
                target="_blank">
                ID {{ primaryLinkedTransSplit.id }}
              </Link>
            </div>
            <div v-else>Nothing linked</div>
          </dd>
        </div>

        <!-- Split Load Actions -->
        <div v-if="!combinedForm.is_split_load_primary">
          <SecondaryButton
            class="m-1 mt-3"
            @click="$emit('view-split-link')">
            Link Split Trade
          </SecondaryButton>

          <SecondaryButton
            class="m-1 mt-3"
            @click="$emit('delete-trans-link', selectedTransaction.id)">
            Remove Link
          </SecondaryButton>

          <SplitLinkModal
            :link_type_id="5"
            :mq_trans_id="selectedTransaction.id"
            :show="viewSplitLinkModal"
            @close="$emit('close-split-link')" />
        </div>

        <!-- Customer Selection -->
        <TransactionCustomerSelect
          v-model="combinedForm.customer_id"
          :customers="filteredCustomers"
          label="Customer" />

        <!-- Customer Order Number -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Customer Order number</dt>
          <dd class="flex items-start gap-x-2">
            <input
              v-model="combinedForm.customer_order_number"
              class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              type="text" />
          </dd>
        </div>

        <!-- Offloading Number -->
        <div class="flex justify-between gap-x-4 py-1">
          <dt class="text-gray-500">Offloading no</dt>
          <dd class="flex items-start gap-x-2">
            <input
              id="loading_no"
              v-model="combinedForm.driver_vehicle_loading_number"
              class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              type="text" />
          </dd>
        </div>

        <!-- SC Linked (for MQ contracts) -->
        <div
          v-if="selectedTransaction.contract_type_id === 4"
          class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">SC Linked</dt>
          <dd class="flex items-start gap-x-2">
            <div v-if="filteredLinkedContractsSc[0]">
              <div>SC:{{ filteredLinkedContractsSc[0].transport_trans_id }}</div>
              <div>
                {{
                  filteredLinkedContractsSc[0].transport_transaction_pc.customer
                    .last_legal_name
                }}
              </div>
              <div>
                {{
                  filteredLinkedContractsSc[0].transport_transaction_pc.supplier
                    .last_legal_name
                }}
              </div>
              <div>
                {{ filteredLinkedContractsSc[0].transport_transaction_pc.product.name }}
              </div>
              <div>
                {{
                  filteredLinkedContractsSc[0].transport_transaction_pc.transport_load
                    .no_units_incoming
                }}
              </div>
            </div>
            <div v-else>Nothing linked..</div>
          </dd>
        </div>

        <!-- Link MQ to SC Button -->
        <div class="flex justify-between gap-x-4 py-3">
          <dd class="text-gray-700">
            <div>
              <div v-if="selectedTransaction.contract_type_id === 4">
                <SecondaryButton
                  class="m-1 mt-3"
                  @click="$emit('view-contract-link-sc')">
                  Link MQ to SC
                </SecondaryButton>

                <ContractLinkModalSc
                  :link_type_id="4"
                  :mq_trans_id="selectedTransaction.id"
                  :show="viewContractLinkModalSc"
                  @close="$emit('close-contract-link-sc')" />
              </div>
            </div>
          </dd>
        </div>
      </dl>
    </li>

    <!-- Customer Account Details Card -->
    <li class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">
          Customer Account details
        </div>
      </div>
      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <!-- Delivery Address -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Delivery Address</dt>
          <dd class="flex items-start gap-x-2">
            <div>
              <div class="mt-2">
                <Combobox
                  v-model="combinedForm.delivery_address_id"
                  as="div">
                  <div class="relative mt-2">
                    <ComboboxInput
                      :display-value="(address) => address?.line_1"
                      class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                      @change="
                        $emit('update:delivery-address-query', $event.target.value)
                      " />
                    <ComboboxButton
                      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                      <ChevronUpDownIcon
                        aria-hidden="true"
                        class="h-5 w-5 text-gray-400" />
                    </ComboboxButton>

                    <ComboboxOptions
                      v-if="filteredDeliveryAddress.length > 0"
                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                      <ComboboxOption
                        v-for="address in filteredDeliveryAddress"
                        :key="address.id"
                        v-slot="{ active, selected }"
                        :value="address"
                        as="template">
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
                              <span
                                :class="['ml-3 truncate', selected && 'font-semibold']">
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
              <div class="mt-2">
                <Link
                  :href="route('customer.show', combinedForm.customer_id)"
                  class="underline text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  + Add customer address
                </Link>
              </div>
            </div>
          </dd>
        </div>

        <!-- Selected Delivery Address Display -->
        <div class="flex justify-between gap-x-4 py-3">
          <div>
            <div v-if="combinedForm.delivery_address_id">
              <h3 class="text-base font-semibold leading-7 text-indigo-400">
                Selected Delivery Address:
              </h3>

              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">Line 1</dt>
                <dd class="text-gray-700">
                  <div>{{ combinedForm.delivery_address_id.line_1 }}</div>
                </dd>
              </div>

              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">Line 2</dt>
                <dd class="text-gray-700">
                  <div>{{ combinedForm.delivery_address_id.line_2 }}</div>
                </dd>
              </div>

              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">Line 3</dt>
                <dd class="text-gray-700">
                  <div>{{ combinedForm.delivery_address_id.line_3 }}</div>
                </dd>
              </div>

              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">Country</dt>
                <dd class="text-gray-700">
                  <div>{{ combinedForm.delivery_address_id.country }}</div>
                </dd>
              </div>

              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">Code</dt>
                <dd class="text-gray-700">
                  <div>{{ combinedForm.delivery_address_id.code }}</div>
                </dd>
              </div>
            </div>

            <div v-else>No customer addresses loaded...</div>
          </div>
        </div>

        <!-- Payment Terms -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Payment Terms</dt>
          <dd class="text-gray-700">
            <div>{{ selectedTransaction.customer.terms_of_payment.value }}</div>
          </dd>
        </div>

        <!-- Invoice Basis -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Invoice basis</dt>
          <dd class="text-gray-700">
            <div>{{ selectedTransaction.customer.invoice_basis.value }}</div>
          </dd>
        </div>

        <!-- VAT Exempt -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">VAT Exempt</dt>
          <dd class="text-gray-700">
            <div>
              <check-icon
                v-if="selectedTransaction.customer.is_vat_exempt"
                class="w-6 h-6 fill-green-300" />
              <XCircleIcon
                v-else
                class="w-6 h-6 fill-red-400" />
            </div>
          </dd>
        </div>

        <!-- VAT Certificate -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">VAT Certificate</dt>
          <dd class="text-gray-700">
            <div>
              <check-icon
                v-if="selectedTransaction.customer.is_vat_cert_received"
                class="w-6 h-6 fill-green-300" />
              <XCircleIcon
                v-else
                class="w-6 h-6 fill-red-400" />
            </div>
          </dd>
        </div>
      </dl>
    </li>

    <!-- Product Details Card (Customer Tab View) -->
    <li class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Product details</div>
      </div>
      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <!-- Product Name -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Product</dt>
          <dd class="text-gray-700">
            <div>{{ selectedTransaction.product.name }}</div>
          </dd>
        </div>

        <!-- Billing Units Outgoing -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Billing Units outgoing</dt>
          <dd class="text-gray-700">
            <Combobox
              v-model="combinedForm.billing_units_outgoing_id"
              as="div">
              <div class="relative mt-2">
                <ComboboxInput
                  :display-value="(units) => units?.name"
                  class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  @change="
                    $emit('update:billing-units-outgoing-query', $event.target.value)
                  " />
                <ComboboxButton
                  class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                  <ChevronUpDownIcon
                    aria-hidden="true"
                    class="h-5 w-5 text-gray-400" />
                </ComboboxButton>

                <ComboboxOptions
                  v-if="filteredBillingUnitsOutgoing.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                  <ComboboxOption
                    v-for="units in filteredBillingUnitsOutgoing"
                    :key="units.id"
                    v-slot="{ active, selected }"
                    :value="units"
                    as="template">
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
                            aria-hidden="true"
                            class="h-5 w-5" />
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
          <dd class="text-gray-700">
            <Combobox
              v-model="combinedForm.packaging_outgoing_id"
              as="div">
              <div class="relative mt-2">
                <ComboboxInput
                  :display-value="(packaging) => packaging?.name"
                  class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  @change="$emit('update:package-outgoing-query', $event.target.value)" />
                <ComboboxButton
                  class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                  <ChevronUpDownIcon
                    aria-hidden="true"
                    class="h-5 w-5 text-gray-400" />
                </ComboboxButton>

                <ComboboxOptions
                  v-if="filteredPackageOutgoing.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                  <ComboboxOption
                    v-for="packaging in filteredPackageOutgoing"
                    :key="packaging.id"
                    v-slot="{ active, selected }"
                    :value="packaging"
                    as="template">
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
                            aria-hidden="true"
                            class="h-5 w-5" />
                        </span>
                      </li>
                    </ul>
                  </ComboboxOption>
                </ComboboxOptions>
              </div>
            </Combobox>
          </dd>
        </div>

        <!-- Selling Price Per Unit -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Selling Price / Unit</dt>
          <dd class="text-gray-700">
            <div>
              {{
                NiceNumber(selectedTransaction.transport_finance.selling_price_per_unit)
              }}
            </div>
          </dd>
        </div>

        <!-- Selling Price Per Ton -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Selling Price / Ton</dt>
          <dd class="text-gray-700">
            <div>
              {{
                NiceNumber(selectedTransaction.transport_finance.selling_price_per_ton)
              }}
            </div>
          </dd>
        </div>

        <!-- Total Selling Price -->
        <div class="flex justify-between gap-x-4 py-3">
          <dt class="text-gray-500">Total Selling Price</dt>
          <dd class="text-gray-700">
            <div>
              {{ NiceNumber(selectedTransaction.transport_finance.selling_price) }}
            </div>
          </dd>
        </div>
      </dl>
    </li>

    <!-- Customer Notes Card -->
    <li class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Customer notes</div>
      </div>
      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <div class="flex justify-between gap-x-4 py-3">
          <AreaInput
            id="customer_notes"
            v-model="combinedForm.customer_notes"
            :rows="12"
            class="mt-1 block w-1/3"
            placeholder="Optional notes..."
            type="text" />

          <InputError
            :message="combinedForm.errors.customer_notes"
            class="mt-2" />
        </div>
      </dl>
    </li>
  </ul>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3';
  import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    Switch,
    SwitchGroup,
  } from '@headlessui/vue';
  import { CheckIcon, ChevronUpDownIcon, XCircleIcon } from '@heroicons/vue/20/solid';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TransactionCustomerSelect from '@/Components/TransactionSummary/TransactionCustomerSelect.vue';
  import SplitLinkModal from '@/Components/UI/SplitLinkModal.vue';
  import ContractLinkModalSc from '@/Components/UI/ContractLinkModalSc.vue';
  import AreaInput from '@/Components/AreaInput.vue';
  import InputError from '@/Components/InputError.vue';
  import { useNumberFormatters } from '@/Composables/useNumberFormatters.js';

  const { NiceNumber } = useNumberFormatters();

  defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    selectedTransaction: {
      type: Object,
      required: true,
    },
    filteredCustomers: {
      type: Array,
      required: true,
    },
    filteredDeliveryAddress: {
      type: Array,
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
    filteredLinkedContractsSc: {
      type: Array,
      default: () => [],
    },
    primaryLinkedTransSplit: {
      type: Object,
      default: null,
    },
    viewSplitLinkModal: {
      type: Boolean,
      default: false,
    },
    viewContractLinkModalSc: {
      type: Boolean,
      default: false,
    },
  });

  defineEmits([
    'update:delivery-address-query',
    'update:billing-units-outgoing-query',
    'update:package-outgoing-query',
    'view-split-link',
    'close-split-link',
    'delete-trans-link',
    'view-contract-link-sc',
    'close-contract-link-sc',
  ]);
</script>
