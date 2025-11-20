<template>
  <div>
    <ul
      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8"
      role="list">
      <!-- Buying Price Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Buying price</div>
          <div class="text-sm font-light text-gray-900">(From Supplier)</div>
        </div>

        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Supplier</dt>
            <dd class="text-gray-700">
              <div>{{ selectedTransaction.supplier.last_legal_name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Product</dt>
            <dd class="text-gray-700">
              <div>{{ selectedTransaction.product.name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Supply Packaging</dt>
            <dd class="text-gray-700">
              <TransactionPackagingSelect
                v-model="combinedForm.packaging_incoming_id"
                :packaging="filteredPackageIncoming"
                label="" />
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Billing Units</dt>
            <dd class="text-gray-700">
              <TransactionBillingUnitsSelect
                v-model="combinedForm.billing_units_incoming_id"
                :billing-units="filteredBillingUnitsIncoming"
                label="" />
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">No Units</dt>
            <dd class="text-gray-700">
              {{ selectedTransaction.transport_load.no_units_incoming }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Supply Weight (tons)</dt>
            <dd class="text-gray-700">
              {{ selectedTransaction.transport_finance.weight_ton_incoming }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Cost Price / Unit</dt>
            <dd class="text-gray-700">
              <input
                v-model="combinedForm.cost_price_per_unit"
                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                type="number" />
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Total Supplier Cost</dt>
            <dd class="text-gray-700">
              {{ formatNiceNumber(selectedTransaction.transport_finance.cost_price) }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Cost Price / Ton</dt>
            <dd class="text-gray-700">
              {{
                formatNiceNumber(selectedTransaction.transport_finance.cost_price_per_ton)
              }}
            </dd>
          </div>
        </dl>
      </li>

      <!-- Transport Costs Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Transport costs</div>
        </div>

        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Transport rate basis</dt>
            <dd class="text-gray-700">
              <div>
                <select
                  v-model="combinedForm.transport_rate_basis_id"
                  class="mt-2 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  <option
                    v-for="n in allTransportRates"
                    :key="n.id"
                    :value="n.id">
                    {{ n.name }}
                  </option>
                </select>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Transport rate</dt>
            <dd class="text-gray-700">
              <div>
                <input
                  v-model="combinedForm.transport_rate"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="number" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Transport rate / Ton</dt>
            <dd class="text-gray-700">
              {{
                formatNiceNumber(
                  selectedTransaction.transport_finance.transport_rate_per_ton
                )
              }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Transport costs</dt>
            <dd class="text-gray-700">
              {{ formatNiceNumber(selectedTransaction.transport_finance.transport_cost) }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Load Insurance per ton</dt>
            <dd class="text-gray-700">
              {{
                formatNiceNumber(
                  selectedTransaction.transport_finance.load_insurance_per_ton
                )
              }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Transport cost in price</dt>
            <dd class="text-gray-700">
              <SwitchGroup
                as="div"
                class="flex m-2 items-center">
                <Switch
                  v-model="combinedForm.is_transport_costs_inc_price"
                  :class="[
                    combinedForm.is_transport_costs_inc_price
                      ? 'bg-indigo-600'
                      : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                  ]">
                  <span
                    :class="[
                      combinedForm.is_transport_costs_inc_price
                        ? 'translate-x-5'
                        : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    ]"
                    aria-hidden="true" />
                </Switch>
              </SwitchGroup>
            </dd>
          </div>
        </dl>
      </li>

      <!-- Selling Price Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Selling Price</div>
          <div class="text-sm font-light text-gray-900">(To Customer)</div>
        </div>
        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Customer</dt>
            <dd class="text-gray-700">
              <div>{{ selectedTransaction.customer.last_legal_name }}</div>
            </dd>
          </div>
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Invoice Basis</dt>
            <dd class="text-gray-700">
              <div>{{ selectedTransaction.customer.invoice_basis?.value || 'N/A' }}</div>
            </dd>
          </div>
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Product</dt>
            <dd class="text-gray-700">
              <div>{{ selectedTransaction.product.name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Selling Packaging</dt>
            <dd class="flex items-start gap-x-2">
              <TransactionPackagingSelect
                v-model="combinedForm.packaging_outgoing_id"
                :packaging="filteredPackageOutgoing"
                label="" />
            </dd>
          </div>
          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Billing units</dt>
            <dd class="flex items-start gap-x-2">
              <TransactionBillingUnitsSelect
                v-model="combinedForm.billing_units_outgoing_id"
                :billing-units="filteredBillingUnitsOutgoing"
                label="" />
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">No Units</dt>
            <dd class="text-gray-700">
              {{ selectedTransaction.transport_load.no_units_outgoing }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Selling Weight (tons)</dt>
            <dd class="text-gray-700">
              {{ selectedTransaction.transport_finance.weight_ton_outgoing }}
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Selling Price / Unit</dt>
            <dd class="text-gray-700">
              <input
                v-model="combinedForm.selling_price_per_unit"
                class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                type="number" />
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Total Selling Price</dt>
            <dd class="text-gray-700">
              <div>
                {{
                  formatNiceNumber(selectedTransaction.transport_finance.selling_price)
                }}
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-3">
            <dt class="text-gray-500">Selling Price / Ton</dt>
            <dd class="text-gray-700">
              <div>
                {{
                  formatNiceNumber(
                    selectedTransaction.transport_finance.selling_price_per_ton
                  )
                }}
              </div>
            </dd>
          </div>
        </dl>
      </li>

      <!-- Margin Calculation Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">
            Margin Calculation
          </div>
        </div>

        <div>
          <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
            <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">
              Additional costs:
            </h3>

            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">
                <input
                  v-model="combinedForm.additional_cost_desc_1"
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  placeholder="Description..."
                  type="text" />
              </dt>
              <dd class="text-gray-700">
                <div>
                  <input
                    v-model="combinedForm.additional_cost_1"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    type="number" />
                </div>
              </dd>
            </div>
            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">
                <input
                  v-model="combinedForm.additional_cost_desc_2"
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  placeholder="Description..."
                  type="text" />
              </dt>
              <dd class="text-gray-700">
                <div>
                  <input
                    v-model="combinedForm.additional_cost_2"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    type="number" />
                </div>
              </dd>
            </div>
            <div class="flex justify-between gap-x-4 py-3">
              <dt class="text-gray-500">
                <input
                  v-model="combinedForm.additional_cost_desc_3"
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  placeholder="Description..."
                  type="text" />
              </dt>
              <dd class="text-gray-700">
                <div>
                  <input
                    v-model="combinedForm.additional_cost_3"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    type="number" />
                </div>
              </dd>
            </div>

            <div>
              <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                Adjusted GP:
              </h3>
              <div class="flex justify-between gap-x-4 py-3">
                <dt class="text-gray-500">
                  <input
                    v-model="combinedForm.adjusted_gp_notes"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="Description..."
                    type="text" />
                </dt>
                <dd class="text-gray-700">
                  <div>
                    <input
                      v-model="combinedForm.adjusted_gp"
                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                      type="number" />
                  </div>
                </dd>
              </div>
            </div>
          </dl>

          <div class="m-2 p-2">
            <table class="min-w-full divide-y divide-gray-300">
              <thead>
                <tr class="divide-x divide-gray-200">
                  <th
                    class="py-2 pl-4 pr-4 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                    scope="col">
                    Item
                  </th>
                  <th
                    class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900"
                    scope="col">
                    Plan
                  </th>
                  <th
                    class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900"
                    scope="col">
                    Actual
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Tons In
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{ selectedTransaction.transport_finance.weight_ton_incoming }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{ selectedTransaction.transport_finance.weight_ton_incoming_actual }}
                  </td>
                </tr>
                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Tons Out
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{ selectedTransaction.transport_finance.weight_ton_outgoing }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{ selectedTransaction.transport_finance.weight_ton_outgoing_actual }}
                  </td>
                </tr>
                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Selling Price
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.selling_price
                      )
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.selling_price_actual
                      )
                    }}
                  </td>
                </tr>
                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Cost Price
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(selectedTransaction.transport_finance.cost_price)
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.cost_price_actual
                      )
                    }}
                  </td>
                </tr>

                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Transport Cost
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.transport_cost
                      )
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.transport_cost_actual
                      )
                    }}
                  </td>
                </tr>

                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    Total Cost Price
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.total_cost_price
                      )
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.total_cost_price_actual
                      )
                    }}
                  </td>
                </tr>

                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    GP
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(selectedTransaction.transport_finance.gross_profit)
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.gross_profit_actual
                      )
                    }}
                  </td>
                </tr>

                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    GP / Ton
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.gross_profit_per_ton
                      )
                    }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      formatNiceNumber(
                        selectedTransaction.transport_finance.gross_profit_per_ton_actual
                      )
                    }}
                  </td>
                </tr>

                <tr class="divide-x divide-gray-200">
                  <td
                    class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-0">
                    GP %
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{ selectedTransaction.transport_finance.gross_profit_percent }}
                  </td>
                  <td class="whitespace-nowrap text-right text-sm text-gray-500">
                    {{
                      selectedTransaction.transport_finance.gross_profit_percent_actual
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
  import { Switch, SwitchGroup } from '@headlessui/vue';
  import TransactionPackagingSelect from './TransactionPackagingSelect.vue';
  import TransactionBillingUnitsSelect from './TransactionBillingUnitsSelect.vue';
  import { formatNiceNumber } from '@/Composables/useNumberFormatters.js';

  const props = defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    selectedTransaction: {
      type: Object,
      required: true,
    },
    allTransportRates: {
      type: Array,
      required: true,
    },
    filteredPackageIncoming: {
      type: Array,
      required: true,
    },
    filteredBillingUnitsIncoming: {
      type: Array,
      required: true,
    },
    filteredPackageOutgoing: {
      type: Array,
      required: true,
    },
    filteredBillingUnitsOutgoing: {
      type: Array,
      required: true,
    },
  });
</script>
