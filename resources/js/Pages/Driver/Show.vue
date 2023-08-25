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
import { EnvelopeIcon, PhoneIcon } from '@heroicons/vue/20/solid';

const swal = inject('$swal');

const props = defineProps({
    driver: Object,
});
const permissions = computed(() => usePage().props.permissions)
const emptyErrors = computed(() => Object.keys(driverForm.errors).length === 0 && driverForm.errors.constructor === Object)



/*    ['first_name','last_name','cell_no','comment','is_active'];*/

let driverForm = useForm({
    first_name: props.driver.first_name ?? null,
    last_name: props.driver.last_name ?? null,
    cell_no: props.driver.cell_no ?? null,
    is_active: props.driver.is_active ?? 1,
    comment: props.driver.comment ?? null,

});


const updateDriver = () => {
    driverForm.put(route('regular_driver.update', props.driver.id),
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


const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_update_product = computed(() => usePage().props.roles_permissions.permissions.includes("update_product"));

</script>

<template>
    <AppLayout title="Driver">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Driver / <span class="text-indigo-400">{{ driverForm.first_name }}</span>
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
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Static Driver information.</p>
                                        </div>

                                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                                            <div class="sm:col-span-3">
                                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">First Name</label>
                                                <div class="mt-2">
                                                    <input v-model="driverForm.first_name" :disabled="editDisabled" type="text" name="name" id="name"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="driverForm.errors.first_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Last Name</label>
                                                <div class="mt-2">
                                                    <input v-model="driverForm.last_name" :disabled="editDisabled" type="text" name="last_name" id="last_name"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="driverForm.errors.last_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Cell no</label>
                                                <div class="mt-2">
                                                    <input v-model="driverForm.cell_no" :disabled="editDisabled" type="text" name="last_name" id="cell_no"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="driverForm.errors.cell_no"/>
                                            </div>


                                            <div class="sm:col-span-3">
                                                <label  class="block text-sm font-medium leading-6 text-gray-900">Driver status</label>
                                                <div class="mt-2">
                                                    <select v-model="driverForm.is_active" :disabled="editDisabled"
                                                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                        <option key="1" value="1">
                                                            Active
                                                        </option>

                                                        <option key="0" value="0">
                                                            Suspended
                                                        </option>
                                                    </select>
                                                </div>
                                                <InputError class="mt-2" :message="driverForm.errors.is_active"/>
                                            </div>


                                            <div class="sm:col-span-6">
                                                <label for="comments" class="block text-sm font-medium leading-6 text-gray-900">Comments</label>
                                                <AreaInput
                                                    id="comments"
                                                    :rows=6
                                                    placeholder="Optional comments..."
                                                    v-model="driverForm.comment"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :disabled="editDisabled"
                                                />
                                                <InputError class="mt-2" :message="driverForm.errors.comment"/>

                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <div class="mt-6 flex items-center justify-end gap-x-6">

                                    <SecondaryButton v-if="can_update_product" class="m-1" @click="toggleEdit">
                                        Edit
                                    </SecondaryButton>

                                    <SecondaryButton v-if="!editDisabled && can_update_product" @click="updateDriver" class="m-1">
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
