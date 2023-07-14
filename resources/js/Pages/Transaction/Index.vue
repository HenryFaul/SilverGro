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

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const format = () => {
    const _date = new Date(filterForm.end_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatStart = () => {
    const _date = new Date(filterForm.start_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}



const open = ref(true)

const props = defineProps({
    transactions: Object,
    filters: Object,
    start_date: String,
    end_date: String,
    contract_types:Object
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
    show: props.filters.show ?? 25,
    supplier_name:props.filters.supplier_name ?? null,
    customer_name:props.filters.customer_name ?? null,
    transporter_name:props.filters.transporter_name ?? null,
    product_name:props.filters.product_name ?? null,
    start_date:props.filters.start_date ?? null,
    end_date:props.filters.end_date ?? null,
    contract_type_id:props.filters.contract_type_id ?? 1,
})

let curClient = ref(null);

//let tableStats = ref("Showing page " + props.transactions.current_page + "  of " + props.transactions.total + " entries.");

let filter = debounce(() => {

    filterForm.get(
        route('transport_transaction.index'),
        {
            preserveState: true,
            preserveScroll: true,
        },
    );

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

watch(
    () => filterForm.start_date,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.end_date,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.contract_type_id,
    (exampleField, prevExampleField) => {
        filter();
    }
);




const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;

    mon.value=true;
    tue.value=true;
    wed.value=true;
    thu.value=true;
    fri.value=true;
    sat.value=true;
    sun.value=true;

  /*  ua.value=true;
    pc.value=true;
    sc.value=true;
    mq.value=true;*/

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

let mon = ref(true);
let tue = ref(true);
let wed = ref(true);
let thu = ref(true);
let fri = ref(true);
let sat = ref(true);
let sun = ref(true);

//Unallocated
//'PC'
//'SC'
//'MQ'

/*let ua = ref(true);
let pc = ref(true);
let sc = ref(true);
//let mq = ref(true);*/

let NiceDay = (_date) => {
    return new Date(_date).getDay()
};

let dayIncluded = (_date) => {
    let _day = NiceDay(_date);
    switch(_day) {
        case 1:
            return mon.value;
        case 2:
            return tue.value;
        case 3:
            return wed.value;
        case 4:
            return thu.value;
        case 5:
            return fri.value;
        case 6:
            return sat.value;
        case 7:
            return sun.value;
        default:
            return false;
    }
};

/*let typeIncluded = (typeId) => {

    switch(typeId) {
        case 1:
            return ua.value;
        case 2:
            return pc.value;
        case 3:
            return sc.value;
        case 4:

         return  filterForm.mq.value;
            //return mq.value;
        default:
            return false;
    }
};*/


let filteredTrans = computed(() =>
    (mon.value && tue.value && wed.value && thu.value && fri.value && sat.value && sun.value )
        ? props.transactions.data
        : props.transactions.data.filter((trans) => {
            return dayIncluded(trans.transport_date_earliest)
        })
);

/*const tableStats = computed(() => {
    return "Showing page " + props.transactions.current_page + "  of " + filteredTrans.length+ " entries.";
})*/

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

                        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

                            <div class="flex col-span-6 mt-1">

                                <div>
                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                        Start Date
                                    </div>
                                    <div>
                                        <VueDatePicker style="width: 250px;"
                                                       v-model="filterForm.start_date"
                                                       :format="formatStart"
                                                       :teleport="true"></VueDatePicker>
                                    </div>


                                </div>
                                <div class="ml-4">
                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                        End Date
                                    </div>
                                    <div>
                                        <VueDatePicker style="width: 250px;"
                                                       v-model="filterForm.end_date"
                                                       :format="format"
                                                       :teleport="true"></VueDatePicker>
                                    </div>
                                </div>

<!--                                <div class="mt-6 ml-6">
                                    <div class="flex">
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input v-model="ua" id="ua" aria-describedby="candidates-description"  type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="mon" class="font-medium text-gray-900">UA</label>
                                            </div>
                                        </div>

                                        <div class="relative ml-3 flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input v-model="pc" id="pc" aria-describedby="candidates-description"  type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="mon" class="font-medium text-gray-900">PC</label>
                                            </div>
                                        </div>


                                        <div class="relative ml-3 flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input v-model="sc" id="sc" aria-describedby="candidates-description"  type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="tue" class="font-medium text-gray-900">SC</label>
                                            </div>
                                        </div>
                                        <div class="relative ml-3 flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input v-model="filterForm.mq" id="mq" aria-describedby="candidates-description"  type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="tue" class="font-medium text-gray-900">MQ</label>
                                            </div>
                                        </div>

                                    </div>

                                </div>-->

                            </div>
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

                            <div class="col-span-6  mt-3">
                                <div class="">
                                    <select v-model="filterForm.contract_type_id"
                                            class="input-filter-l w-1/6  rounded-md rounded-md shadow-sm border border-gray-300 text-gray-500">
                                       <option selected :value="null">All contracts</option>

                                        <option v-for="n in contract_types" :key="n.id" :value="n.id">
                                            {{n.name}}
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-span-6  mt-3">
                                <div class="flex">
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input v-model="mon" id="mon" aria-describedby="candidates-description" name="mon" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="mon" class="font-medium text-gray-900">Mon</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input v-model="tue" id="tue" aria-describedby="candidates-description" name="tue" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="tue" class="font-medium text-gray-900">Tue</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="wed" v-model="wed" aria-describedby="candidates-description" name="wed" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="wed" class="font-medium text-gray-900">Wed</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="thu" v-model="thu" aria-describedby="candidates-description" name="thu" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="thu" class="font-medium text-gray-900">Thu</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="fri" v-model="fri" aria-describedby="candidates-description" name="fri" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="fri" class="font-medium text-gray-900">Fri</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="sat" v-model="sat" aria-describedby="candidates-description" name="sat" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="sat" class="font-medium text-gray-900">Sat</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="sun" v-model="sun" aria-describedby="candidates-description" name="sun" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="sun" class="font-medium text-gray-900">Sun</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-span-4 mb-3">

                                <select v-model="filterForm.show"
                                        class="input-filter-l block w-1/6 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                    <option :value=5>5</option>
                                    <option :value=10>10</option>
                                    <option :value=25>25</option>
                                    <option :value=100>100</option>
                                    <option :value=200>200</option>
                                    <option :value=500>500</option>

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

                                <table class="min-w-full divide-y  divide-gray-200">
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
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Type</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">


<!--                                    'id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                    'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'-->

                                    <tr @click="edit(transaction.id)"  v-for="(transaction, index) in filteredTrans"
                                        :key="transaction.id" class="hover:bg-gray-100 text-sm focus-within:bg-gray-100 ">

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

                                        <td class="py-4 px-6 ">
                                            {{transaction.contract_type.name}}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',transaction.id)" >View trans</Link>
                                        </td>

                                    </tr>

                                    </tbody>
                                </table>

                            <div class="ml-3 mt-2">
                                {{ "Showing page " + props.transactions.current_page + "  of " + props.transactions.total+ " entries." }}
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
