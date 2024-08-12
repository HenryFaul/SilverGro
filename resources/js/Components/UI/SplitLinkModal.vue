<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";
import {CheckIcon, ChevronUpDownIcon, PaperClipIcon} from '@heroicons/vue/20/solid';
import {Switch, SwitchGroup, SwitchLabel,Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue'
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';

import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';

//let addressApi = ref();
const emit = defineEmits(['close']);

let props = defineProps({

    selected_id:1,
    mq_trans_id: Number,
    link_type_id: Number,
    show: false,
    closeable: true,
});

let pcQuery = ref('');
let scQuery = ref('');
let splitQuery = ref('');

let contractLinkModalProps = ref(null);
let contractLinkModalPropsSc = ref(null);
let contractLinkModalPropsSplit = ref(null);


const filteredPc = computed(() =>
    pcQuery.value === ''
        ? contractLinkModalProps.value['transport_trans']
        : contractLinkModalProps.value['transport_trans'].filter((contract) => {
            return  (contract.id >= pcQuery.value)
        })
);

const filteredSc = computed(() =>
    scQuery.value === ''
        ? contractLinkModalPropsSc.value['transport_trans']
        : contractLinkModalPropsSc.value['transport_trans'].filter((contract) => {
            return  (contract.id >= scQuery.value)
        })
);

const filteredSplits = computed(() =>
    scQuery.value === ''
        ? contractLinkModalPropsSplit.value['transport_trans']
        : contractLinkModalPropsSplit.value['transport_trans'].filter((contract) => {
            return  (contract.id >= scQuery.value)
        })
);



const getComponentProps = () => {
    //props.trade_slide_over

    //props.all_products.find(element => element.id === props.transaction.product_id)

    axios.get(route('props.contract_link_split_primary'),).then((res) => {
        contractLinkModalProps.value = res.data;
       form.to_link_id = res.data['transport_trans'][0];

    });

    axios.get(route('props.contract_link_sc_modal'),).then((res) => {
        contractLinkModalPropsSc.value = res.data;
        form.to_link_id_sc = res.data['transport_trans'][0];

    });

    axios.get(route('props.contract_link_split_primary'),).then((res) => {
        contractLinkModalPropsSplit.value = res.data;
        form.to_link_id_split = res.data['transport_trans'][0];

    });

};

const close = () => {
    emit('close');
};



onUnmounted(async () => {

})

onBeforeMount(async () => {
    await getComponentProps();
});




const form = useForm({

    mq_trans_id: props.mq_trans_id,
    to_link_id:null,
    to_link_id_sc:null,
    to_link_id_split:null,
    link_type_id:props.link_type_id

});

const createTransLink = () => {

     form.post(route('trans_link.split.store'), {
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
        <dialog-modal :show="show"
                      :closeable="closeable"
                      @close="close">


            <template #content>
                <div>

                    <div class="" v-if=" contractLinkModalProps != null && contractLinkModalPropsSc != null">

                        <form class="w-full">


<!--                            'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
                            'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
                            'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
                            'traders_notes','operations_alert_notes'-->


                            <div  class="text-xl mb-2 text-indigo-400">Link Split Trades</div>

                            <div :class="borderClass">

                                <div class="mt-3"> </div>

                                <div v-if="link_type_id ===5">
                                    <div class="mt-3">

                                        <div>
                                            <label
                                                class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Primary Split Contracts:</label>
                                        </div>

                                        <Combobox as="div" v-model="form.to_link_id" >
                                            <div class="relative mt-2">
                                                <ComboboxInput
                                                    class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                    @change="splitQuery = $event.target.value"
                                                    :display-value="(type) => type?.id"/>
                                                <ComboboxButton
                                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                       aria-hidden="true"/>
                                                </ComboboxButton>

                                                <ComboboxOptions
                                                    v-if="filteredSplits.length > 0"
                                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                    <ComboboxOption
                                                        v-for="contract in filteredSplits"
                                                        :key="contract.id" :value="contract" as="template"
                                                        v-slot="{ active, selected }">
                                                        <li :class="['relative cursor-default select-none py-2 pl-3 pr-9 text-xs', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['', selected && 'font-semibold']">
                                                                  MQ:{{ contract.id }} (Old MQ:{{contract.old_id}}) {{contract.customer.last_legal_name}} {{contract.supplier.last_legal_name}} {{contract.product.name}} {{contract.transport_load.no_units_incoming}}
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

                                    </div>

                                    <div class="mt-3">

<!--                                        <div class="m-1 p-1">
                                            <div>
                                                <div class="px-4 sm:px-0">
                                                    <h3 class="text-base font-semibold leading-7 text-gray-900">PC Contract details</h3>
                                                    <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Selected PC will be linked to the underlying MQ.</p>
                                                </div>
                                                <div class="mt-6 border-t border-gray-100">
                                                    <dl class="divide-y divide-gray-100">
                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Customer Name</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{form.to_link_id.customer.last_legal_name}}</dd>
                                                        </div>

                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Supplier Name</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{form.to_link_id.supplier.last_legal_name}}</dd>
                                                        </div>
                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">ID</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">PC:{{form.to_link_id.id}} (PC old:{{form.to_link_id.old_id}})</dd>
                                                        </div>
                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Transport Date Earliest</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{form.to_link_id.transport_date_earliest}}</dd>
                                                        </div>
                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Product</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{form.to_link_id.product.name}}</dd>
                                                        </div>
                                                        <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Qty Incoming</dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{form.to_link_id.transport_load.no_units_incoming}}</dd>
                                                        </div>

                                                    </dl>
                                                </div>
                                            </div>
                                        </div>-->


                                    </div>
                                </div>

                            </div>


                        </form>

                    </div>

                    <div v-else>
                        Loading...
                    </div>

                </div>


            </template>

            <template #footer>

                <div >
                    <SecondaryButton @click="createTransLink" class="bg-red-400">
                        Create
                    </SecondaryButton>
                </div>

                <SecondaryButton class="ml-1" @click="close()">
                    Close
                </SecondaryButton>

            </template>
        </dialog-modal>
    </div>

</template>
