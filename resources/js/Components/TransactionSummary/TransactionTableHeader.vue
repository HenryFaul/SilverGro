<script setup>
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
    'sticky top-0 z-10 hidden border-b border-gray-300 bg-gray-200 px-3 py-1 text-left text-sm font-semibold text-gray-900 sm:table-cell';

  const firstHeaderClass =
    'sticky top-0 z-10 border-b border-gray-300 bg-gray-200 py-1 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8';

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
        STATUS
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
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        C_ORDER
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
        REG#
      </th>
      <th
        :class="headerClass"
        scope="col">
        PRODUCT
      </th>

      <!-- Detail Columns (conditional) -->
      <th v-if="showDetails" :class="headerClass" scope="col">D/T</th>
      <th v-if="showDetails" :class="headerClass" scope="col">P/O</th>
      <th v-if="showDetails" :class="headerClass" scope="col">S/O</th>
      <th v-if="showDetails" :class="headerClass" scope="col">T/O</th>
      <th v-if="showDetails" :class="headerClass" scope="col">WB</th>
      <th v-if="showDetails" :class="headerClass" scope="col">INV</th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        Units
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        Cost/Ton
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        TC/Ton
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        Price/Ton
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        GP/Ton
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        GP%
      </th>
      <th
        v-if="showDetails"
        :class="headerClass"
        scope="col">
        Process Notes
      </th>
    </tr>
  </thead>
</template>
