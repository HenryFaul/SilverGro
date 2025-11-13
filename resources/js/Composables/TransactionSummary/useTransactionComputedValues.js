/**
 * useTransactionComputedValues
 *
 * Computed values for split load calculations
 * Extracts computed logic from Index.vue
 */

import { computed } from 'vue';

export function useTransactionComputedValues(combined_Form, props) {
  // Calculate units remaining to be allocated across split customers
  const no_units_to_allocate = computed(
    () =>
      combined_Form.no_units_outgoing -
      combined_Form.no_units_outgoing_2 -
      combined_Form.no_units_outgoing_3 -
      combined_Form.no_units_outgoing_4 -
      combined_Form.no_units_outgoing_5
  );

  // Calculate selling price remaining to be allocated
  const selling_price_to_allocate = computed(
    () =>
      props.selected_transaction.transport_finance.selling_price -
      combined_Form.selling_price_2 -
      combined_Form.selling_price_3 -
      combined_Form.selling_price_4 -
      combined_Form.selling_price_5
  );

  // Calculate transport cost remaining to be allocated
  const transport_cost_to_allocate = computed(
    () =>
      props.selected_transaction.transport_finance.transport_cost -
      combined_Form.transport_cost_2 -
      combined_Form.transport_cost_3 -
      combined_Form.transport_cost_4 -
      combined_Form.transport_cost_5
  );

  return {
    no_units_to_allocate,
    selling_price_to_allocate,
    transport_cost_to_allocate,
  };
}
