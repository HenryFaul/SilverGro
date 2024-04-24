<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationModified from "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import {EnvelopeIcon, PhoneIcon} from '@heroicons/vue/20/solid'
import {useForm} from "@inertiajs/vue3";
import {computed, onMounted, onBeforeMount, watch, ref} from "vue";
import {debounce} from "lodash";
import BaseTooltip from "@/Components/UI/BaseTooltip.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import TradeSlideOver from  "@/Components/UI/TradeSlideOver.vue";
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    transport_trans: Object,
    filters: Object,
    end_of_week: Object,
    start_of_week: Object,
    no_loads: Number,
    weight_uploaded: Number,
    planned_tons: Number,
    weight_offloaded: Number,
    cost_price: Number,
    trans_cost: Number,
    other_costs: Number,
    selling_price: Number,
    gp: Number,
    gp_perc: Number,
    contract_types:Object

});

const Form = useForm({
    date: props.filters.date ?? new Date().toISOString().substr(0, 10),
    show: props.filters.show ?? 25,
    contract_type_id:props.filters.contract_type_id ?? null,
})


onMounted(() => {

})

let addDay = () => {
    const result = new Date(Form.date);
    result.setDate(result.getDate() + 7);
    Form.date = result.toISOString().substr(0, 10);
};

let lessDay = () => {
    const result = new Date(Form.date);
    result.setDate(result.getDate() - 7);
    Form.date = result.toISOString().substr(0, 10);

};

