import { useFilteredList } from './useFilteredList.js';

/**
 * Product tab filters for Transaction Summary
 * Manages product, packaging, billing units, and product source filters
 */
export function useProductTab(props) {
  // Product filter
  const { query: productQuery, filteredItems: filteredProducts } = useFilteredList(
    props.all_products,
    'name'
  );

  // Product source filter
  const { query: productSourceQuery, filteredItems: filteredProductSources } =
    useFilteredList(props.all_product_sources, 'name');

  // Billing units incoming filter
  const {
    query: billingUnitsIncomingQuery,
    filteredItems: filteredBillingUnitsIncoming,
  } = useFilteredList(props.all_billing_units, 'name');

  // Package incoming filter
  const { query: packageIncomingQuery, filteredItems: filteredPackageIncoming } =
    useFilteredList(props.all_packaging, 'name');

  // Billing units outgoing filter
  const {
    query: billingUnitsOutgoingQuery,
    filteredItems: filteredBillingUnitsOutgoing,
  } = useFilteredList(props.all_billing_units, 'name');

  // Package outgoing filter
  const { query: packageOutgoingQuery, filteredItems: filteredPackageOutgoing } =
    useFilteredList(props.all_packaging, 'name');

  // Contract type filter
  const { query: contractTypeQuery, filteredItems: filteredContractTypes } =
    useFilteredList(props.contract_types, 'name');

  // NOTE: deliveryAddressQuery and collectionAddressQuery are NOT included here
  // because they filter form.addressable relationships (e.g., combined_Form.customer_id.addressable)
  // not top-level props. They remain local in Index.vue.

  return {
    productQuery,
    filteredProducts,
    productSourceQuery,
    filteredProductSources,
    billingUnitsIncomingQuery,
    filteredBillingUnitsIncoming,
    packageIncomingQuery,
    filteredPackageIncoming,
    billingUnitsOutgoingQuery,
    filteredBillingUnitsOutgoing,
    packageOutgoingQuery,
    filteredPackageOutgoing,
    contractTypeQuery,
    filteredContractTypes,
  };
}
