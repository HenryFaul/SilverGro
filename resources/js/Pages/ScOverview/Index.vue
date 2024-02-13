<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, inject, onBeforeMount, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage, Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import TradeSlideOver from "@/Components/UI/TradeSlideOver.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {
    EllipsisHorizontalIcon,
    CheckIcon,
    ChevronUpDownIcon,
    PaperClipIcon,
    XCircleIcon,
    InformationCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    TruckIcon
} from '@heroicons/vue/20/solid';
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import DriverVehicleModal from "@/Components/UI/DriverVehicleModal.vue";
import DriverVehicleModalAdd from "@/Components/UI/DriverVehicleModal.vue";
import BaseTooltip from "@/Components/UI/BaseTooltip.vue";

const swal = inject('$swal');

import {
    Switch,
    SwitchGroup,
    SwitchLabel,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';

import ContractLinkModal from "@/Components/UI/ContractLinkModal.vue";
import ContractLinkModalSc from "@/Components/UI/ContractLinkModal.vue";

let dayIncluded = (_date) => {
    let _day = NiceDay(_date);
    switch (_day) {
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

let NiceTDate = (date) => {
    const _date = new Date(date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const dayString = (_date.toLocaleString('en', {weekday: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
};

let TrunkCateText = (_text) => {
    return _text.length > 40 ? _text.slice(0, 40) + "..." : _text;
}

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

let NiceNumber = (_number) => {

    if(_number === null){
        return 0
    } else {
        let val = (_number / 1).toFixed(2).replace(".", ".");
        return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }


}

const formatEarly = () => {
    const _date = new Date(transport_trans_Form.transport_date_earliest);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatLate = () => {
    const _date = new Date(transport_trans_Form.transport_date_latest);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoicePdDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_paid_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoicePayByDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_pay_by_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoiceDate = () => {
    const _date = new Date(transport_invoice_Form.invoice_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const props = defineProps({
    transactions: Object,
    selected_transaction: Object,
    filters: Object,
    start_date: String,
    end_date: String,
    rules_with_approvals: Object,
    deal_ticket: Object,
    transport_order: Object,
    purchase_order: Object,
    sales_order: Object,
    linked_trans_other:Object

});

onBeforeMount(async () => {

});


const permissions = computed(() => usePage().props.permissions)
const roles_permissions = computed(() => usePage().props.roles_permissions)
const can_adjust_gp = computed(() => usePage().props.roles_permissions.permissions.includes("edit_adjusted_gp"))

const selectedSplitCustomer = ref(2);

let isLoading = ref(false);
let isUpdating = ref(false);

const selectedTabId = ref(0);
const selectTab = (id) => {
    selectedTabId.value = id;
};


const newTradeAdded = () => {

    filterForm.new_trade_added = true;
}


const filterForm = useForm({
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 5,
    supplier_name: props.filters.supplier_name ?? null,
    customer_name: props.filters.customer_name ?? null,
    transporter_name: props.filters.transporter_name ?? null,
    product_name: props.filters.product_name ?? null,
    start_date: props.filters.start_date ?? null,
    end_date: props.filters.end_date ?? null,
    id: props.filters.id ?? null,
    selected_trans_id: props.selected_transaction.id ?? null,
    new_trade_added: false,
    old_id: null,

})


let filter = debounce(() => {

    isLoading.value = true;
    filterForm.get(
        route('sc_overview.index'),
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: visit => {
                isLoading.value = false;
            },
        },
    );

}, 150);

let sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
}


watch(
    () => filterForm.a_mq,
    (exampleField, prevExampleField) => {
        filter();
    }
);

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

watch(
    () => filterForm.old_id,
    (exampleField, prevExampleField) => {
        filter();
    }
);


let mon = ref(true);
let tue = ref(true);
let wed = ref(true);
let thu = ref(true);
let fri = ref(true);
let sat = ref(true);
let sun = ref(true);

let filteredTrans = computed(() =>
    (mon.value && tue.value && wed.value && thu.value && fri.value && sat.value && sun.value)
        ? props.transactions.data
        : props.transactions.data.filter((trans) => {
            return dayIncluded(trans.transport_date_earliest)
        })
);

let updateSelectedTrans = async (_id) => {
    filterForm.selected_trans_id = _id;
    filter();
};

const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;
    filterForm.id = null;
    filterForm.old_id = null;
    filterForm.a_mq = null;

    mon.value = true;
    tue.value = true;
    wed.value = true;
    thu.value = true;
    fri.value = true;
    sat.value = true;
    sun.value = true;

    filter();
}


let showDetails = ref(true);

const toggleDetails = () => {

    showDetails.value === true ? showDetails.value = false : showDetails.value = true;
}

let weightRemaining = () => {

    let pc_weight = 0;
    let mq_weight=0;

    if (props.linked_trans_other != null){

        for (let linked of props.linked_trans_other){

                if(linked.transport_transaction.transport_finance.weight_ton_outgoing != null){
                    mq_weight += linked.transport_transaction.transport_finance.weight_ton_outgoing
                }
        }
    }

    //props.selected_transaction.transport_load.no_units_incoming
    if(props.selected_transaction.transport_finance.weight_ton_outgoing != null){

        pc_weight = props.selected_transaction.transport_finance.weight_ton_outgoing;
    }

    return (pc_weight - mq_weight).toFixed(4);

}


const getTitle = computed(() => {
    return props.selected_transaction != null ? props.selected_transaction.contract_type.name + props.selected_transaction.id : "PC Overview";
});


const header_styler = computed(() => "sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell");
const row_styler = computed(() => "whitespace-nowrap border-b px-3 py-4 text-sm text-gray-500 lg:table-cell");


</script>

<template>
    <AppLayout :title="getTitle">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">

                <div v-if="selected_transaction != null">SC Overview: {{ getTitle }}</div>
                <div v-else>SC Overview</div>
            </h2>
        </template>

        <div class="py-2">

            <div class="bg-white m-2 p-2 shadow-xl sm:rounded-lg">

                <div>
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="mt-3 flow-root">
                            <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle">
                                    <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

                                        <div class="flex col-span-6">
                                            <div>
                                                <div class="ml-3 text-indigo-400 text-sm font-bold">
                                                    Start Date
                                                </div>
                                                <div class="w-48">
                                                    <VueDatePicker v-model="filterForm.start_date"
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

                                            <div class="mt-5 ml-2">
                                                <input v-model.number="filterForm.old_id" aria-label="Search"
                                                       class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="old contract no..."
                                                       type="search"/>
                                            </div>


                                        </div>
                                        <div class="col-span-4 flex">
                                            <input v-model.number="filterForm.supplier_name" aria-label="Search"
                                                   class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   placeholder="supplier name..."
                                                   type="search"/>

                                            <input v-model.number="filterForm.customer_name" aria-label="Search"
                                                   class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   placeholder="customer name..."
                                                   type="search"/>

                                            <input v-model.number="filterForm.transporter_name" aria-label="Search"
                                                   class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   placeholder="transporter name..."
                                                   type="search"/>

                                            <input v-model.number="filterForm.product_name" aria-label="Search"
                                                   class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   placeholder="product name..."
                                                   type="search"/>

                                            <input v-model.number="filterForm.id" aria-label="Search"
                                                   class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   placeholder="contract no..."
                                                   type="search"/>


                                        </div>
                                        <div class="col-span-4 flex">

                                            <div>
                                                <secondary-button class="" @click="filter">Search</secondary-button>
                                                <secondary-button class=" ml-1" @click="clear">Clear</secondary-button>
                                                <secondary-button class=" ml-1" @click="toggleDetails">Toggle
                                                </secondary-button>
                                            </div>

                                            <div class="flex ml-6">
                                                <div class="relative flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="mon" v-model="mon"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="mon" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="mon">Mon</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="tue" v-model="tue"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="tue" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="tue">Tue</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="wed" v-model="wed"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="wed" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="wed">Wed</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="thu" v-model="thu"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="thu" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="thu">Thu</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="fri" v-model="fri"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="fri" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="fri">Fri</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="sat" v-model="sat"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="sat" type="checkbox"/>
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label class="font-medium text-gray-900" for="sat">Sat</label>
                                                    </div>
                                                </div>
                                                <div class="relative ml-2 flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input id="sun" v-model="sun"
                                                               aria-describedby="candidates-description"
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               name="sun" type="checkbox"/>
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
                                    </div>
                                    <div class="">
                                        <div class="">
                                            <table class="min-w-full border-separate border-spacing-0">
                                                <thead>
                                                <tr>
                                                    <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                                        scope="col">ID
                                                    </th>
                                                    <th :class="header_styler" scope="col">TYPE</th>
                                                    <th :class="header_styler" scope="col">DATE</th>
                                                    <th :class="header_styler" scope="col">SUPPLIER</th>
                                                    <th :class="header_styler" scope="col">CUSTOMER</th>
                                                    <th :class="header_styler" scope="col">TRANSPORTER</th>
                                                    <th :class="header_styler" scope="col">PRODUCT</th>

                                                    <th v-if="showDetails" scope="col" :class="header_styler">Units Incoming</th>
                                                    <th v-if="showDetails" scope="col" :class="header_styler">Cost Price</th>
                                                    <th v-if="showDetails" scope="col" :class="header_styler">Selling Price</th>
                                                    <th v-if="showDetails" scope="col" :class="header_styler">Gross Profit</th>


                                                </tr>
                                                </thead>
                                                <tbody>

                                                <tr @click="updateSelectedTrans(transaction.id)"
                                                    v-for="(transaction, index) in filteredTrans"
                                                    :key="transaction.id"
                                                    :class="[transaction.id === props.selected_transaction.id  ? 'bg-indigo-300' : '', 'hover:bg-gray-100 text-sm focus-within:bg-gray-100']"
                                                >

                                                    <td :class="row_styler">
                                                        <div class="font-bold">{{ transaction.id }}</div>
                                                        <div>{{ transaction.old_id }}</div>
                                                        <div class="text-indigo-500" v-if="transaction.a_mq">
                                                            {{ transaction.a_mq }}
                                                        </div>

                                                    </td>
                                                    <td :class="row_styler">
                                                        {{ transaction.contract_type.name }}
                                                    </td>

                                                    <td :class="row_styler">
                                                        {{ NiceTDate(transaction.transport_date_earliest) }}
                                                    </td>

                                                    <td :class="row_styler">
                                                        {{ transaction.supplier.last_legal_name }}
                                                    </td>
                                                    <td :class="row_styler">
                                                        {{ transaction.customer.last_legal_name }}
                                                    </td>
                                                    <td :class="row_styler">
                                                        {{ transaction.transporter.last_legal_name }}
                                                    </td>

                                                    <td :class="row_styler">
                                                        {{ transaction.product.name }}
                                                    </td>

                                                    <td v-if="showDetails"
                                                        :class="row_styler">
                                                     {{transaction.transport_load.no_units_incoming}}
                                                    </td>

                                                    <td v-if="showDetails"
                                                        :class="row_styler">
                                                        {{NiceNumber(transaction.transport_finance.cost_price)}}
                                                    </td>

                                                    <td v-if="showDetails"
                                                        :class="row_styler">
                                                        {{NiceNumber(transaction.transport_finance.selling_price)}}
                                                    </td>

                                                    <td v-if="showDetails"
                                                        :class="row_styler">
                                                        {{NiceNumber(transaction.transport_finance.gross_profit)}}
                                                    </td>


                                                </tr>

                                                </tbody>
                                            </table>

                                            <div v-if="transactions.data.length"
                                                 class="w-full flex justify-center mt-5 mb-4">
                                                <PaginationModified :links="transactions.links"/>
                                            </div>
                                        </div>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="sticky bg-white m-2 p-2  shadow-xl sm:rounded-lg">

                <div>
                    <div class="px-4 sm:px-6 lg:px-8">


                        <div>
                            <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                                <div class="md:flex md:items-center md:justify-between">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">Linked
                                        Transactions</h3>
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
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Contract</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Supplier</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Transporter</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Product</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Weight</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cost</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Selling Price</th>

                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                    <span class="sr-only">Edit</span>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                            <tr v-for="contract in props.linked_trans_other" :key="contract.id">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{contract.id}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{contract.transport_transaction.supplier.last_legal_name}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{contract.transport_transaction.customer.last_legal_name}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{contract.transport_transaction.transporter.last_legal_name}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{contract.transport_transaction.product.name}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{contract.transport_transaction.transport_load.no_units_incoming}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"> {{NiceNumber(contract.transport_transaction.transport_finance.cost_price)}}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{NiceNumber(contract.transport_transaction.transport_finance.selling_price)}}</td>
                                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                    <a href="#" class="text-indigo-600 hover:text-indigo-900"
                                                    >Edit<span class="sr-only">, x</span></a
                                                    >
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div>

                                            <div class="relative mt-3">
                                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                                    <div class="w-full border-t border-gray-300" />
                                                </div>
                                                <div class="relative flex justify-center">
                                                    <span class="bg-white px-2 text-sm text-gray-500">SC Details</span>
                                                </div>
                                            </div>

                                            <table class="mt-3 min-w-full divide-y divide-gray-300">
                                                <thead>
                                                <tr>
                                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Units</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cost Price</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Transport Costs</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Other Costs</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Selling Price</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">GP</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tons Outstanding</th>

                                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                        <span class="sr-only">Edit</span>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                <tr>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{props.selected_transaction.transport_load.no_units_incoming}}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{NiceNumber(props.selected_transaction.transport_finance.cost_price)}}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{NiceNumber(props.selected_transaction.transport_finance.transport_cost)}}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><div v-if="props.selected_transaction.transport_finance.other_costs">{{NiceNumber(props.selected_transaction.transport_finance.other_costs)}}</div></td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{NiceNumber(props.selected_transaction.transport_finance.selling_price)}}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{NiceNumber(props.selected_transaction.transport_finance.gross_profit)}}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{weightRemaining()}} tons</td>
                                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                        <a href="#" class="text-indigo-600 hover:text-indigo-900"
                                                        >Edit<span class="sr-only">, x</span></a
                                                        >
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
