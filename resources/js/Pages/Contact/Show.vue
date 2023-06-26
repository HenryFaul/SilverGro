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
    contact: Object,
    contact_type: Object
});
const permissions = computed(() => usePage().props.permissions)


let contactForm = useForm({

    _method: 'PUT',
    first_name: props.contact == null ? '' : props.contact.first_name,
    last_legal_name: props.contact == null ? '' : props.contact.last_legal_name,
    nickname: props.contact == null ? '' : props.contact.nickname,
    title: props.contact == null ? '' : props.contact.title,
    job_description: props.contact == null ? '' : props.contact.job_description,
    id_reg_no: props.contact == null ? '' : props.contact.id_reg_no,
    is_active: props.contact == null ? 1 : props.contact.is_active,
    branch: props.contact == null ? '' : props.contact.branch,
    department: props.contact == null ? '' : props.contact.department,
    comment: props.contact == null ? '' : props.contact.comment,

});

const emptyErrors = computed(() => Object.keys(contactForm.errors).length === 0 && contactForm.errors.constructor === Object)


const updateContact = () => {


    contactForm.post(route('customer.update', props.customer.id), {

        preserveScroll: false,
        onSuccess: () => {

            // swal('Customer updated.');
        },
    });
};

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

const relatedClass = ref('App\\Models\\Customer');
const relatedClassContact = ref('App\\Models\\Contact');


</script>

<template>
    <AppLayout title="Contact">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contact / <span class="text-indigo-400">{{ contactForm.last_legal_name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div
                        :class="!emptyErrors ?'m-2 p-2 rounded-md rounded-md shadow-sm border border-red-500':  editDisabled ? 'm-2 p-2':'m-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500' ">
                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">System Contact</div>
                            <form class="mt-3">
                                <div class="grid grid-cols-6 gap-4">

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Title:</label>
                                        <input v-model="contactForm.title" :disabled="editDisabled" type="text"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>

                                        <InputError class="mt-2" :message="contactForm.errors.title"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">First
                                            name:</label>
                                        <input v-model="contactForm.first_name" :disabled="editDisabled" type="text"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>

                                        <InputError class="mt-2" :message="contactForm.errors.first_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Last/Legal
                                            name:</label>
                                        <input v-model="contactForm.last_legal_name" :disabled="editDisabled"
                                               type="text"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>

                                        <InputError class="mt-2" :message="contactForm.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Nickname:</label>
                                        <input v-model="contactForm.nickname" type="text" :disabled="editDisabled"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>

                                        <InputError class="mt-2" :message="contactForm.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Id/Reg
                                            no:</label>
                                        <input v-model="contactForm.id_reg_no" type="text" :disabled="editDisabled"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>

                                        <InputError class="mt-2" :message="contactForm.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Job
                                            description:</label>
                                        <input v-model="contactForm.job_description" type="text"
                                               :disabled="editDisabled"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>
                                        <InputError class="mt-2" :message="contactForm.errors.job_description"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Branch:</label>
                                        <input v-model="contactForm.branch" type="text" :disabled="editDisabled"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>
                                        <InputError class="mt-2" :message="contactForm.errors.branch"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Department:</label>
                                        <input v-model="contactForm.department" type="text" :disabled="editDisabled"
                                               class="block w-full p-2 rounded-md shadow-sm border border-gray-300 dark:border-gray-600 text-gray-500"/>
                                        <InputError class="mt-2" :message="contactForm.errors.department"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Status:</label>

                                        <select v-model="contactForm.is_active"
                                                class="input-filter-l block ml-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value=1>Active</option>
                                            <option :value=0>Inactive</option>

                                        </select>
                                        <InputError class="mt-2" :message="contactForm.errors.is_active"/>
                                    </div>


                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Comments:</label>
                                        <AreaInput
                                            id="comments"
                                            :rows=3
                                            placeholder="Optional comments..."
                                            v-model="contactForm.comment"
                                            type="text"
                                            class="mt-1 block w-1/3"
                                            :disabled="editDisabled"
                                        />
                                        <InputError class="mt-2" :message="contactForm.errors.last_legal_name"/>
                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton class="m-1" @click="toggleEdit">
                                            Edit
                                        </SecondaryButton>

                                        <SecondaryButton v-if="!editDisabled" @click="" class="m-1">
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


                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 ml-4 p-2">

                        <number-contact-detail-modal :related_id="contact.id" :contact_type="contact_type"
                                                     :related_class="relatedClassContact"
                                                     :show="viewNumberContactDetailModal" @close="closeNumberContactDetailModal" />

                        <email-contact-detail-modal :related_id="contact.id" :contact_type="contact_type"
                                                    :related_class="relatedClassContact"
                                                    :show="viewEmailContactDetailModal" @close="closeEmailDetailModal" />

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Contact Details</div>

                          <div class="mt-3">
                              <SecondaryButton @click="viewNumberContactDetail" class="ml-2" >
                                  <PhoneIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                              </SecondaryButton>

                              <SecondaryButton @click="viewEmailContactDetail" class="ml-2" >
                                  <EnvelopeIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                              </SecondaryButton>
                          </div>


                            <div class="mt-8">

                                <label class="block text-sm font-medium leading-6 text-gray-900">All
                                    emails:</label>
                                <ul class="mt-3">

                                    <li v-for="n in contact.emailable" :key="n.id" :value="n.id"
                                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                        <div class="flex flex-row">
                                            <div class="basis-1/4">
                                                <icon name="envelope" class="mr-2 w-6 h-6 fill-green-200"/>
                                            </div>
                                            <div class="basis-1/2">{{ n.value }}</div>
                                            <div class="basis-1/2">
                                                <div v-if="n.comment">({{ n.comment }})</div>
                                            </div>
                                            <div class="basis-1/4">
                                                <SecondaryButton class="m-1">
                                                    Delete
                                                </SecondaryButton>
                                            </div>


                                        </div>

                                    </li>

                                </ul>
                            </div>

                            <div class="mt-5">

                                <label class="block text-sm font-medium leading-6 text-gray-900">All
                                    numbers:</label>
                                <ul class="mt-3">

                                    <li v-for="n in contact.numberable" :key="n.id" :value="n.id"
                                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                        <div class="flex flex-row">
                                            <div class="basis-1/4">
                                                <icon v-if="n.contact_detail_type_id === 1" name="phone" class="mr-2 w-6 h-6 fill-green-200"/>
                                                <icon v-if="n.contact_detail_type_id === 2" name="cell" class="mr-2 w-6 h-6 fill-green-200"/>
                                                <icon v-if="n.contact_detail_type_id === 3" name="document" class="mr-2 w-6 h-6 fill-green-200"/>
                                            </div>


                                            <div class="basis-1/2">({{ n.country_code }}) {{ n.value }}</div>
                                            <div class="basis-1/2"><div v-if="n.comment">({{ n.comment }})</div></div>
                                            <div class="basis-1/4">
                                                <SecondaryButton class="m-1">
                                                    Delete
                                                </SecondaryButton>
                                            </div>


                                        </div>

                                    </li>

                                </ul>
                            </div>

                        </div>

                    </div>

                </div>


            </div>
        </div>
    </AppLayout>
</template>
