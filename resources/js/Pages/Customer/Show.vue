<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, inject, ref } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import AddressModal from '@/Components/UI/AddressModal.vue';
import ContactModal from '@/Components/UI/ContactModal.vue';
import NumberContactDetailModal from '@/Components/UI/NumberContactDetailModal.vue';
import EmailContactDetailModal from '@/Components/UI/EmailContactDetailModal.vue';
import { EnvelopeIcon, PhoneIcon } from '@heroicons/vue/20/solid';

const swal = inject('$swal');

  const props = defineProps({
    customer: Object,
    invoice_basis: Object,
    terms_of_payment_basis: Object,
    terms_of_payment: Object,
    customer_rating: Object,
    staff: Object,
    contact_type: Object,
    all_customer_parents: Object,
  });
  const permissions = computed(() => usePage().props.permissions);
  const emptyErrors = computed(
    () =>
      Object.keys(customerForm.errors).length === 0 &&
      customerForm.errors.constructor === Object
  );

  let customerForm = useForm({
    _method: 'PUT',
    first_name: props.customer.first_name ?? null,
    last_legal_name: props.customer.last_legal_name ?? null,
    nickname: props.customer.nickname ?? null,
    title: props.customer.title ?? null,
    customer_parent_id: props.customer.customer_parent_id ?? null,
    job_description: props.customer.job_description ?? null,
    id_reg_no: props.customer.id_reg_no ?? null,
    is_active: props.customer.is_active ?? null,
    terms_of_payment_id: props.customer.terms_of_payment_id ?? null,
    terms_of_payment_basis_id: props.customer.terms_of_payment_basis_id ?? 1,
    invoice_basis_id: props.customer.invoice_basis_id ?? null,
    customer_rating_id: props.customer.customer_rating_id ?? null,
    days_overdue_allowed_id: props.customer.days_overdue_allowed_id ?? null,
    is_vat_exempt: props.customer.is_vat_exempt ?? null,
    is_vat_cert_received: props.customer.is_vat_cert_received ?? null,
    credit_limit: props.customer.credit_limit ?? null,
    credit_limit_hard: props.customer.credit_limit_hard ?? null,
    comment: props.customer.comment ?? null,
  });

  const updateCustomer = () => {
    customerForm.put(route('customer.update', props.customer.id), {
      preserveScroll: true,
      onSuccess: () => {
        swal(usePage().props.jetstream.flash?.banner || '');
        toggleEdit();
      },
    });
  };

  let staffForm = useForm({
    staff_id: 1,
    related_id: props.customer.id ?? 1,
    related_class: 'App\\Models\\Customer',
  });

  const addStaff = () =>
    staffForm.post(route('staff_link.store'), {
      preserveScroll: true,
      onSuccess: () => {
        swal('Staff member linked.');
      },
    });

  const deleteStaff = (id, name) => {
    if (confirm('Sure you want to delete ' + name + '?')) {
      staffForm.staff_id = id;

      staffForm.put(route('staff_link.update', { staff_link: id }), {
        preserveScroll: true,
        onSuccess: () => {
          swal('Staff member unlinked.');
        },
      });
    } else {
    }
  };

  const viewAddressModal = ref(false);
  const viewContactModal = ref(false);
  const viewNumberContactDetailModal = ref(false);
  const viewEmailContactDetailModal = ref(false);

  const currentAddress = ref(null);
  const currentContact = ref(null);

  const relatedClass = ref('App\\Models\\Customer');
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
    router.get('customer/' + id);
  };

  const roles_permissions = computed(() => usePage().props.roles_permissions);
  const can_update_customer = computed(() =>
    usePage().props.roles_permissions.permissions.includes('update_customer')
  );
</script>

