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

    axios.get(route('props.vehicle_slide_over'),).then((res) => {
        vehicleSlideProps.value = res.data['vehicle_types'];
    });

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


    form.put(route('transport_driver_vehicle.update.load', props.driver_vehicle.id),
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

   // await getComponentProps();
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
                            <div >
                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">weighbridge_upload_weight</label>
                                    <TextInput
                                        id="line_1"
                                        v-model="form.weighbridge_upload_weight"
                                        type="number"
                                        class="mt-1 block w-32"
                                    />
                                    <!--                                    <InputError class="mt-2" :message="form.errors.line_1"/>-->
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">weighbridge_offload_weight</label>
                                    <TextInput
                                        id="weighbridge_offload_weight"
                                        v-model="form.weighbridge_offload_weight"
                                        type="number"
                                        class="mt-1 block w-32"
                                    />
                                    <!--                                    <InputError class="mt-2" :message="form.errors.line_1"/>-->
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

            <div >
                <div v-if="props.driver_vehicle == null">
                   <div>Error driver vehicle not created.</div>
                </div>
                <div v-else>
                    <SecondaryButton @click="updateDriverVehicle" class="mt-1 bg-green-400">
                        Save
                    </SecondaryButton>
                </div>
            </div>
        </div>

</template>

