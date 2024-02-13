<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch, inject} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage, Link} from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import AddressModal from "@/Components/UI/AddressModal.vue";
import ContactModal from "@/Components/UI/ContactModal.vue";
import NumberContactDetailModal from "@/Components/UI/NumberContactDetailModal.vue";
import EmailContactDetailModal from "@/Components/UI/EmailContactDetailModal.vue";
import {EnvelopeIcon, PhoneIcon} from '@heroicons/vue/20/solid'
import {PhotoIcon, UserCircleIcon} from '@heroicons/vue/24/solid'

const swal = inject('$swal');

const props = defineProps({
    custom_reports: Object,
    model_data: Object
});

const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_update_customer = computed(() => usePage().props.roles_permissions.permissions.includes("update_customer"));

let customReportForm = useForm({
    name: null,
    comment: null,
});

//['id','created_by_id','report_id','class_name','attribute_name','comment','is_active']

let customReportModelForm = useForm({
    class_name: null,
    attribute_name: null,
    comment: null,
    report_id: 1
});

const createReport = () => {

    customReportForm.post(route('custom_report.store'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('created');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};


const createModel = () => {

    customReportModelForm.post(route('custom_report_model.store'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('created');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};


const FilterAttributes = () => {

    if (customReportModelForm.class_name != null) {
        for (let i = 0; i < props.model_data.data.length; i++) {

            if(props.model_data.data[i].name === customReportModelForm.class_name){
                console.log(props.model_data.data[i].name)
                selectedAttributes.value = props.model_data.data[i].attributes;
            }
        }
    }
};

let selectedAttributes = ref(null);
let reportAttributes = ref(null);


const filterAttributes = computed(() =>

         props.custom_reports.filter((report) => {
            if(report.id === customReportModelForm.report_id){

                return report;
            }
        })
);



</script>

<template>
    <AppLayout title="Customer">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Report Generator
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Add Report</div>

                            <form class="flex w-1/2 h-full flex-col overflow-y-scroll">
                                <div class="flex-1">
                                    <!-- Divider container -->
                                    <div
                                        class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                        <!-- First name -->
                                        <div
                                            class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5">Name</label>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <input v-model="customReportForm.name" type="text" name="first_name"
                                                       id="first_name" autocomplete="given-name"
                                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                <InputError class="mt-2" :message="customReportForm.errors.name"/>

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
                                                    v-model="customReportForm.comment"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                />
                                                <InputError class="mt-2" :message="customReportForm.errors.comment"/>

                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                                    <div class="flex justify-end space-x-3">

                                        <button type="button" @click="createReport"
                                                class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                            Create Report
                                        </button>
                                    </div>
                                </div>
                            </form>


                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Add attribute to report</div>

                            <div class="w-72">
                                <div v-if="custom_reports != null" class="mt-2">


                                    <select v-model="customReportModelForm.report_id"
                                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option
                                            v-for="n in custom_reports"
                                            :key="n.id" :value="n.id">
                                            {{ n.name }}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" :message="customReportModelForm.errors.report_id"/>
                                </div>
                                <div v-else> null</div>
                            </div>

                            <div class=" mt-3 w-72">
                                <div v-if="custom_reports != null" class="mt-2">

                                    <select v-model="customReportModelForm.class_name" @change="FilterAttributes"
                                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option
                                            v-for="n in model_data.data"
                                            :key="n.name" :value="n.name">
                                            {{ n.name }}
                                        </option>
                                    </select>
                                </div>
                                <div v-else> null</div>
                            </div>


                            <div class="mt-3 w-72">
                                <div v-if="custom_reports != null" class="mt-2">

                                    <select v-model="customReportModelForm.attribute_name"
                                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option
                                            v-for="n in selectedAttributes"
                                            :key="n" :value="n">
                                            {{ n }}
                                        </option>
                                    </select>
                                </div>
                                <div v-else> null</div>
                            </div>

                            <button type="button" @click="createModel"
                                    class="inline-flex mt-3 justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Add attribute
                            </button>

                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="text-lg mb-2 text-indigo-400">Attributes linked to report</div>

                        <div class="m-3 p-3">

                            <ul class="list-disc">
                                <li v-for="n in filterAttributes[0].custom_report_models"
                                    :key="n" >
                                    {{n.attribute_name}}
                                </li>
                            </ul>

                        </div>

                    </div>

                </div>


            </div>
        </div>
    </AppLayout>
</template>