<template>
  <AppLayout title="Customer">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Customer /
        <span class="text-indigo-400">{{ customerForm.last_legal_name }}</span>
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
                        Static customer information.
                      </p>
                    </div>

                    <div
                      class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Customer Parent
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.customer_parent_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in all_customer_parents"
                              :key="n.id"
                              :value="n.id">
                              {{ n.last_legal_name }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.terms_of_payment_id"
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
                            v-model="customerForm.first_name"
                            :disabled="editDisabled"
                            autocomplete="given-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="first_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="customerForm.errors.first_name"
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
                            v-model="customerForm.last_legal_name"
                            :disabled="editDisabled"
                            autocomplete="family-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="last_legal_name"
                            type="text" />
                        </div>
                        <InputError
                          :message="customerForm.errors.last_legal_name"
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
                            v-model="customerForm.nickname"
                            :disabled="editDisabled"
                            autocomplete="nickname"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="nickname"
                            type="text" />
                        </div>
                        <InputError
                          :message="customerForm.errors.nickname"
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
                            v-model="customerForm.id_reg_no"
                            :disabled="editDisabled"
                            autocomplete="id_reg_no"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="id_reg_no"
                            type="text" />
                        </div>
                        <InputError
                          :message="customerForm.errors.id_reg_no"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="id_reg_no">
                          Customer rating
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.customer_rating_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in customer_rating"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.customer_rating_id"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="id_reg_no">
                          Customer status
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.is_active"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              key="1"
                              value="1">
                              Active
                            </option>

                            <option
                              key="0"
                              value="0">
                              Suspended
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.is_active"
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
                          v-model="customerForm.comment"
                          :disabled="editDisabled"
                          :rows="6"
                          class="mt-1 block w-full"
                          placeholder="Optional comments..."
                          type="text" />
                        <InputError
                          :message="customerForm.errors.comment"
                          class="mt-2" />
                      </div>
                    </div>
                  </div>

                  <div
                    class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                      <h2 class="text-base font-semibold leading-7 text-gray-900">
                        Payment Information
                      </h2>
                      <p class="mt-1 text-sm leading-6 text-gray-600">
                        Customer payment information.
                      </p>
                    </div>

                    <div
                      class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Credit limit
                        </label>
                        <div class="mt-2">
                          <input
                            v-model="customerForm.credit_limit"
                            :disabled="editDisabled"
                            class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            type="number" />
                        </div>
                        <InputError
                          :message="customerForm.errors.credit_limit"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Credit Hard limit
                        </label>
                        <div class="mt-2">
                          <input
                            v-model="customerForm.credit_limit_hard"
                            :disabled="editDisabled"
                            class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            type="number" />
                        </div>
                        <InputError
                          :message="customerForm.errors.credit_limit_hard"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Terms of payment basis
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.terms_of_payment_basis_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in terms_of_payment_basis"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.terms_of_payment_id"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Terms of payment
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.terms_of_payment_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in terms_of_payment"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.terms_of_payment_id"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Day overdue allowed
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.days_overdue_allowed_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in terms_of_payment"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.days_overdue_allowed_id"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="first_name">
                          Invoice basis
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.invoice_basis_id"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option
                              v-for="n in invoice_basis"
                              :key="n.id"
                              :value="n.id">
                              {{ n.value }}
                            </option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.invoice_basis_id"
                          class="mt-2" />
                      </div>

                      <div class="sm:col-span-3">
                        <label
                          class="block text-sm font-medium leading-6 text-gray-900"
                          for="is_vat_exempt">
                          VAT Exempt
                        </label>
                        <div class="mt-2">
                          <select
                            v-model="customerForm.is_vat_exempt"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option :value="1">Yes</option>
                            <option :value="0">No</option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.is_vat_exempt"
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
                            v-model="customerForm.is_vat_cert_received"
                            :disabled="editDisabled"
                            class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option :value="1">Yes</option>
                            <option :value="0">No</option>
                          </select>
                        </div>
                        <InputError
                          :message="customerForm.errors.is_vat_cert_received"
                          class="mt-2" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                  <SecondaryButton
                    v-if="can_update_customer"
                    class="m-1"
                    @click="toggleEdit">
                    Edit
                  </SecondaryButton>

                  <SecondaryButton
                    v-if="!editDisabled && can_update_customer"
                    class="m-1"
                    @click="updateCustomer">
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
              <div class="text-lg mb-2 text-indigo-400">Staff allocated</div>

              <div>
                <div class="col-span-4">
                  <label class="block text-sm font-medium leading-6 text-gray-900">
                    Available staff:
                  </label>

                  <div class="">
                    <select
                      v-model="staffForm.staff_id"
                      class="mt-2 block w-1/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                      <option
                        v-for="n in staff"
                        :key="n.id"
                        :value="n.id">
                        {{ n.first_name }}
                      </option>
                    </select>

                    <SecondaryButton
                      class="mt-2"
                      @click="addStaff">
                      Link (+)
                    </SecondaryButton>
                  </div>

                  <InputError
                    :message="staffForm.errors.staff_id"
                    class="mt-2" />
                  <InputError
                    :message="staffForm.errors.related_id"
                    class="mt-2" />
                </div>
              </div>

              <form class="mt-5">
                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">
                  Staff members linked:
                </label>
                <div>
                  <div class="flex">
                    <ul class="w-1/3">
                      <li
                        v-for="n in customer.staff"
                        :key="n.id"
                        :value="n.id"
                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
                        <div class="flex mt-1">
                          <div class="flex-none w-1/3">
                            <icon
                              class="mr-2 w-6 h-6 fill-green-200"
                              name="tick-circle" />
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
                        :related_id="customer.id"
                        :show="viewAddressModal"
                        @close="closeModal" />
                    </div>

                    <ul class="w-3/2">
                      <li
                        v-for="n in customer.addressable"
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
                :related_id="customer.id"
                :show="viewContactModal"
                @close="closeContactModal" />

              <div class="mt-5">
                <label class="block mb-1 text-gray-500 dark:text-gray-300 font-medium">
                  All contacts:
                </label>
                <ul class="w-3/2">
                  <li
                    v-for="n in customer.contactable"
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
