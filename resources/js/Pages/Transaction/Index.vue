<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";


const props = defineProps({
    transactions: Object,
    filters: Object,
});
const permissions = computed(() => usePage().props.permissions)


const filterForm = useForm({
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 10,

})

let curClient = ref(null);
let showModel = ref(false);

let tableStats = ref("Showing page " + props.transactions.current_page + "  of " + props.transactions.total + " entries.");

let filter = debounce(() => {

    filterForm.get(
        route('transport_transaction.index'),
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
},150);

let sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
}

watch(
    () => filterForm.searchName,
    (exampleField, prevExampleField) => {
        filter();
    }
)

watch(
    () => filterForm.show,
    (exampleField, prevExampleField) => {
        filter();
    }
)

const clear = () => {
    filterForm.searchName = null
    filterForm.isActive = null
    filter()
}

const edit = (id) => {
    router.get('transport_transaction/'+id);
}

const completeFunction = (val) => {
    showModel.value = false;
};


</script>

<template>
    <AppLayout title="Transactions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transactions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-4 lg:px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trade/Transaction Data</h2>

                        <div class="mb-4 mt-5">
                            <div class="mt-2">

                                <select v-model="filterForm.isActive"
                                        class="input-filter-l block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                    <option :value="null">All</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>

                                </select>

                            </div>
                            <div class="mt-2">

                                <select v-model="filterForm.show"
                                        class="input-filter-l block w-1/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                    <option :value=5>5</option>
                                    <option :value=10>10</option>
                                    <option :value=25>25</option>
                                    <option :value=100>100</option>

                                </select>

                            </div>
                            <secondary-button @click="filter" class="mt-3">Search</secondary-button>
                            <secondary-button class="mt-3 ml-1">Clear</secondary-button>
                            <secondary-button  class="mt-3 ml-1">Add (+)</secondary-button>
                        </div>
                        <div>

                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-indigo-400 text-right">
                                    <tr class="font-bold ">
                                        <th scope="col" class="w-2/12 ml-5 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                             DATE
                                        </th>
                                        <th scope="col" class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                            Supplier
                                        </th>
                                        <th scope="col" class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Customer</th>
                                        <th scope="col" class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Transporter</th>
                                        <th scope="col" class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Done</th>
                                        <th scope="col" class="w-2/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">


<!--                                    'id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                    'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'-->

                                    <tr @click="edit(transaction.id)"  v-for="(transaction, index) in transactions.data"
                                        :key="transactions.id" class="hover:bg-gray-100 text-sm focus-within:bg-gray-100 ">

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{transaction.transport_date_earliest}}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{transaction.supplier.last_legal_name}}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{transaction.customer.last_legal_name}}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{transaction.transporter.last_legal_name}}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">

                                            <div v-if="transaction.is_transaction_done ===1">

                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M4.5 12.75l6 6 9-13.5"/>
                                                </svg>

                                            </div>
                                            <div v-else>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </div>

                                        </td>


                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',transaction.id)" >View</Link>
                                        </td>



                                    </tr>

                                    </tbody>
                                </table>

                            <div class="ml-3 mt-2">
                                {{ tableStats }}
                            </div>
                        </div>
                        <div v-if="transactions.data.length" class="w-full flex justify-center mt-5 mb-4">

                            <PaginationModified :links="transactions.links"/>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
