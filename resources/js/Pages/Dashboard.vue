<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    month: String,
    start_date: String,
    end_date: String,
    is_custom_period: Boolean,
    planned_tons_in: Number,
    planned_tons_out: Number,
    weight_uploaded: Number,
    weight_offloaded: Number,
    cost_price: Number,
    trans_cost: Number,
    other_costs: Number,
    selling_price: Number,
    gp: Number,
    gp_perc: Number,
    chart: Object,
  });

  const stats = [
    { id: 1, name: 'Creators on the platform', value: '8,000+' },
    { id: 2, name: 'Flat platform fee', value: '3%' },
    { id: 3, name: 'Uptime guarantee', value: '99.9%' },
    { id: 4, name: 'Paid out to creators', value: '$70M' },
  ];

  // Seeded from the server so the boxes show the period actually being
  // reported, whether that is a range somebody set or the default month.
  const startDate = ref(props.start_date ?? '');
  const endDate = ref(props.end_date ?? '');

  const applyPeriod = () => {
    router.get(
      route('dashboard'),
      { start_date: startDate.value || null, end_date: endDate.value || null },
      { preserveScroll: true }
    );
  };

  const resetPeriod = () => {
    router.get(route('dashboard'), { reset_period: 1 }, { preserveScroll: true });
  };

  const prettyDate = (d) =>
    d
      ? new Date(d).toLocaleDateString('en-ZA', { day: 'numeric', month: 'short', year: 'numeric' })
      : '';

  let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace('.', '.');
    return 'R ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
              <div class="mx-auto max-w-2xl lg:max-w-none">
                <div>
                  <div class="text-center">
                    <h2
                      class="text-3xl font-bold tracking-tight text-indigo-500 sm:text-4xl">
                      Current Stats overview:
                    </h2>

                    <p class="mt-2 text-sm text-gray-600">
                      {{ prettyDate(start_date) }} to {{ prettyDate(end_date) }}
                      <span
                        v-if="!is_custom_period"
                        class="text-gray-400">
                        (this month)
                      </span>
                    </p>
                  </div>

                  <div
                    class="mt-4 flex flex-wrap items-end justify-center gap-3 border-t border-gray-100 pt-4">
                    <div>
                      <label
                        class="block text-xs font-medium text-gray-600"
                        for="dash-start">
                        Start
                      </label>
                      <input
                        id="dash-start"
                        v-model="startDate"
                        class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        type="date"
                        @keyup.enter="applyPeriod" />
                    </div>

                    <div>
                      <label
                        class="block text-xs font-medium text-gray-600"
                        for="dash-end">
                        End
                      </label>
                      <input
                        id="dash-end"
                        v-model="endDate"
                        class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        type="date"
                        @keyup.enter="applyPeriod" />
                    </div>

                    <button
                      class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-600"
                      type="button"
                      @click="applyPeriod">
                      Apply
                    </button>

                    <button
                      v-if="is_custom_period"
                      class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                      type="button"
                      @click="resetPeriod">
                      Reset to this month
                    </button>
                  </div>
                </div>

                <div class="mt-4 text-center text-2xl">
                  <span class="font-bold">GP Percentage:</span>

                  <span class="text-green-500 font-bold">{{ gp_perc }} %</span>
                </div>

                <div class="m-3 shadow-2xl">
                  <dl
                    class="mt-16 grid grid-cols-1 gap-0.5 overflow-hidden rounded-2xl text-center sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Planned tons in:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ planned_tons_in }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Planned tons out:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ planned_tons_out }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Weight uploaded:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ weight_uploaded }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Weight offloaded:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ weight_offloaded }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Cost price:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ NiceNumber(cost_price) }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Trans cost:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ NiceNumber(trans_cost) }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Selling price:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ NiceNumber(selling_price) }}
                      </dd>
                    </div>

                    <div class="flex flex-col bg-yellow-100 p-8">
                      <dt class="text-sm font-semibold leading-6 text-gray-600">
                        Gross profit:
                      </dt>
                      <dd
                        class="order-first text-3xl font-semibold tracking-tight text-gray-900">
                        {{ NiceNumber(gp) }}
                      </dd>
                    </div>
                  </dl>
                </div>

                <div
                  v-if="false"
                  class="shadow-2xl m-10 p-10">
                  <div>
                    <apexchart
                      :height="chart.height"
                      :options="chart.options"
                      :series="chart.series"
                      :type="chart.type"
                      :width="chart.width" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
