<template>
  <ul
    class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-2 xl:gap-x-8"
    role="list">
    <!-- First Card: Linked Contracts (MQ to PC) -->
    <li
      v-if="selectedTransaction.contract_type_id === 4"
      class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Linked Contracts</div>
      </div>

      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <div>
          <div class="">
            <div class="">
              <div v-if="selectedTransaction.contract_type_id === 1">Unallocated</div>

              <div v-if="selectedTransaction.contract_type_id === 4">
                <div class="text-indigo-400 font-bold">MQ</div>

                <SecondaryButton
                  class="m-1 mt-3"
                  @click="$emit('view-contract-link')">
                  Link MQ to PC
                </SecondaryButton>

                <ContractLinkModal
                  :link_type_id="3"
                  :mq_trans_id="selectedTransaction.id"
                  :show="viewContractLinkModal"
                  @close="$emit('close-contract-link')" />

                <div class="mt-3">
                  <div>PC linked to this MQ:</div>

                  <div>
                    <form class="mt-5">
                      <div class="px-4 sm:px-6 lg:px-8">
                        <div class="-mx-4 mt-8 flow-root sm:mx-0">
                          <table class="min-w-full">
                            <colgroup>
                              <col class="w-full sm:w-1/2" />
                              <col class="sm:w-1/6" />
                              <col class="sm:w-1/6" />
                              <col class="sm:w-1/6" />
                            </colgroup>
                            <thead class="border-b border-gray-300 text-gray-900">
                              <tr>
                                <th
                                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                  scope="col">
                                  Parties
                                </th>
                                <th
                                  class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                  scope="col">
                                  Product
                                </th>
                                <th
                                  class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                  scope="col">
                                  GP
                                </th>
                                <th
                                  class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0"
                                  scope="col"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="contract in selectedTransaction.linked_contracts"
                                :key="contract.id"
                                class="border-b border-gray-200">
                                <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                  <div class="font-medium text-gray-900">
                                    {{
                                      contract.transport_transaction_pc.supplier
                                        .last_legal_name
                                    }}
                                    -
                                    {{
                                      contract.transport_transaction_pc.customer
                                        .last_legal_name
                                    }}
                                  </div>
                                  <div class="mt-1 truncate text-gray-500">
                                    {{
                                      contract.transport_transaction_pc
                                        .customer_order_number
                                    }}
                                  </div>
                                </td>
                                <td
                                  class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                  {{ contract.transport_transaction_pc.product.name }}
                                </td>
                                <td
                                  class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                  {{
                                    NiceNumber(
                                      contract.transport_transaction_pc.transport_finance
                                        .gross_profit
                                    )
                                  }}
                                </td>
                                <td
                                  class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                  <Link
                                    :data="{
                                      selected_trans_id:
                                        contract.transport_transaction_pc.id,
                                    }"
                                    class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    href="/transaction_summary"
                                    method="get"
                                    target="_blank">
                                    Summary
                                  </Link>

                                  <Link
                                    :data="{
                                      selected_trans_id:
                                        contract.transport_transaction_pc.id,
                                    }"
                                    class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    href="/pc_overview"
                                    method="get"
                                    target="_blank">
                                    Overview
                                  </Link>
                                </td>
                              </tr>
                            </tbody>
                            <tfoot></tfoot>
                          </table>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </dl>
    </li>

    <!-- Second Card: Linked Contracts (MQ to SC) -->
    <li
      v-if="selectedTransaction.contract_type_id === 4"
      class="overflow-hidden rounded-xl border border-gray-200">
      <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
        <div class="text-sm font-medium leading-6 text-gray-900">Linked Contracts</div>
      </div>

      <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
        <div>
          <div class="">
            <div class="">
              <div v-if="selectedTransaction.contract_type_id === 1">Unallocated</div>
              <div v-if="selectedTransaction.contract_type_id === 3">SC</div>

              <div v-if="selectedTransaction.contract_type_id === 4">
                <div class="text-indigo-400 font-bold">MQ</div>

                <SecondaryButton
                  class="m-1 mt-3"
                  @click="$emit('view-contract-link-sc')">
                  Link MQ to SC
                </SecondaryButton>

                <ContractLinkModalSc
                  :link_type_id="4"
                  :mq_trans_id="selectedTransaction.id"
                  :show="viewContractLinkScModal"
                  @close="$emit('close-contract-link-sc')" />

                <div class="mt-3">
                  <div>SC linked to this MQ:</div>

                  <div>
                    <form class="mt-5">
                      <div class="px-4 sm:px-6 lg:px-8">
                        <div class="-mx-4 mt-8 flow-root sm:mx-0">
                          <table class="min-w-full">
                            <colgroup>
                              <col class="w-full sm:w-1/2" />
                              <col class="sm:w-1/6" />
                              <col class="sm:w-1/6" />
                              <col class="sm:w-1/6" />
                            </colgroup>
                            <thead class="border-b border-gray-300 text-gray-900">
                              <tr>
                                <th
                                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                  scope="col">
                                  Parties
                                </th>
                                <th
                                  class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                  scope="col">
                                  Product
                                </th>
                                <th
                                  class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell"
                                  scope="col">
                                  GP
                                </th>
                                <th
                                  class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0"
                                  scope="col"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="contract in selectedTransaction.linked_contracts_sc"
                                :key="contract.id"
                                class="border-b border-gray-200">
                                <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                  <div class="font-medium text-gray-900">
                                    {{
                                      contract.transport_transaction_sc.supplier
                                        .last_legal_name
                                    }}
                                    -
                                    {{
                                      contract.transport_transaction_sc.customer
                                        .last_legal_name
                                    }}
                                  </div>
                                  <div class="mt-1 truncate text-gray-500">
                                    {{
                                      contract.transport_transaction_sc
                                        .customer_order_number
                                    }}
                                  </div>
                                </td>
                                <td
                                  class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                  {{ contract.transport_transaction_sc.product.name }}
                                </td>
                                <td
                                  class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">
                                  {{
                                    NiceNumber(
                                      contract.transport_transaction_sc.transport_finance
                                        .gross_profit
                                    )
                                  }}
                                </td>
                                <td
                                  class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                  <Link
                                    :data="{
                                      selected_trans_id:
                                        contract.transport_transaction_sc.id,
                                    }"
                                    class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    href="/transaction_summary"
                                    method="get"
                                    target="_blank">
                                    Summary
                                  </Link>

                                  <Link
                                    :data="{
                                      selected_trans_id:
                                        contract.transport_transaction_sc.id,
                                    }"
                                    class="m-1 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    href="/transaction_summary"
                                    method="get"
                                    target="_blank">
                                    Overview
                                  </Link>
                                </td>
                              </tr>
                            </tbody>
                            <tfoot></tfoot>
                          </table>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </dl>
    </li>
  </ul>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import ContractLinkModal from '@/Components/UI/ContractLinkModal.vue';
  import ContractLinkModalSc from '@/Components/UI/ContractLinkModalSc.vue';
  import { formatNiceNumber } from '@/Composables/useNumberFormatters.js';

  const NiceNumber = formatNiceNumber;

  const props = defineProps({
    selectedTransaction: {
      type: Object,
      required: true,
    },
    viewContractLinkModal: {
      type: Boolean,
      default: false,
    },
    viewContractLinkScModal: {
      type: Boolean,
      default: false,
    },
  });

  defineEmits([
    'view-contract-link',
    'close-contract-link',
    'view-contract-link-sc',
    'close-contract-link-sc',
  ]);
</script>
