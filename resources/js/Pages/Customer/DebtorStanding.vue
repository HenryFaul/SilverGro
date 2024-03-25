<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, inject, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage, Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import CustomerSlideOver from "@/Components/UI/CustomerSlideOver.vue";
import {CalculatorIcon, FlagIcon} from '@heroicons/vue/20/solid';

const swal = inject('$swal');
import BaseTooltip from "@/Components/UI/BaseTooltip.vue";


const props = defineProps({
    debtors_standings: Object,
    filters: Object,
    max_date: Date,
    invoices: Object,
    selected_client_id: Number,
    selected_client: Object

});

const viewCustomerSlideOver = ref(false);

const showCustomerSlideOver = () => {
    viewCustomerSlideOver.value = true;
};

const closeCustomerSlideOver = () => {
    viewCustomerSlideOver.value = false;
};


const permissions = computed(() => usePage().props.permissions)


const filterForm = useForm({
    searchName: props.filters.searchName ?? null,
    isActive: props.filters.isActive ?? true,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 5,
    hasBalance: props.filters.isActive ?? true,
    selected_client_id: props.selected_client_id ?? null,

})

let curClient = ref(null);
let showModel = ref(false);

let isLoading = ref(false);

/*
let tableStats = ref("Showing page " + props.debtors_standings.current_page + "  of " + props.debtors_standings.total + " entries.");
*/

let filter = debounce(() => {

    filterForm.get(
        route('debtor.index'),
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
}, 150);

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
    () => filterForm.hasBalance,
    (exampleField, prevExampleField) => {
        filter();
    }
)

/*
watch(
    () => filterForm.isActive,
    (exampleField, prevExampleField) => {
        filter();
    }
)
*/

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
    router.get('customer/' + id);
}

const completeFunction = (val) => {
    showModel.value = false;
};

let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
};


const getComponentProps = async () => {

    isLoading.value = true;

    await axios.get(route('debtor.calculating'),).then((res) => {

    }).catch((r) => {
        console.log(r);
    }).finally(() => {
        swal('Debtors re-calculated.');
        isLoading.value = false;
        filter();
        //location.reload();
    });

};

let updateSelectedCustomer = async (_id) => {
    filterForm.selected_client_id = _id;
    filter();
};


const totalOutstanding = (invoices) => {
    let total = 0.0;

    invoices.forEach(function (arrayItem) {
        let outstanding = arrayItem.transport_invoice_details.outstanding;
        total += outstanding;
    });

    return total;
}

const totalOverdue = (invoices) => {
    let total = 0.0;

    invoices.forEach(function (arrayItem) {
        let overdue = arrayItem.transport_invoice_details.overdue;
        total += overdue;
    });

    return total;
}


</script>

