<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, inject, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";

const query = ref('')

const emit = defineEmits(['close']);

const props = defineProps({
    related_id: Number,
    related_class:String,
    contact: Object,
    show:false,
    closeable:true
});

const swal = inject('$swal');

const close = () => {
    emit('close');
};


onBeforeMount(async () => {


})

onUnmounted(async () => {

})

onMounted(async () => {


})

const form = useForm({
    related_id: props.related_id,
    related_class:props.related_class,
    first_name:props.contact == null ? '' : props.contact.first_name,
    last_legal_name:props.contact == null ? '' : props.contact.last_legal_name,
    nickname:props.contact == null ? '' : props.contact.nickname,
    title:props.contact == null ? '' : props.contact.title,
    job_description:props.contact == null ? '' : props.contact.job_description,
    id_reg_no:props.contact == null ? '' : props.contact.id_reg_no,
    is_active:props.contact == null ? 1 : props.contact.is_active,
    branch:props.contact == null ? '' : props.contact.branch,
    department:props.contact == null ? '' : props.contact.department,
    comment:props.contact == null ? '' : props.contact.comment,
});

const deleteContact = () => {

    if (confirm("Sure you want to delete?")) {

        form.delete(route('address.destroy', props.address.id), {
            preserveScroll: true,
            onSuccess: () => {
                close();
            },
        });
    }


};

const updateContact = () => {

    form.put(route('address.update', props.address.id), {
        preserveScroll: true,
        onSuccess: () => {
            close();
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

const createContact = () => {

    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
             swal('Contact created.');
            close();
        },

        onError: (e) => {
            console.log(e);
        },
    });
};

let emptyErrors = computed(() => Object.keys(form.errors).length === 0 && form.errors.constructor === Object)
let borderClass = computed(() => !emptyErrors ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-red-500': form.latitude !== '' && form.latitude != null ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-green-300': 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray')



</script>


<template>
    <div>
        <dialog-modal :show="show"
                      :closeable="closeable"
                      @close="close" >

            <template #content>
                <div>

                    <div class="">

                        <form class="w-full m-3 p-3" >

                            <div :class="borderClass">

                                <div class="text-lg mb-2 text-indigo-400">Add Contact</div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Title</label>
                                    <TextInput
                                        id="country"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-1/3"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.title"/>
                                </div>


                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">First name </label>
                                    <TextInput
                                        id="first_name"
                                        v-model="form.first_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.first_name"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Last/Legal name</label>
                                    <TextInput
                                        id="last_legal_name"
                                        v-model="form.last_legal_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.last_legal_name"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Nickname</label>
                                    <TextInput
                                        id="line_3"
                                        v-model="form.nickname"
                                        type="text"
                                        class="mt-1 block w-full"

                                    />
                                    <InputError class="mt-2" :message="form.errors.nickname"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Id/reg no</label>
                                    <TextInput
                                        id="id_reg_no"
                                        v-model="form.id_reg_no"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.id_reg_no"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Job description</label>
                                    <TextInput
                                        id="job_description"
                                        v-model="form.job_description"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.job_description"/>
                                </div>


                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Branch</label>
                                    <TextInput
                                        id="branch"
                                        v-model="form.branch"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.branch"/>
                                </div>


                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Department</label>
                                    <TextInput
                                        id="department"
                                        v-model="form.department"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.department"/>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Comments</label>

                                    <AreaInput
                                        id="comment"

                                        :rows="3"
                                        placeholder="Optional comments..."
                                        v-model="form.comment"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />

                                    <InputError class="mt-2" :message="form.errors.comment"/>
                                </div>

                                <div class="mt-3">

                                    <select v-model="form.is_active"
                                            class="input-filter-l block ml-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value=1>Active</option>
                                        <option :value=0>Inactive</option>

                                    </select>

                                    <InputError class="mt-2" :message="form.errors.is_active"/>
                                </div>


                            </div>



                        </form>

                    </div>

                </div>


            </template>

            <template #footer>

                <div v-if="props.address == null">
                    <SecondaryButton class="bg-red-400" @click="createContact()">
                        Create
                    </SecondaryButton>
                </div>
                <div v-else>
                    <SecondaryButton class="bg-red-400" @click="deleteContact()">
                        Delete
                    </SecondaryButton>
                    <SecondaryButton class="ml-1 bg-green-400"  @click="updateContact()">
                        Save
                    </SecondaryButton>
                </div>

                <SecondaryButton class="ml-1" @click="close()">
                    Close
                </SecondaryButton>
            </template>
        </dialog-modal>
    </div>

</template>
