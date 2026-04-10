<template>
  <div>
    <div class="font-bold text-indigo-500">Staff allocation</div>
    <div class="">
      <form class="">
        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-2 sm:grid-cols-6">
          <div class="col-span-4">
            <SecondaryButton
              class=""
              @click="$emit('view-assigned-new-comm')">
              Add Comm User (+)
            </SecondaryButton>
            <div v-if="viewAssignedCommNewModal">
              <assigned-comm-modal
                :all_staff="allStaff"
                :assigned_user_comm="null"
                :show="viewAssignedCommNewModal"
                :transport_finance_id="selectedTransaction.transport_finance.id"
                :transport_trans_id="selectedTransaction.id"
                @close="$emit('close-assigned-new-comm')" />
            </div>
          </div>

          <div class="col-span-4">
            <div
              v-for="(user_comm, index) in selectedTransaction.assigned_user_comm"
              :key="user_comm.id">
              <div class="ml-5 border border-gray-200 p-6 mb-5 rounded-lg shadow-md bg-white">
                <div class="px-2 pt-3 pb-3 sm:px-0">
                  <h3 class="text-base font-semibold leading-2 text-indigo-400">
                    User Comm {{ index + 1 }}
                  </h3>
                  <h3 class="text-base font-semibold leading-2 text-sm text-gray-400">
                    Reference {{ user_comm.id }}
                  </h3>
                </div>
                <div class="mt-2 border-t border-gray-100">
                  <dl class="divide-y divide-gray-100">
                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                      <dt class="text-sm font-medium leading-6 text-gray-900">
                        Supplier User
                      </dt>
                      <dd
                        class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ user_comm.assigned_user_supplier.first_name }}
                        {{ user_comm.assigned_user_supplier.last_legal_name }}
                      </dd>
                    </div>

                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                      <dt class="text-sm font-medium leading-6 text-gray-900">
                        Customer User
                      </dt>
                      <dd
                        class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ user_comm.assigned_user_customer.first_name }}
                        {{ user_comm.assigned_user_customer.last_legal_name }}
                      </dd>
                    </div>

                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                      <dt class="text-sm font-medium leading-6 text-gray-900">
                        Supplier Commission
                      </dt>
                      <dd
                        class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ NiceNumber(user_comm.supplier_comm) }}
                      </dd>
                    </div>

                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                      <dt class="text-sm font-medium leading-6 text-gray-900">
                        Customer Commission
                      </dt>
                      <dd
                        class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ NiceNumber(user_comm.customer_comm) }}
                      </dd>
                    </div>
                  </dl>
                </div>

                <div class="mt-3 col-span-4">
                  <div v-if="viewAssignedCommModal">
                    <assigned-comm-modal
                      :all_staff="allStaff"
                      :assigned_user_comm="currentAssignedComm"
                      :show="viewAssignedCommModal"
                      :transport_finance_id="selectedTransaction.transport_finance.id"
                      :transport_trans_id="selectedTransaction.id"
                      @close="$emit('close-assigned-comm')" />
                  </div>

                  <SecondaryButton
                    class="mt-4"
                    @click="$emit('delete-assigned-comm', user_comm.id)">
                    Delete
                  </SecondaryButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import AssignedCommModal from '@/Components/UI/AssignedCommModal.vue';
  import { formatNiceNumber } from '@/Composables/useNumberFormatters.js';

  const NiceNumber = formatNiceNumber;

  const props = defineProps({
    selectedTransaction: {
      type: Object,
      required: true,
    },
    allStaff: {
      type: Array,
      default: () => [],
    },
    viewAssignedCommNewModal: {
      type: Boolean,
      default: false,
    },
    viewAssignedCommModal: {
      type: Boolean,
      default: false,
    },
    currentAssignedComm: {
      type: Object,
      default: null,
    },
  });

  defineEmits([
    'view-assigned-new-comm',
    'close-assigned-new-comm',
    'close-assigned-comm',
    'delete-assigned-comm',
  ]);
</script>
