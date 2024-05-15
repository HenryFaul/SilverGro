<script setup>
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";


const emit = defineEmits(['close']);

let props = defineProps({
    transport_trans_id: Number,
    transport_finance_id: Number,
    assigned_user_comm:Object,
    all_staff:Object,
    show: false,
    closeable: true,
});

const close = () => {
    emit('close');
};


onUnmounted(async () => {

})

onBeforeMount(async () => {


})

/*'transport_trans_id','transport_finance_id','assigned_user_supplier_id','assigned_user_customer_id',
    'supplier_comm','customer_comm','notes'*/


const form = useForm({
    transport_trans_id: props.transport_trans_id == null ? null : props.transport_trans_id,
    transport_finance_id: props.transport_trans_id == null ? null : props.transport_finance_id,
    assigned_user_supplier_id: props.assigned_user_comm == null? 1: props.assigned_user_comm.assigned_user_supplier_id,
    assigned_user_customer_id: props.assigned_user_comm == null? 1: props.assigned_user_comm.assigned_user_customer_id,
    notes: props.assigned_user_comm == null? null: props.assigned_user_comm.notes,

});

const deleteAssignedComm =  () => {

    if (confirm("Sure you want to delete?")) {

         form.delete(route('assigned_user_comm.destroy', props.assigned_user_comm.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    close();
                },
                onError: (e) => {
                    console.log(e);
                },
            }
        );
    }
};

const updateAssignedComm = async () => {
   await form.put(route('assigned_user_comm.update', props.assigned_user_comm.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                close();
            },
            onError: (e) => {
                console.log(e);
            },
        }
    );

};

const createAssignedComm = () => {
     form.post(route('assigned_user_comm.store'), {
          preserveScroll: true,
          onSuccess: () => {
              close();
          },
          onError: (e) => {
              console.log(e);
          },
      });
};

let emptyErrors = computed(() => Object.keys(form.errors).length === 0 && form.errors.constructor === Object)
let borderClass = computed(() => !emptyErrors ? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-red-500' : 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray')

</script>


<template>
    <div>
        <dialog-modal :show="show"
                      :closeable="closeable"
                      @close="close">


            <template #content>
                <div>

                    <div class="">

                        <form class="w-full">


<!--                            'transport_trans_id','transport_finance_id','assigned_user_supplier_id','assigned_user_customer_id',
                            'supplier_comm','customer_comm','notes'-->

                            <div :class="borderClass">

                                <div class="text-lg mb-2 text-indigo-400">Assigned User Commission</div>

                                <div class="mt-3">

                                    <label class="block text-sm font-medium leading-6 text-gray-900">Assigned Supplier Staff</label>

                                    <select v-model="form.assigned_user_supplier_id"
                                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option v-for="n in props.all_staff" :key="n.id" :value="n.id">
                                            {{n.first_name}} {{n.last_legal_name}}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" :message="form.errors.assigned_user_supplier_id"/>
                                </div>

                                <div class="mt-3">

                                    <label class="block text-sm font-medium leading-6 text-gray-900">Assigned Customer Staff</label>

                                    <select v-model="form.assigned_user_customer_id"
                                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option v-for="n in props.all_staff" :key="n.id" :value="n.id">
                                            {{n.first_name}} {{n.last_legal_name}}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" :message="form.errors.assigned_user_customer_id"/>
                                </div>

<!--                                <div class="mt-5">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">notes</label>

                                    <AreaInput
                                        id="notes"

                                        :rows="4"
                                        placeholder="Optional directions or comments..."
                                        v-model="form.notes"
                                        type="text"
                                        class="mt-1 block w-1/3"
                                    />

                                    <InputError class="mt-2" :message="form.errors.notes"/>
                                </div>-->

                            </div>


                        </form>

                    </div>

                </div>


            </template>

            <template #footer>

                <div v-if="props.assigned_user_comm == null">
                    <SecondaryButton @click="createAssignedComm" class="bg-red-400">
                        Create
                    </SecondaryButton>
                </div>
                <div v-else>
                    <SecondaryButton @click="updateAssignedComm" class="ml-1 bg-green-400">
                        Save
                    </SecondaryButton>
                </div>

                <SecondaryButton class="ml-1" @click="close">
                    Close
                </SecondaryButton>
            </template>
        </dialog-modal>
    </div>

</template>
