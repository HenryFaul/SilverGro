/**
 * Transaction filtering logic for Transaction Summary
 * Extracted from Index.vue - Phase 2 of refactoring
 */

import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { isDayIncluded } from './useDateFormatters';

/**
 * Initialize and manage transaction filters
 */
export function useTransactionFilters(props, updateSelectValuesCallback) {
  // Day selection flags
  const mon = ref(true);
  const tue = ref(true);
  const wed = ref(true);
  const thu = ref(true);
  const fri = ref(true);
  const sat = ref(true);
  const sun = ref(true);

  // Loading state
  const isLoading = ref(false);

  // Filter form
  const filterForm = useForm({
    isActive: props.filters?.isActive ?? null,
    field: props.filters?.field ?? null,
    direction: props.filters?.direction ?? 'asc',
    show: props.filters?.show ?? 25,
    supplier_name: props.filters?.supplier_name ?? null,
    customer_name: props.filters?.customer_name ?? null,
    transporter_name: props.filters?.transporter_name ?? null,
    product_name: props.filters?.product_name ?? null,
    start_date: props.filters?.start_date ?? null,
    end_date: props.filters?.end_date ?? null,
    contract_type_id: props.filters?.contract_type_id ?? null,
    id: props.filters?.id ?? null,
    selected_trans_id: props.selected_transaction?.id ?? null,
    new_trade_added: false,
    old_id: null,
    a_mq: null,
    a_pc: null,
    a_sc: null,
  });

  // Debounced filter function
  const filter = debounce(() => {
    isLoading.value = true;
    filterForm.get(route('transaction_summary.index'), {
      preserveState: true,
      preserveScroll: true,
      // Partial reload: only the trade/list data changes when filtering or after a save.
      // Reference lists (all_customers, all_suppliers, all_drivers, etc.) are already loaded
      // and rarely change, so we exclude them to avoid re-running their (expensive, N+1) queries.
      only: [
        'transactions', 'selected_transaction', 'filters', 'start_date', 'end_date',
        'deal_ticket', 'transport_order', 'purchase_order', 'sales_order', 'rules_with_approvals',
        'linked_trans_sc', 'linked_trans_pc', 'linked_trans_other',
        'linked_trans_split', 'primary_linked_trans_split', 'split_totals', 'model_activity',
      ],
      onFinish: (visit) => {
        if (updateSelectValuesCallback) {
          updateSelectValuesCallback();
        }
        isLoading.value = false;
      },
    });
  }, 150);

  // Sort function
  const sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
  };

  // Watch all filter fields
  watch(
    () => filterForm.a_mq,
    () => filter()
  );
  watch(
    () => filterForm.a_pc,
    () => filter()
  );
  watch(
    () => filterForm.a_sc,
    () => filter()
  );
  watch(
    () => filterForm.supplier_name,
    () => filter()
  );
  watch(
    () => filterForm.customer_name,
    () => filter()
  );
  watch(
    () => filterForm.transporter_name,
    () => filter()
  );
  watch(
    () => filterForm.product_name,
    () => filter()
  );
  watch(
    () => filterForm.show,
    () => filter()
  );
  watch(
    () => filterForm.start_date,
    () => filter()
  );
  watch(
    () => filterForm.end_date,
    () => filter()
  );
  watch(
    () => filterForm.contract_type_id,
    () => filter()
  );
  watch(
    () => filterForm.id,
    () => filter()
  );
  watch(
    () => filterForm.old_id,
    () => filter()
  );

  // Filtered transactions based on day selection
  const filteredTrans = computed(() => {
    const allDaysSelected =
      mon.value &&
      tue.value &&
      wed.value &&
      thu.value &&
      fri.value &&
      sat.value &&
      sun.value;

    if (allDaysSelected) {
      return props.transactions?.data || [];
    }

    const dayFlags = {
      mon: mon.value,
      tue: tue.value,
      wed: wed.value,
      thu: thu.value,
      fri: fri.value,
      sat: sat.value,
      sun: sun.value,
    };

    return (props.transactions?.data || []).filter((trans) => {
      return isDayIncluded(trans.transport_date_earliest, dayFlags);
    });
  });

  // Update selected transaction
  const updateSelectedTrans = async (_id) => {
    filterForm.selected_trans_id = _id;
    filter();
  };

  // Clear all filters
  const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;
    filterForm.id = null;
    filterForm.old_id = null;
    filterForm.a_mq = null;
    filterForm.a_pc = null;
    filterForm.a_sc = null;

    mon.value = true;
    tue.value = true;
    wed.value = true;
    thu.value = true;
    fri.value = true;
    sat.value = true;
    sun.value = true;

    filter();
  };

  return {
    // State
    filterForm,
    isLoading,
    mon,
    tue,
    wed,
    thu,
    fri,
    sat,
    sun,

    // Computed
    filteredTrans,

    // Methods
    filter,
    sort,
    updateSelectedTrans,
    clear,
  };
}
