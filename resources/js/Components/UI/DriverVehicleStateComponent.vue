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


//let addressApi = ref();
const emit = defineEmits(['close']);

let props = defineProps({
    transport_trans_id: Number,
    transport_job_id: Number,
    driver_vehicle: Object,
    all_drivers:Object,
    all_vehicles:Object,
    show: false,
    closeable: true,
});

const form = useForm({
    transport_trans_id: props.transport_trans_id == null ? null : props.transport_trans_id,
    transport_job_id: props.transport_job_id == null ? null : props.transport_job_id,
    regular_driver_id: props.driver_vehicle == null ? 1 : props.driver_vehicle.regular_driver_id,
    regular_vehicle_id: props.driver_vehicle == null ? 1 : props.driver_vehicle.regular_vehicle_id,
    weighbridge_upload_weight: props.driver_vehicle == null ? 0 : props.driver_vehicle.weighbridge_upload_weight,
    weighbridge_offload_weight: props.driver_vehicle == null ? 0 : props.driver_vehicle.weighbridge_offload_weight,
    is_weighbridge_variance: props.driver_vehicle == null ? false : props.driver_vehicle.is_weighbridge_variance,
    is_cancelled: props.driver_vehicle == null ? false : props.driver_vehicle.is_cancelled,
    date_cancelled: props.driver_vehicle == null ? null : props.driver_vehicle.date_cancelled,
    is_loaded: props.driver_vehicle == null ? false : props.driver_vehicle.is_loaded,
    date_loaded: props.driver_vehicle == null ? null : props.driver_vehicle.date_loaded,
    is_onroad: props.driver_vehicle == null ? false : props.driver_vehicle.is_onroad,
    date_onroad: props.driver_vehicle == null ? null : props.driver_vehicle.date_onroad,
    is_delivered: props.driver_vehicle == null ? false : props.driver_vehicle.is_delivered,
    date_delivered: props.driver_vehicle == null ? null : props.driver_vehicle.date_delivered,
    is_transport_scheduled: props.driver_vehicle == null ? false : props.driver_vehicle.is_transport_scheduled,
    date_scheduled: props.driver_vehicle == null ? null : props.driver_vehicle.date_scheduled,
    is_paid: props.driver_vehicle == null ? false : props.driver_vehicle.is_paid,
    date_paid: props.driver_vehicle == null ? null : props.driver_vehicle.date_paid,
    is_payment_overdue: props.driver_vehicle == null ? false : props.driver_vehicle.is_payment_overdue,
    traders_notes: props.driver_vehicle == null ? null : props.driver_vehicle.traders_notes,
    operations_alert_notes: props.driver_vehicle == null ? null : props.driver_vehicle.operations_alert_notes,
    driver_vehicle_loading_number: props.driver_vehicle == null ? null : props.driver_vehicle.driver_vehicle_loading_number,

});

let showDriver = ref(false);
let showVehicle = ref(false);

const toggleShowDriver = () => {
    showDriver.value =  !showDriver.value;
}

const toggleShowVehicle = () => {
    showVehicle.value =  !showVehicle.value;
}


const handleChange = () => {
    alert('changed');
}

let driverForm = useForm({
    first_name:  null,
    last_name:  null,
    cell_no:  null,
    comment: null,
});

let vehicleForm = useForm({
    vehicle_type_id:  1,
    comment: null,
    reg_no: null,
});

let vehicleSlideProps = ref(null);

const getComponentProps = () => {

};

const createProduct = () => {

    driverForm.post(route('regular_driver.store'), {
        preserveScroll: true,
        onSuccess: () => {
            driverForm.first_name = null;
            driverForm.last_name = null;
            driverForm.cell_no = null;
            driverForm.comment = null;
            toggleShowDriver();
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

const createProductVehicle = () => {

    vehicleForm.post(route('regular_vehicle.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toggleShowVehicle()
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

const deleteDriverVehicle = () => {

    if (confirm("Sure you want to delete?")) {

        form.delete(route('transport_driver_vehicle.destroy', props.driver_vehicle.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    close();
                },
                onError: (e) => {
                    close();
                    console.log(e);
                },
            }
        );

        close();
    }


};

const updateDriverVehicle = () => {


    form.put(route('transport_driver_vehicle.update.state', props.driver_vehicle.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                close();
            },
            onError: (e) => {
                console.log(e);
            },
        }
    );

};

const createDriverVehicle = () => {

    form.post(route('transport_driver_vehicle.store'), {
        preserveScroll: true,
        onSuccess: () => {
            close();
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

onBeforeMount(async () => {

    //await getComponentProps();
});

let emptyErrors = computed(() => Object.keys(form.errors).length === 0 && form.errors.constructor === Object)
let borderClass = computed(() => !emptyErrors ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-red-500' : 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray')

</script>

<template>
        <div>

            <div >
                <div>
                    <div class="">

                        <form class="w-full">
                            <div>
                                <div class="mt-3">
                                    <div class="flex col-span-2 mt-2">
                                        <SwitchGroup  as="div" class="flex m-2 items-center">
                                            <Switch @update:modelValue="updateDriverVehicle" v-model="form.is_cancelled"
                                                    :class="[form.is_cancelled ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[form.is_cancelled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Cancelled</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch @update:modelValue="updateDriverVehicle" v-model="form.is_loaded"
                                                    :class="[form.is_loaded ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[form.is_loaded ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Loaded</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                    </div>

                                    <div class="flex col-span-2 mt-2">

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch @update:modelValue="updateDriverVehicle" v-model="form.is_onroad"
                                                    :class="[form.is_onroad ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[form.is_onroad ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">On road</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch @update:modelValue="updateDriverVehicle" v-model="form.is_delivered"
                                                    :class="[form.is_delivered ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[form.is_delivered ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Delivered</span>
                                            </SwitchLabel>
                                        </SwitchGroup>
                                    </div>

                                </div>

                                <div class="mt-3">
                                    <div class="flex col-span-2 mt-2">
                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch @update:modelValue="updateDriverVehicle" v-model="form.is_transport_scheduled"
                                                    :class="[form.is_transport_scheduled ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[form.is_transport_scheduled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Scheduled</span>
                                            </SwitchLabel>
                                        </SwitchGroup>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>

            <div >

                <div v-if="props.driver_vehicle == null">
                   <div>
                       Missing driver vehicle. Contact support.
                   </div>
                </div>
                <div v-else>
<!--                    <SecondaryButton @click="updateDriverVehicle" class="ml-1 bg-green-400">
                        Upate
                    </SecondaryButton>-->
                </div>


            </div>
        </div>
</template>

