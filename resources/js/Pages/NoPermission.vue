<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                No Permission
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="bg-white py-24 sm:py-32">
                        <div class="mx-auto max-w-7xl px-6 lg:px-8">
                            <div class="mx-auto max-w-2xl lg:max-w-none">


                                <div class="text-center">
                                    <p class="text-base font-semibold text-indigo-600">403</p>
                                    <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">No permission.</h1>
                                    <p class="mt-6 text-base leading-7 text-gray-600">Sorry, you dont have the permission to view this page.</p>
                                    <div class="mt-10 flex items-center justify-center gap-x-6">

                                        <Link :href="route('dashboard')" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Go back home</Link>

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
