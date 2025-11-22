<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, inject, ref } from 'vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import Icon from '@/Components/Icon.vue';
  import InputError from '@/Components/InputError.vue';
  import AreaInput from '@/Components/AreaInput.vue';
  import SectionBorder from '@/Components/SectionBorder.vue';
  import { EnvelopeIcon, PhoneIcon } from '@heroicons/vue/20/solid';

  import NumberContactDetailModal from '@/Components/UI/NumberContactDetailModal.vue';
  import EmailContactDetailModal from '@/Components/UI/EmailContactDetailModal.vue';
  import ContactModal from '@/Components/UI/ContactModal.vue';
  import AddressModal from '@/Components/UI/AddressModal.vue';

  const swal = inject('$swal');
  const viewContactModal = ref(false);

  const props = defineProps({
    transporter: Object,
    terms_of_payments: Object,
    contact_type: Object,
  });
  const permissions = computed(() => usePage().props.permissions);

  /*
'id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
    'terms_of_payment_id','account_number','comment','is_git'*/

  let Form = useForm({
    first_name: props.transporter == null ? '' : props.transporter.first_name,
    last_legal_name: props.transporter == null ? '' : props.transporter.last_legal_name,
    nickname: props.transporter == null ? '' : props.transporter.nickname,
    title: props.transporter == null ? '' : props.transporter.title,
    job_description: props.transporter == null ? '' : props.transporter.job_description,
    id_reg_no: props.transporter == null ? '' : props.transporter.id_reg_no,
    is_active: props.transporter == null ? 1 : props.transporter.is_active,
    terms_of_payment_id:
      props.transporter == null ? '' : props.transporter.terms_of_payment_id,
    is_vat_exempt:
      props.transporter == null ? 0 : props.transporter.is_vat_exempt ? 1 : 0,
    is_vat_cert_received:
      props.transporter == null ? 0 : props.transporter.is_vat_cert_received ? 1 : 0,
    account_number: props.transporter == null ? '' : props.transporter.account_number,
    is_git: props.transporter == null ? 1 : props.transporter.is_git,
    comment: props.transporter == null ? '' : props.transporter.comment,
  });

  const emptyErrors = computed(
    () => Object.keys(Form.errors).length === 0 && Form.errors.constructor === Object
  );

  const updateTransporter = () => {
    Form.put(route('transporter.update', props.transporter.id), {
      preserveScroll: true,
      onSuccess: () => {
        swal(usePage().props.jetstream.flash?.banner || '');
        toggleEdit();
      },
    });
  };

  const closeContactModal = () => {
    viewContactModal.value = false;
  };

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

  const relatedClass = ref('App\\Models\\Transporter');
  const relatedClassContact = ref('App\\Models\\Contact');

  const viewAddressModal = ref(false);
  const currentAddress = ref(null);
  const currentContact = ref(null);

  const viewAddress = (address) => {
    currentAddress.value = address;
    viewAddressModal.value = true;
  };

  const viewContact = (contact) => {
    currentContact.value = contact;
    viewContactModal.value = true;
  };

  const closeModal = () => {
    viewAddressModal.value = false;
  };

  const roles_permissions = computed(() => usePage().props.roles_permissions);
  const can_update_transporter = computed(() =>
    usePage().props.roles_permissions.permissions.includes('update_transporter')
  );
</script>

