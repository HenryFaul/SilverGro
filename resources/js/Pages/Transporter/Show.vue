<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch, inject} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage} from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import {EnvelopeIcon, PhoneIcon} from '@heroicons/vue/20/solid';


import NumberContactDetailModal from "@/Components/UI/NumberContactDetailModal.vue";
import EmailContactDetailModal from "@/Components/UI/EmailContactDetailModal.vue";

const swal = inject('$swal');


const props = defineProps({
    transporter: Object,
    terms_of_payments: Object
});
const permissions = computed(() => usePage().props.permissions)

/*
'id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
    'terms_of_payment_id','account_number','comment','is_git'*/


let Form = useForm({

    _method: 'PUT',
    first_name: props.transporter == null ? '' : props.transporter.first_name,
    last_legal_name: props.transporter == null ? '' : props.transporter.last_legal_name,
    nickname: props.transporter == null ? '' : props.transporter.nickname,
    title: props.transporter == null ? '' : props.transporter.title,
    job_description: props.transporter == null ? '' : props.transporter.job_description,
    id_reg_no: props.transporter == null ? '' : props.transporter.id_reg_no,
    is_active: props.transporter == null ? 1 : props.transporter.is_active,
    terms_of_payment_id: props.transporter == null ? '' : props.transporter.terms_of_payment_id,
    account_number: props.transporter == null ? '' : props.transporter.account_number,
    is_git: props.transporter == null ? false : props.transporter.is_git,
    comment: props.transporter == null ? '' : props.transporter.comment,

});

const emptyErrors = computed(() => Object.keys(Form.errors).length === 0 && Form.errors.constructor === Object)




const updateTransporter = () => {
    Form.put(route('transporter.update', props.transporter.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
                toggleEdit();
            },
        }
    );
}

const editDisabled = ref(true);
const toggleEdit = () => {
    editDisabled.value = !editDisabled.value;
};

const viewNumberContactDetailModal = ref(false);
const viewEmailContactDetailModal = ref(false);

const viewNumberContactDetail = () => {
    viewNumberContactDetailModal.value = true;
};

const viewEmailContactDetail = () => {
    viewEmailContactDetailModal.value = true;
};

const closeNumberContactDetailModal = () => {
    viewNumberContactDetailModal.value = false;
};

const closeEmailDetailModal = () => {
    viewEmailContactDetailModal.value = false;
};

const relatedClass = ref('App\\Models\\Transporter');
const relatedClassContact = ref('App\\Models\\Contact');

const viewAddressModal = ref(false);
const currentAddress = ref(null);


const viewAddress = (address) => {
    currentAddress.value = address;
    viewAddressModal.value = true;
};

const closeModal = () => {
    viewAddressModal.value = false;
};


</script>

<template>
    <AppLayout title="Transporter">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transporter / <span class="text-indigo-400">{{ Form.last_legal_name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div
                        :class="!emptyErrors ?'m-2 p-2 rounded-md rounded-md shadow-sm border border-red-500':  editDisabled ? 'm-2 p-2':'m-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500' ">
                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transporter</div>
                            <form class="mt-3">
                                <div class="grid grid-cols-6 gap-4">

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Title:</label>
                                        <input v-model="Form.title" :disabled="editDisabled" type="text"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="Form.errors.title"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">First
                                            name:</label>
                                        <input v-model="Form.first_name" :disabled="editDisabled" type="text"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="Form.errors.first_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Last/Legal
                                            name:</label>
                                        <input v-model="Form.last_legal_name" :disabled="editDisabled"
                                               type="text"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="Form.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Nickname:</label>
                                        <input v-model="Form.nickname" type="text" :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="Form.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Id/Reg
                                            no:</label>
                                        <input v-model="Form.id_reg_no" type="text" :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="Form.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Job
                                            description:</label>
                                        <input v-model="Form.job_description" type="text"
                                               :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        <InputError class="mt-2" :message="Form.errors.job_description"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Account number:</label>
                                        <input v-model="Form.account_number" type="text"
                                               :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        <InputError class="mt-2" :message="Form.errors.account_number"/>
                                    </div>

                                    <div class="col-span-4 mt-2">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Terms of payment:</label>

                                        <select v-model="Form.terms_of_payment_id"
                                                class="input-filter-l block ml-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option v-for="n in props.terms_of_payments" :key="n.id" :value="n.id">
                                                {{n.value}}
                                            </option>

                                        </select>
                                        <InputError class="mt-2" :message="Form.errors.is_active"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Is Git:</label>

                                        <select v-model="Form.is_git"
                                                class="input-filter-l block ml-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value=1>Active</option>
                                            <option :value=0>Inactive</option>

                                        </select>
                                        <InputError class="mt-2" :message="Form.errors.is_active"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Status:</label>

                                        <select v-model="Form.is_active"
                                                class="input-filter-l block ml-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value=1>Active</option>
                                            <option :value=0>Inactive</option>

                                        </select>
                                        <InputError class="mt-2" :message="Form.errors.is_active"/>
                                    </div>


                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Comments:</label>
                                        <AreaInput
                                            id="comments"
                                            :rows=3
                                            placeholder="Optional comments..."
                                            v-model="Form.comment"
                                            type="text"
                                            class="mt-1 block w-1/3"
                                            :disabled="editDisabled"
                                        />
                                        <InputError class="mt-2" :message="Form.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton class="m-1" @click="toggleEdit">
                                            Edit
                                        </SecondaryButton>

                                        <SecondaryButton v-if="!editDisabled" @click="updateTransporter" class="m-1">
                                            Save
                                        </SecondaryButton>
                                        <SecondaryButton class="m-1">
                                            Delete
                                        </SecondaryButton>
                                    </div>

                                </div>
                            </form>
                        </div>


                    </div>

                </div>

            </div>
        </div>
    </AppLayout>
</template>
