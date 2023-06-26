<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, inject, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput';

const phoneNumber = ref()
const results = ref()

const query = ref('')

const emit = defineEmits(['close']);

const props = defineProps({
    contact_type: Object,
    related_id: Number,
    related_class:String,
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


//['value','comment','country_code','contact_detail_type_id','system_player_id'];

const form = useForm({
    related_id: props.related_id,
    related_class:props.related_class,
    contact_detail_type_id:1,
    value:null,
    comment:null,
    country_code:null,
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

const createContactDetail = () => {

    form.post(route('contact_detail.store'), {
        preserveScroll: true,
        onSuccess: () => {
             swal('Contact detail created.');
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

                                <div class="mt-3">

                                    <label class="block text-sm font-medium leading-6 text-gray-900">Contact Type</label>

                                    <select v-model="form.contact_detail_type_id"
                                            class="input-filter-l w-1/3 rounded-md rounded-md shadow-sm border border-gray-300 text-gray-500">
                                        <option v-for="n in contact_type" :key="n.id" :value="n.id">
                                            {{n.type}}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" :message="form.errors.contact_detail_type_id"/>
                                </div>


                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Value</label>

                                    <div>
                                        <div class="relative mt-2 rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 flex items-center">
                                                <label for="country" class="sr-only">Country</label>
                                                <select id="country" name="country" autocomplete="country" class="h-full rounded-md border-0 bg-transparent py-0 pl-3 pr-7 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                                    <option>US</option>
                                                    <option>CA</option>
                                                    <option>EU</option>
                                                </select>
                                            </div>
                                            <input type="text" name="phone-number" id="phone-number" class="block w-full rounded-md border-0 py-1.5 pl-16 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0731008214" />
                                        </div>
                                    </div>

                                    <InputError class="mt-2" :message="form.errors.value"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Value</label>

                                    <div>
                                        <input  type="email"
                                               class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>


                                    </div>

                                    <InputError class="mt-2" :message="form.errors.value"/>
                                </div>


                            </div>

                        </form>

                    </div>

                </div>


            </template>

            <template #footer>

                <div v-if="true">
                    <SecondaryButton class="bg-red-400" @click="createContactDetail">
                        Create
                    </SecondaryButton>
                </div>
                <div v-else>
                    <SecondaryButton class="bg-red-400" @click="">
                        Delete
                    </SecondaryButton>
                    <SecondaryButton class="ml-1 bg-green-400"  @click="">
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
