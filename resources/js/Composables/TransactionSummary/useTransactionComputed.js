import { computed } from 'vue';

/**
 * Computed values for Transaction Summary
 * Manages derived/calculated values that don't modify state
 * Phase 7 of TransactionSummary refactor
 */
export function useTransactionComputed(props, combinedForm) {
  // Filter linked contracts by type (MQ)
  const filteredLinkedContractsMq = computed(() =>
    props.linked_trans.filter((trans_link) => {
      return trans_link.trans_link_type_id === 3;
    })
  );

  // Filter linked contracts by type (PC - Purchase Contract)
  const filteredLinkedContractsPc = computed(() =>
    props.linked_trans_pc.filter((trans_link) => {
      return trans_link.trans_link_type_id === 3;
    })
  );

  // Filter linked contracts by type (SC - Sales Contract)
  const filteredLinkedContractsSc = computed(() =>
    props.linked_trans_sc.filter((trans_link) => {
      return trans_link.trans_link_type_id === 4;
    })
  );

  // Sum gross profit for linked MQ contracts
  const sumLinkedContractsMq = computed(() => {
    let sum = 0;
    if (props.linked_trans != null) {
      for (let linked of props.linked_trans) {
        if (linked.trans_link_type_id === 3) {
          if (linked.transport_transaction != null) {
            sum += linked.transport_transaction.transport_finance.gross_profit;
          }
        }
      }
    }
    return sum;
  });

  // Sum gross profit for linked PC contracts
  const sumLinkedContractsPc = computed(() => {
    let sum = 0;
    if (props.linked_trans != null) {
      for (let linked of props.linked_trans) {
        if (linked.trans_link_type_id === 3) {
          if (linked.transport_transaction_pc != null) {
            sum += linked.transport_transaction_pc.transport_finance.gross_profit;
          }
        }
      }
    }
    return sum;
  });

  // Check if form has validation errors
  const emptyErrorsTrans = computed(
    () =>
      Object.keys(combinedForm.errors).length === 0 &&
      combinedForm.errors.constructor === Object
  );

  // Get payment terms for the selected customer
  const paymentTerms = computed(() =>
    props.all_terms_of_payments.find(
      (element) => element.id === combinedForm.customer_id.terms_of_payment_id
    )
  );

  // Get display title (MQ number or ID)
  const getTitle = computed(() => {
    if (props.selected_transaction.a_mq != null) {
      return 'MQ' + props.selected_transaction.a_mq;
    } else {
      return 'ID:' + props.selected_transaction.id;
    }
  });

  return {
    // Linked contracts filters
    filteredLinkedContractsMq,
    filteredLinkedContractsPc,
    filteredLinkedContractsSc,

    // Linked contracts sums
    sumLinkedContractsMq,
    sumLinkedContractsPc,

    // Form validation
    emptyErrorsTrans,

    // Customer payment terms
    paymentTerms,

    // Display title
    getTitle,
  };
}
