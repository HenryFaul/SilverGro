import { watch } from 'vue';

/**
 * Composable for managing address clearing when customers/suppliers change
 * Prevents invalid addresses from being retained when entity changes
 */
export function useAddressClearing(combinedForm) {
  // Watch for customer changes to clear delivery addresses
  watch(
    () => combinedForm.customer_id,
    (newCustomer, oldCustomer) => {
      // Only clear if customer actually changed (not initial load)
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combinedForm.delivery_address_id = null;
      }
    }
  );

  watch(
    () => combinedForm.customer_id_2,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combinedForm.delivery_address_id_2 = null;
      }
    }
  );

  watch(
    () => combinedForm.customer_id_3,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combinedForm.delivery_address_id_3 = null;
      }
    }
  );

  watch(
    () => combinedForm.customer_id_4,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combinedForm.delivery_address_id_4 = null;
      }
    }
  );

  watch(
    () => combinedForm.customer_id_5,
    (newCustomer, oldCustomer) => {
      if (oldCustomer && newCustomer?.id !== oldCustomer?.id) {
        combinedForm.delivery_address_id_5 = null;
      }
    }
  );
}
