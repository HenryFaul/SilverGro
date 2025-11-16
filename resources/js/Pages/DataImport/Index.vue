<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref } from 'vue';

  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { router, useForm, usePage } from '@inertiajs/vue3';

  import NProgress from 'nprogress';

  let timeout = null;

  router.on('start', () => {
    timeout = setTimeout(() => NProgress.start(), 250);
  });

  router.on('progress', (event) => {
    if (NProgress.isStarted() && event.detail.progress.percentage) {
      NProgress.set((event.detail.progress.percentage / 100) * 0.9);
    }
  });

  router.on('finish', (event) => {
    clearTimeout(timeout);
    if (!NProgress.isStarted()) {
    } else if (event.detail.visit.completed) {
      NProgress.done();
    } else if (event.detail.visit.interrupted) {
      NProgress.set(0);
    } else if (event.detail.visit.cancelled) {
      NProgress.done();
      NProgress.remove();
    }
  });

  const props = defineProps({
    //user: Object,
  });

  const form = useForm({
    // Don't store file in form object - it doesn't work well with Inertia
  });

  const permissions = computed(() => usePage().props.permissions);

  const FilePreview = ref(null);
  const FileName = ref(null);
  const FileInput = ref(null);
  const isUploading = ref(false);

  const UploadFile = () => {
    console.log('UploadFile called');

    if (FileInput.value && FileInput.value.files.length > 0) {
      const file = FileInput.value.files[0];
      console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);

      isUploading.value = true;

      // Use Inertia's router with forceFormData to upload the file
      // Backend now handles Inertia's serialization issues
      router.post(
        route('import.process'),
        {
          file: file,
        },
        {
          forceFormData: true,
          preserveScroll: true,
          onSuccess: (page) => {
            console.log('Upload successful');
            isUploading.value = false;
            clearFileInput();
          },
          onError: (errors) => {
            console.error('Upload errors:', errors);
            isUploading.value = false;
          },
          onFinish: () => {
            isUploading.value = false;
          },
        }
      );
    } else {
      console.error('No file selected');
      alert('Please select a file first');
    }
  };

  const updateFilePreview = () => {
    const file = FileInput.value.files[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = (e) => {
      FilePreview.value = e.target.result;
      FileName.value = file.name;
    };

    reader.readAsDataURL(file);
  };

  const selectNewFile = () => {
    FileInput.value.click();
  };

  const clearFileInput = () => {
    if (FileInput.value) {
      FileInput.value.value = '';
      FilePreview.value = null;
      FileName.value = null;
    }
  };
</script>

<template>
  <AppLayout title="DataImports">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Imports</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="m-2 p-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              Import Your Data
              <span class="text-xs text-gray-500">(v4.0 - Backend Fix)</span>
            </h2>

            <div>
              <form @submit.prevent="UploadFile">
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                  <div class="col-span-2">
                    <input
                      ref="FileInput"
                      class="hidden"
                      type="file"
                      @change="updateFilePreview" />

                    <div
                      v-show="FilePreview"
                      class="mt-2">
                      {{ FileName }}
                    </div>

                    <SecondaryButton
                      class="mt-2 mr-2"
                      type="button"
                      @click.prevent="selectNewFile">
                      Select A File
                    </SecondaryButton>
                  </div>

                  <PrimaryButton
                    :loading="isUploading"
                    class="mt-2 mr-2"
                    load
                    type="button"
                    @click.prevent="UploadFile">
                    Import File
                  </PrimaryButton>

                  <div class="mt-2 pt-2 text-lg">
                    <span v-if="$page.props.jetstream.flash">
                      {{ $page.props.jetstream.flash.banner }}
                    </span>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
