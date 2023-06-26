<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {computed, onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {Loader} from "@googlemaps/js-api-loader";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";

let loader = new Loader({apiKey: "AIzaSyAvFQCBzN_0f4PMWZosics0sBV_J9vvH1g", libraries: ["places"]})

let isGoogleApi = ref(true);
let autocomplete = ref();
let addressApi = ref();

const query = ref('')

const emit = defineEmits(['close']);

const props = defineProps({
    related_id: Number,
    related_class:String,
    address: Object,
    show:false,
    closeable:true
});

const close = () => {
    emit('close');
};


let clearValues = () => {
    form.line_1 = '';
    form.line_2 = '';
    form.line_3 = '';
    form.country = '';
    form.code = '';
    form.latitude = '';
    form.longitude = '';
    form.directions='';
}


let getAddress = () => {
    if (autocomplete != null) {
        addressApi = autocomplete.getPlace();
        if (addressApi) {

            for (const component of addressApi.address_components) {
                const componentType = component.types[0];

                switch (componentType) {
                    case "street_number": {

                        form.line_1 = component.long_name;

                        break;
                    }

                    case "route": {

                        form.line_1 += component.long_name;

                        break;
                    }

                    case "postal_code": {

                        form.code += component.long_name;

                        break;
                    }

                    case "postal_code_suffix": {

                        form.code += component.long_name;
                        break;
                    }
                    case "sublocality_level_1":

                        form.line_2 += component.long_name;
                        break;
                    case "locality":

                        form.line_3 += component.long_name;
                        break;
                    case "administrative_area_level_1": {

                        break;
                    }
                    case "country":

                        form.country += component.long_name;

                        break;
                }
            }

            if (addressApi.geometry.location) {
                form.latitude = addressApi.geometry.location.lat();
                form.longitude = addressApi.geometry.location.lng();
            }


        }

    }
    else {

    }
}


onBeforeMount(async () => {


})

onUnmounted(async () => {
    if (autocomplete) {
        isGoogleApi.value = false;
        google.maps.event.clearInstanceListeners(autocomplete);
    }
})

onMounted(async () => {
    if (loader){
        loader
            .load()
            .then((google) => {
                isGoogleApi.value = true;

                autocomplete = new google.maps.places.Autocomplete(document.getElementById("autocomplete_id"),
                    {
                        fields: ["address_components", "geometry"],
                        types: ["address"],
                    }
                );

                autocomplete.setComponentRestrictions({ // restrict the country
                    country: ["za"]
                });


                google.maps.event.addListener(autocomplete, "place_changed", () => {
                    clearValues();
                    getAddress();
                });


            })
            .catch(e => {
                // do something
                isGoogleApi.value = false;
                alert('error')
                console.log(e);

            });
    }

})

const form = useForm({
    address:null,
    address_type_id: props.address == null? 1: props.address.address_type_id,
    line_1: props.address == null ? '' : props.address.line_1,
    line_2: props.address == null ? '' :props.address.line_2,
    line_3: props.address == null ? '' :props.address.line_3,
    country: props.address == null ? '' :props.address.country,
    code: props.address == null ? '' :props.address.code,
    latitude: props.address == null ? '' : props.address.latitude,
    longitude: props.address == null ? '' :props.address.longitude,
    directions:props.address == null ? '' :props.address.directions,
    is_primary:props.address == null ? 0 :props.address.is_primary,
    related_class: props.related_class,
    related_id:props.related_id
});

const deleteAddress = () => {

    if (confirm("Sure you want to delete?")) {

        form.delete(route('address.destroy', props.address.id), {
            preserveScroll: true,
            onSuccess: () => {
                close();
            },
        });
    }


};

const updateAddress = () => {

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

const createAddress = () => {

    form.post(route('address.store'), {
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

                            <div>

                                <select v-model="form.address_type_id"
                                        class="input-filter-l ml-4 mt-2 block w-1/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option :value=1>Delivery</option>
                                    <option :value=2>Physical</option>
                                    <option :value=3>Postal</option>

                                </select>
                            </div>


                            <div class="ml-3" >
                                <TextInput
                                    class="mt-5 bg-gray-200 border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"

                                    id="autocomplete_id"
                                    name="autocomplete_id"
                                    type="text"
                                    placeholder="Search address..."
                                    v-model="form.address"
                                />

                            </div>

                            <div :class="borderClass">

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Line 1</label>
                                    <TextInput
                                        id="line_1"
                                        v-model="form.line_1"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.line_1"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Line 2</label>
                                    <TextInput
                                        id="line_2"
                                        v-model="form.line_2"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.line_2"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Line 3</label>
                                    <TextInput
                                        id="line_3"
                                        v-model="form.line_3"
                                        type="text"
                                        class="mt-1 block w-full"

                                    />
                                    <InputError class="mt-2" :message="form.errors.line_3"/>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Country</label>
                                    <TextInput
                                        id="country"
                                        v-model="form.country"
                                        type="text"
                                        class="mt-1 block w-1/3"
                                        required

                                    />
                                    <InputError class="mt-2" :message="form.errors.country"/>
                                </div>


                                <div class="mt-3 flex flex-row">
                                    <div class="basis-1/2">
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">Code</label>
                                            <TextInput
                                                id="code"
                                                v-model="form.code"
                                                type="text"
                                                class="mt-1 block w-1/3"
                                                required

                                            />
                                            <InputError class="mt-2" :message="form.errors.code"/>
                                        </div>
                                    </div>
                                    <div v-if="form.longitude !== '' && form.longitude != null" class="mt-3 basis-1/2">
                                        <div v-if="form.longitude !== ''" class="mt-3 ml-3">Long: {{form.longitude}} </div>
                                        <div v-if="form.latitude !== ''" class="mt-3 ml-3">Lat: {{form.latitude}}</div>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Directions</label>

                                    <AreaInput
                                        id="directions"

                                        :rows="4"
                                        placeholder="Optional directions or comments..."
                                        v-model="form.directions"
                                        type="text"
                                        class="mt-1 block w-1/3"
                                    />

                                    <InputError class="mt-2" :message="form.errors.directions"/>
                                </div>

                                <div class="mt-5">

                                    <div>
                                        <select v-model="form.is_primary"
                                                class="input-filter-l mt-2 block w-1/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option :value=1>Primary address</option>
                                            <option :value=0>Not Primary address</option>
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <InputError class="mt-2" :message="form.errors.related_class"/>
                            <InputError class="mt-2" :message="form.errors.related_id"/>

                        </form>

                    </div>

                </div>


            </template>

            <template #footer>

                <div v-if="props.address == null">
                    <SecondaryButton class="bg-red-400" @click="createAddress()">
                        Create
                    </SecondaryButton>
                </div>
                <div v-else>
                    <SecondaryButton class="bg-red-400" @click="deleteAddress()">
                        Delete
                    </SecondaryButton>
                    <SecondaryButton class="ml-1 bg-green-400"  @click="updateAddress()">
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
