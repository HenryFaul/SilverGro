<template>
  <div>
    <ul
      class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8"
      role="list">
      <!-- Invoice Basis Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
          <div class="text-sm font-light text-gray-900">(Basis)</div>
        </div>

        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Supplier</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ selectedTransaction.supplier.last_legal_name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Product</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ selectedTransaction.product.name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Customer</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ selectedTransaction.customer.last_legal_name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Order no</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ combinedForm.customer_order_number }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Billing basis</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ selectedTransaction.customer.invoice_basis.value }}</div>
            </dd>
          </div>
        </dl>
      </li>

      <!-- Invoice Inputs Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
          <div class="text-sm font-light text-gray-900">(Inputs)</div>
        </div>

        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Weighbridge Certificate received</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <SwitchGroup
                  as="div"
                  class="flex m-1 items-center">
                  <Switch
                    v-model="combinedForm.is_weighbridge_certificate_received"
                    :class="[
                      combinedForm.is_weighbridge_certificate_received
                        ? 'bg-indigo-600'
                        : 'bg-gray-200',
                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                    ]">
                    <span
                      :class="[
                        combinedForm.is_weighbridge_certificate_received
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

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Transporter</dt>
            <dd class="flex items-start gap-x-2">
              <div>{{ selectedTransaction.transporter.last_legal_name }}</div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Vehicle registration</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                {{
                  selectedTransaction.transport_job.transport_driver_vehicle[0].vehicle
                    .reg_no
                }}
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Weighbridge Upload</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <input
                  v-model="combinedForm.weighbridge_upload_weight"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="number" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Weighbridge Offload</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <input
                  v-model="combinedForm.weighbridge_offload_weight"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="number" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Weighbridge variance</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                {{
                  formatNiceVariance(
                    combinedForm.weighbridge_upload_weight,
                    combinedForm.weighbridge_offload_weight
                  )
                }}
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <AreaInput
              v-model="combinedForm.notes"
              :rows="4"
              class="mt-1 block w-full"
              placeholder="Optional comments..."
              type="text" />
          </div>
        </dl>
      </li>

      <!-- Invoice and Debtor Control Card -->
      <li class="overflow-hidden rounded-xl border border-gray-200">
        <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
          <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
          <div class="text-sm font-light text-gray-900">(and Debtor control)</div>
        </div>

        <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice No</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <input
                  v-model="combinedForm.invoice_no"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="text" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice date</dt>
            <dd class="flex items-start gap-x-2">
              <div class="w-48">
                <VueDatePicker
                  v-model="combinedForm.invoice_date"
                  :format="formatInvoiceDate"
                  :teleport="true"></VueDatePicker>
                <div class="ml-3 text-sm text-indigo-400">Invoice date</div>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice pay by date</dt>
            <dd class="flex items-start gap-x-2">
              <div class="w-48">
                <VueDatePicker
                  v-model="combinedForm.invoice_pay_by_date"
                  :format="formatInvoicePayByDay"
                  :teleport="true"></VueDatePicker>
                <div class="ml-3 text-sm text-indigo-400">
                  Invoice pay by date
                  <span v-if="paymentTerms">
                    ({{ paymentTerms.value }} / {{ paymentTerms.days }} days)
                  </span>
                </div>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice paid date</dt>
            <dd class="flex items-start gap-x-2">
              <div class="w-48">
                <VueDatePicker
                  v-model="combinedForm.invoice_paid_date"
                  :format="formatInvoicePdDay"
                  :teleport="true"></VueDatePicker>
                <div class="ml-3 text-sm text-indigo-400">Invoice paid date</div>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice amount</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <input
                  v-model="combinedForm.invoice_amount"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="number" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoice amount paid</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <input
                  v-model="combinedForm.invoice_amount_paid"
                  class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                  type="number" />
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Balance overdue</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                {{
                  formatNiceNumber(
                    selectedTransaction.transport_invoice.transport_invoice_details
                      .overdue
                  )
                }}
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Outstanding</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                {{
                  formatNiceNumber(
                    selectedTransaction.transport_invoice.transport_invoice_details
                      .outstanding
                  )
                }}
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Transaction done</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <SwitchGroup
                  as="div"
                  class="flex m-1 items-center">
                  <Switch
                    v-model="combinedForm.is_transaction_done"
                    :class="[
                      combinedForm.is_transaction_done ? 'bg-indigo-600' : 'bg-gray-200',
                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                    ]">
                    <span
                      :class="[
                        combinedForm.is_transaction_done
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

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Phase</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <select
                  v-model="combinedForm.status_id"
                  class="mt-2 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  <option
                    v-for="n in allInvoiceStatuses"
                    :key="n.id"
                    :value="n.id">
                    {{ n.name }}
                  </option>
                </select>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Active</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <SwitchGroup
                  as="div"
                  class="flex m-1 items-center">
                  <Switch
                    v-model="combinedForm.is_active"
                    :class="[
                      combinedForm.is_active ? 'bg-indigo-600' : 'bg-gray-200',
                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                    ]">
                    <span
                      :class="[
                        combinedForm.is_active ? 'translate-x-5' : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                      ]"
                      aria-hidden="true" />
                  </Switch>
                </SwitchGroup>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Invoiced</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <SwitchGroup
                  as="div"
                  class="flex m-1 items-center">
                  <Switch
                    v-model="combinedForm.is_invoiced"
                    :class="[
                      combinedForm.is_invoiced ? 'bg-indigo-600' : 'bg-gray-200',
                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                    ]">
                    <span
                      :class="[
                        combinedForm.is_invoiced ? 'translate-x-5' : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                      ]"
                      aria-hidden="true" />
                  </Switch>
                </SwitchGroup>
              </div>
            </dd>
          </div>

          <div class="flex justify-between gap-x-4 py-1">
            <dt class="text-gray-500">Paid</dt>
            <dd class="flex items-start gap-x-2">
              <div>
                <SwitchGroup
                  as="div"
                  class="flex m-1 items-center">
                  <Switch
                    v-model="combinedForm.is_invoice_paid"
                    :class="[
                      combinedForm.is_invoice_paid ? 'bg-indigo-600' : 'bg-gray-200',
                      'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                    ]">
                    <span
                      :class="[
                        combinedForm.is_invoice_paid ? 'translate-x-5' : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                      ]"
                      aria-hidden="true" />
                  </Switch>
                </SwitchGroup>
              </div>
            </dd>
          </div>
        </dl>
      </li>
    </ul>
  </div>
</template>

<script setup>
  import { Switch, SwitchGroup } from '@headlessui/vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import AreaInput from '@/Components/AreaInput.vue';

  const props = defineProps({
    combinedForm: {
      type: Object,
      required: true,
    },
    selectedTransaction: {
      type: Object,
      required: true,
    },
    allInvoiceStatuses: {
      type: Array,
      required: true,
    },
    paymentTerms: {
      type: Object,
      default: null,
    },
    formatInvoiceDate: {
      type: Function,
      required: true,
    },
    formatInvoicePayByDay: {
      type: Function,
      required: true,
    },
    formatInvoicePdDay: {
      type: Function,
      required: true,
    },
  });
</script>
