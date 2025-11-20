<script setup>
import TransactionTableHeader from './TransactionTableHeader.vue';
import TransactionTableRow from './TransactionTableRow.vue';
import PaginationModified from '@/Components/UI/PaginationModified.vue';

const props = defineProps({
    transactions: {
      type: Object,
      required: true,
    },
    filteredTransactions: {
      type: Array,
      required: true,
    },
    selectedTransaction: {
      type: Object,
      default: () => ({}),
    },
    showDetails: {
      type: Boolean,
      default: false,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    sortField: {
      type: String,
      default: null,
    },
    sortDirection: {
      type: String,
      default: 'asc',
    },
  });

  const emit = defineEmits(['sort', 'select-transaction']);

  const handleSort = (field) => {
    emit('sort', field);
  };

  const handleSelectTransaction = (id) => {
    emit('select-transaction', id);
  };

  const isTransactionSelected = (transaction) => {
    return transaction.id === props.selectedTransaction?.id;
  };
</script>

<template>
  <div class="bg-white overflow-x-auto m-1 p-1 shadow-xl sm:rounded-lg">
    <!-- Loading State -->
    <div
      v-if="isLoading"
      class="text-center py-8">
      <div
        class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      <p class="mt-2 text-sm text-gray-500">Loading transactions...</p>
    </div>

    <!-- Table -->
    <div v-else>
      <div class="overflow-y-auto h-64">
        <table class="min-w-full border-separate border-spacing-0">
          <transaction-table-header
            :show-details="showDetails"
            :sort-direction="sortDirection"
            :sort-field="sortField"
            @sort="handleSort" />

          <tbody>
            <transaction-table-row
              v-for="transaction in filteredTransactions"
              :key="transaction.id"
              :is-selected="isTransactionSelected(transaction)"
              :show-details="showDetails"
              :transaction="transaction"
              @select="handleSelectTransaction" />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="transactions.data && transactions.data.length"
        class="w-full flex justify-center mt-5 mb-4">
        <pagination-modified :links="transactions.links" />
      </div>

      <!-- Empty State -->
      <div
        v-if="!filteredTransactions || filteredTransactions.length === 0"
        class="text-center py-8">
        <p class="text-sm text-gray-500">No transactions found</p>
      </div>
    </div>
  </div>
</template>