<template>
    <AppLayout title="Debtor Standing">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Debtor Standing
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

                            <div class="flex col-span-6">
                                <input type="search" v-model.number="filterForm.searchName" aria-label="Search"
                                       placeholder="Search name or reg..."
                                       class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

                                <!--
                                                                <select v-model="filterForm.isActive"
                                                                        class="input-filter-l block w-40  ml-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                                                    <option :value="null">All</option>
                                                                    <option :value=true>Active Only</option>


                                                                </select>
                                -->

                                <select v-model="filterForm.hasBalance"
                                        class="input-filter-l block w-40 ml-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option :value="null">All</option>
                                    <option :value=true>Has Balance</option>

                                </select>

                                <select v-model="filterForm.show"
                                        class="input-filter-l block w-32 ml-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                    <option selected :value=5>5</option>
                                    <option :value=10>10</option>
                                    <option :value=25>25</option>
                                    <option :value=100>100</option>

                                </select>


                            </div>


                            <div class="flex col-span-6">
                                <secondary-button @click="getComponentProps" class="ml-1">

                                    update
                                    <calculator-icon :class="[isLoading? 'animate-spin': '','w-5 h-5'] "/>

                                </secondary-button>
                                <secondary-button @click="filter" class="ml-1">Search</secondary-button>
                                <secondary-button @click="clear" class="ml-1">Clear</secondary-button>
                                <div class="ml-2 mt-1">
                                    <span>Last Updated: </span> <span>{{ max_date }}</span>
                                </div>


                            </div>

                        </div>

                        <div>

                            <div>


                                <div class="bg-white mt-6 rounded-md shadow overflow-x-auto">

                                    <table class="min-w-full border-separate border-spacing-0">
                                        <thead>
                                        <tr>

                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                                scope="col">NAME
                                            </th>
                                            <th class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell"
                                                scope="col">RATING
                                            </th>
                                            <th class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter lg:table-cell"
                                                scope="col">STATUS
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">OUTSTANDING
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">OVERDUE
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">SOFT LIMIT
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">HARD LIMIT
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">BASIS
                                            </th>
                                            <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                                scope="col">TERMS
                                            </th>


                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr @click="updateSelectedCustomer(debtor.customer_id)"
                                            v-for="(debtor, index) in debtors_standings.data"
                                            :key="debtor.id"
                                            :class="'hover:bg-gray-100 text-sm focus-within:bg-gray-100'"
                                        >
                                            <td class="border-b border-gray-200 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8'">
                                                {{ debtor.customer.last_legal_name }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ debtor.customer.customer_rating.value }}
                                            </td>

                                            <td class="border-b border-gray-200'">

                                                <base-tooltip
                                                    v-if="debtor.customer.credit_limit > debtor.total_outstanding"
                                                    content="All Payments Up to Date">
                                                    <flag-icon class="h-5 w-5 fill-green-500"/>
                                                </base-tooltip>

                                                <base-tooltip
                                                    v-else-if="debtor.customer.credit_limit_hard < debtor.total_outstanding && debtor.customer.credit_limit < debtor.total_overdue"
                                                    content="Hard Credit Limit exceeded and some payments overdue">
                                                    <flag-icon class="h-5 w-5 fill-red-500"/>
                                                </base-tooltip>

                                                <base-tooltip
                                                    v-else-if=" debtor.customer.credit_limit < debtor.total_outstanding"
                                                    content="Payment outstanding exceeds Credit Limit">
                                                    <flag-icon class="h-5 w-5 fill-yellow-500"/>
                                                </base-tooltip>

                                                <base-tooltip
                                                    v-else-if="debtor.customer.credit_limit_hard < debtor.total_outstanding"
                                                    content="Hard Credit Limit exceeded">
                                                    <flag-icon class="h-5 w-5 fill-orange-500"/>
                                                </base-tooltip>

                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ NiceNumber(debtor.total_outstanding) }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ NiceNumber(debtor.total_overdue) }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ NiceNumber(debtor.customer.credit_limit) }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ NiceNumber(debtor.customer.credit_limit_hard) }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ debtor.customer.terms_of_payment_basis.value }}
                                            </td>

                                            <td class="border-b border-gray-200'">
                                                {{ debtor.customer.terms_of_payment.value }}
                                            </td>

                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="ml-3 mt-2">
                                    <!--
                                                                    {{ tableStats }}
                                    -->
                                </div>
                            </div>
                            <div v-if="debtors_standings.data.length && !isLoading"
                                 class="w-full flex justify-center mt-5 mb-4">
                                <PaginationModified :links="debtors_standings.links"/>
                            </div>

                        </div>

                    </div>


                </div>
                <div class="sticky bg-white m-2 p-2  shadow-xl sm:rounded-lg">

                    <div>
                        <div class="px-4 sm:px-6 lg:px-8">

                            <div v-if="selected_client_id">
                                <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                                    <div class="md:flex md:items-center md:justify-between">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900">Linked
                                            Invoices: {{ selected_client.last_legal_name }}</h3>
                                        <div class="mt-3 flex md:absolute md:right-0 md:top-3 md:mt-0">

                                        </div>
                                    </div>

                                </div>
                                <div class="m-2 p-2">
                                    <div class="mb-2">
                                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                            <table class="min-w-full divide-y divide-gray-300">
                                                <thead>
                                                <tr>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        ID
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        Invoice No
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        Invoice Date
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        Pay by Date
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        Outstanding
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        Overdue
                                                    </th>
                                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                        <span class="sr-only">Edit</span>
                                                    </th>

                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                <tr v-for="invoice in props.invoices" :key="invoice.id">
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                        {{ invoice.id }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ invoice.transport_invoice_details.invoice_no }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ invoice.transport_invoice_details.invoice_date }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ invoice.transport_invoice_details.invoice_pay_by_date }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ NiceNumber(invoice.transport_invoice_details.outstanding) }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ NiceNumber(invoice.transport_invoice_details.overdue) }}
                                                    </td>

                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="whitespace-nowrap px-3 py-4">TOTALS</td>
                                                    <td class="whitespace-nowrap px-3 py-4">
                                                        {{ NiceNumber(totalOutstanding(props.invoices)) }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4">
                                                        {{ NiceNumber(totalOverdue(props.invoices)) }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>

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
