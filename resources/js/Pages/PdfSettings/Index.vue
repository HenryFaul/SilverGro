<template>
  <AppLayout title="PDF Settings">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        PDF Document Settings
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <!-- Company Information Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                  Company Information
                </h3>

                <div class="grid grid-cols-1 gap-6">
                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="company_name">
                      Company Name *
                    </label>
                    <input
                      id="company_name"
                      v-model="form.company_name"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required
                      type="text" />
                    <div
                      v-if="form.errors.company_name"
                      class="text-red-600 text-sm mt-1">
                      {{ form.errors.company_name }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Address Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="po_box">
                      P.O. Box
                    </label>
                    <input
                      id="po_box"
                      v-model="form.po_box"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="street_address">
                      Street Address
                    </label>
                    <input
                      id="street_address"
                      v-model="form.street_address"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="city">
                      City
                    </label>
                    <input
                      id="city"
                      v-model="form.city"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="postal_code">
                      Postal Code
                    </label>
                    <input
                      id="postal_code"
                      v-model="form.postal_code"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div class="md:col-span-2">
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="country">
                      Country
                    </label>
                    <input
                      id="country"
                      v-model="form.country"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>
                </div>
              </div>

              <!-- Contact Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                  Contact Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="phone">
                      Phone
                    </label>
                    <input
                      id="phone"
                      v-model="form.phone"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="fax">
                      Fax
                    </label>
                    <input
                      id="fax"
                      v-model="form.fax"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="text" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="email">
                      Email
                    </label>
                    <input
                      id="email"
                      v-model="form.email"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="email" />
                  </div>

                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700"
                      for="website">
                      Website
                    </label>
                    <input
                      id="website"
                      v-model="form.website"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      type="url" />
                  </div>
                </div>
              </div>

              <!-- Logo Section -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Company Logo</h3>

                <div class="flex items-start space-x-6">
                  <!-- Current Logo Preview -->
                  <div class="flex-shrink-0">
                    <img
                      v-if="settings?.logo_path"
                      :src="`/${settings.logo_path}?t=${Date.now()}`"
                      alt="Current Logo"
                      class="w-48 h-auto border border-gray-300 rounded-lg shadow-sm" />
                  </div>

                  <!-- Upload Section -->
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Upload New Logo
                    </label>
                    <input
                      ref="logoInput"
                      accept="image/jpeg,image/jpg,image/png"
                      class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                      type="file"
                      @change="handleLogoChange" />
                    <p class="mt-2 text-sm text-gray-500">
                      JPG, JPEG or PNG. Max size 2MB.
                    </p>

                    <!-- Preview new logo -->
                    <div
                      v-if="logoPreview"
                      class="mt-4">
                      <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                      <img
                        :src="logoPreview"
                        alt="New Logo Preview"
                        class="w-48 h-auto border border-gray-300 rounded-lg shadow-sm" />
                    </div>

                    <button
                      class="mt-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                      type="button"
                      @click="resetLogo">
                      Reset to Default Logo
                    </button>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div
                class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                <button
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  type="submit">
                  {{ form.processing ? 'Saving...' : 'Save Settings' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    settings: Object,
  });

  const logoInput = ref(null);
  const logoPreview = ref(null);

  const form = useForm({
    company_name: props.settings?.company_name || '',
    po_box: props.settings?.po_box || '',
    street_address: props.settings?.street_address || '',
    city: props.settings?.city || '',
    postal_code: props.settings?.postal_code || '',
    country: props.settings?.country || '',
    phone: props.settings?.phone || '',
    fax: props.settings?.fax || '',
    email: props.settings?.email || '',
    website: props.settings?.website || '',
    logo: null,
  });

  const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
      form.logo = file;

      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        logoPreview.value = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  const submit = () => {
    form.post(route('pdf-settings.update'), {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        logoPreview.value = null;
        if (logoInput.value) {
          logoInput.value.value = '';
        }
      },
    });
  };

  const resetLogo = () => {
    if (confirm('Are you sure you want to reset the logo to default?')) {
      form.post(route('pdf-settings.reset-logo'), {
        preserveScroll: true,
        onSuccess: () => {
          logoPreview.value = null;
          if (logoInput.value) {
            logoInput.value.value = '';
          }
        },
      });
    }
  };
</script>
