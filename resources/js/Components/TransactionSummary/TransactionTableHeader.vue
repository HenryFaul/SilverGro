<script setup>
  import { computed } from 'vue';

  const props = defineProps({
    showDetails: {
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

  const emit = defineEmits(['sort']);

  const headerClass =
    'sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-1 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell';

  const firstHeaderClass =
    'sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-1 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8';

  const handleSort = (field) => {
    emit('sort', field);
  };
</script>

<template>
  <thead>
    <tr>
      <th
        :class="firstHeaderClass"
        scope="col">
        ID
      </th>
      <th
        :class="headerClass"
        scope="col">
        TYPE
      </th>
      <th
        :class="headerClass"
        scope="col">
        DATE
      </th>
      <th
        :class="headerClass"
        scope="col">
        SUPPLIER
      </th>
      <th
        :class="headerClass"
        scope="col">
        CUSTOMER
      </th>
      <th
        :class="headerClass"
        scope="col">
        TRANSPORTER
      </th>
      <th
        :class="headerClass"
        scope="col">
        PRODUCT
      </th>

      <!-- Detail Columns (conditional) -->
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        D/T
      </th>
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        P/O
      </th>
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        S/O
      </th>
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        T/O
      </th>
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        WB
      </th>
      <th
        v-if="showDetails"
        scope="col"
        :class="headerClass">
        INV
      </th>

      <th
        :class="headerClass"
        scope="col">
        NOTES
      </th>
    </tr>
  </thead>
</template>
