import { computed, ref } from 'vue';

/**
 * Composable for managing address filtering logic for collection and delivery addresses
 * Reduces repetitive address filter code across multiple customer/supplier fields
 */
export function useAddressFilters(combinedForm) {
  // Collection address (from supplier)
  const collectionAddressQuery = ref('');

  const filteredCollectionAddress = computed(() => {
    if (!combinedForm.supplier_id?.addressable) return [];

    return collectionAddressQuery.value === ''
      ? combinedForm.supplier_id.addressable
      : combinedForm.supplier_id.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(collectionAddressQuery.value.toLowerCase());
        });
  });

  // Delivery addresses (customer 1-5)
  const deliveryAddressQuery = ref('');
  const deliveryAddressQuery2 = ref('');
  const deliveryAddressQuery3 = ref('');
  const deliveryAddressQuery4 = ref('');
  const deliveryAddressQuery5 = ref('');

  const filteredDeliveryAddress = computed(() => {
    if (!combinedForm.customer_id?.addressable) return [];

    return deliveryAddressQuery.value === ''
      ? combinedForm.customer_id.addressable
      : combinedForm.customer_id.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery.value.toLowerCase());
        });
  });

  const filteredDeliveryAddress2 = computed(() => {
    if (!combinedForm.customer_id_2?.addressable) return [];

    return deliveryAddressQuery2.value === ''
      ? combinedForm.customer_id_2.addressable
      : combinedForm.customer_id_2.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery2.value.toLowerCase());
        });
  });

  const filteredDeliveryAddress3 = computed(() => {
    if (!combinedForm.customer_id_3?.addressable) return [];

    return deliveryAddressQuery3.value === ''
      ? combinedForm.customer_id_3.addressable
      : combinedForm.customer_id_3.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery3.value.toLowerCase());
        });
  });

  const filteredDeliveryAddress4 = computed(() => {
    if (!combinedForm.customer_id_4?.addressable) return [];

    return deliveryAddressQuery4.value === ''
      ? combinedForm.customer_id_4.addressable
      : combinedForm.customer_id_4.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery4.value.toLowerCase());
        });
  });

  const filteredDeliveryAddress5 = computed(() => {
    if (!combinedForm.customer_id_5?.addressable) return [];

    return deliveryAddressQuery5.value === ''
      ? combinedForm.customer_id_5.addressable
      : combinedForm.customer_id_5.addressable.filter((address) => {
          return address.line_1
            .toLowerCase()
            .includes(deliveryAddressQuery5.value.toLowerCase());
        });
  });

  return {
    // Collection address
    collectionAddressQuery,
    filteredCollectionAddress,

    // Delivery addresses
    deliveryAddressQuery,
    deliveryAddressQuery2,
    deliveryAddressQuery3,
    deliveryAddressQuery4,
    deliveryAddressQuery5,
    filteredDeliveryAddress,
    filteredDeliveryAddress2,
    filteredDeliveryAddress3,
    filteredDeliveryAddress4,
    filteredDeliveryAddress5,
  };
}
