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

const swal = inject('$swal');

const props = defineProps({
    supplier: Object,
    invoice_basis: Object,
    terms_of_payment: Object,
    staff: Object,
    contact_type: Object
});
const permissions = computed(() => usePage().props.permissions)
const emptyErrors = computed(() => Object.keys(supplierForm.errors).length === 0 && supplierForm.errors.constructor === Object)


/*'id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
    'terms_of_payment_id','account_number','comment*/


let supplierForm = useForm({

    _method: 'PUT',
    first_name: props.supplier.first_name ?? null,
    last_legal_name: props.supplier.last_legal_name ?? null,
    nickname: props.supplier.nickname ?? null,
    title: props.supplier.title ?? null,
    job_description: props.supplier.job_description ?? null,
    id_reg_no: props.supplier.id_reg_no ?? null,
    is_active: props.supplier.is_active ?? null,
    terms_of_payment_id: props.supplier.terms_of_payment_id ?? null,
    account_number:props.supplier.account_number ?? null,
    comment: props.supplier.comment ?? null,


});


const updateSupplier = () => {
    supplierForm.put(route('supplier.update', props.supplier.id),
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
    supplier_id: props.supplier.id ?? 1
});


const addStaff = () => staffForm.post('/staff', {
    preserveScroll: true,
    onSuccess: () => {

        swal('Staff member linked.');

    },
})

const deleteStaff = (id, name) => {

    if (confirm("Sure you want to delete " + name + "?")) {

        staffForm.put(route('staff.update', {staff: id}),
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

const relatedClass = ref('App\\Models\\supplier');
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
    //alert(id)
    //router.get('contact/' + 3);
    router.get('supplier/'+3);
}

import { EnvelopeIcon, PhoneIcon } from '@heroicons/vue/20/solid'

</script>

<template>
    <AppLayout title="Supplier">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Supplier / <span class="text-indigo-400">{{ supplierForm.last_legal_name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div
                        :class="!emptyErrors ?'m-2 p-2 rounded-md rounded-md shadow-sm border border-red-500':  editDisabled ? 'm-2 p-2':'m-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500' ">
                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">General details</div>
                            <form>
                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <div class="col-span-4">

                                        <label class="block text-sm font-medium leading-6 text-gray-900">First
                                            name:</label>
                                        <div class="mt-2">
                                            <input v-model="supplierForm.first_name" :disabled="editDisabled"
                                                   type="text"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                            <InputError class="mt-2" :message="supplierForm.errors.first_name"/>
                                        </div>


                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Last/Legal
                                            name:</label>

                                        <div class="mt-2">

                                        <input v-model="supplierForm.last_legal_name" :disabled="editDisabled"
                                               type="text"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="supplierForm.errors.last_legal_name"/>

                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Nickname:</label>

                                        <div class="mt-2">

                                        <input v-model="supplierForm.nickname" type="text" :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="supplierForm.errors.nickname"/>

                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Id/Reg
                                            no:</label>
                                        <div class="mt-2">
                                        <input v-model="supplierForm.id_reg_no" type="text" :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="supplierForm.errors.id_reg_no"/>
                                            </div>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Account number:</label>

                                        <div class="mt-2">
                                        <input v-model="supplierForm.account_number" type="number"
                                               :disabled="editDisabled"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        <InputError class="mt-2" :message="supplierForm.errors.account_number"/>

                                            </div>
                                    </div>


                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Terms of
                                            payment:</label>

                                        <select v-model="supplierForm.terms_of_payment_id" :disabled="editDisabled"
                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="n in terms_of_payment" :key="n.id" :value="n.id">{{
                                                    n.value
                                                }}
                                            </option>
                                        </select>

                                        <InputError class="mt-2" :message="supplierForm.errors.terms_of_payment_id"/>

                                    </div>



                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Comments:</label>
                                        <AreaInput
                                            id="comments"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            v-model="supplierForm.comment"
                                            type="text"
                                            class="mt-1 block w-1/3"
                                            :disabled="editDisabled"
                                        />
                                        <InputError class="mt-2" :message="supplierForm.errors.comment"/>
                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton class="m-1" @click="toggleEdit">
                                            Edit
                                        </SecondaryButton>

                                        <SecondaryButton v-if="!editDisabled" @click="updateSupplier" class="m-1">
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
                                    <InputError class="mt-2" :message="staffForm.errors.supplier_id"/>

                                </div>
                            </div>

                            <form class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">Staff members
                                    linked:</label>
                                <div>


                                    <div class="flex">

                                        <ul class="w-1/3">

                                            <li v-for="n in supplier.staff" :key="n.id" :value="n.id"
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
                                            <address-modal :address="currentAddress" :related_id="supplier.id"
                                                           :related_class="relatedClass"
                                                           :show="viewAddressModal" @close="closeModal"/>
                                        </div>

                                        <ul class="w-3/2">

                                            <li v-for="n in supplier.addressable" :key="n.id" :value="n.id"
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

                            <contact-modal :contact="null" :related_id="supplier.id" :related_class="relatedClass"
                                           :show="viewContactModal" @close="closeContactModal"/>


                            <div class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">All
                                    contacts:</label>
                                <ul class="w-3/2">

                                    <li v-for="n in supplier.contactable" :key="n.id" :value="n.id"
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
            </div>
        </div>
    </AppLayout>
</template>
