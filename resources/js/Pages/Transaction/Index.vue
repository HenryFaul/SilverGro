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
    contract_types:Object,
    download_url:Object,
    custom_reports:Object
});
const roles_permissions = computed(() => usePage().props.roles_permissions)

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
    contract_type_id:props.filters.contract_type_id ?? null,
    id:props.filters.id ?? null,
    custom_report_id:1
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

watch(
    () => filterForm.id,
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
    filterForm.id = null;

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
   // router.get('transport_transaction/'+id);
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


const generateExcel = () => {
    filterForm.get(route('excel_report.transactions.generate'),
        {
            only: ['download_url','custom_report_id'],
            preserveScroll: true,
            onSuccess: (res) => {
               // console.log(res);
            },
        }
    );
}



</script>

<template>
    <AppLayout title="Transactions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Report Exporter
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-4 lg:px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

                            <div class="flex col-span-6">
                                <div>
                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                        Start Date
                                    </div>
                                    <div class="w-48">
                                        <VueDatePicker  v-model="filterForm.start_date"
                                                        :format="formatStart"
                                                        :teleport="true"
                                        ></VueDatePicker>
                                    </div>

                                </div>
                                <div class="ml-2">
                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                        End Date
                                    </div>
                                    <div class="w-48">
                                        <VueDatePicker v-model="filterForm.end_date"
                                                       :format="format"
                                                       :teleport="true"
                                        ></VueDatePicker>
                                    </div>
                                </div>

                                <div class="mt-5 ml-2">
                                    <select v-model="filterForm.contract_type_id"
                                            class="input-filter-l  w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option  :value="null">All contracts</option>

                                        <option v-for="n in contract_types" :key="n.id" :value="n.id">
                                            {{n.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="mt-5 ml-2">
                                    <select v-model="filterForm.show"
                                            class="input-filter-l  w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value=5>5</option>
                                        <option :value=10>10</option>
                                        <option :value=25>25</option>
                                        <option :value=100>100</option>
                                        <option :value=200>200</option>
                                        <option :value=500>500</option>
                                    </select>

                                </div>


                            </div>
                            <div class="col-span-4 flex">
                                <input v-model.number="filterForm.supplier_name" aria-label="Search" class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="supplier name..."
                                       type="search"/>

                                <input v-model.number="filterForm.customer_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="customer name..."
                                       type="search"/>

                                <input v-model.number="filterForm.transporter_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="transporter name..."
                                       type="search"/>

                                <input v-model.number="filterForm.product_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="product name..."
                                       type="search"/>

                                <input v-model.number="filterForm.id" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="contract no..."
                                       type="search"/>

                            </div>
                            <div class="col-span-4 flex">

                                <div>
                                    <secondary-button class="" @click="filter">Search</secondary-button>
                                    <secondary-button class=" ml-1" @click="clear">Clear</secondary-button>
                                    <secondary-button class=" ml-1"  @click="showTradeSlideOver">Add (+)</secondary-button>
                                    <secondary-button class=" ml-1"  @click="generateExcel">Export</secondary-button>

                                    <div class=" mt-3 w-72">
                                        <div v-if="custom_reports != null" class="mt-2">

                                            <select v-model="filterForm.custom_report_id"
                                                    class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option
                                                    v-for="n in custom_reports"
                                                    :key="n.id" :value="n.id">
                                                    {{ n.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-else> null</div>
                                    </div>

                                    <div class="m-2 p-2" v-if="download_url">
                                        <a :href="route('excel_report.transactions.download',download_url)" target="_blank"
                                           class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                            Download Excel
                                        </a>

                                    </div>

                                </div>

                                <div class="flex ml-6">
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="mon" v-model="mon" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="mon" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="mon">Mon</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="tue" v-model="tue" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="tue" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="tue">Tue</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="wed" v-model="wed" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="wed" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="wed">Wed</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="thu" v-model="thu" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="thu" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="thu">Thu</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="fri" v-model="fri" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="fri" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="fri">Fri</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="sat" v-model="sat" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="sat" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="sat">Sat</label>
                                        </div>
                                    </div>
                                    <div class="relative ml-2 flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="sun" v-model="sun" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="sun" type="checkbox" />
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label class="font-medium text-gray-900" for="sun">Sun</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-4 mb-3">

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
                                            ID
                                        </th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Type</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                             DATE
                                        </th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                            Supplier
                                        </th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Customer</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Transporter</th>
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Product</th>

<!--
                                        <th scope="col" class="w-2/12 py-4 px-6 text-xs font-semibold tracking-wider text-left text-white uppercase">Actions</th>
-->
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">


<!--                                    'id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                    'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'-->

                                    <tr  v-for="(transaction, index) in filteredTrans"
                                        :key="transaction.id" class="hover:bg-gray-100 text-sm focus-within:bg-gray-100 ">

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{ transaction.id}}
                                        </td>
                                        <td class="py-4 px-6 ">
                                            {{transaction.contract_type.name}}
                                        </td>

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

<!--                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',transaction.id)" >View trans</Link>
                                        </td>-->

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
