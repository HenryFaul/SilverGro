<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";
import {CheckIcon, ChevronUpDownIcon, PaperClipIcon} from '@heroicons/vue/20/solid';
import {
    Switch,
    SwitchGroup,
    SwitchLabel,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions
} from '@headlessui/vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';

import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import {XMarkIcon} from '@heroicons/vue/24/outline';
import {LinkIcon, PlusIcon, QuestionMarkCircleIcon} from '@heroicons/vue/20/solid';

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};


let props = defineProps({
    show: false,
    closeable: true,
});

const form = useForm({
    contract_type_id: null,
    transport_date_earliest: new Date(),
    traders_notes: null,
    product_id: null,
    supplier_id: null,
    customer_id: null,
    transporter_id: null,
    billing_units_id: null,
    no_units: 0,
    transport_rate_basis_id: 1

});

const format = () => {
    const _date = new Date();
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

let tradeSlideProps = ref(null);

onMounted(() => {
});

onBeforeMount(async () => {
    getComponentProps();
});


const getComponentProps = () => {
    //props.trade_slide_over

    axios.get(route('props.trade_slide_over'),).then((res) => {
        tradeSlideProps.value = res.data;
        form.contract_type_id = res.data["contract_types"][0];
        form.product_id = res.data["products"][0];
        form.supplier_id = res.data["suppliers"][0];
        form.customer_id = res.data["customers"][0];
        form.transporter_id = res.data["transporters"][0];
        form.billing_units_id = res.data["all_billing_units"][0];
    });

};

let contractTypeQuery = ref('');

const filteredContractTypes = computed(() =>
    contractTypeQuery.value === ''
        ? tradeSlideProps.value['contract_types']
        : tradeSlideProps.value['contract_types'].filter((type) => {
            return type.name.toLowerCase().includes(contractTypeQuery.value.toLowerCase())
        })
);

let productQuery = ref('');

const filteredProducts = computed(() =>
    productQuery.value === ''
        ? tradeSlideProps.value['products']
        : tradeSlideProps.value['products'].filter((product) => {
            return product.name.toLowerCase().includes(productQuery.value.toLowerCase())
        })
);

let supplierQuery = ref('');

const filteredSuppliers = computed(() =>
    supplierQuery.value === ''
        ? tradeSlideProps.value['suppliers']
        : tradeSlideProps.value['suppliers'].filter((supplier) => {
            return supplier.last_legal_name.toLowerCase().includes(supplierQuery.value.toLowerCase())
        })
);

let customerQuery = ref('');

const filteredCustomers = computed(() =>
    customerQuery.value === ''
        ? tradeSlideProps.value['customers']
        : tradeSlideProps.value['suppliers'].filter((customer) => {
            return customer.last_legal_name.toLowerCase().includes(customerQuery.value.toLowerCase())
        })
);

let transporterQuery = ref('');

const filteredTransporters = computed(() =>
    transporterQuery.value === ''
        ? tradeSlideProps.value['transporters']
        : tradeSlideProps.value['transporters'].filter((transporter) => {
            return transporter.last_legal_name.toLowerCase().includes(transporterQuery.value.toLowerCase())
        })
);

let billingUnitsIncomingQuery = ref('');

const filteredBillingUnitsIncoming = computed(() =>
    billingUnitsIncomingQuery.value === ''
        ? tradeSlideProps.value['all_billing_units']
        : tradeSlideProps.value['all_billing_units'].filter((type) => {
            return type.name.toLowerCase().includes(billingUnitsIncomingQuery.value.toLowerCase())
        })
);


const createTransaction = () => {

    form.post(route('transport_transaction.store'), {
        preserveScroll: true,
        onSuccess: () => {

            close();
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

let emptyErrors = computed(() => Object.keys(form.errors).length === 0 && form.errors.constructor === Object)
let borderClass = computed(() => !emptyErrors ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-red-500' : 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray')

</script>


<template>
    <div>
        <TransitionRoot as="template" :show="props.show">

            <Dialog as="div" class="relative z-10" @close="close">
                <div class="fixed inset-0"/>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                            <TransitionChild as="template"
                                             enter="transform transition ease-in-out duration-500 sm:duration-700"
                                             enter-from="translate-x-full" enter-to="translate-x-0"
                                             leave="transform transition ease-in-out duration-500 sm:duration-700"
                                             leave-from="translate-x-0" leave-to="translate-x-full">
                                <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                                    <form class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                                        <div class="flex-1">
                                            <!-- Header -->
                                            <div class="bg-indigo-200 px-4 py-6 sm:px-6">
                                                <div class="flex items-start justify-between space-x-3">
                                                    <div class="space-y-1">
                                                        <DialogTitle
                                                            class="text-base font-semibold leading-6 text-gray-900">New
                                                            Trade
                                                        </DialogTitle>
                                                        <p class="text-sm text-gray-500">Complete the Required details
                                                            to load a new trade.</p>
                                                    </div>
                                                    <div class="flex h-7 items-center">
                                                        <button type="button" class="text-gray-400 hover:text-gray-500"
                                                                @click="close">
                                                            <span class="sr-only">Close panel</span>
                                                            <XMarkIcon class="h-6 w-6" aria-hidden="true"/>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Divider container -->
                                            <div
                                                class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                                <!-- No_units -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">No
                                                            units</label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <input v-model="form.no_units" type="number" name="project-name"

                                                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                        <InputError class="mt-2" :message="form.errors.no_units"/>

                                                    </div>
                                                </div>

                                                <!-- Project name -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Date</label>
                                                    </div>
                                                    <div class="sm:col-span-2">

                                                        <div>
                                                            <VueDatePicker style="width: 250px;"
                                                                           v-model="form.transport_date_earliest"
                                                                           :format="format"
                                                                           :teleport="true"></VueDatePicker>
                                                        </div>

                                                        <div class="ml-3 text-sm font-bold">
                                                            Transport date earliest
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contract Type -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Contract
                                                            Type</label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">

                                                            <Combobox as="div" v-model="form.contract_type_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="contractTypeQuery = $event.target.value"
                                                                        :display-value="(type) => type?.name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions
                                                                        v-if="filteredContractTypes.length > 0"
                                                                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="type in filteredContractTypes"
                                                                            :key="type.id" :value="type" as="template"
                                                                            v-slot="{ active, selected }">
                                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ type.name }}
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

                                                            <InputError class="mt-2" :message="form.errors['contract_type_id.id']"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Customer -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Customer</label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">

                                                            <Combobox as="div" v-model="form.customer_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="customerQuery = $event.target.value"
                                                                        :display-value="(customer) => customer?.last_legal_name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions v-if="filteredCustomers.length > 0"
                                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="customer in filteredCustomers"
                                                                            :key="customer.id" :value="customer"
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

                                                            <InputError class="mt-2" :message="form.errors.customer_id"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Supplier -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Supplier</label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">

                                                            <Combobox as="div" v-model="form.supplier_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="supplierQuery = $event.target.value"
                                                                        :display-value="(supplier) => supplier?.last_legal_name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions v-if="filteredSuppliers.length > 0"
                                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="supplier in filteredSuppliers"
                                                                            :key="supplier.id" :value="supplier"
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

                                                            <InputError class="mt-2" :message="form.errors.supplier_id"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Transporter -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Transporter</label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">


                                                            <Combobox as="div" v-model="form.transporter_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="transporterQuery = $event.target.value"
                                                                        :display-value="(transporter) => transporter?.last_legal_name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions
                                                                        v-if="filteredTransporters.length > 0"
                                                                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="transporter in filteredTransporters"
                                                                            :key="transporter.id" :value="transporter"
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

                                                            <InputError class="mt-2" :message="form.errors.transporter_id"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Product -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Product</label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">

                                                            <Combobox as="div" v-model="form.product_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="productQuery = $event.target.value"
                                                                        :display-value="(product) => product?.name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions v-if="filteredProducts.length > 0"
                                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="product in filteredProducts"
                                                                            :key="product" :value="product"
                                                                            as="template"
                                                                            v-slot="{ active, selected }">
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

                                                            <InputError class="mt-2" :message="form.errors['product_id.id']"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Billing Units -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Billing
                                                            units </label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">

                                                            <Combobox as="div" v-model="form.billing_units_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                        @change="billingUnitsIncomingQuery = $event.target.value"
                                                                        :display-value="(units) => units?.name"/>
                                                                    <ComboboxButton
                                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                                           aria-hidden="true"/>
                                                                    </ComboboxButton>

                                                                    <ComboboxOptions
                                                                        v-if="filteredBillingUnitsIncoming.length > 0"
                                                                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                                        <ComboboxOption
                                                                            v-for="units in filteredBillingUnitsIncoming"
                                                                            :key="units.id" :value="units" as="template"
                                                                            v-slot="{ active, selected }">
                                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ units.name }}
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

                                                            <InputError class="mt-2" :message="form.errors['billing_units_id.id']"/>

                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- Transport Rate -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Transport
                                                            rate basis </label>
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <div v-if="tradeSlideProps != null" class="mt-2">


                                                            <select v-model="form.transport_rate_basis_id"
                                                                    class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                                <option
                                                                    v-for="n in tradeSlideProps['all_transport_rates']"
                                                                    :key="n.id" :value="n.id">
                                                                    {{ n.name }}
                                                                </option>
                                                            </select>

                                                            <InputError class="mt-2" :message="form.errors.transport_rate_basis_id"/>
                                                        </div>
                                                        <div v-else> null</div>
                                                    </div>
                                                </div>

                                                <!-- traders_notes: -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">traders_notes:
                                                        </label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <textarea v-model="form.traders_notes" id="project-description"
                                                                  name="project-description" rows="3"
                                                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                        <InputError class="mt-2" :message="form.errors.traders_notes"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action buttons -->
                                        <div class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                                            <div class="flex justify-end space-x-3">
                                                <button type="button"
                                                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                        @click="close">Cancel
                                                </button>
                                                <button type="button" @click="createTransaction"
                                                        class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                    Create
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>

</template>
