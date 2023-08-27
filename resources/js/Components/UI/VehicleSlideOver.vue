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


let vehicleForm = useForm({
    vehicle_type_id:  1,
    comment: null,
    reg_no: null,
});

const format = () => {
    const _date = new Date();
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}


onMounted(() => {
});

onBeforeMount(async () => {

   await getComponentProps();
});

let vehicleSlideProps = ref(null);

const getComponentProps = () => {

    axios.get(route('props.vehicle_slide_over'),).then((res) => {
        vehicleSlideProps.value = res.data['vehicle_types'];

    });

};


const createProduct = () => {

    vehicleForm.post(route('regular_vehicle.store'), {
        preserveScroll: true,
        onSuccess: () => {
            close();
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

let emptyErrors = computed(() => Object.keys(vehicleForm.errors).length === 0 && vehicleForm.errors.constructor === Object)
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
                                                            Regular Vehicle
                                                        </DialogTitle>
                                                        <p class="text-sm text-gray-500">Complete the Required details
                                                            to load a new Vehicle.</p>
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
                                                <!--  reg no -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                               class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Reg no</label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <input v-model="vehicleForm.reg_no" type="text" name="reg_no" id="reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                        <InputError class="mt-2" :message="vehicleForm.errors.reg_no"/>
                                                    </div>
                                                </div>

                                                <!--  vehicle type -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Vehicle type</label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <select v-model="vehicleForm.vehicle_type_id"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in vehicleSlideProps" :key="n.id" :value="n.id">{{
                                                                    n.name
                                                                }}
                                                            </option>

                                                        </select>
                                                        <InputError class="mt-2" :message="vehicleForm.errors.vehicle_type_id"/>
                                                    </div>
                                                </div>



                                                <!-- Comment -->
                                                <div
                                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Comments</label>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <AreaInput
                                                            id="comments"
                                                            :rows=6
                                                            placeholder="Optional comments..."
                                                            v-model="vehicleForm.comment"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                        />
                                                        <InputError class="mt-2" :message="vehicleForm.errors.comment"/>

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
                                                <button type="button" @click="createProduct"
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
