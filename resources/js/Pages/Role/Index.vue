<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref, watch } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';

  const props = defineProps({
    roles: Array,
    all_permissions: Array,
  });

  const can_manage = computed(() =>
    usePage().props.roles_permissions.permissions.includes('manage_users')
  );

  // ─── Role selection ──────────────────────────────────────────────
  const selectedRoleId = ref(null);
  const selectedRole = computed(() =>
    props.roles.find((r) => r.id === selectedRoleId.value) ?? null
  );

  // ─── Local edit state (tracks unsaved changes) ───────────────────
  const localIsActive = ref(true);
  const localPermissions = ref([]); // array of permission name strings
  const isSaving = ref(false);

  const isDirty = computed(() => {
    if (!selectedRole.value) return false;
    if (localIsActive.value !== Boolean(selectedRole.value.is_active)) return true;
    const local = [...localPermissions.value].sort().join(',');
    const server = selectedRole.value.permissions.map((p) => p.name).sort().join(',');
    return local !== server;
  });

  function selectRole(role) {
    if (isDirty.value && !confirm('You have unsaved changes. Switch role anyway?')) return;
    selectedRoleId.value = role.id;
    syncLocalFromServer(role);
  }

  function syncLocalFromServer(role) {
    localIsActive.value = Boolean(role.is_active);
    localPermissions.value = role.permissions.map((p) => p.name);
  }

  // Keep local state in sync when server props refresh (after save)
  watch(selectedRole, (role) => {
    if (role && !isDirty.value) syncLocalFromServer(role);
  });

  // ─── Permission helpers ──────────────────────────────────────────
  function hasLocal(permName) {
    return localPermissions.value.includes(permName);
  }

  function toggleLocalPerm(permName) {
    const idx = localPermissions.value.indexOf(permName);
    if (idx >= 0) localPermissions.value.splice(idx, 1);
    else localPermissions.value.push(permName);
  }

  function selectAll() {
    localPermissions.value = props.all_permissions.map((p) => p.name);
  }

  function clearAll() {
    localPermissions.value = [];
  }

  // ─── Save / Cancel ───────────────────────────────────────────────
  function save() {
    if (!selectedRole.value || isSaving.value) return;
    isSaving.value = true;
    router.put(
      route('roles.update', selectedRole.value.id),
      {
        is_active: localIsActive.value,
        permissions: localPermissions.value,
      },
      {
        preserveScroll: true,
        only: ['roles'],
        onFinish: () => { isSaving.value = false; },
      }
    );
  }

  function cancel() {
    if (selectedRole.value) syncLocalFromServer(selectedRole.value);
  }

  // ─── Formatting helpers ──────────────────────────────────────────
  const roleColors = {
    1: { badge: 'bg-gray-100 text-gray-700 ring-gray-400/20', dot: 'bg-gray-400' },
    2: { badge: 'bg-red-50 text-red-700 ring-red-500/20', dot: 'bg-red-500' },
    3: { badge: 'bg-yellow-50 text-yellow-800 ring-yellow-500/20', dot: 'bg-yellow-500' },
    4: { badge: 'bg-green-50 text-green-700 ring-green-500/20', dot: 'bg-green-500' },
    5: { badge: 'bg-blue-50 text-blue-700 ring-blue-500/20', dot: 'bg-blue-500' },
    6: { badge: 'bg-indigo-50 text-indigo-700 ring-indigo-500/20', dot: 'bg-indigo-500' },
    7: { badge: 'bg-purple-50 text-purple-700 ring-purple-500/20', dot: 'bg-purple-500' },
  };
  const fallbackColor = { badge: 'bg-orange-50 text-orange-700 ring-orange-500/20', dot: 'bg-orange-500' };

  function colorFor(id) {
    return roleColors[id] ?? fallbackColor;
  }

  function formatPerm(name) {
    return name.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
  }

  const enabledCount = computed(() => localPermissions.value.length);
  const totalCount = computed(() => props.all_permissions.length);
</script>

