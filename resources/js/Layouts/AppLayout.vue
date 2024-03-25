<script setup>
import {computed, ref} from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});


const classesCustomers = computed(() => {
    return route().current('customer.*') || route().current('customer_parent.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesSuppliers = computed(() => {
    return route().current('supplier.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesProducts = computed(() => {
    return route().current('product.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesContact = computed(() => {
    return route().current('contact.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});


const classesTransSummary = computed(() => {
    return route().current('transaction_summary.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesTransporters = computed(() => {
    return route().current('regular_driver.*') || route().current('transporter.*')  || route().current('regular_vehicle.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});


const classesSystemPlayer = computed(() => {
    return route().current('customer.*') || route().current('contact.*') || route().current('supplier.*') || route().current('transporter.*') || route().current('customer_parent.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesViews = computed(() => {
    return route().current('planning.diary') ||route().current('planning.week') ||route().current('transaction_spreadsheet.index')
    ||route().current('debtor.*')  || route().current('pc_overview.index')
    || route().current('sc_overview.index')  || route().current('home_overview.index')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});


const classesAdmin= computed(() => {
    return route().current('staff.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesReports = computed(() => {
    return route().current('custom_report.*') || route().current('transport_transaction.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});



const classesPlanning = computed(() => {
    return route().current('planning.*') || route().current('transport_transaction.*')
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});

const classesSetup = computed(() => {
    return false
        ? 'border-b-2 border-indigo-400  focus:outline-none focus:border-indigo-700 transition'
        : '';
});


const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Home
                                </NavLink>
                            </div>


                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                    Create New Team
                                                </DropdownLink>

                                                <div class="border-t border-gray-200" />

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <div class="border-t border-gray-200" />

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <ResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </ResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex">
                        <div class="mr-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="#" onclick="history.back();return false;">Go Back</a>
                        </div>
                        <slot name="header" />

                        <div >
                            <div class="hidden sm:flex sm:items-center sm:ml-6">

                                <!-- Customers -->
                                <div class="ml-3 relative">
                                    <div :class="classesCustomers">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                            <span  class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                            Customers

                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>

                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!-- Account Management -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Customers
                                                </div>

                                                <DropdownLink :href="route('customer_parent.index')">
                                                    Customer Parents
                                                </DropdownLink>

                                                <DropdownLink :href="route('customer.index')">
                                                    Customers
                                                </DropdownLink>

                                                <div class="border-t border-gray-100" />

                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>

                                <div :class="classesSuppliers">
                                    <DropdownLink  :href="route('supplier.index')">
                                        Suppliers
                                    </DropdownLink>
                                </div>

                                <!-- Transporters -->
                                <div class="ml-3 relative">
                                    <div :class="classesTransporters">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                            <span  class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                Transporters

                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>

                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!-- Account Management -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Transporters
                                                </div>

                                                <DropdownLink :href="route('transporter.index')">
                                                    Transporters
                                                </DropdownLink>

                                                <DropdownLink :href="route('regular_driver.index')">
                                                    Regular Drivers
                                                </DropdownLink>

                                                <DropdownLink :href="route('regular_vehicle.index')">
                                                    Regular Vehicles
                                                </DropdownLink>

                                                <div class="border-t border-gray-100" />

                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>

                                <div :class="classesProducts">
                                    <DropdownLink :href="route('product.index')">
                                        Products
                                    </DropdownLink>
                                </div>

                                <div :class="classesTransSummary">
                                    <DropdownLink :href="route('transaction_summary.index')">
                                        Trades
                                    </DropdownLink>

                                </div>


                                <!-- Views -->
                                <div class="ml-3 relative">

                                    <div :class="classesViews">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                            <span  class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                Views

                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>

                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!-- Account Management -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Views
                                                </div>

                                                <DropdownLink :href="route('planning.diary')">
                                                    Diary View
                                                </DropdownLink>

                                                <DropdownLink :href="route('planning.week')">
                                                    Week view
                                                </DropdownLink>

                                                <DropdownLink :href="route('transaction_summary.index')">
                                                    Trade view
                                                </DropdownLink>

                                                <DropdownLink :href="route('transaction_spreadsheet.index')">
                                                    Spreadsheet view
                                                </DropdownLink>

                                                <DropdownLink :href="route('debtor.index')">
                                                    Debtor Standing
                                                </DropdownLink>

                                                <DropdownLink :href="route('pc_overview.index')">
                                                    PC Overview
                                                </DropdownLink>

                                                <DropdownLink :href="route('sc_overview.index')">
                                                    SC Overview
                                                </DropdownLink>

                                                <DropdownLink :href="route('home_overview.index')">
                                                    Home Overview
                                                </DropdownLink>


                                                <div class="border-t border-gray-100" />

                                            </template>
                                        </Dropdown>
                                    </div>


                                </div>

                                <div :class="classesContact">
                                    <DropdownLink :href="route('contact.index')">
                                        Contacts
                                    </DropdownLink>
                                </div>


                                <!-- Admin Dropdown -->
                                <div class="ml-3 relative">
                                    <div :class="classesAdmin">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                            <span  class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                            Admin

                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>

                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!-- Account Management -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Admin
                                                </div>
                                                <DropdownLink :href="route('staff.index')">
                                                    Staff
                                                </DropdownLink>

                                                <div class="border-t border-gray-100" />

                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>

                                <!-- Reports Dropdown -->
                                <div class="ml-3 relative">

                                    <div :class="classesReports">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                            <span  class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                Reports
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>

                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!-- Account Management -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Workflow
                                                </div>

                                                <DropdownLink :href="route('custom_report.index')">
                                                    Report Setup
                                                </DropdownLink>

                                                <DropdownLink :href="route('transport_transaction.index')">
                                                    Report Exporter
                                                </DropdownLink>

                                                <div class="border-t border-gray-100" />

                                            </template>
                                        </Dropdown>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
