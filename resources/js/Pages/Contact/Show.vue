<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch, inject} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
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
    contact_type: Object,
    linked_customer: Object,
    linked_supplier: Object,
    linked_transporter: Object
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
    contactForm.put(route('contact.update', props.contact.id),
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

const relatedClass = ref('App\\Models\\Customer');
const relatedClassContact = ref('App\\Models\\Contact');

const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_update_contact = computed(() => usePage().props.roles_permissions.permissions.includes("update_contact"));


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
                            <form>
                                <div class="text-lg mb-4 text-indigo-400">General details</div>
                                <div class="space-y-12">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Static Information</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Static Contact information.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.title" :disabled="editDisabled" type="text" name="title" id="title"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="contactForm.errors.title"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.first_name" :disabled="editDisabled" type="text" name="first_name" id="first_name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="contactForm.errors.first_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="last_legal_name" class="block text-sm font-medium leading-6 text-gray-900">Last / Legal name</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.last_legal_name" :disabled="editDisabled" type="text" name="last_legal_name" id="last_legal_name" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="contactForm.errors.last_legal_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="nickname" class="block text-sm font-medium leading-6 text-gray-900">Nick name</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.nickname" type="text" :disabled="editDisabled" name="nickname" id="nickname" autocomplete="nickname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="contactForm.errors.nickname"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Id/Reg no</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.id_reg_no" type="text" :disabled="editDisabled" name="id_reg_no" id="id_reg_no" autocomplete="id_reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="contactForm.errors.id_reg_no"/>
                                            </div>



                                            <div class="sm:col-span-6">
                                                <label for="comments" class="block text-sm font-medium leading-6 text-gray-900">Comments</label>
                                                <AreaInput
                                                    id="comments"
                                                    :rows=6
                                                    placeholder="Optional comments..."
                                                    v-model="contactForm.comment"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :disabled="editDisabled"
                                                />
                                                <InputError class="mt-2" :message="contactForm.errors.comment"/>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Additional Information</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Contact additional information.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Job Description</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.job_description" type="text"
                                                           :disabled="editDisabled"
                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    <InputError class="mt-2" :message="contactForm.errors.job_description"/>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Branch</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.branch" type="text"
                                                           :disabled="editDisabled"
                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    <InputError class="mt-2" :message="contactForm.errors.branch"/>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Department</label>
                                                <div class="mt-2">
                                                    <input v-model="contactForm.department" type="text"
                                                           :disabled="editDisabled"
                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    <InputError class="mt-2" :message="contactForm.errors.department"/>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                                <div class="mt-2">
                                                    <select v-model="contactForm.is_active"
                                                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option :value=1>Active</option>
                                                        <option :value=0>Inactive</option>

                                                    </select>
                                                    <InputError class="mt-2" :message="contactForm.errors.is_active"/>

                                                </div>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Linked to contact</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Parties Linked.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">

                                            {{linked_supplier}}
                                            <div v-if="linked_customer" class="sm:col-span-3">
                                                <div class="mt-2">
                                                    <div>
                                                        <div class="px-4 sm:px-0">
                                                            <h3 class="text-base font-semibold leading-7 text-gray-900">Customer</h3>
                                                            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Customer Linked</p>
                                                        </div>
                                                        <div class="mt-6 border-t border-gray-100">
                                                            <dl class="divide-y divide-gray-100">
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{linked_customer.last_legal_name}}</dd>
                                                                </div>
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Access</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                        <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('customer.show',linked_customer.id)" >view</Link>

                                                                    </dd>
                                                                </div>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="linked_supplier" class="sm:col-span-3">
                                                <div class="mt-2">
                                                    <div>
                                                        <div class="px-4 sm:px-0">
                                                            <h3 class="text-base font-semibold leading-7 text-gray-900">Supplier</h3>
                                                            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Supplier Linked</p>
                                                        </div>
                                                        <div class="mt-6 border-t border-gray-100">
                                                            <dl class="divide-y divide-gray-100">
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{linked_supplier.last_legal_name}}</dd>
                                                                </div>
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Access</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                        <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('supplier.show',linked_supplier.id)" >view</Link>

                                                                    </dd>
                                                                </div>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="linked_transporter" class="sm:col-span-3">
                                                <div class="mt-2">
                                                    <div>
                                                        <div class="px-4 sm:px-0">
                                                            <h3 class="text-base font-semibold leading-7 text-gray-900">Transporter</h3>
                                                            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Transporter Linked</p>
                                                        </div>
                                                        <div class="mt-6 border-t border-gray-100">
                                                            <dl class="divide-y divide-gray-100">
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Name</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{linked_transporter.last_legal_name}}</dd>
                                                                </div>
                                                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                    <dt class="text-sm font-medium leading-6 text-gray-900">Access</dt>
                                                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                        <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transporter.show',linked_transporter.id)" >view</Link>

                                                                    </dd>
                                                                </div>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                <div class="mt-6 flex items-center justify-end gap-x-6">

                                    <SecondaryButton v-if="can_update_contact" class="m-1" @click="toggleEdit">
                                        Edit
                                    </SecondaryButton>

                                    <SecondaryButton v-if="!editDisabled && can_update_contact" @click="updateContact" class="m-1">
                                        Save
                                    </SecondaryButton>
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
