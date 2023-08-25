<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'
const props = defineProps({
    month:Date,
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
    chart: Object

});

const stats = [
    { id: 1, name: 'Creators on the platform', value: '8,000+' },
    { id: 2, name: 'Flat platform fee', value: '3%' },
    { id: 3, name: 'Uptime guarantee', value: '99.9%' },
    { id: 4, name: 'Paid out to creators', value: '$70M' },
]

let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="bg-white py-24 sm:py-32">
                        <div class="mx-auto max-w-7xl px-6 lg:px-8">
                            <div class="mx-auto max-w-2xl lg:max-w-none">

                                <div >
                                    <div class="text-center">
                                        <h2 class="text-3xl font-bold tracking-tight text-indigo-500 sm:text-4xl">Stats overview for:</h2>
                                        <p class="mt-4 text-lg leading-8 text-gray-600">Current month: {{month}}</p>
                                    </div>
                                </div>


                                <div class="mt-4 text-center text-2xl">

                                    <span class="font-bold">
                                          GP Percentage:
                                    </span>

                                    <span class="text-green-500 font-bold">
                                        {{gp_perc}} %
                                    </span>

                                </div>

                                <div class="m-3 shadow-2xl">
                                    <dl class="mt-16 grid grid-cols-1 gap-0.5 overflow-hidden rounded-2xl text-center sm:grid-cols-2 lg:grid-cols-4">

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Planned tons in:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{planned_tons_in}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Planned tons out:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{planned_tons_out}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Weight uploaded:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{weight_uploaded}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Weight offloaded:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{weight_offloaded}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Cost price:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{NiceNumber(cost_price)}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Trans cost:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{NiceNumber(trans_cost)}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Selling price:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{NiceNumber(selling_price)}}</dd>
                                        </div>

                                        <div  class="flex flex-col bg-yellow-100 p-8">
                                            <dt class="text-sm font-semibold leading-6 text-gray-600">Gross profit:</dt>
                                            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">{{NiceNumber(gp)}}</dd>
                                        </div>



                                    </dl>

                                </div>

                                <div v-if="false" class="shadow-2xl m-10 p-10">

                                    <div >
                                        <apexchart :width="chart.width" :height="chart.height" :type="chart.type" :options="chart.options" :series="chart.series"></apexchart>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="m-3 mt-5 bg-white shadow-2xl rounded">



                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