<template>
  <AppLayout title="Roles">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles &amp; Permissions</h2>
    </template>

    <div class="py-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white m-2 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="flex divide-x divide-gray-100 min-h-[600px]">

            <!-- ── Left panel: role list ─────────────────────── -->
            <div class="w-72 flex-shrink-0 flex flex-col">
              <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Roles</h3>
              </div>

              <ul class="flex-1 overflow-y-auto divide-y divide-gray-50 py-1">
                <li
                  v-for="role in roles"
                  :key="role.id"
                  @click="selectRole(role)"
                  :class="[
                    'group flex flex-col gap-1 px-4 py-3 cursor-pointer transition-colors select-none',
                    selectedRoleId === role.id
                      ? 'bg-indigo-50 border-l-2 border-indigo-500'
                      : 'border-l-2 border-transparent hover:bg-gray-50',
                  ]">

                  <div class="flex items-center justify-between">
                    <!-- Name badge -->
                    <span
                      :class="[colorFor(role.id).badge, 'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset']">
                      <span
                        :class="[colorFor(role.id).dot, 'mr-1.5 h-1.5 w-1.5 rounded-full flex-shrink-0']" />
                      {{ role.name }}
                    </span>

                    <!-- Active indicator -->
                    <span
                      :class="role.is_active
                        ? 'bg-green-50 text-green-700 ring-green-600/20'
                        : 'bg-gray-100 text-gray-400 ring-gray-400/20'"
                      class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset">
                      {{ role.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>

                  <p class="text-xs text-gray-400 pl-0.5">
                    {{ role.permissions.length }} / {{ all_permissions.length }} permissions
                  </p>
                </li>
              </ul>
            </div>

            <!-- ── Right panel: role editor ─────────────────── -->
            <div class="flex-1 flex flex-col">

              <!-- Empty state -->
              <div
                v-if="!selectedRole"
                class="flex-1 flex flex-col items-center justify-center text-center p-12">
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                  <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 12c0 6.627 5.373 12 12 12s12-5.373 12-12c0-2.042-.507-3.968-1.397-5.654" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Select a role to view and edit its permissions</p>
                <p class="text-xs text-gray-400 mt-1">Changes won't be saved until you click Save</p>
              </div>

              <!-- Role editor -->
              <div v-else class="flex-1 flex flex-col">

                <!-- Header bar -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50">
                  <div class="flex items-center gap-x-3">
                    <span
                      :class="[colorFor(selectedRole.id).badge, 'inline-flex items-center rounded-lg px-4 py-2 text-base font-semibold ring-1 ring-inset']">
                      <span :class="[colorFor(selectedRole.id).dot, 'mr-2.5 h-2.5 w-2.5 rounded-full flex-shrink-0']" />
                      {{ selectedRole.name }}
                    </span>
                    <span
                      v-if="isDirty"
                      class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                      Unsaved changes
                    </span>
                  </div>

                  <p class="text-xs text-gray-400" v-if="!can_manage">
                    Read-only — requires <code class="bg-gray-100 rounded px-1">manage_users</code>
                  </p>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                  <!-- Status section -->
                  <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Status</h4>
                    <div class="flex items-center gap-x-4">
                      <!-- Toggle -->
                      <button
                        v-if="can_manage"
                        @click="localIsActive = !localIsActive"
                        :class="localIsActive ? 'bg-indigo-600' : 'bg-gray-200'"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                        <span
                          :class="localIsActive ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                      </button>
                      <span class="text-sm font-medium" :class="localIsActive ? 'text-gray-900' : 'text-gray-400'">
                        {{ localIsActive ? 'Active' : 'Inactive' }}
                      </span>
                      <span v-if="localIsActive !== Boolean(selectedRole.is_active)" class="text-xs text-amber-600">
                        (was {{ selectedRole.is_active ? 'Active' : 'Inactive' }})
                      </span>
                    </div>
                  </div>

                  <!-- Divider -->
                  <div class="border-t border-gray-100" />

                  <!-- Permissions section -->
                  <div>
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Permissions
                        <span class="ml-2 text-indigo-500 font-bold">{{ enabledCount }} / {{ totalCount }}</span>
                      </h4>
                      <div v-if="can_manage" class="flex items-center gap-x-2">
                        <button
                          @click="selectAll"
                          class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                          Select all
                        </button>
                        <span class="text-gray-300">|</span>
                        <button
                          @click="clearAll"
                          class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                          Clear all
                        </button>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                      <label
                        v-for="perm in all_permissions"
                        :key="perm.id"
                        :class="[
                          'flex items-center gap-x-3 rounded-lg border px-4 py-2.5 transition-colors',
                          can_manage ? 'cursor-pointer' : 'cursor-default',
                          hasLocal(perm.name)
                            ? 'border-indigo-200 bg-indigo-50'
                            : 'border-gray-200 bg-white hover:bg-gray-50',
                        ]">
                        <input
                          type="checkbox"
                          :checked="hasLocal(perm.name)"
                          :disabled="!can_manage"
                          @change="toggleLocalPerm(perm.name)"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 disabled:opacity-50" />
                        <span
                          :class="[
                            'text-sm select-none',
                            hasLocal(perm.name) ? 'text-indigo-800 font-medium' : 'text-gray-600',
                          ]">
                          {{ formatPerm(perm.name) }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Footer: Save / Cancel -->
                <div
                  v-if="can_manage"
                  class="flex items-center justify-end gap-x-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                  <button
                    @click="cancel"
                    :disabled="!isDirty || isSaving"
                    class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    Cancel
                  </button>
                  <button
                    @click="save"
                    :disabled="!isDirty || isSaving"
                    :class="isDirty && !isSaving ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-indigo-300 cursor-not-allowed'"
                    class="inline-flex items-center gap-x-2 rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed transition">
                    <svg
                      v-if="isSaving"
                      class="h-4 w-4 animate-spin"
                      fill="none"
                      viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    {{ isSaving ? 'Saving…' : 'Save Changes' }}
                  </button>
                </div>

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
