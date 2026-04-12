import { computed, ref, unref } from 'vue';

/**
 * Generic composable for filtered dropdown/combobox lists in Transaction Summary
 * Handles search query and filtered results for any collection
 *
 * @param {Object} items - The items to filter (e.g., props.all_customers)
 * @param {String} searchField - The field to search on (e.g., 'last_legal_name')
 * @returns {Object} - { query, filteredItems }
 */
export function useFilteredList(items, searchField) {
  const query = ref('');

  const filteredItems = computed(() => {
    const itemsArray = unref(items);

    if (query.value === '') {
      return itemsArray;
    }

    return itemsArray.filter((item) => {
      const fieldValue = item[searchField];
      if (!fieldValue) return false;

      return fieldValue.toLowerCase().includes(query.value.toLowerCase());
    });
  });

  return {
    query,
    filteredItems,
  };
}

/**
 * Specialized version for items with multiple searchable fields (e.g., first_name + last_name)
 */
export function useFilteredListMultiField(items, searchFields) {
  const query = ref('');

  const filteredItems = computed(() => {
    // Get the actual value if items is a computed ref
    const itemsArray = unref(items);

    if (query.value === '') {
      return itemsArray;
    }

    return itemsArray.filter((item) => {
      return searchFields.some((field) => {
        const fieldValue = item[field];
        if (!fieldValue) return false;

        return fieldValue.toLowerCase().includes(query.value.toLowerCase());
      });
    });
  });

  return {
    query,
    filteredItems,
  };
}
