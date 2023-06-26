<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AreaInput from '@/Components/AreaInput.vue';
import {onMounted, onUnmounted, reactive, ref, onBeforeMount, computed} from "vue";
import {useForm} from "@inertiajs/vue3";
import DangerButton from '@/Components/DangerButton.vue';
import {Loader} from "@googlemaps/js-api-loader"
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";


let isGoogleApi = ref(true);
let autocomplete = ref();
let addressApi = ref();

const loader = new Loader({apiKey: "AIzaSyAvFQCBzN_0f4PMWZosics0sBV_J9vvH1g", libraries: ["places"]})

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
}


onBeforeMount(async () => {

    loader
        .load()
        .then((google) => {
            isGoogleApi.value = true;
            autocomplete = new google.maps.places.Autocomplete(document.getElementById("autocomplete"),
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
            })

        })
        .catch(e => {
            // do something
            isGoogleApi.value = false;
            console.log(e);
        });
})

onUnmounted(async () => {
    if (autocomplete) {
        google.maps.event.clearInstanceListeners(autocomplete);
    }
})

onMounted(async () => {


})

const form = useForm({
    type: 1,
    address: addressApi,
    line_1: '',
    line_2: '',
    line_3: '',
    country: '',
    code: '',
    is_primary: true,
    latitude: '',
    longitude: '',
    directions:'',
});

</script>


<template>
    <AppLayout title="Test">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Component Test
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">


                    <dialog-modal :show="true"
                                   >


                        <template #content>
                            <div>

                                <form class="w-1/2 ml-2">

                                    <div>

                                        <select v-model="form.type"
                                                class="input-filter-l block ml-4 mt-4 w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value=1>Physical</option>
                                            <option :value=2>Postal</option>

                                        </select>
                                    </div>


                                    <div class="ml-3" v-if="isGoogleApi">
                                        <TextInput
                                            class="mt-5 bg-gray-200 border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"

                                            id="autocomplete"
                                            type="text"
                                            v-model="form.address"
                                            placeholder="Search address..."
                                        />

                                    </div>

                                    <div :class="form.latitude !== ''? 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-green-300': 'ml-4 mt-4 p-4 rounded-md border-solid border-2 border-gray'">

                                        <div class="mt-3">
                                            <InputLabel for="line_1" value="Line 1"/>
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
                                            <InputLabel for="line_2" value="Line 2"/>
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
                                            <InputLabel for="line_3" value="Line 3"/>
                                            <TextInput
                                                id="line_3"
                                                v-model="form.line_3"
                                                type="text"
                                                class="mt-1 block w-full"

                                            />
                                            <InputError class="mt-2" :message="form.errors.line_3"/>
                                        </div>

                                        <div class="mt-3">
                                            <InputLabel for="country" value="Country "/>
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
                                                    <InputLabel for="code" value="Code"/>
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
                                            <div v-if="form.longitude !== ''" class="mt-3 basis-1/2">
                                                <div v-if="form.longitude !== ''" class="mt-3 ml-3">Long: {{form.longitude}} </div>
                                                <div v-if="form.latitude !== ''" class="mt-3 ml-3">Lat: {{form.latitude}}</div>
                                            </div>
                                        </div>





                                        <div class="mt-5">
                                            <InputLabel for="directions" value="Directions"/>

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

                                        <div class="mt-3">

                                            <div>
                                                <input
                                                    class="mt-[0.3rem] mr-2 h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 dark:bg-neutral-600 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 dark:after:bg-neutral-400 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary dark:checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary dark:checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s]"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="flexSwitchChecked"
                                                    v-model="form.is_primary"
                                                />
                                                <label
                                                    class="inline-block pl-[0.15rem] hover:cursor-pointer"
                                                    for="flexSwitchChecked"
                                                >Primary Address</label
                                                >
                                            </div>
                                        </div>

                                    </div>



                                    <div class="flex items-center justify-end mt-4 mb-4">


                                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                                       :disabled="form.processing">
                                            Add
                                        </PrimaryButton>


                                    </div>
                                </form>

                            </div>


                        </template>


                    </dialog-modal>



                </div>
            </div>
        </div>
    </AppLayout>
</template>