<template>
  <AppLayout title="Transporter">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Transporter /
        <span class="text-indigo-400">{{ Form.last_legal_name }}</span>
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div
            :class="
              !emptyErrors
                ? 'm-2 p-2 rounded-md rounded-md shadow-sm border border-red-500'
                : editDisabled
                  ? 'm-2 p-2'
                  : 'm-2 p-2 rounded-md rounded-md shadow-sm border border-indigo-500'
            ">
            <div class="">
              <form>
                <div class="text-lg mb-4 text-indigo-400">General details</div>
                <div class="space-y-12">
                  <div
                    class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                      <h2 class="text-base font-semibold leading-7 text-gray-900">
                        Static Information
                      </h2>
                      <p class="mt-1 text-sm leading-6 text-gray-600">
                        Static Transporter information.
                      </p>
                    </div>

                    <div
                      class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          First name
                        </label>
                        <div class="mt-2">
                          <input
                            id="title"
                            v-model="Form.title"
                            :disabled="editDisabled"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="title"
                            type="text" />
                        </div>
                        <InputError
                          :message="Form.errors.title"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          First name
                        </label>
                        <div class="mt-2">
                          <input
                            id="first_name"
                            v-model="Form.first_name"
                            :disabled="editDisabled"
                            autocomplete="given-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="first_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="Form.errors.first_name"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="last_legal_name">
                          Last / Legal name
                        </label>
                        <div class="mt-2">
                          <input
                            id="last_legal_name"
                            v-model="Form.last_legal_name"
                            :disabled="editDisabled"
                            autocomplete="family-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="last_legal_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="Form.errors.last_legal_name"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="nickname">
                          Nick name
                        </label>
                        <div class="mt-2">
                          <input
                            id="nickname"
                            v-model="Form.nickname"
                            :disabled="editDisabled"
                            autocomplete="nickname"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="nickname"
                            type="text" />
                        </div>
                        <InputError
                          :message="Form.errors.nickname"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="id_reg_no">
                          Id/Reg no
                        </label>
                        <div class="mt-2">
                          <input
                            id="id_reg_no"
                            v-model="Form.id_reg_no"
                            :disabled="editDisabled"
                            autocomplete="id_reg_no"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="id_reg_no"
                            type="text" />
                        </div>
                        <InputError
                          :message="Form.errors.id_reg_no"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="id_reg_no">
                          Status
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="Form.is_active"
                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                          </select>
                        </div>
                        <InputError
                          :message="Form.errors.is_active"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-6">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="comments">
                          Comments
                        </label>
                        <AreaInput
                          id="comments"
                          v-model="Form.comment"
                          :disabled="editDisabled"
                          :rows="6"
                          class="mt-1 block w-full"
                          placeholder="Optional comments..."
                          type="text" />
                        <InputError
                          :message="Form.errors.comment"
                          class="mt-2" />
                      </div>
                    </div>
                  </div>

                  <div
                    class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                      <h2 class="text-base font-semibold leading-7 text-gray-900">
                        Additional Information
                      </h2>
                      <p class="mt-1 text-sm leading-6 text-gray-600">
                        Transporter additional information.
                      </p>
                    </div>

                    <div
                      class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Account number
                        </label>
                        <div class="mt-2">
                          <input
                            v-model="Form.account_number"
                            :disabled="editDisabled"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            type="text" />
                          <InputError
                            :message="Form.errors.account_number"
                            class="mt-2" />
                        </div>
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Payment terms
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="Form.terms_of_payment_id"
                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option
                              v-for="n in props.terms_of_payments"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                          <InputError
                            :message="Form.errors.terms_of_payments"
                            class="mt-2" />
                        </div>
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Is Git
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="Form.is_git"
                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                          </select>

                          <InputError
                            :message="Form.errors.is_git"
                            class="mt-2" />
                        </div>
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="is_vat_exempt">
                          VAT Exempt
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="Form.is_vat_exempt"
                            :disabled="editDisabled"
                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option :value="1">Yes</option>
                            <option :value="0">No</option>
                          </select>
                        </div>
                        <InputError
                          :message="Form.errors.is_vat_exempt"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="is_vat_cert_received">
                          VAT Certificate Received
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="Form.is_vat_cert_received"
                            :disabled="editDisabled"
                            class="input-filter-l block w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option :value="1">Yes</option>
                            <option :value="0">No</option>
                          </select>
                        </div>
                        <InputError
                          :message="Form.errors.is_vat_cert_received"
                          class="mt-2" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                  <SecondaryButton
                    v-if="can_update_transporter"
                    class="m-1"
                    @click="toggleEdit">
                    Edit
                  </SecondaryButton>

                  <SecondaryButton
                    v-if="!editDisabled && can_update_transporter"
                    class="m-1"
                    @click="updateTransporter">
                    Save
                  </SecondaryButton>
                </div>
              </form>
            </div>
          </div>
        </div>

        <SectionBorder />
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <div class="">
              <div class="text-lg mb-2 text-indigo-400">Addresses</div>

              <SecondaryButton @click="viewAddress(null)">Add (+)</SecondaryButton>

              <form class="mt-5">
                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">
                  All addresses:
                </label>
                <div>
                  <div>
                    <div v-if="viewAddressModal">
                      <address-modal
                        :address="currentAddress"
                        :related_class="relatedClass"
                        :related_id="transporter.id"
                        :show="viewAddressModal"
                        @close="closeModal" />
                    </div>

                    <ul class="w-3/2">
                      <li
                        v-for="n in transporter.addressable"
                        :key="n.id"
                        :value="n.id"
                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
                        <div class="flex row mt-1">
                          <div class="flex-none w-1/6">
                            <icon
                              v-if="n.address_type_id === 1"
                              class="mr-2 w-6 h-6 fill-green-200"
                              name="truck" />
                            <icon
                              v-if="n.address_type_id === 2"
                              class="mr-2 w-6 h-6 fill-green-200"
                              name="house" />
                            <icon
                              v-if="n.address_type_id === 3"
                              class="mr-2 w-6 h-6 fill-green-200"
                              name="envelope" />
                          </div>

                          <div class="flex-auto w-3/6">
                            {{ n.line_1 }} {{ n.line_2 }} {{ n.line_3 }}
                            {{ n.country }}
                            {{ n.code }}
                          </div>
                          <div class="flex-auto w-1/6">
                            <icon
                              v-if="n.is_primary === 1"
                              class="mr-2 w-6 h-6 fill-green-200"
                              name="tick-circle" />
                          </div>
                          <div class="flex-auto w-1/6">
                            <SecondaryButton
                              class="ml-2"
                              @click="viewAddress(n)">
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

        <SectionBorder />
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <div class="">
              <div class="text-lg mb-2 text-indigo-400">Contacts</div>

              <SecondaryButton @click="viewContact(null)">Add (+)</SecondaryButton>

              <contact-modal
                :contact="null"
                :related_class="relatedClass"
                :related_id="transporter.id"
                :show="viewContactModal"
                @close="closeContactModal" />

              <div class="mt-5">
                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">
                  All contacts:
                </label>
                <ul class="w-3/2">
                  <li
                    v-for="n in transporter.contactable"
                    :key="n.id"
                    :value="n.id"
                    class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
                    <number-contact-detail-modal
                      :contact_type="contact_type"
                      :related_class="relatedClassContact"
                      :related_id="n.id"
                      :show="viewNumberContactDetailModal"
                      @close="closeNumberContactDetailModal" />

                    <email-contact-detail-modal
                      :contact_type="contact_type"
                      :related_class="relatedClassContact"
                      :related_id="n.id"
                      :show="viewEmailContactDetailModal"
                      @close="closeEmailDetailModal" />

                    <div class="flex row mt-1">
                      <div class="flex-none w-1/6">
                        <icon
                          class="mr-2 w-6 h-6 fill-green-200"
                          name="person" />
                      </div>

                      <div class="flex-auto w-3/6">
                        {{ n.title }} {{ n.first_name }} {{ n.last_legal_name }}

                        <div v-if="n.job_description">({{ n.job_description }})</div>
                      </div>
                      <div class="flex-auto w-1/6">
                        <icon
                          v-if="n.is_primary === 1"
                          class="mr-2 w-6 h-6 fill-green-200"
                          name="tick-circle" />
                      </div>
                      <div class="flex-auto w-1/6">
                        <SecondaryButton
                          class="ml-2"
                          @click="viewNumberContactDetail">
                          <PhoneIcon
                            aria-hidden="true"
                            class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" />
                        </SecondaryButton>

                        <SecondaryButton
                          class="ml-2"
                          @click="viewEmailContactDetail">
                          <EnvelopeIcon
                            aria-hidden="true"
                            class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" />
                        </SecondaryButton>

                        <Link
                          :href="route('contact.show', n.id)"
                          class="inline-flex items-center ml-2 mt-3 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                          View
                        </Link>
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
