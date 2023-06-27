<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationModified from "@/Components/UI/PaginationModified.vue";
import BaseTooltip from "@/Components/UI/BaseTooltip.vue";
import {useForm} from "@inertiajs/vue3";
import {onMounted, watch} from "vue";
import {debounce} from "lodash";
import Icon from "@/Components/Icon.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    transport_trans: Object,
    filters: Object,
    weight_uploaded: Number,
    planned_tons_in: Number,
    planned_tons_out: Number,
    weight_offloaded: Number,
    cost_price: Number,
    trans_cost: Number,
    other_costs: Number,
    selling_price: Number,
    gp: Number,
    gp_perc: Number,

});

const format = () => {
    const _date = new Date(Form.date)
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const Form = useForm({

    date: props.filters.date ?? new Date().toISOString().substr(0, 10),
    show: props.filters.show ?? 1,
})

onMounted(() => {

})

let addDay = () => {
    const result = new Date (Form.date);
    result.setDate(result.getDate() + 1);
    Form.date = result.toISOString().substr(0, 10);
};

let lessDay = () => {
    const result = new Date (Form.date);
    result.setDate(result.getDate() - 1);
    Form.date = result.toISOString().substr(0, 10);

};

let filter = debounce(() => {

    Form.get(
        route('planning.diary'),
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

const card_style = "bg-yellow-100 h-36 overflow-hidden shadow-xl sm:rounded-lg p-2 hover:bg-indigo-200 focus-within:bg-indigo-200 overflow-scroll";
const card_style_cod = "bg-orange-300 h-36 overflow-hidden shadow-xl sm:rounded-lg  p-2 hover:bg-indigo-200 focus-within:bg-indigo-200 overflow-scroll";

//HelperFunctions
let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
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

const isPaymentOverdue = (driver_vehicles) => {

    if(Array.isArray(driver_vehicles)){
        return  false;
    }

    driver_vehicles.forEach(function (arrayItem) {
        var paymentStatus = arrayItem.is_payment_overdue;

        if (paymentStatus === 1){
            return true;
        }
    });

    return false;
}

const toolTipGen = (Message, Truck) => {
    return Message + " truck: " + (Truck + 1) + ".";
}

const isMillAlert = (Warnings) => {

    Warnings.forEach(function (arrayItem) {
        var entity = arrayItem.status_entity.entity;
        var type = arrayItem.status_type.type;

        if(entity === 3){
            return true;
        }
    });
    return false;
}

const isQualityAlert = (Warnings) => {

    Warnings.forEach(function (arrayItem) {
        var entity = arrayItem.status_entity.entity;
        var type = arrayItem.status_type.type;

        if(entity === 5){
            return true;
        }
    });
    return false;
}

const contractName = (trans) => {

let name = "";

if(trans != null){

    if(trans.contract_type.id === 4){

        if(trans.deal_ticket.old_id != null){
           name =  trans.deal_ticket.old_id;
        }else{
            name =  trans.deal_ticket.id;
        }

    }else {

        if(trans.transport_invoice.old_id != null){
            name =  trans.transport_invoice.old_id;
        }else{
            name =  trans.transport_invoice.id;
        }

    }

    return  trans.contract_type.name+":"+name;
}
else return "N/A";

}

const invoiceNo = (trans) => {

    let name = "";
    if(trans != null){
        if(trans.transport_invoice.old_id != null){
            name =  trans.transport_invoice.old_id;
        }else{
            name =  trans.transport_invoice.id;
        }
        return name;
        }

    else return "N/A";

}

const niceWeighUpOrOutgoing = (trans) => {

   let weighBridgeUpload = totalWeighBridgeUpload(trans.transport_driver_vehicle);

    if (weighBridgeUpload > 0){
        return weighBridgeUpload;
    }
    else {

        return trans.transport_finance.weight_ton_outgoing;
    }

}

const niceWeighOffOrOutgoing = (trans) => {

    let weighBridgeOffload = totalWeighBridgeOffload(trans.transport_driver_vehicle);

    if (weighBridgeOffload > 0){
        return weighBridgeOffload;
    }
    else {

        return trans.transport_finance.weight_ton_outgoing;
    }

}


</script>

<template>
    <AppLayout title="Diary">
        <template #header>
            <h2 class="font-semibold text-xl   text-gray-800 leading-tight">
                Diary
            </h2>
        </template>

        <div class="py-1">
            <div class="">
                <div class="bg-gray-50 overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="flex flex-row m-2 p-2">

                        <div class="basis-1/2">
                            <div class="flex">


                                <button @click="lessDay()" type="button"
                                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    -
                                </button>

                                <div class="">
                                    <VueDatePicker style="width: 250px;" v-model="Form.date" :format="format"
                                                   :teleport="true"></VueDatePicker>
                                </div>
<!--                                <input type="date" v-model="Form.date"
                                       class="block ml-2 mr-2  lg:w-1/6 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                       id="birthday" name="birthday">-->

                                <button @click="addDay()" type="button"
                                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    +
                                </button>

                            </div>
                        </div>

                        <div class="basis-1/4">
                            <div class="mt-2">

                               <div class="flex">
                                   <div class="m-2">Per page: </div>
                                   <select v-model="Form.show"
                                           class="input-filter-l block w-3/12 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                       <option :value=1>1</option>
                                       <option :value=5>5</option>
                                       <option :value=10>10</option>
                                       <option :value=25>25</option>
                                       <option :value=100>100</option>

                                   </select>
                               </div>

                            </div>
                        </div>

                        <div class="basis-1/4">
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <button type="button"
                                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Add trans
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="p-2 m-2">

                        <div class="px-1 sm:px-3 lg:px-2">

                            <div class="flow-root">

                                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

                                        <div v-if="transport_trans.data.length > 0">
                                            <table class="min-w-full">
                                                <thead>
                                                <tr>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        DT
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        SUPPLY
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        CUSTOMER
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        TRANSPORT
                                                    </th>
                                                    <th scope="col"
                                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                        NOTES
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-sm">
                                                <tr v-for="trans in transport_trans.data" :key="trans.id">
                                                    <td class="whitespace-nowrap">

                                                        <div
                                                            :class="card_style">

                                                            <div class="flex">

                                                                <div class="font-bold">
                                                                    <div>
                                                                        {{ contractName(trans) }}
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span>GP: </span> <span>{{NiceNumber(trans.transport_finance.gross_profit)}}</span>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span>Invoice: </span> <span>{{invoiceNo(trans)}}</span>

                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <div class="bg-red-600 rounded text-white"  v-if="!trans.include_in_calculations">
                                                                            Excl calc
                                                                        </div>
                                                                        <div v-else>
                                                                            Inc calc
                                                                        </div>
                                                                    </div>

                                                                </div>


                                                            </div>


                                                        </div>

                                                    </td>
                                                    <td class="  ">
                                                        <div
                                                            :class="trans.supplier.terms_of_payment_id === 1 ? card_style_cod: card_style">

                                                            <div class="flex">

                                                                <div class="basis-3/4">

                                                                    <div class="font-bold">
                                                                        {{ trans.supplier.last_legal_name }}

                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <div class="flex">

                                                                            <div class="basis-1/2">
                                                                                {{ trans.product.name }}
                                                                            </div>

                                                                            <div class="basis-1/4 whitespace-nowrap">
                                                                                {{ NiceNumber(trans.transport_finance.cost_price_per_ton) }}
                                                                            </div>

                                                                            <div class="basis-1/4 ml-3 whitespace-nowrap">

                                                                                {{niceWeighUpOrOutgoing(trans)}} Tons
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span class="font-bold">Loading hours: </span> <span>{{trans.transport_job.offloading_hours_from.name}}</span>
                                                                    </div>
                                                                    <div class="mt-1" v-if="trans.supplier_notes">
                                                                        <span class="font-bold">Notes: </span> <span>
                                                                     {{trans.supplier_notes}}
                                                                    </span>
                                                                    </div>
                                                                    <div class="mt-1"  v-else>
                                                                        <span class="font-bold">Notes: </span> <span>None...</span>
                                                                    </div>

                                                                </div>

                                                                <div class="basis-1/4">

                                                                    <div class="">

                                                                        <div class="flex-row">
                                                                            <button
                                                                                    class="block w-10 h-10 ml-auto bg-indigo-300 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                                <div v-if="trans.transport_driver_vehicle">
                                                                                    <div
                                                                                        v-for="(item,index) of trans.transport_driver_vehicle">


                                                                                        <div v-if="item.is_delivered">
                                                                                            <div v-if="item.is_delivered">
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
                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                                <div v-if="isMillAlert(trans.transport_status)">
                                                                                    <base-tooltip  content="Mill alert."><icon  name="building" class="mr-3 w-6 h-6 fill-green-300 animate-pulse"/> </base-tooltip>
                                                                                </div>

                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto  bg-indigo-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                            </button>
                                                                        </div>


                                                                    </div>


                                                                </div>

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="  ">
                                                        <div
                                                            :class="card_style">

                                                            <div class="flex">

                                                                <div class="basis-3/4">

                                                                    <div class="font-bold">
                                                                        {{ trans.customer.last_legal_name }}

                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <div class="flex">

                                                                            <div class="basis-1/2 whitespace-nowrap">
                                                                                {{NiceNumber(trans.transport_finance.selling_price_per_ton)}}
                                                                            </div>

                                                                            <div class="basis-1/2 ml-6">
                                                                                {{niceWeighOffOrOutgoing(trans)}} Tons
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span class="font-bold">Offloading hours: </span> <span>{{trans.transport_job.offloading_hours_to.name}}</span>
                                                                    </div>
                                                                    <div class="mt-1" v-if="trans.customer_notes">
                                                                        <span class="font-bold">Notes: </span>
                                                                        <span>
                                                                            {{trans.customer_notes}}
                                                                    </span>
                                                                    </div>
                                                                    <div class="mt-1"  v-else>
                                                                        <span class="font-bold">Notes: </span> <span>None...</span>
                                                                    </div>

                                                                </div>

                                                                <div class="basis-1/4">

                                                                    <div class="">

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto  bg-indigo-300 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                                <div v-if="trans.transport_driver_vehicle">
                                                                                    <div
                                                                                        v-for="(item,index) of trans.transport_driver_vehicle">


                                                                                        <div v-if="item.is_loaded">
                                                                                            <div v-if="item.is_loaded">
                                                                                                <base-tooltip
                                                                                                    :content='toolTipGen("Loaded",index)'>
                                                                                                    <icon name="truck"
                                                                                                          class="mr-3 w-6 h-6 fill-green-300 animate-pulse"/>
                                                                                                </base-tooltip>

                                                                                            </div>

                                                                                        </div>

                                                                                        <div v-else>
                                                                                            <icon name="cross-solid" class="mr-2 w-3 h-3"/>
                                                                                        </div>


                                                                                    </div>
                                                                                </div>
                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                                <div v-if="isQualityAlert(trans.transport_status)">
                                                                                    <base-tooltip  content="Quality Alerty"><icon  name="building" class="mr-3 w-6 h-6 fill-yellow-300  animate-pulse"/> </base-tooltip>
                                                                                </div>

                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto  bg-indigo-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                                <div v-if="isPaymentOverdue(trans.transport_driver_vehicle)">
                                                                                    <base-tooltip  content="Payment overdue."><icon  name="dollar" class="mr-3 w-6 h-6 fill-red-500  animate-pulse"/> </base-tooltip>
                                                                                </div>
                                                                            </button>
                                                                        </div>


                                                                    </div>


                                                                </div>

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="  ">
                                                        <div
                                                            :class="card_style">

                                                            <div class="flex">

                                                                <div class="basis-3/4">

                                                                    <div class="flex">
                                                                        <div class="font-bold basis-3/4">
                                                                            {{ trans.transporter.last_legal_name }}
                                                                        </div>

                                                                        <div class="font-bold basis-1/4">

                                                                            <div v-if="trans.transport_driver_vehicle">
                                                                                <div v-for="driver_vehicle of trans.transport_driver_vehicle">
                                                                                    <span>Reg #: </span>
                                                                                    <span >{{ driver_vehicle.vehicle.reg_no }} </span>
                                                                                </div>

                                                                            </div>
                                                                            <div v-else>
                                                                                <span>Reg #: </span>
                                                                                <span >N/A </span>
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                    <div class="mt-1">
                                                                        <div class="flex">

                                                                            <div class="basis-1/2">
                                                                                <span class="font-bold">Rate/Ton: </span> <span>{{trans.transport_finance.transport_rate_per_ton}}</span>
                                                                            </div>

                                                                            <div class="basis-1/2">
                                                                                <span class="font-bold">Load Rate: </span> <span>{{trans.transport_finance.transport_rate_per_ton}}</span>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-1" v-if="trans.customer_notes">
                                                                        <span class="font-bold">Notes: </span> <span>
                                                                        {{trans.customer_notes}}
                                                                    </span>
                                                                    </div>
                                                                    <div class="mt-1"  v-else>
                                                                        <span class="font-bold">Notes: </span> <span>None...</span>
                                                                    </div>

                                                                </div>

                                                                <div class="basis-1/4">

                                                                    <div class="">

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto  bg-indigo-300 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                            </button>
                                                                        </div>

                                                                        <div class="flex-row">
                                                                            <button
                                                                                class="block w-10 h-10 ml-auto  bg-indigo-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">

                                                                            </button>
                                                                        </div>


                                                                    </div>


                                                                </div>

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class=" ">
                                                        <div
                                                            :class="card_style">
                                                            <div class="">

                                                                <div class="flex-row">
                                                                    <span class="font-bold">Process notes: </span>
                                                                    <span v-if="trans.process_notes">
                                                                       {{ trans.process_notes }}
                                                                    </span>
                                                                    <span v-else >None...</span>
                                                                </div>

                                                                <div class="flex-row mt-3">
                                                                    <span class="font-bold">Traders notes: </span>
                                                                    <span v-if="trans.process_notes">
                                                                    {{ trans.traders_notes }}
                                                                    </span>
                                                                    <span v-else >None...</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table class="ml-3 w-2/3 mt-5 divide-y divide-gray-300">
                                                <thead>
                                                <tr>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        TONS IN
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        TONS OUT
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        WEIGHT UPLOADED
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        WEIGHT OFFLOADED
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        COST PRICE
                                                    </th>

                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        TRANS COST
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        OTHER COSTS
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                        SELL PRICE
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">GP
                                                    </th>
                                                    <th scope="col"
                                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">GP
                                                        %
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">


                                                <tr class="hover:bg-indigo-200 focus-within:bg-indigo-200">
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light text-gray-900 sm:pl-0">
                                                        {{planned_tons_in}}
                                                    </td>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-light  text-gray-900 sm:pl-0">
                                                        {{planned_tons_out}}
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
