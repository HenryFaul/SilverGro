<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { computed, ref, watch } from 'vue';
  import { router, useForm, usePage } from '@inertiajs/vue3';

  const props = defineProps({
    trade_rules: Array,
    trade_rule_opps: Array,
    all_roles: Array,
  });

  const can_manage = computed(() =>
    usePage().props.roles_permissions.permissions.includes('manage_users')
  );

  // ─── Trigger descriptions (conditions are hardcoded in DealTicket.php) ──
  const OPP_TRIGGERS = {
    1: 'Triggered when supplier payment terms are C.O.D (terms_of_payment_id = 1)',
    2: 'Triggered when a customer account is inactive / suspended (is_active ≠ 1)',
  };
  function triggerFor(opp) {
    return OPP_TRIGGERS[opp.id] ?? 'Custom trigger — requires code change to activate';
  }

  // ─── Selection ───────────────────────────────────────────────────────────
  // type: 'trade_rule' | 'trade_rule_opp' | 'new' | null
  const selectedType = ref(null);
  const selectedId   = ref(null);

  const selectedRule = computed(() =>
    selectedType.value === 'trade_rule'
      ? props.trade_rules.find((r) => r.id === selectedId.value) ?? null
      : null
  );
  const selectedOpp = computed(() =>
    selectedType.value === 'trade_rule_opp'
      ? props.trade_rule_opps.find((r) => r.id === selectedId.value) ?? null
      : null
  );

  // ─── Local edit state ────────────────────────────────────────────────────
  const localName     = ref('');
  const localMin      = ref(0);
  const localMax      = ref(0);
  const localIsActive = ref(true);
  const localRoles    = ref([]);
  const isDeleting    = ref(false);

  // useForm for server error binding
  const form = useForm({});
  const isSaving = computed(() => form.processing);

  function syncFromRule(rule) {
    localName.value     = rule.name;
    localMin.value      = rule.min_trade_value;
    localMax.value      = rule.max_trade_value;
    localIsActive.value = Boolean(rule.is_active);
    localRoles.value    = (rule.PolyRuleRoles ?? []).map((r) => r.role);
  }

  function syncFromOpp(opp) {
    localName.value     = opp.name;
    localIsActive.value = Boolean(opp.is_active);
    localRoles.value    = (opp.PolyRuleRoles ?? []).map((r) => r.role);
  }

  function resetNew() {
    localName.value     = '';
    localMin.value      = 0;
    localMax.value      = 0;
    localIsActive.value = true;
    localRoles.value    = [];
  }

  // Keep local state fresh after partial prop reloads
  watch(selectedRule, (rule) => { if (rule && !isDirty.value) syncFromRule(rule); });
  watch(selectedOpp,  (opp)  => { if (opp  && !isDirty.value) syncFromOpp(opp); });

  // ─── Dirty detection ─────────────────────────────────────────────────────
  const isDirty = computed(() => {
    if (selectedType.value === 'new') {
      return localName.value !== '' || Number(localMin.value) !== 0 ||
             Number(localMax.value) !== 0 || localRoles.value.length > 0;
    }
    if (selectedType.value === 'trade_rule' && selectedRule.value) {
      const r = selectedRule.value;
      if (localName.value !== r.name) return true;
      if (Number(localMin.value) !== Number(r.min_trade_value)) return true;
      if (Number(localMax.value) !== Number(r.max_trade_value)) return true;
      if (localIsActive.value !== Boolean(r.is_active)) return true;
      return rolesChanged(r.PolyRuleRoles);
    }
    if (selectedType.value === 'trade_rule_opp' && selectedOpp.value) {
      const o = selectedOpp.value;
      if (localName.value !== o.name) return true;
      if (localIsActive.value !== Boolean(o.is_active)) return true;
      return rolesChanged(o.PolyRuleRoles);
    }
    return false;
  });

  function rolesChanged(serverRoles) {
    const local  = [...localRoles.value].sort().join(',');
    const server = (serverRoles ?? []).map((r) => r.role).sort().join(',');
    return local !== server;
  }

  // ─── Selection handlers ──────────────────────────────────────────────────
  function guardDirty() {
    return !isDirty.value || confirm('You have unsaved changes. Discard them?');
  }

  function selectRule(rule) {
    if (!guardDirty()) return;
    selectedType.value = 'trade_rule';
    selectedId.value   = rule.id;
    syncFromRule(rule);
  }

  function selectOpp(opp) {
    if (!guardDirty()) return;
    selectedType.value = 'trade_rule_opp';
    selectedId.value   = opp.id;
    syncFromOpp(opp);
  }

  function startNewRule() {
    if (!guardDirty()) return;
    selectedType.value = 'new';
    selectedId.value   = null;
    resetNew();
  }

  function cancel() {
    if (selectedType.value === 'new') {
      selectedType.value = null;
    } else if (selectedType.value === 'trade_rule' && selectedRule.value) {
      syncFromRule(selectedRule.value);
    } else if (selectedType.value === 'trade_rule_opp' && selectedOpp.value) {
      syncFromOpp(selectedOpp.value);
    }
  }

  // ─── Role toggle ─────────────────────────────────────────────────────────
  function hasRole(name) { return localRoles.value.includes(name); }

  function toggleRole(name) {
    const idx = localRoles.value.indexOf(name);
    if (idx >= 0) localRoles.value.splice(idx, 1);
    else           localRoles.value.push(name);
  }

  // ─── Client-side pre-validation ──────────────────────────────────────────
  const clientErrors = computed(() => {
    const errs = {};
    if (selectedType.value !== 'trade_rule' && selectedType.value !== 'new') return errs;
    const min = Number(localMin.value);
    const max = Number(localMax.value);
    if (!localName.value.trim()) errs.name = 'Rule name is required.';
    if (isNaN(min) || min < 0) errs.min_trade_value = 'Minimum must be 0 or greater.';
    if (isNaN(max) || max <= 0) errs.max_trade_value = 'Maximum must be greater than 0.';
    else if (max <= min) errs.max_trade_value = 'Maximum must be greater than the minimum.';
    if (localRoles.value.length === 0) errs.roles = 'Select at least one approver role.';
    return errs;
  });

  // Check for range overlap against existing rules (client-side, approximate)
  const overlapWarning = computed(() => {
    if (selectedType.value !== 'trade_rule' && selectedType.value !== 'new') return null;
    const min = Number(localMin.value);
    const max = Number(localMax.value);
    if (isNaN(min) || isNaN(max) || max <= min) return null;
    const conflict = props.trade_rules.find((r) => {
      if (selectedType.value === 'trade_rule' && r.id === selectedId.value) return false;
      return Number(r.min_trade_value) < max && Number(r.max_trade_value) > min;
    });
    if (!conflict) return null;
    return `Range overlaps with "${conflict.name}" (${formatZAR(conflict.min_trade_value)} – ${formatZAR(conflict.max_trade_value)}).`;
  });

  // Server errors from Inertia
  const serverErrors = computed(() => form.errors);

  // Combined: prefer server errors (post-submit) over client errors (pre-submit)
  const errors = computed(() => ({ ...clientErrors.value, ...serverErrors.value }));
  const hasBlockingErrors = computed(() =>
    Object.keys(clientErrors.value).length > 0 || overlapWarning.value !== null
  );

  // ─── Save / Delete ───────────────────────────────────────────────────────
  function save() {
    if (form.processing) return;
    form.clearErrors();

    const payload = {
      name:      localName.value,
      is_active: localIsActive.value,
      roles:     localRoles.value,
    };
    const opts = { preserveScroll: true, only: ['trade_rules', 'trade_rule_opps'] };

    if (selectedType.value === 'new') {
      form.transform(() => ({ ...payload, min_trade_value: localMin.value, max_trade_value: localMax.value }))
          .post(route('trade_rules.store'), {
            ...opts,
            onSuccess: () => { selectedType.value = null; selectedId.value = null; form.reset(); },
          });
    } else if (selectedType.value === 'trade_rule') {
      form.transform(() => ({ ...payload, min_trade_value: localMin.value, max_trade_value: localMax.value }))
          .put(route('trade_rules.update', selectedId.value), opts);
    } else if (selectedType.value === 'trade_rule_opp') {
      form.transform(() => payload)
          .put(route('trade_rule_opps.update', selectedId.value), opts);
    }
  }

  function deleteRule() {
    if (!confirm('Delete this Trade Rule? Any associated approval records may reference it.')) return;
    isDeleting.value = true;
    router.delete(route('trade_rules.destroy', selectedId.value), {
      preserveScroll: true,
      only: ['trade_rules'],
      onSuccess: () => { selectedType.value = null; selectedId.value = null; },
      onFinish:  () => { isDeleting.value = false; },
    });
  }

  // ─── Formatting ──────────────────────────────────────────────────────────
  function formatZAR(val) {
    return 'R\u00a0' + Number(val).toLocaleString('en-ZA', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  }

  function roleBadge(roleName) {
    const map = {
      TraderRole:           'bg-green-50 text-green-700 ring-green-500/20',
      TradeDirectorRole:    'bg-indigo-50 text-indigo-700 ring-indigo-500/20',
      FinancialDirectorRole:'bg-purple-50 text-purple-700 ring-purple-500/20',
      AdminRole:            'bg-yellow-50 text-yellow-800 ring-yellow-500/20',
      LogisticsRole:        'bg-blue-50 text-blue-700 ring-blue-500/20',
    };
    return (map[roleName] ?? 'bg-gray-100 text-gray-600 ring-gray-400/20') +
           ' inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset';
  }

  const panelTitle = computed(() => {
    if (selectedType.value === 'new') return 'New Trade Rule';
    if (selectedType.value === 'trade_rule' && selectedRule.value) return selectedRule.value.name;
    if (selectedType.value === 'trade_rule_opp' && selectedOpp.value) return selectedOpp.value.name;
    return '';
  });
</script>

<template>
  <AppLayout title="Trade Rules">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trade Rules</h2>
    </template>

    <div class="py-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white m-2 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="flex divide-x divide-gray-100 min-h-[600px]">

            <!-- ── Left sidebar ─────────────────────────────────────── -->
            <div class="w-80 flex-shrink-0 flex flex-col overflow-y-auto">

              <!-- Trade Rules section -->
              <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Trade Rules</h3>
                <button
                  v-if="can_manage"
                  @click="startNewRule"
                  class="inline-flex items-center gap-x-1 text-xs font-medium text-indigo-600 hover:text-indigo-800">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                  </svg>
                  Add Rule
                </button>
              </div>

              <ul class="divide-y divide-gray-50">
                <li
                  v-for="rule in trade_rules"
                  :key="rule.id"
                  @click="selectRule(rule)"
                  :class="[
                    'flex flex-col gap-1.5 px-4 py-3 cursor-pointer transition-colors select-none border-l-2',
                    selectedType === 'trade_rule' && selectedId === rule.id
                      ? 'bg-indigo-50 border-indigo-500'
                      : 'border-transparent hover:bg-gray-50',
                  ]">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-800 truncate">{{ rule.name }}</span>
                    <span
                      :class="rule.is_active ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-gray-100 text-gray-400 ring-gray-400/20'"
                      class="flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset">
                      {{ rule.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-400">
                    {{ formatZAR(rule.min_trade_value) }} – {{ formatZAR(rule.max_trade_value) }}
                  </p>
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="rr in (rule.PolyRuleRoles ?? [])"
                      :key="rr.id"
                      :class="roleBadge(rr.role)">
                      {{ rr.role }}
                    </span>
                    <span v-if="!(rule.PolyRuleRoles ?? []).length" class="text-xs text-red-400">No approvers set</span>
                  </div>
                </li>

                <li v-if="trade_rules.length === 0" class="px-4 py-6 text-center text-xs text-gray-400">
                  No trade rules defined.
                </li>
              </ul>

              <!-- Operation Rules section -->
              <div class="px-4 py-3 border-b border-t border-gray-100 bg-gray-50 mt-1">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operation Rules</h3>
                <p class="mt-0.5 text-xs text-gray-400">Conditional — trigger logic is in code</p>
              </div>

              <ul class="divide-y divide-gray-50">
                <li
                  v-for="opp in trade_rule_opps"
                  :key="opp.id"
                  @click="selectOpp(opp)"
                  :class="[
                    'flex flex-col gap-1.5 px-4 py-3 cursor-pointer transition-colors select-none border-l-2',
                    selectedType === 'trade_rule_opp' && selectedId === opp.id
                      ? 'bg-amber-50 border-amber-500'
                      : 'border-transparent hover:bg-gray-50',
                  ]">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-800 truncate">{{ opp.name }}</span>
                    <span
                      :class="opp.is_active ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-gray-100 text-gray-400 ring-gray-400/20'"
                      class="flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset">
                      {{ opp.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-400 italic leading-tight">{{ triggerFor(opp) }}</p>
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="rr in (opp.PolyRuleRoles ?? [])"
                      :key="rr.id"
                      :class="roleBadge(rr.role)">
                      {{ rr.role }}
                    </span>
                    <span v-if="!(opp.PolyRuleRoles ?? []).length" class="text-xs text-red-400">No approvers set</span>
                  </div>
                </li>
              </ul>

            </div>

            <!-- ── Right panel ──────────────────────────────────────── -->
            <div class="flex-1 flex flex-col">

              <!-- Empty state -->
              <div
                v-if="!selectedType"
                class="flex-1 flex flex-col items-center justify-center text-center p-12">
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                  <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Select a rule to view and edit it</p>
                <p class="text-xs text-gray-400 mt-1">Changes won't be saved until you click Save</p>
              </div>

              <!-- Editor -->
              <div v-else class="flex-1 flex flex-col">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50">
                  <div class="flex items-center gap-x-3">
                    <span
                      :class="selectedType === 'new'
                        ? 'bg-indigo-50 text-indigo-700 ring-indigo-500/20'
                        : selectedType === 'trade_rule_opp'
                          ? 'bg-amber-50 text-amber-700 ring-amber-500/20'
                          : 'bg-indigo-50 text-indigo-700 ring-indigo-500/20'"
                      class="inline-flex items-center rounded-lg px-4 py-2 text-base font-semibold ring-1 ring-inset">
                      {{ panelTitle || 'New Trade Rule' }}
                    </span>
                    <span
                      v-if="selectedType === 'trade_rule_opp'"
                      class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-500/20">
                      Operation Rule
                    </span>
                    <span
                      v-if="isDirty"
                      class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                      Unsaved changes
                    </span>
                  </div>
                  <p v-if="!can_manage" class="text-xs text-amber-600">
                    Read-only — requires <code class="bg-gray-100 rounded px-1">manage_users</code>
                  </p>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                  <!-- Operation Rule: trigger info banner -->
                  <div
                    v-if="selectedType === 'trade_rule_opp' && selectedOpp"
                    class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 flex gap-x-3">
                    <svg class="h-5 w-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-amber-800">Trigger condition (read-only)</p>
                      <p class="mt-0.5 text-sm text-amber-700">{{ triggerFor(selectedOpp) }}</p>
                      <p class="mt-1 text-xs text-amber-600">This condition is hardcoded in <code>DealTicket::calculateRules()</code>. Only the name, status, and required approvers can be changed here.</p>
                    </div>
                  </div>

                  <!-- Details section -->
                  <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Details</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                      <!-- Name -->
                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rule Name</label>
                        <input
                          v-model="localName"
                          :disabled="!can_manage"
                          type="text"
                          placeholder="e.g. R0 to R199,999.00"
                          :class="errors.name ? 'border-red-300 ring-red-300' : 'border-gray-300'"
                          class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500" />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                      </div>

                      <!-- Min / Max (TradeRule only) -->
                      <template v-if="selectedType === 'trade_rule' || selectedType === 'new'">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Min Trade Value (ZAR)</label>
                          <input
                            v-model.number="localMin"
                            :disabled="!can_manage"
                            type="number"
                            min="0"
                            step="1"
                            :class="errors.min_trade_value ? 'border-red-300 ring-red-300' : 'border-gray-300'"
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500" />
                          <p class="mt-1 text-xs text-gray-400">{{ formatZAR(localMin) }}</p>
                          <p v-if="errors.min_trade_value" class="mt-1 text-xs text-red-600">{{ errors.min_trade_value }}</p>
                        </div>

                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Max Trade Value (ZAR)</label>
                          <input
                            v-model.number="localMax"
                            :disabled="!can_manage"
                            type="number"
                            min="0"
                            step="1"
                            :class="errors.max_trade_value ? 'border-red-300 ring-red-300' : 'border-gray-300'"
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500" />
                          <p class="mt-1 text-xs text-gray-400">{{ formatZAR(localMax) }}</p>
                          <p v-if="errors.max_trade_value" class="mt-1 text-xs text-red-600">{{ errors.max_trade_value }}</p>
                        </div>

                        <!-- Overlap warning (client-side preview) -->
                        <div
                          v-if="overlapWarning"
                          class="sm:col-span-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 flex gap-x-3">
                          <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                          </svg>
                          <p class="text-sm text-red-700">{{ overlapWarning }}</p>
                        </div>
                      </template>

                      <!-- Active toggle -->
                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center gap-x-3">
                          <button
                            v-if="can_manage"
                            @click="localIsActive = !localIsActive"
                            :class="localIsActive ? 'bg-indigo-600' : 'bg-gray-200'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                            <span
                              :class="localIsActive ? 'translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                          </button>
                          <span class="text-sm" :class="localIsActive ? 'text-gray-900 font-medium' : 'text-gray-400'">
                            {{ localIsActive ? 'Active' : 'Inactive' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="border-t border-gray-100" />

                  <!-- Required Approvers section -->
                  <div>
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Required Approvers
                        <span class="ml-2 text-indigo-500 font-bold">{{ localRoles.length }}</span>
                      </h4>
                    </div>

                    <p class="mb-3 text-xs text-gray-500">
                      All selected roles must approve the deal ticket before it is considered approved.
                    </p>

                    <div
                      :class="errors.roles ? 'ring-1 ring-red-300 rounded-lg p-1' : ''"
                      class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                      <label
                        v-for="r in all_roles"
                        :key="r.id"
                        :class="[
                          'flex items-center gap-x-3 rounded-lg border px-4 py-2.5 transition-colors',
                          can_manage ? 'cursor-pointer' : 'cursor-default',
                          hasRole(r.name) ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50',
                        ]">
                        <input
                          type="checkbox"
                          :checked="hasRole(r.name)"
                          :disabled="!can_manage"
                          @change="toggleRole(r.name)"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 disabled:opacity-50" />
                        <span :class="['text-sm select-none', hasRole(r.name) ? 'text-indigo-800 font-medium' : 'text-gray-600']">
                          {{ r.name }}
                        </span>
                      </label>
                    </div>
                    <p v-if="errors.roles" class="mt-2 text-xs text-red-600">{{ errors.roles }}</p>
                  </div>

                </div>

                <!-- Footer -->
                <div v-if="can_manage" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50">
                  <!-- Delete (trade rules only, not new, not opps) -->
                  <button
                    v-if="selectedType === 'trade_rule'"
                    @click="deleteRule"
                    :disabled="isDeleting"
                    class="inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 hover:bg-red-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    Delete Rule
                  </button>
                  <div v-else />

                  <div class="flex items-center gap-x-3">
                    <button
                      @click="cancel"
                      :disabled="!isDirty || isSaving"
                      class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed transition">
                      Cancel
                    </button>
                    <button
                      @click="save"
                      :disabled="!isDirty || isSaving || hasBlockingErrors"
                      :class="isDirty && !isSaving && !hasBlockingErrors ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-indigo-300 cursor-not-allowed'"
                      class="inline-flex items-center gap-x-2 rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed transition">
                      <svg v-if="isSaving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                      </svg>
                      {{ isSaving ? 'Saving…' : selectedType === 'new' ? 'Create Rule' : 'Save Changes' }}
                    </button>
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
