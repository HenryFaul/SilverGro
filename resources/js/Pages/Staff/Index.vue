<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import StaffSlideOver from  "@/Components/UI/StaffSlideOver.vue";



const props = defineProps({
    staff:Object,
    filters: Object,
});
const permissions = computed(() => usePage().props.permissions)


const filterForm = useForm({
    searchName: props.filters.searchName ?? null,
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 10,

});

const viewStaffSlideOver = ref(false);

const showStaffSlideOver = () => {
    viewStaffSlideOver.value = true;
};

const closeStaffSlideOver = () => {
    viewStaffSlideOver.value = false;
};

let curClient = ref(null);
let showModel = ref(false);

let tableStats = ref("Showing page " + props.staff.current_page + "  of " + props.staff.total + " entries.");

let filter = debounce(() => {

    filterForm.get(
        route('staff.index'),
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
    router.get('staff/'+id);
}


</script>

<template>
    <AppLayout title="Staff">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Staff
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Data</h2>

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
                            <secondary-button @click="showStaffSlideOver"  class="mt-3 ml-1">Add (+)</secondary-button>
                        </div>
                        <div>

                            <div>
                                <staff-slide-over :show="viewStaffSlideOver" @close="closeStaffSlideOver" />
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
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Active</th>
                                        <th scope="col" class="w-3/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Roles</th>
                                        <th scope="col" class="w-1/12 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">



                                    <tr  v-for="(staff, index) in staff.data"
                                        :key="staff.id" class="hover:bg-gray-100 focus-within:bg-gray-100 ">


                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ staff.name }}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">

                                            <div v-if="staff.staff.is_active  === 1">

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
                                            <ul>
                                                <li v-if="staff.roles.length > 0" v-for="(role, index) in staff.roles"
                                                    :key="index">
                                                    <span v-if="role.id ===1" class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{role.name}}</span>
                                                    <span v-if="role.id ===2" class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">{{role.name}}</span>
                                                    <span v-if="role.id ===3" class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">{{role.name}}</span>
                                                    <span v-if="role.id ===4" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{role.name}}</span>
                                                    <span v-if="role.id ===5" class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{role.name}}</span>
                                                    <span v-if="role.id ===6" class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">{{role.name}}</span>
                                                    <span v-if="role.id ===7" class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{role.name}}</span>
                                                </li>

                                                <li v-else>
                                                    <span  class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">None</span>
                                                </li>

                                            </ul>

                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('staff.show',staff.id)" >View</Link>
                                        </td>

                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="ml-3 mt-2">
                                {{ tableStats }}
                            </div>
                        </div>
                        <div v-if="staff.data.length" class="w-full flex justify-center mt-5 mb-4">

                            <PaginationModified :links="staff.links"/>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
