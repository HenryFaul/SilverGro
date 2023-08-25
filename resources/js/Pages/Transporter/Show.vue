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
    first_name: props.transporter == null ? '' : props.transporter.first_name,
    last_legal_name: props.transporter == null ? '' : props.transporter.last_legal_name,
    nickname: props.transporter == null ? '' : props.transporter.nickname,
    title: props.transporter == null ? '' : props.transporter.title,
    job_description: props.transporter == null ? '' : props.transporter.job_description,
    id_reg_no: props.transporter == null ? '' : props.transporter.id_reg_no,
    is_active: props.transporter == null ? 1 : props.transporter.is_active,
    terms_of_payment_id: props.transporter == null ? '' : props.transporter.terms_of_payment_id,
    account_number: props.transporter == null ? '' : props.transporter.account_number,
    is_git: props.transporter == null ? 1 : props.transporter.is_git,
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

const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_update_transporter = computed(() => usePage().props.roles_permissions.permissions.includes("update_transporter"));


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
                            <form>
                                <div class="text-lg mb-4 text-indigo-400">General details</div>
                                <div class="space-y-12">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Static Information</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Static Transporter information.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.title" :disabled="editDisabled" type="text" name="title" id="title"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.title"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.first_name" :disabled="editDisabled" type="text" name="first_name" id="first_name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.first_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="last_legal_name" class="block text-sm font-medium leading-6 text-gray-900">Last / Legal name</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.last_legal_name" :disabled="editDisabled" type="text" name="last_legal_name" id="last_legal_name" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.last_legal_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="nickname" class="block text-sm font-medium leading-6 text-gray-900">Nick name</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.nickname" type="text" :disabled="editDisabled" name="nickname" id="nickname" autocomplete="nickname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.nickname"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Id/Reg no</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.id_reg_no" type="text" :disabled="editDisabled" name="id_reg_no" id="id_reg_no" autocomplete="id_reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.id_reg_no"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                                <div class="mt-2">
                                                    <select v-model="Form.is_active"
                                                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option :value=1>Active</option>
                                                        <option :value=0>Inactive</option>
                                                    </select>

                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.is_active"/>
                                            </div>



                                            <div class="sm:col-span-6">
                                                <label for="comments" class="block text-sm font-medium leading-6 text-gray-900">Comments</label>
                                                <AreaInput
                                                    id="comments"
                                                    :rows=6
                                                    placeholder="Optional comments..."
                                                    v-model="Form.comment"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :disabled="editDisabled"
                                                />
                                                <InputError class="mt-2" :message="Form.errors.comment"/>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Additional Information</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Transporter additional information.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Account number</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.account_number" type="text"
                                                           :disabled="editDisabled"
                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    <InputError class="mt-2" :message="Form.errors.account_number"/>

                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Payment terms</label>
                                                <div class="mt-2">

                                                    <select v-model="Form.terms_of_payment_id"
                                                            class="input-filter-l block  w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option v-for="n in props.terms_of_payments" :key="n.id" :value="n.id">
                                                            {{n.value}}
                                                        </option>
                                                    </select>
                                                    <InputError class="mt-2" :message="Form.errors.terms_of_payments"/>

                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Is Git</label>
                                                <div class="mt-2">

                                                    <select v-model="Form.is_git"
                                                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option :value=1>Active</option>
                                                        <option :value=0>Inactive</option>
                                                    </select>

                                                    <InputError class="mt-2" :message="Form.errors.is_git"/>
                                                </div>
                                            </div>






                                        </div>
                                    </div>


                                </div>

                                <div class="mt-6 flex items-center justify-end gap-x-6">

                                    <SecondaryButton v-if="can_update_transporter" class="m-1" @click="toggleEdit">
                                        Edit
                                    </SecondaryButton>

                                    <SecondaryButton v-if="!editDisabled && can_update_transporter" @click="updateTransporter" class="m-1">
                                        Save
                                    </SecondaryButton>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </AppLayout>
</template>
