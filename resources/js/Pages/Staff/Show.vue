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
    staff: Object,
    terms_of_payments: Object,
    all_roles: Object,
    permissions:Object
});
const permissions = computed(() => usePage().props.permissions)

/*'user_id', 'first_name','last_legal_name','user_id','nickname','title','job_description','id_reg_no','is_active'*/

let Form = useForm({

    first_name: props.staff.staff == null ? '' : props.staff.staff.first_name,
    last_legal_name: props.staff.staff == null ? '' : props.staff.staff.last_legal_name,
    email:props.staff == null ? '' : props.staff.email,
    password:null,
    title: props.staff.staff == null ? '' : props.staff.staff.title,
    nick_name: props.staff.staff == null ? '' : props.staff.staff.nick_name,
    job_description: props.staff.staff == null ? '' : props.staff.staff.job_description,
    id_reg_no: props.staff.staff == null ? '' : props.staff.staff.id_reg_no,
    is_active: props.staff.staff == null ? '' : props.staff.staff.is_active,

});

const updateStaff = () => {
    Form.put(route('staff.update', props.staff.staff.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
                toggleEdit();
            },
        }
    );
}

const emptyErrors = computed(() => Object.keys(Form.errors).length === 0 && Form.errors.constructor === Object)


let roleForm = useForm({
    role_name: "ViewOnlyRole",
    user_id: props.staff.id ?? 1
});

const addRole = () => roleForm.post(route('roles.modify.store'), {
    preserveScroll: true,
    onSuccess: () => {

        swal('Staff member linked.');

    },
})

const deleteRole = (id, name) => {

    if (confirm("Sure you want to delete " + name + "?")) {

        roleForm.role_name = name;

        roleForm.put(route('roles.modify.destroy', {staff: id}),
            {
                preserveScroll: true,
                onSuccess: () => {
                    swal('Staff member unlinked.');
                }
            }
        );

    } else {

    }
}



const editDisabled = ref(true);
const toggleEdit = () => {
    editDisabled.value = !editDisabled.value;
};

const roles_permissions = computed(() => usePage().props.roles_permissions);
const can_manage_users = computed(() => usePage().props.roles_permissions.permissions.includes("manage_users"));

</script>

<template>
    <AppLayout title="Staff">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Staff / <span class="text-indigo-400">{{ Form.first_name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div
                        :class="!emptyErrors ?'m-2 p-2 rounded-md rounded-md shadow-sm border border-red-500':  editDisabled ? 'm-2 p-2':'m-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500' ">
                        <div class="">
                            <form>
                                <div class="text-lg mb-4 text-indigo-400">Staff details</div>
                                <div class="space-y-12">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                                        <div>
                                            <h2 class="text-base font-semibold leading-7 text-gray-900">Static Information</h2>
                                            <p class="mt-1 text-sm leading-6 text-gray-600">Static Staff information.</p>
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
                                                    <input v-model="Form.nick_name" type="text" :disabled="editDisabled" name="nickname" id="nickname" autocomplete="nickname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.nick_name"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="nickname" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.email" type="email" :disabled="editDisabled" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.email"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Id/Reg no</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.id_reg_no" type="text" :disabled="editDisabled" name="id_reg_no" id="id_reg_no" autocomplete="id_reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.id_reg_no"/>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.password" type="password" :disabled="editDisabled" name="password" id="password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.password"/>
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
                                                <label for="id_reg_no" class="block text-sm font-medium leading-6 text-gray-900">Job description</label>
                                                <div class="mt-2">
                                                    <input v-model="Form.job_description" type="text" :disabled="editDisabled" name="id_reg_no" id="job_description" autocomplete="id_reg_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                </div>
                                                <InputError class="mt-2" :message="Form.errors.job_description"/>
                                            </div>

                                        </div>
                                    </div>



                                </div>

                                <div class="mt-6 flex items-center justify-end gap-x-6">

                                    <SecondaryButton v-if="can_manage_users" class="m-1" @click="toggleEdit">
                                        Edit
                                    </SecondaryButton>

                                    <SecondaryButton v-if="!editDisabled && can_manage_users" @click="updateStaff" class="m-1">
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
                            <div class="text-lg mb-2 text-indigo-400">Roles allocated</div>

                            <div>
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Available
                                        roles:</label>
                                    <div class="">
                                        <select v-model="roleForm.role_name"
                                                class="mt-2 block w-1/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="n in all_roles" :key="n.id" :value="n.name">{{
                                                    n.name
                                                }}
                                            </option>
                                        </select>


                                        <SecondaryButton @click="addRole()" class="mt-2">
                                            Link (+)
                                        </SecondaryButton>

                                    </div>

                                    <InputError class="mt-2" :message="roleForm.errors.role_name"/>
                                    <InputError class="mt-2" :message="roleForm.errors.user_id"/>

                                </div>
                            </div>

                            <form class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">Roles linked:</label>
                                <div>


                                    <div class="flex">

                                        <ul class="w-2/3">

                                            <li v-for="n in staff.roles" :key="n.id" :value="n.id"
                                                class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                                <div class="flex mt-1">
                                                    <div class="flex-none w-1/3">
                                                        <icon name="tick-circle" class="mr-2 w-6 h-6 fill-green-200"/>
                                                    </div>
                                                    <div class="flex-auto font-bold w-2/3">

                                                        <span v-if="n.id ===1" class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ n.name }}</span>
                                                        <span v-if="n.id ===2" class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">{{n.name}}</span>
                                                        <span v-if="n.id ===3" class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">{{n.name}}</span>
                                                        <span v-if="n.id ===4" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{n.name}}</span>
                                                        <span v-if="n.id ===5" class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{n.name}}</span>
                                                        <span v-if="n.id ===6" class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">{{n.name}}</span>
                                                        <span v-if="n.id ===7" class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{n.name}}</span>
                                                        <span v-if="n.id > 7" class="inline-flex items-center rounded-md bg-orange-100 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{n.name}}</span>
                                                    </div>
                                                    <div class="flex-auto w-1/3">
                                                        <SecondaryButton @click="deleteRole(n.id, n.name)" >
                                                            UNLINK (-)
                                                        </SecondaryButton>
                                                    </div>
                                                </div>

                                            </li>

                                        </ul>
                                    </div>

                                </div>
                            </form>

                            <form class="mt-5">
                                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">Permissions via Roles:</label>
                                <div class="m-2 p-2 shadow-lg rounded-md">


                                    <span  v-for="(p,index) in permissions" :key="index" class="inline-flex ml-1 mt-1 items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">{{ p }}</span>

                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>


        </div>
    </AppLayout>
</template>
