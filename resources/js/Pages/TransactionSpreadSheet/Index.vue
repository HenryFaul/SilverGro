<script setup>
import AppLayout from '@/Layouts/AppLayoutSpreadSheet.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import TradeSlideOver from  "@/Components/UI/TradeSlideOver.vue";

import Icon from "@/Components/Icon.vue";
import {EllipsisHorizontalIcon,CheckIcon, ChevronUpDownIcon, PaperClipIcon, XCircleIcon, InformationCircleIcon, ExclamationTriangleIcon, XMarkIcon, TruckIcon} from '@heroicons/vue/20/solid';
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';

import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';


const props = defineProps({
    transactions:Object,
    filters: Object,
    all_suppliers:Object,
    all_customers:Object,
    all_transporters:Object,
    all_products:Object,
    contract_types:Object

});

const viewTradeSlideOver = ref(false);

const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
};
const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
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

let NiceDay = (_date) => {
    return new Date(_date).getDay()
};


const header_styler = computed(() => "sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell");
const row_styler = computed(() => "whitespace-nowrap border-b bg-white px-3 py-4 text-sm text-gray-500 lg:table-cell");

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
})


const unitForm = useForm({

    selected_trans:null,
    selected_trans_id: null,
    no_units_incoming: 0,
    no_units_outgoing:0
})

const transForm = useForm({
    selected_trans:null,
    selected_trans_id: null,
    supplier_id: null,
    customer_id: null,
    transporter_id: null,
    product_id: null,
})



