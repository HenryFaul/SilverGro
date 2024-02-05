<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import CustomerSlideOver from  "@/Components/UI/CustomerParentSlideOver.vue";



const props = defineProps({
    customer_parents: Object,
    filters: Object,
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
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 10,

})

let curClient = ref(null);
let showModel = ref(false);

let tableStats = ref("Showing page " + props.customer_parents.current_page + "  of " + props.customer_parents.total + " entries.");

let filter = debounce(() => {

    filterForm.get(
        route('customer_parent.index'),
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
    router.get('customer_parent/'+id);
}

const completeFunction = (val) => {
    showModel.value = false;
};


</script>

<template>
    <AppLayout title="Customer Parents">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Parent
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customer Parent Data</h2>

                        <div class="mb-4 mt-5">
                            <input type="search" v-model.number="filterForm.searchName" aria-label="Search"
                                   placeholder="Search name or reg..."
                                   class="block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>

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
                            <secondary-button @click="clear" class="mt-3 ml-1">Clear</secondary-button>
                            <secondary-button @click="showCustomerSlideOver"  class="mt-3 ml-1">Add (+)</secondary-button>
                        </div>
                        <div>

                            <div>
                                <customer-slide-over :show="viewCustomerSlideOver" @close="closeCustomerSlideOver" />
                            </div>

                            <div class="bg-white rounded-md shadow overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                    <thead class="bg-indigo-400">
                                    <tr class="text-left font-bold">
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">

                                            <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('first_name')">First

                                            <svg v-if="filterForm.field === 'first_name' && filterForm.direction === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                            </svg>

                                            <svg v-if="filterForm.field === 'first_name' && filterForm.direction === 'desc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                            </svg>
                                            </span>

                                        </th>
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">

                                             <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('last_name')">Last/Legal

                                            <icon name="tick-asc" v-if="filterForm.field === 'last_name' && filterForm.direction === 'asc'" class="mr-2 w-6 h-6" />

                                            <icon name="tick-desc" v-if="filterForm.field === 'last_name' && filterForm.direction === 'desc'" class="mr-2 w-6 h-6" />


                                            </span>


                                        </th>
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Reg no</th>
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Active</th>
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Rating</th>
                                        <th scope="col" class="w-1/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">



                                    <tr @click="edit(customer.id)"  v-for="(customer, index) in customer_parents.data"
                                        :key="customer.id" class="hover:bg-gray-100 focus-within:bg-gray-100 ">


                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ customer.first_name }}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ customer.last_legal_name }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ customer.id_reg_no }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">

                                            <div v-if="customer.is_active ===1">

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
                                            {{ customer.customer_rating.value }}
                                        </td>


                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('customer_parent.show',customer.id)" >View</Link>
                                        </td>



                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="ml-3 mt-2">
                                {{ tableStats }}
                            </div>
                        </div>
                        <div v-if="customer_parents.data.length" class="w-full flex justify-center mt-5 mb-4">

                            <PaginationModified :links="customer_parents.links"/>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