let filter = debounce(() => {

    Form.get(
        route('planning.week'),
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
}, 150);


watch(
    () => Form.date,
    (exampleField, prevExampleField) => {

        filter();
    }
)

watch(
    () => Form.show,
    (exampleField, prevExampleField) => {
        filter();
    }
)

watch(
    () => Form.contract_type_id,
    (exampleField, prevExampleField) => {
        filter();
    }
)

const col_class = "text-xs text-white whitespace-nowrap";

let NiceDate = (_date) => {
    return new Date(_date).toDateString();
};

let NiceTDate = (date) => {

    const _date = new Date(date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const dayString = (_date.toLocaleString('en', {weekday: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
};

let NiceDay = (_date) => {
    return new Date(_date).getDay()
};

//Monday sky-200
//Tuesday lime-200
//Wednesday orange-200
//Thursday fuchsia-200
//Friday rose-200
//Saturday zinc-200
//Sunday emerald-200

let DayStyle = (_date) => {
    let _day = NiceDay(_date);
    return _day === 1 ? "bg-sky-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100" : _day === 2 ? "bg-lime-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100" : _day === 3 ? "bg-orange-200  hover:bg-gray-100 text-xs focus-within:bg-gray-100" : _day === 4 ? "bg-fuchsia-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100" : _day === 5 ? "bg-rose-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100" : _day === 6 ? "bg-zinc-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100" : "emerald-200 hover:bg-gray-100 text-xs focus-within:bg-gray-100";
};

let TrunkCateText = (_text) => {
    return _text.length > 30 ? _text.slice(0, 30) + "..." : _text;
}

let NiceNumber = (_number) => {
    let val = isNaN(_number) ? 0.0 : (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

let shortDate = () => {
    return Form.date.toISOString().substr(0, 10);
};

let calculatedCostPrice = (trans) =>{

    let offload = totalWeighBridgeOffload(trans.transport_driver_vehicle);
    let outgoing = trans
}

//const isNamePresent = computed(() => name.value.length > 0)

const format = () => {
    const _date = new Date(Form.date)
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const warningLister = (Warnings) => {
    var the_list = "";

    Warnings.forEach(function (arrayItem) {
        var entity = arrayItem.status_entity.entity;
        var type = arrayItem.status_type.type;

        the_list += "  " + entity + "_" + type + "."

    });
    return the_list;
}

const toolTipGen = (Message, Truck) => {
    return Message + " truck: " + (Truck + 1) + ".";
}

const totalWeighBridgeUpload = (driver_vehicles) => {
    var total = 0.0;

    if(Array.isArray(driver_vehicles)){
        return  total;
    }
    driver_vehicles.forEach(function (arrayItem) {
        var uploadWeight = arrayItem.weighbridge_upload_weight;
        total +=uploadWeight;
    });

    return total;
}

const totalWeighBridgeOffload = (driver_vehicles) => {
    var total = 0.0;

    if(Array.isArray(driver_vehicles)){
        return  total;
    }

    driver_vehicles.forEach(function (arrayItem) {
        var uploadWeight = arrayItem.weighbridge_upload_weight;
        total +=uploadWeight;
    });

    return total;
}

let mon = ref(true);
let tue = ref(true);
let wed = ref(true);
let thu = ref(true);
let fri = ref(true);
let sat = ref(true);
let sun = ref(true);

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

let filteredTrans = computed(() =>
    (mon.value && tue.value && wed.value && thu.value && fri.value && sat.value && sun.value )
        ? props.transport_trans.data
        : props.transport_trans.data.filter((trans) => {
            return dayIncluded(trans.transport_date_earliest)
        })
);

const viewTradeSlideOver = ref(false);

const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
};

const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
};

const contractName = (trans) => {

    if(trans != null){
        return  trans.contract_type.name+":"+trans.id;
    }
    else return "N/A";

}

const contractNameOld = (trans) => {

    if(trans != null){

        if(trans.deal_ticket != null){

            return   trans.deal_ticket.old_id == null ? trans.contract_type.name+":"+trans.old_id :trans.contract_type.name+":"+trans.deal_ticket.old_id;
        }

        return  trans.contract_type.name+":"+trans.old_id;
    }
    else return "N/A";

}



</script>

<template>
    <AppLayout title="Weekly View">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Weekly View
            </h2>
        </template>

        <div class="py-1">
            <div class="max-w-10xl mx-auto sm:px-3 lg:px-4 ">
                <div class="bg-gray-50  overflow-hidden shadow-xl sm:rounded-lg min-h-600">

                    <div class="flex flex-row m-2 p-2">

                        <div class="basis-1/2">
                            <div class="flex min-h-fit">

                                <button @click="lessDay()" type="button"
                                        class="block mr-1 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    -
                                </button>

                                <div class="">
                                    <VueDatePicker style="width: 250px;" v-model="Form.date" :format="format"
                                                   :teleport="true"></VueDatePicker>
                                </div>


                                <button @click="addDay()" type="button"
                                        class="block ml-1 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    +
                                </button>

                            </div>

                            <div class="text-xs mt-1 ml-2">
                                ({{ NiceTDate(start_of_week) }} to {{ NiceTDate(end_of_week) }})
                            </div>

                            <div class="row  mt-3">
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
                        </div>

                        <div class="basis-1/4">
                            <div class="mt-2">

                                <div class="flex">
                                    <div class="m-2">Per page:</div>
                                    <select v-model="Form.show"
                                            class="input-filter-l block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                        <option :value=1>1</option>
                                        <option :value=5>5</option>
                                        <option :value=10>10</option>
                                        <option :value=25>25</option>
                                        <option :value=100>100</option>
                                        <option :value=200>200</option>

                                    </select>
                                </div>

                            </div>
                        </div>

                        <div>
                            <trade-slide-over :show="viewTradeSlideOver" @close="closeTradeSlideOver"  />
                        </div>

                        <div class="basis-1/2">
                            <div class="">
                                <select v-model="Form.contract_type_id"
                                        class="input-filter-l w-2/6  rounded-md rounded-md shadow-sm border border-gray-300 text-gray-500">
                                    <option selected :value="null">All contracts</option>

                                    <option v-for="n in contract_types" :key="n.id" :value="n.id">
                                        {{n.name}}
                                    </option>
                                </select>
                            </div>

                        </div>


                        <div class="basis-1/4">
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <button @click="showTradeSlideOver" type="button"
                                        class="block rounded-md  bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Add trans
                                </button>
                            </div>

                        </div>


                    </div>


                    <div class="p-2 m-2">

                        <div class="px-4 sm:px-6 lg:px-8">



                            <div class="mt-2 flow-root">

                                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

                                        <div v-if="filteredTrans.length > 0">
                                            <div class="bg-white rounded-md shadow overflow-x-auto">
                                                <table class="min-w-full table-auto">
                                                    <thead class="bg-indigo-400">
                                                    <tr class="">
                                                        <th scope="col" :class="col_class">Status</th>
                                                        <th scope="col" :class="col_class">Date</th>
                                                        <th scope="col" :class="col_class">Contract</th>
                                                        <th scope="col" :class="col_class">Product</th>
                                                        <th scope="col" :class="col_class">Tons</th>
                                                        <th scope="col" :class="col_class">Loaded</th>
                                                        <th scope="col" :class="col_class">Supplier</th>
                                                        <th scope="col" :class="col_class">Customer</th>
                                                        <th scope="col" :class="col_class">Del?</th>
                                                        <th scope="col" :class="col_class">Transporter</th>
                                                        <th scope="col" :class="col_class">Tr/Reg.</th>
                                                        <th scope="col" :class="col_class">Cost/Ton</th>
                                                        <th scope="col" :class="col_class">Price/Ton</th>
                                                        <th scope="col" :class="col_class">TC/Ton</th>
                                                        <th scope="col" :class="col_class">GP</th>
                                                        <th scope="col" :class="col_class">Traders Notes</th>
                                                        <th scope="col" :class="col_class">Process Notes</th>
                                                        <th scope="col" :class="col_class">Cost</th>
                                                        <th scope="col" :class="col_class">T/Cost</th>
                                                        <th scope="col" :class="col_class">Selling</th>
                                                        <th scope="col" :class="col_class">GP%</th>
                                                        <th scope="col" :class="col_class">GP/Ton</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">


                                                    <tr v-for="(trans, index) in filteredTrans"
                                                        :key="trans.id" :class="DayStyle(trans.transport_date_earliest)">

                                                        <td class="py-4 px-6">
                                                            <div v-if="trans.supplier.terms_of_payment_id == 1">
                                                                <base-tooltip content="Supplier C.O.D account.">
                                                                    <icon name="bell-solid"
                                                                          class="mr-3 w-6 h-6 fill-red-500 "/>
                                                                </base-tooltip>
                                                            </div>

                                                            <div
                                                                v-if="trans.transport_driver_vehicle.is_payment_overdue">
                                                                <base-tooltip content="Payment overdue.">
                                                                    <icon name="dollar"
                                                                          class="mr-3 w-3 h-3 fill-red-500 "/>
                                                                </base-tooltip>
                                                            </div>

                                                            <div v-if="trans.transport_status.length > 0">
                                                                <base-tooltip
                                                                    :content=warningLister(trans.transport_status)>
                                                                    <icon name="triangle"
                                                                          class="mr-3 w-3 h-3 animate-pulse fill-red-500 "/>
                                                                </base-tooltip>
                                                            </div>

                                                            <div v-if="!trans.include_in_calculations">
                                                                <base-tooltip content="Exclude from calculations.">
                                                                    <icon name="info"
                                                                          class="mr-3 w-3 h-3 fill-gray-500"/>
                                                                </base-tooltip>
                                                            </div>


                                                        </td>
                                                        <td class="py-4 px-6">

                                                            {{ NiceTDate(trans.transport_date_earliest) }}

                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div class="font-bold">
                                                            <Link href="/transaction_summary" method="get" target="_blank" :data="{ selected_trans_id: trans.id }">  {{contractName(trans)}}</Link>
                                                            </div>
                                                            <div class="italic">
                                                                {{contractNameOld(trans)}}
                                                            </div>

                                                        </td>
                                                        <td class="py-4 px-6">
                                                            {{ trans.product.name }}
                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="totalWeighBridgeUpload(trans.transport_driver_vehicle)>0">
                                                                {{ totalWeighBridgeUpload(trans.transport_driver_vehicle)}}
                                                            </div>

                                                            <div v-else>
                                                                {{ trans.transport_finance.weight_ton_incoming }}
                                                            </div>


                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="trans.transport_driver_vehicle">

                                                                <div
                                                                    v-for="(item,index) of trans.transport_driver_vehicle">

                                                                    <div v-if="item.is_loaded">
                                                                        Truck {{ index + 1 }}:
                                                                        {{ NiceTDate(item.date_loaded) }}
                                                                    </div>

                                                                    <div v-else>
                                                                        <icon name="cross-solid" class="mr-2 w-3 h-3"/>
                                                                    </div>

                                                                </div>

                                                            </div>


                                                        </td>
                                                        <td class="py-4 px-6">

                                                            {{ trans.supplier.last_legal_name }}

                                                        </td>
                                                        <td class="py-4 px-6">

                                                            {{ trans.customer.last_legal_name }}
                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="trans.transport_driver_vehicle">
                                                                <div
                                                                    v-for="(item,index) of trans.transport_driver_vehicle">


                                                                    <div v-if="item.is_delivered || item.is_loaded">
                                                                        <div
                                                                            v-if="item.is_delivered">

                                                                            <base-tooltip
                                                                                :content='toolTipGen("Delivered",index)'>
                                                                                <icon name="truck"
                                                                                      class="mr-3 w-6 h-6 fill-green-300 animate-pulse"/>
                                                                            </base-tooltip>

                                                                        </div>

                                                                        <div
                                                                            v-if="item.is_loaded">
                                                                            <base-tooltip
                                                                                :content='toolTipGen("Loaded",index)'>
                                                                                <icon name="truck"
                                                                                      class="mr-3 w-6 h-6 fill-yellow-300 animate-pulse"/>
                                                                            </base-tooltip>

                                                                        </div>
                                                                    </div>

                                                                    <div v-else>
                                                                        <icon name="cross-solid" class="mr-2 w-3 h-3"/>
                                                                    </div>


                                                                </div>
                                                            </div>


                                                        </td>
                                                        <td class="py-4 px-6">

                                                            {{ trans.transporter.last_legal_name }}
                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="trans.transport_driver_vehicle">
                                                                <div v-for="driver_vehicle of trans.transport_driver_vehicle">
                                                                    {{ driver_vehicle.vehicle.reg_no }}
                                                                </div>
                                                            </div>
                                                            <div v-else>
                                                                No vehicle
                                                            </div>

                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">

                                                            {{NiceNumber(trans.transport_finance.cost_price_per_ton)}}


                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">

                                                             {{NiceNumber(trans.transport_finance.selling_price_per_ton)}}

                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">


                                                            {{NiceNumber(trans.transport_finance.transport_rate_per_ton)}}

                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">

                                                            {{ NiceNumber(trans.transport_finance.gross_profit) }}


                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="trans.traders_notes">

                                                                <base-tooltip :content="trans.traders_notes">
                                                                    {{ TrunkCateText(trans.traders_notes) }}
                                                                </base-tooltip>

                                                            </div>
                                                            <div v-else>None...</div>

                                                        </td>
                                                        <td class="py-4 px-6">

                                                            <div v-if="trans.process_notes">

                                                                <base-tooltip :content="trans.process_notes">
                                                                    {{ TrunkCateText(trans.process_notes) }}
                                                                </base-tooltip>

                                                            </div>
                                                            <div v-else>None...</div>
                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">


                                                            <div
                                                                :class="trans.supplier.terms_of_payment_id === 1? 'p-2 bg-red-600 rounded': ''">
                                                                {{
                                                                    NiceNumber(trans.transport_finance.cost_price)
                                                                }}
                                                            </div>

                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">
                                                            {{NiceNumber(trans.transport_finance.transport_rate_per_ton * trans.transport_finance.weight_ton_incoming) }}
                                                        </td>

                                                        <td class="py-4 px-6 whitespace-nowrap">

                                                            <div v-if="totalWeighBridgeUpload(trans.transport_driver_vehicle)>0">
                                                                {{ NiceNumber(totalWeighBridgeUpload(trans.transport_driver_vehicle*trans.transport_finance.selling_price_per_ton))}}
                                                            </div>
                                                            <div v-else>
                                                                {{NiceNumber(trans.transport_finance.selling_price_per_ton*trans.transport_finance.weight_ton_incoming)}}
                                                            </div>
                                                        </td>

                                                        <td class="py-4 px-6 whitespace-nowrap">
                                                            {{ trans.transport_finance.gross_profit_percent }} %
                                                        </td>
                                                        <td class="py-4 px-6 whitespace-nowrap">
                                                            {{NiceNumber(trans.transport_finance.gross_profit_per_ton) }}
                                                        </td>


                                                    </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <table class="ml-3 w-2/3 mt-5 divide-y divide-gray-300">
                                                <thead>
                                                <tr>

                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        No Loads
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Planned Tons
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Weight Uploaded
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Weight Offloaded
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Cost Price
                                                    </th>

                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Transport Cost
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Other Costs
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Selling Price
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        Gross Profit
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        GP %
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">


                                                <tr class="hover:bg-indigo-200 focus-within:bg-indigo-200">
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                                                        {{no_loads}}

                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{planned_tons}}

                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                       {{weight_uploaded}}


                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{weight_offloaded}}


                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{NiceNumber(cost_price)}}

                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{NiceNumber(trans_cost)}}


                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{NiceNumber(other_costs)}}

                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{NiceNumber(selling_price)}}

                                                    </td>

                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">

                                                        {{NiceNumber(gp)}}


                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">
                                                        {{gp_perc}} %
                                                    </td>

                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div v-else>
                                            No data over the period...
                                        </div>

                                    </div>


                                </div>
                            </div>


                        </div>

                        <div class="mt-5 flex items-center justify-center">

                            <PaginationModified :links="transport_trans.links"/>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
