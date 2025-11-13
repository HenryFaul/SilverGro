<template>
  <div>
    <div class="font-bold text-indigo-500">Model Activity</div>
    <table class="min-w-full divide-y divide-gray-300">
      <thead>
        <tr>
          <th
            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
            scope="col">
            Id
          </th>
          <th
            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
            scope="col">
            Date
          </th>
          <th
            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
            scope="col">
            Event
          </th>
          <th
            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
            scope="col">
            Subject ID
          </th>
          <th
            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
            scope="col">
            Causer Type
          </th>
          <th
            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
            scope="col">
            Causer ID
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white">
        <tr
          v-for="activity in modelActivity"
          :key="activity.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td
            class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
            {{ activity.id }}
          </td>

          <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
            {{ activity.created_at }}
          </td>

          <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
            {{ activity.event }}
          </td>
          <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
            {{ activity.subject_id }}
          </td>

          <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
            {{ activity.causer_type }}
          </td>

          <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">
            {{ activity.causer_id }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
  const props = defineProps({
    modelActivity: {
      type: Array,
      default: () => [],
    },
  });
</script>
