<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';

  const props = defineProps({
    roles: Array,
    all_permissions: Array,
  });

  const can_manage = computed(() =>
    usePage().props.roles_permissions.permissions.includes('manage_users')
  );

  // --- Selected role ---
  const selectedRoleId = ref(null);
  const selectedRole = computed(() =>
    props.roles.find((r) => r.id === selectedRoleId.value) ?? null
  );

  function selectRole(role) {
    selectedRoleId.value = role.id;
  }

  // --- Role colour badge (matches Staff/Show pattern) ---
  const roleColors = {
    1: 'bg-gray-50 text-gray-600 ring-gray-500/10',
    2: 'bg-red-50 text-red-700 ring-red-600/10',
    3: 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
    4: 'bg-green-50 text-green-700 ring-green-600/20',
    5: 'bg-blue-50 text-blue-700 ring-blue-700/10',
    6: 'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    7: 'bg-purple-50 text-purple-700 ring-purple-700/10',
  };
  function badgeClass(id) {
    return (
      (roleColors[id] ?? 'bg-orange-100 text-orange-700 ring-orange-600/20') +
      ' inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset'
    );
  }

  // --- Toggle role active/inactive ---
  function toggleActive(role) {
    if (!can_manage.value) return;
    router.put(
      route('roles.update', role.id),
      { is_active: !role.is_active },
      { preserveScroll: true, only: ['roles'] }
    );
  }

  // --- Toggle permission on selected role ---
  function togglePermission(perm) {
    if (!can_manage.value || !selectedRole.value) return;
    router.put(
      route('roles.permission.toggle', selectedRole.value.id),
      { permission_name: perm.name },
      { preserveScroll: true, only: ['roles'] }
    );
  }

  function roleHasPermission(role, permName) {
    return role.permissions.some((p) => p.name === permName);
  }
</script>

<template>
  <AppLayout title="Roles">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles & Permissions</h2>
    </template>

    <div class="py-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        <div class="bg-white m-2 p-4 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="lg:grid lg:grid-cols-3 lg:gap-6">

            <!-- Left panel: Role list -->
            <div class="lg:col-span-1">
              <h3 class="text-lg font-semibold text-indigo-400 mb-3">Roles</h3>

              <ul class="divide-y divide-gray-100">
                <li
                  v-for="role in roles"
                  :key="role.id"
                  @click="selectRole(role)"
                  :class="[
                    'flex items-center justify-between gap-x-3 py-3 px-3 rounded-md cursor-pointer transition',
                    selectedRoleId === role.id
                      ? 'bg-indigo-50 ring-1 ring-indigo-300'
                      : 'hover:bg-gray-50',
                  ]">

                  <!-- Name + permission count -->
                  <div class="min-w-0">
                    <span :class="badgeClass(role.id)">{{ role.name }}</span>
                    <p class="mt-1 text-xs text-gray-400">
                      {{ role.permissions.length }} permission{{ role.permissions.length !== 1 ? 's' : '' }}
                    </p>
                  </div>

                  <!-- Active toggle -->
                  <div class="flex-none" @click.stop>
                    <button
                      v-if="can_manage"
                      @click="toggleActive(role)"
                      :class="role.is_active ? 'bg-indigo-600' : 'bg-gray-200'"
                      class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                      :title="role.is_active ? 'Active — click to deactivate' : 'Inactive — click to activate'">
                      <span
                        :class="role.is_active ? 'translate-x-4' : 'translate-x-0'"
                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                    </button>
                    <span
                      v-else
                      :class="role.is_active ? 'text-green-600' : 'text-gray-400'"
                      class="text-xs font-medium">
                      {{ role.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Right panel: Permissions for selected role -->
            <div class="mt-6 lg:mt-0 lg:col-span-2">
              <!-- No selection -->
              <div
                v-if="!selectedRole"
                class="flex h-full min-h-48 items-center justify-center rounded-lg border-2 border-dashed border-gray-200">
                <p class="text-sm text-gray-400">Select a role to manage its permissions</p>
              </div>

              <!-- Role selected -->
              <div v-else>
                <div class="flex items-center gap-x-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">
                    Permissions for
                    <span :class="badgeClass(selectedRole.id)" class="ml-1">{{ selectedRole.name }}</span>
                  </h3>
                  <span
                    :class="selectedRole.is_active ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-gray-100 text-gray-500 ring-gray-400/20'"
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset">
                    {{ selectedRole.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>

                <p v-if="!can_manage" class="mb-3 text-xs text-amber-600">
                  You need the <code>manage_users</code> permission to edit permissions.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  <div
                    v-for="perm in all_permissions"
                    :key="perm.id"
                    :class="[
                      'flex items-center justify-between rounded-md border px-4 py-2.5 transition',
                      roleHasPermission(selectedRole, perm.name)
                        ? 'border-indigo-200 bg-indigo-50'
                        : 'border-gray-200 bg-white',
                    ]">
                    <span class="text-sm text-gray-700">{{ perm.name }}</span>

                    <button
                      v-if="can_manage"
                      @click="togglePermission(perm)"
                      :class="roleHasPermission(selectedRole, perm.name) ? 'bg-indigo-600' : 'bg-gray-200'"
                      class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                      <span
                        :class="roleHasPermission(selectedRole, perm.name) ? 'translate-x-4' : 'translate-x-0'"
                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                    </button>

                    <span
                      v-else
                      :class="roleHasPermission(selectedRole, perm.name) ? 'text-indigo-600' : 'text-gray-300'"
                      class="text-sm font-medium">
                      {{ roleHasPermission(selectedRole, perm.name) ? 'On' : 'Off' }}
                    </span>
                  </div>
                </div>

                <!-- Summary -->
                <p class="mt-3 text-xs text-gray-400">
                  {{ selectedRole.permissions.length }} of {{ all_permissions.length }} permissions enabled
                </p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
