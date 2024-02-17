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
import { EnvelopeIcon, PhoneIcon } from '@heroicons/vue/20/solid'
import { PhotoIcon, UserCircleIcon } from '@heroicons/vue/24/solid'

const swal = inject('$swal');

const props = defineProps({
    customer_parent: Object,
    invoice_basis: Object,
    terms_of_payment_basis:Object,
    terms_of_payment: Object,
    customer_rating: Object,
    staff: Object,
    contact_type: Object
});
const permissions = computed(() => usePage().props.permissions)
const emptyErrors = computed(() => Object.keys(customerForm.errors).length === 0 && customerForm.errors.constructor === Object)


let customerForm = useForm({

    _method: 'PUT',
    first_name: props.customer_parent.first_name ?? null,
    last_legal_name: props.customer_parent.last_legal_name ?? null,
    nickname: props.customer_parent.nickname ?? null,
    title: props.customer_parent.title ?? null,
    job_description: props.customer_parent.job_description ?? null,
    id_reg_no: props.customer_parent.id_reg_no ?? null,
    is_active: props.customer_parent.is_active ?? null,
    terms_of_payment_id: props.customer_parent.terms_of_payment_id ?? null,
    terms_of_payment_basis_id: props.customer_parent.terms_of_payment_basis_id ?? 1,
    invoice_basis_id: props.customer_parent.invoice_basis_id ?? null,
    customer_rating_id: props.customer_parent.customer_rating_id ?? null,
    days_overdue_allowed_id: props.customer_parent.days_overdue_allowed_id ?? null,
    is_vat_exempt: props.customer_parent.is_vat_exempt ?? null,
    is_vat_cert_received: props.customer_parent.is_vat_cert_received ?? null,
    credit_limit: props.customer_parent.credit_limit ?? null,
    credit_limit_hard: props.customer_parent.credit_limit_hard ?? null,
    comment: props.customer_parent.comment ?? null,


});


const updateCustomer = () => {
    customerForm.put(route('customer_parent.update', props.customer_parent.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
                toggleEdit();
            },
        }
    );
}

let staffForm = useForm({
    staff_id: 1,
    related_id:props.customer_parent.id ?? 1,
    related_class:'App\\Models\\CustomerParent',
});

const addStaff = () => staffForm.post(route('staff_link.store'), {
    preserveScroll: true,
    onSuccess: () => {

        swal('Staff member linked.');

    },
})

const deleteStaff = (id, name) => {

    if (confirm("Sure you want to delete " + name + "?")) {

        staffForm.staff_id = id;

        staffForm.put(route('staff_link.update', {staff_link: id}),
            {
                preserveScroll: true,
                onSuccess: () => {
                    swal('Staff member unlinked.');
                },
            }
        );

    } else {

    }
}


const viewAddressModal = ref(false);
const viewContactModal = ref(false);
const viewNumberContactDetailModal = ref(false);
const viewEmailContactDetailModal = ref(false);

const currentAddress = ref(null);
const currentContact = ref(null);

const relatedClass = ref('App\\Models\\CustomerParent');
const relatedClassContact = ref('App\\Models\\Contact');

const viewAddress = (address) => {
    currentAddress.value = address;
    viewAddressModal.value = true;
};

const viewContact = (contact) => {
    currentContact.value = contact;
    viewContactModal.value = true;
};

const viewNumberContactDetail = () => {
    viewNumberContactDetailModal.value = true;
};

const viewEmailContactDetail = () => {
    viewEmailContactDetailModal.value = true;
};

const closeModal = () => {
    viewAddressModal.value = false;
};

const closeContactModal = () => {
    viewContactModal.value = false;
};

const closeNumberContactDetailModal = () => {
    viewNumberContactDetailModal.value = false;
};

const closeEmailDetailModal = () => {
    viewEmailContactDetailModal.value = false;
};

const editDisabled = ref(true);

const toggleEdit = () => {
    editDisabled.value = !editDisabled.value;
};

const showContact = (id) => {
    router.get('customer_parent/'+3);
}

const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_update_customer = computed(() => usePage().props.roles_permissions.permissions.includes("update_customer"));


</script>

