<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import TradeSlideOver from  "@/Components/UI/TradeSlideOver.vue";

import Icon from "@/Components/Icon.vue";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { LinkIcon, PlusIcon, QuestionMarkCircleIcon } from '@heroicons/vue/20/solid'



const open = ref(true)

const props = defineProps({
    transactions: Object,
    filters: Object,
});
const permissions = computed(() => usePage().props.permissions)

let NiceTDate = (date) => {

    const _date = new Date(date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const dayString = (_date.toLocaleString('en', {weekday: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
};

const filterForm = useForm({
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 10,
    supplier_name:props.filters.supplier_name ?? null,
    customer_name:props.filters.customer_name ?? null,
    transporter_name:props.filters.transporter_name ?? null,
    product_name:props.filters.product_name ?? null,

})

let curClient = ref(null);

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
    () => filterForm.supplier_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.customer_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.transporter_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.product_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.show,
    (exampleField, prevExampleField) => {
        filter();
    }
);

const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;

    filter();
}

const edit = (id) => {
    router.get('transport_transaction/'+id);
}

const viewTradeSlideOver = ref(false);

const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
};

const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaction Data</h2>

                        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="col-span-4 flex">
                                <input type="search" v-model.number="filterForm.supplier_name" aria-label="Search"
                                       placeholder="Search supplier name..."
                                       class="block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

                                <input type="search" v-model.number="filterForm.customer_name" aria-label="Search"
                                       placeholder="Search customer name..."
                                       class="block ml-2 w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

                                <input type="search" v-model.number="filterForm.transporter_name" aria-label="Search"
                                       placeholder="Search transporter name..."
                                       class="block ml-2 w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

                                <input type="search" v-model.number="filterForm.product_name" aria-label="Search"
                                       placeholder="Search product name..."
                                       class="block ml-2 w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

                            </div>

                            <div class="col-span-4 mb-3">

                                <select v-model="filterForm.show"
                                        class="input-filter-l block w-1/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                    <option :value=5>5</option>
                                    <option :value=10>10</option>
                                    <option :value=25>25</option>
                                    <option :value=100>100</option>

                                </select>
                                <secondary-button @click="filter" class="mt-3">Search</secondary-button>
                                <secondary-button @click="clear" class="mt-3 ml-1">Clear</secondary-button>
                                <secondary-button @click="showTradeSlideOver"  class="mt-3 ml-1">Add (+)</secondary-button>
                            </div>

                        </div>

                        <div>
                            <trade-slide-over :show="viewTradeSlideOver" @close="closeTradeSlideOver"  />
                        </div>

                        <div>

                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-indigo-400 text-right">
                                    <tr class="font-bold ">
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                             DATE
                                        </th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                            Supplier
                                        </th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Customer</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Transporter</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Product</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">


<!--                                    'id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                    'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'-->

                                    <tr @click="edit(transaction.id)"  v-for="(transaction, index) in transactions.data"
                                        :key="transactions.id" class="hover:bg-gray-100 text-sm focus-within:bg-gray-100 ">

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ NiceTDate( transaction.transport_date_earliest)}}
                                        </td>

                                        <td class="py-4 px-6 ">
                                            {{transaction.supplier.last_legal_name}}
                                        </td>
                                        <td class="py-4 px-6 ">
                                            {{transaction.customer.last_legal_name}}
                                        </td>
                                        <td class="py-4 px-6 ">
                                            {{transaction.transporter.last_legal_name}}
                                        </td>

                                        <td class="py-4 px-6 ">
                                            {{transaction.product.name}}
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