let filter =  debounce( () => {

    filterForm.get(
        route('transaction_spreadsheet.index'),
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: visit => {

            },
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
let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}


let NiceTDate = (date) => {
    const _date = new Date(date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const dayString = (_date.toLocaleString('en', {weekday: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
};


let mon = ref(true);
let tue = ref(true);
let wed = ref(true);
let thu = ref(true);
let fri = ref(true);
let sat = ref(true);
let sun = ref(true);

let filteredTrans = computed(() =>
    (mon.value && tue.value && wed.value && thu.value && fri.value && sat.value && sun.value )
        ? props.transactions.data
        : props.transactions.data.filter((trans) => {
            return dayIncluded(trans.transport_date_earliest)
        })
);

let updateSelectedTrans = async (_id) => {

    unitForm.selected_trans = props.transactions.data.find(element => element.id === _id);

    if (unitForm.selected_trans != null){

        //update unit form
        unitForm.selected_trans_id = unitForm.selected_trans.id;
        unitForm.no_units_incoming = unitForm.selected_trans.transport_load.no_units_incoming;
        unitForm.no_units_outgoing = unitForm.selected_trans.transport_load.no_units_outgoing;

        //update trans form
        transForm.selected_trans = unitForm.selected_trans;
        transForm.selected_trans_id = unitForm.selected_trans.id;
        transForm.supplier_id = unitForm.selected_trans.supplier_id;
        transForm.customer_id = unitForm.selected_trans.customer_id;
        transForm.transporter_id = unitForm.selected_trans.transporter_id;
        transForm.product_id = unitForm.selected_trans.product_id;
    }

};

//transport_load.update_units'

const updateUnits = () => {
    unitForm.put(route('transport_load.update_units', unitForm.selected_trans.transport_load.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                //swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}

const updatePlayers = () => {
    transForm.put(route('transport_transaction.update_players', transForm.selected_trans.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                //swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}


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

    filter();
}

let showDetails = ref(true);

const toggleDetails = () => {

    showDetails.value === true ?  showDetails.value = false : showDetails.value = true;
}

let productQuery = ref('');

const filteredProducts = computed(() =>
    productQuery.value === ''
        ? props.all_products
        : props.all_products.filter((product) => {
            return product.name.toLowerCase().includes(productQuery.value.toLowerCase())
        })
);


let supplierQuery = ref('');

const filteredSuppliers = computed(() =>
    supplierQuery.value === ''
        ? props.all_suppliers
        : props.all_suppliers.filter((supplier) => {
            return supplier.last_legal_name.toLowerCase().includes(supplierQuery.value.toLowerCase())
        })
);


let customerQuery = ref('');

const filteredCustomers = computed(() =>
    customerQuery.value === ''
        ? props.all_customers
        : props.all_customers.filter((customer) => {
            return customer.last_legal_name.toLowerCase().includes(customerQuery.value.toLowerCase())
        })
);

let transporterQuery = ref('');

const filteredTransporters = computed(() =>
    transporterQuery.value === ''
        ? props.all_transporters
        : props.all_transporters.filter((transporter) => {
            return transporter.last_legal_name.toLowerCase().includes(transporterQuery.value.toLowerCase())
        })
);

</script>

<template>
    <AppLayout title="Spreadsheet">

        <div>

            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flow-root">
                    <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                        <div>

                            <div class="ml-4 shadow-xl mt-3 p-3 bg-white grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

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
                                        <secondary-button class=" ml-1"  @click="toggleDetails">Toggle</secondary-button>
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

                        </div>

                        <div class="inline-block min-w-full py-2 align-middle">
                            <table class="min-w-full border-separate border-spacing-0">
                                <thead>
                                <tr>
                                    <th scope="col" class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">ID</th>
                                    <th scope="col" :class="header_styler">Type</th>
                                    <th scope="col" :class="header_styler" >Customer Order No</th>
                                    <th scope="col" :class="header_styler">Date earliest</th>
                                    <th scope="col" :class="header_styler">Driver Vehicle</th>
                                    <th scope="col" :class="header_styler">Supplier</th>
                                    <th scope="col" :class="header_styler">Customer</th>
                                    <th scope="col" :class="header_styler">Transporter</th>
                                    <th scope="col" :class="header_styler">Product</th>
                                    <th scope="col" :class="header_styler">Units in</th>
                                    <th scope="col" :class="header_styler">Units out</th>
                                    <th scope="col" :class="header_styler">Cost/T</th>
                                    <th scope="col" :class="header_styler">Price/T</th>
                                    <th scope="col" :class="header_styler">Transport/T</th>
                                    <th scope="col" :class="header_styler">GP/T</th>
                                    <th scope="col" :class="header_styler">GP%</th>

                                    <th v-if="showDetails" scope="col" :class="header_styler">D/T</th>
                                    <th v-if="showDetails" scope="col" :class="header_styler">P/O</th>
                                    <th v-if="showDetails" scope="col" :class="header_styler">S/O</th>
                                    <th v-if="showDetails" scope="col" :class="header_styler">T/O</th>
                                    <th v-if="showDetails" scope="col" :class="header_styler">WB</th>
                                    <th v-if="showDetails" scope="col" :class="header_styler">INV</th>



                                    <th scope="col" :class="header_styler">Notes</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(transaction, transIdx) in transactions.data" :key="transaction.id">
                                    <td :class="['whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8']">{{transaction.id}}</td>

                                    <td :class="row_styler">
                                        {{transaction.contract_type.name}}
                                    </td>
                                    <td :class="[transIdx !== transactions.length  - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap hidden px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                        <div v-if="transaction.transport_job.customer_order_number">
                                            {{transaction.transport_job.customer_order_number}}
                                        </div>
                                        <div>
                                            N/A
                                        </div>
                                    </td>

                                    <td :class="row_styler">
                                        {{ NiceTDate(transaction.transport_date_earliest)}}
                                    </td>
                                    <td :class="row_styler">

                                        <div v-for="(driver_vehicle, index) in transaction.transport_job.transport_driver_vehicle" :key="driver_vehicle.id">

                                            <div>
                                                <div class="font-bold">Driver vehicle {{index+1}}</div>
                                                <div>
                                                    <span>Loading no: </span> <span>{{driver_vehicle.driver_vehicle_loading_number}}</span>
                                                </div>

                                                <div>
                                                    <span>Driver: </span> <span>{{driver_vehicle.driver.first_name}} {{driver_vehicle.driver.last_name}}</span>
                                                </div>
                                                <div>
                                                    <span>Vehicle: </span>  <span>{{driver_vehicle.vehicle.vehicle_type.name}} </span> <span>({{driver_vehicle.vehicle.reg_no}}) </span>
                                                </div>


                                            </div>

                                        </div>

                                    </td>
                                    <td :class="row_styler">

                                        <div v-if="props.all_suppliers != null" class="mt-2">

                                            <Combobox @click="updateSelectedTrans(transaction.id)" as="div" v-model="transaction.supplier_id">
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-72 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="supplierQuery = $event.target.value"
                                                        :display-value="(supplier) =>  props.all_suppliers.find(element => element.id === supplier).last_legal_name"
                                                    />
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredSuppliers.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption
                                                            v-for="supplier in filteredSuppliers"
                                                            :key="supplier.id" :value="supplier.id"
                                                            as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ supplier.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>
                                            <div v-if="transForm.selected_trans != null">
                                                <button v-if="transaction.id === transForm.selected_trans.id" @click.prevent="updatePlayers" class="mt-2 ml-3 font-light underline text-indigo-400">update player</button>
                                            </div>


                                        </div>
                                        <div v-else> null</div>
                                    </td>
                                    <td :class="row_styler">
                                        <div v-if="props.all_customers != null" class="mt-2">

                                            <Combobox @click="updateSelectedTrans(transaction.id)" as="div" v-model="transaction.customer_id">
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-72 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="customerQuery = $event.target.value"
                                                        :display-value="(customer) =>  props.all_customers.find(element => element.id === customer).last_legal_name"
                                                    />
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredSuppliers.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption
                                                            v-for="customer in filteredCustomers"
                                                            :key="customer.id" :value="customer.id"
                                                            as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ customer.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>
                                            <div v-if="transForm.selected_trans != null">
                                                <button v-if="transaction.id === transForm.selected_trans.id" @click.prevent="updatePlayers" class="mt-2 ml-3 font-light underline text-indigo-400">update player</button>
                                            </div>

                                        </div>
                                        <div v-else> null</div>
                                    </td>
                                    <td :class="row_styler">
                                        <div v-if="props.all_transporters != null" class="mt-2">

                                            <Combobox @click="updateSelectedTrans(transaction.id)" as="div" v-model="transaction.transporter_id">
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-72 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="transporterQuery = $event.target.value"
                                                        :display-value="(transporter) =>  props.all_transporters.find(element => element.id === transporter).last_legal_name"
                                                    />
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredTransporters.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption
                                                            v-for="transporter in filteredTransporters"
                                                            :key="transporter.id" :value="transporter.id"
                                                            as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ transporter.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>
                                            <div v-if="transForm.selected_trans != null">
                                                <button v-if="transaction.id === transForm.selected_trans.id" @click.prevent="updatePlayers" class="mt-2 ml-3 font-light underline text-indigo-400">update player</button>
                                            </div>

                                        </div>
                                        <div v-else> null</div>
                                    </td>
                                    <td :class="row_styler">
                                        <div v-if="props.all_products != null" class="mt-2">

                                            <Combobox @click="updateSelectedTrans(transaction.id)" as="div" v-model="transaction.product_id">
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-72 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="productQuery = $event.target.value"
                                                        :display-value="(product) =>  props.all_products.find(element => element.id === product).name"
                                                    />
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredProducts.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption
                                                            v-for="product in filteredProducts"
                                                            :key="product.id" :value="product.id"
                                                            as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ product.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <div v-if="transForm.selected_trans != null">
                                                <button v-if="transaction.id === transForm.selected_trans.id" @click.prevent="updatePlayers" class="mt-2 ml-3 font-light underline text-indigo-400">update player</button>
                                            </div>


                                        </div>
                                        <div v-else> null</div>
                                    </td>
                                    <td :class="row_styler">

                                        <input @change="updateSelectedTrans(transaction.id)" v-model="transaction.transport_load.no_units_incoming" type="number"
                                               class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <div v-if="unitForm.selected_trans != null">
                                            <button v-if="transaction.transport_load.id === unitForm.selected_trans.transport_load.id" @click.prevent="updateUnits" class="mt-2 font-light underline text-indigo-400">update units</button>
                                        </div>

                                    </td>
                                    <td :class="row_styler">
                                        <input v-model="transaction.transport_load.no_units_outgoing" type="number"
                                               class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <div v-if="unitForm.selected_trans != null">
                                            <button v-if="transaction.transport_load.id === unitForm.selected_trans.transport_load.id" @click.prevent="updateUnits" class="mt-2 font-light underline text-indigo-400">update units</button>
                                        </div>

                                    </td>
                                    <td :class="row_styler">
                                        {{NiceNumber(transaction.transport_finance.cost_price_per_ton)}}
                                    </td>
                                    <td :class="row_styler">
                                        {{NiceNumber(transaction.transport_finance.selling_price_per_ton)}}
                                    </td>
                                    <td :class="row_styler">
                                        {{NiceNumber(transaction.transport_finance.transport_rate_per_ton)}}
                                    </td>
                                    <td :class="row_styler">
                                        {{NiceNumber(transaction.transport_finance.gross_profit_per_ton)}}
                                    </td>
                                    <td :class="row_styler">
                                        {{transaction.transport_finance.gross_profit_percent}}%
                                    </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.deal_ticket.is_active" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>
                                    </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.purchase_order.is_active" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>                                                        </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.sales_order.is_active" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>
                                    </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.transport_order.is_active" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>
                                    </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.transport_load.is_weighbridge_certificate_received" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>
                                    </td>

                                    <td v-if="showDetails" :class="row_styler">
                                        <check-icon v-if="transaction.transport_invoice.is_active" class="w-5 h-5 fill-green-300"/>
                                        <x-mark-icon v-else class="w-5 h-5 fill-red-400"/>
                                    </td>

                                    <td :class="row_styler">
                                        {{transaction.process_notes}}
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <trade-slide-over :show="viewTradeSlideOver" @close="closeTradeSlideOver"  />
            </div>
            <div class="">
                <div class="">
                    <div v-if="transactions.data.length" class="w-full flex justify-center mt-5 mb-4">
                        <PaginationModified :links="transactions.links"/>
                    </div>
                </div>

            </div>

        </div>

    </AppLayout>
</template>