<template>
    <AppLayout title="Customer Parent">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Parent / <span class="text-indigo-400">{{ customerForm.last_legal_name }}</span>
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
                                                <p class="mt-1 text-sm leading-6 text-gray-600">Static customer information.</p>
                                            </div>

                                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.first_name" :disabled="editDisabled" type="text" name="first_name" id="first_name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.first_name"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="last_legal_name" class="block text-sm font-medium leading-6 text-gray-900">Last / Legal name</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.last_legal_name" :disabled="editDisabled" type="text" name="last_legal_name" id="last_legal_name" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.last_legal_name"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="nickname" class="block text-sm font-medium leading-6 text-gray-900">Nick name</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.nickname" type="text" :disabled="editDisabled" name="nickname" id="nickname" autocomplete="nickname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.nickname"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Id/Reg no</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.id_reg_no" type="text" :disabled="editDisabled" name="id_reg_no" id="id_reg_no" autocomplete="id_reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Customer rating</label>
                                                    <div class="mt-2">
                                                        <select v-model="customerForm.customer_rating_id" :disabled="editDisabled"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in customer_rating" :key="n.id" :value="n.id">{{
                                                                    n.value
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.customer_rating_id"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Customer status</label>
                                                    <div class="mt-2">
                                                        <select v-model="customerForm.is_active" :disabled="editDisabled"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option key="1" value="1">
                                                                Active
                                                            </option>

                                                            <option key="0" value="0">
                                                                Suspended
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.is_active"/>
                                                </div>


                                                <div class="sm:col-span-6">
                                                    <label for="comments" class="block text-sm font-medium leading-6 text-gray-900">Comments</label>
                                                    <AreaInput
                                                        id="comments"
                                                        :rows=6
                                                        placeholder="Optional comments..."
                                                        v-model="customerForm.comment"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        :disabled="editDisabled"
                                                    />
                                                    <InputError class="mt-2" :message="customerForm.errors.comment"/>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                            <div>
                                                <h2 class="text-base font-semibold leading-7 text-gray-900">Payment Information</h2>
                                                <p class="mt-1 text-sm leading-6 text-gray-600">Customer payment information.</p>
                                            </div>

                                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Credit limit</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.credit_limit" type="number"
                                                               :disabled="editDisabled"
                                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.credit_limit"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Credit Hard limit</label>
                                                    <div class="mt-2">
                                                        <input v-model="customerForm.credit_limit_hard" type="number"
                                                               :disabled="editDisabled"
                                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.credit_limit_hard"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Terms of payment basis</label>
                                                    <div class="mt-2">
                                                        <select v-model="customerForm.terms_of_payment_basis_id" :disabled="editDisabled"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in terms_of_payment_basis" :key="n.id" :value="n.id">{{
                                                                    n.value
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.terms_of_payment_id"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Terms of payment</label>
                                                    <div class="mt-2">
                                                        <select v-model="customerForm.terms_of_payment_id" :disabled="editDisabled"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in terms_of_payment" :key="n.id" :value="n.id">{{
                                                                    n.value
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.terms_of_payment_id"/>
                                                </div>

                                                <div class="sm:col-span-3">
                                                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Invoice basis</label>
                                                    <div class="mt-2">
                                                        <select v-model="customerForm.invoice_basis_id" :disabled="editDisabled"
                                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in invoice_basis" :key="n.id" :value="n.id">{{
                                                                    n.value
                                                                }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>
                                                </div>

                                            </div>
                                        </div>


                                    </div>

                                    <div class="mt-6 flex items-center justify-end gap-x-6">

                                        <SecondaryButton v-if="can_update_customer" class="m-1" @click="toggleEdit">
                                            Edit
                                        </SecondaryButton>

                                        <SecondaryButton v-if="!editDisabled && can_update_customer" @click="updateCustomer" class="m-1">
                                            Save
                                        </SecondaryButton>
                                    </div>
                                </form>
                        </div>
                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Staff allocated</div>

                            <div>
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Available
                                        staff:</label>

                                    <div class="">
                                    <select v-model="staffForm.staff_id"
                                            class="mt-2 block w-1/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option v-for="n in staff" :key="n.id" :value="n.id">{{
                                                n.first_name
                                            }}
                                        </option>
                                    </select>


                                    <SecondaryButton @click="addStaff" class="mt-2">
                                      Link (+)
                                    </SecondaryButton>

                                    </div>

                                    <InputError class="mt-2" :message="staffForm.errors.staff_id"/>
                                    <InputError class="mt-2" :message="staffForm.errors.related_id"/>

                                </div>
                            </div>

                            <form class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">Staff members
                                    linked:</label>
                                <div>


                                    <div class="flex">

                                        <ul class="w-1/3">

                                            <li v-for="n in customer_parent.staff" :key="n.id" :value="n.id"
                                                class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                                <div class="flex mt-1">
                                                    <div class="flex-none w-1/3">
                                                        <icon name="tick-circle" class="mr-2 w-6 h-6 fill-green-200"/>
                                                    </div>
                                                    <div class="flex-auto font-bold w-1/3">
                                                        {{ n.first_name }} {{ n.last_legal_name }}
                                                    </div>
                                                    <div class="flex-auto w-1/3">
                                                        <SecondaryButton @click="deleteStaff(n.id, n.first_name)">
                                                            UNLINK (-)
                                                        </SecondaryButton>
                                                    </div>
                                                </div>

                                            </li>

                                        </ul>
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
                            <div class="text-lg mb-2 text-indigo-400">Addresses</div>

                            <SecondaryButton @click="viewAddress(null)">
                                Add (+)
                            </SecondaryButton>

                            <form class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">All
                                    addresses:</label>
                                <div>

                                    <div>

                                        <div v-if="viewAddressModal">
                                            <address-modal :address="currentAddress" :related_id="customer_parent.id"
                                                           :related_class="relatedClass"
                                                           :show="viewAddressModal" @close="closeModal"/>
                                        </div>

                                        <ul class="w-3/2">

                                            <li v-for="n in customer_parent.addressable" :key="n.id" :value="n.id"
                                                class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">


                                                <div class="flex row mt-1">
                                                    <div class="flex-none w-1/6">
                                                        <icon v-if="n.address_type_id ===1" name="truck"
                                                              class="mr-2 w-6 h-6 fill-green-200"/>
                                                        <icon v-if="n.address_type_id ===2" name="house"
                                                              class="mr-2 w-6 h-6 fill-green-200"/>
                                                        <icon v-if="n.address_type_id ===3" name="envelope"
                                                              class="mr-2 w-6 h-6 fill-green-200"/>
                                                    </div>

                                                    <div class="flex-auto  w-3/6">
                                                        {{ n.line_1 }} {{ n.line_2 }} {{ n.line_3 }} {{ n.country }}
                                                        {{ n.code }}
                                                    </div>
                                                    <div class="flex-auto w-1/6">

                                                        <icon v-if="n.is_primary ===1" name="tick-circle"
                                                              class="mr-2 w-6 h-6 fill-green-200"/>

                                                    </div>
                                                    <div class="flex-auto w-1/6">


                                                        <SecondaryButton class="ml-2" @click="viewAddress(n)">
                                                            View
                                                        </SecondaryButton>
                                                    </div>
                                                </div>

                                            </li>

                                        </ul>
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
                            <div class="text-lg mb-2 text-indigo-400">Contacts</div>

                            <SecondaryButton @click="viewContact(null)">
                                Add (+)
                            </SecondaryButton>

                            <contact-modal :contact="null" :related_id="customer_parent.id" :related_class="relatedClass"
                                           :show="viewContactModal" @close="closeContactModal"/>







                            <div class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">All
                                    contacts:</label>
                                <ul class="w-3/2">

                                    <li v-for="n in customer_parent.contactable" :key="n.id" :value="n.id"
                                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                        <number-contact-detail-modal :related_id="n.id" :contact_type="contact_type"
                                                                     :related_class="relatedClassContact"
                                                                     :show="viewNumberContactDetailModal" @close="closeNumberContactDetailModal" />

                                        <email-contact-detail-modal :related_id="n.id" :contact_type="contact_type"
                                                                    :related_class="relatedClassContact"
                                                                    :show="viewEmailContactDetailModal" @close="closeEmailDetailModal" />

                                        <div class="flex row mt-1">
                                            <div class="flex-none w-1/6">
                                                <icon name="person"
                                                      class="mr-2 w-6 h-6 fill-green-200"/>
                                            </div>

                                            <div class="flex-auto  w-3/6">
                                                {{ n.title }} {{ n.first_name }} {{ n.last_legal_name }}

                                                <div v-if="n.job_description">({{ n.job_description }})</div>


                                            </div>
                                            <div class="flex-auto w-1/6">

                                                <icon v-if="n.is_primary ===1" name="tick-circle"
                                                      class="mr-2 w-6 h-6 fill-green-200"/>

                                            </div>
                                            <div class="flex-auto w-1/6">

                                                <SecondaryButton class="ml-2" @click="viewNumberContactDetail">
                                                    <PhoneIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                                                </SecondaryButton>

                                                <SecondaryButton class="ml-2" @click="viewEmailContactDetail">
                                                    <EnvelopeIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                                                </SecondaryButton>

                                                <Link class="inline-flex items-center ml-2 mt-3 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" :href="route('contact.show',n.id)" >View</Link>
                                            </div>
                                        </div>

                                    </li>

                                </ul>
                            </div>
                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Child Customers</div>

                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                <tr>
                                    <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Child Name</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Child Registration</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">View</th>

                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">


                               <tr  v-for="(customer, index) in customer_parent.customer"
                                    :key="customer.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{customer.last_legal_name}}</td>
                                    <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900">{{customer.id_reg_no}}</td>
                                    <td class="whitespace-nowrap  py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                        <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('customer.show',customer.id)" >View</Link>
                                    </td>
                               </tr>

                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
