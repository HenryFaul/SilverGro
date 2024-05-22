<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import DriverSlideOver from  "@/Components/UI/DriverSlideOver.vue";




const props = defineProps({
    notifications: Object,
});
const permissions = computed(() => usePage().props.permissions)


</script>

<template>
    <AppLayout title="Notifications">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white m-2 p-2 overflow-hidden shadow-xl sm:rounded-lg">

                    <h1 class="text-3xl mb-4">Your Notifications</h1>

                    <section v-if="notifications.data.length" class="text-gray-700 dark:text-gray-400">
                        <div v-for="notification in notifications.data" :key="notification.id" class="border-b border-gray-200 dark:border-gray-800 py-4 flex justify-between items-center">
                            <div>
                                    <span v-if="notification.type === 'App\\Notifications\\DealTicketApproved'">
                                          <Link href="/transaction_summary" method="get" target="_blank" :data="{ selected_trans_id: notification.data.transport_trans_id }">Deal Ticket was approved MQ:{{notification.data.a_mq}}</Link>
                                    </span>
                            </div>
                            <div>


                                <Link as="button" class="btn-outline underline text-indigo-400 text-xs font-medium uppercase" method="put" :href="route('notification.seen', { notification: notification.id })">Mark as read</Link>

                            </div>

                        </div>
                        <div v-if="notifications.data.length" class="w-full flex justify-center mt-5 mb-4">

                            <PaginationModified :links="notifications.links"/>

                        </div>
                    </section>
                    <div v-else>No unread notifications</div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
