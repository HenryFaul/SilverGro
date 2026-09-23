import { watch } from 'vue';

/**
 * Clears a delivery address when the user picks a different customer, so an
 * address belonging to the previous customer is not carried across.
 *
 * The watcher cannot tell that apart from the screen loading a different trade
 * from the list: both change combinedForm.customer_id. It used to clear in both
 * cases, so every trade opened from the list had the delivery address that
 * updateSelectValues had just loaded for it wiped straight back out of the form.
 * That was the "the address I selected has disappeared when I come back to the
 * trade" report - the address was still stored, the screen was just discarding
 * it. (Until the update path stopped defaulting a missing address to ID 1, the
 * next save then wiped it from the database too.)
 *
 * So compare against the trade's own saved customer. Loading a trade puts that
 * trade's customer on screen, and the address that goes with it is left alone.
 * Only a customer that differs from what the trade has saved means the user
 * chose someone else, and only then is the old address cleared. The supplier
 * side never had this watcher, which is why only the customer tab was affected.
 *
 * @param {Object} combinedForm
 * @param {Function} selectedTransaction getter for the trade currently on screen
 */
export function useAddressClearing(combinedForm, selectedTransaction) {
  const pairs = [
    ['customer_id', 'delivery_address_id'],
    ['customer_id_2', 'delivery_address_id_2'],
    ['customer_id_3', 'delivery_address_id_3'],
    ['customer_id_4', 'delivery_address_id_4'],
    ['customer_id_5', 'delivery_address_id_5'],
  ];

  for (const [customerField, addressField] of pairs) {
    watch(
      () => combinedForm[customerField],
      (newCustomer, oldCustomer) => {
        // Initial load, or the same customer swapped for a fresh copy of itself.
        if (!oldCustomer || newCustomer?.id === oldCustomer?.id) return;

        // A different trade was loaded: this is its own customer, and its own
        // address has just been set alongside it.
        const savedCustomerId = selectedTransaction?.()?.[customerField] ?? null;
        if ((newCustomer?.id ?? null) === savedCustomerId) return;

        combinedForm[addressField] = null;
      }
    );
  }
}
