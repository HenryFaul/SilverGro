<script setup>
  import DialogModal from '@/Components/DialogModal.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import { getGoogleMapsApiKey, loadGoogleMaps } from '@/lib/googleMapsLoader.js';
  import { computed, nextTick, onUnmounted, ref, watch } from 'vue';

  const mapsApiKey = getGoogleMapsApiKey();

  const props = defineProps({
    collectionAddress: { type: [Object, Number, String], default: null },
    deliveryAddress: { type: [Object, Number, String], default: null },
    /** Planned transport date (YYYY-MM-DD from form) */
    transportDateEarliest: { type: [String, Date], default: null },
    loadingHoursFromId: { type: [Number, String], default: null },
    loadingHoursToId: { type: [Number, String], default: null },
    offloadingHoursFromId: { type: [Number, String], default: null },
    offloadingHoursToId: { type: [Number, String], default: null },
    loadingHourOptions: { type: Array, default: () => [] },
  });

  function parseCoords(addr) {
    if (!addr || typeof addr !== 'object') {
      return null;
    }
    const lat = parseFloat(addr.latitude);
    const lng = parseFloat(addr.longitude);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
      return null;
    }
    return { lat, lng };
  }

  function optionById(id, options) {
    if (id == null || id === '') {
      return null;
    }
    const n = Number(id);
    return (options || []).find((o) => Number(o.id) === n) || null;
  }

  /** Parses "08:00" style labels from LoadingHourOption.name */
  function parseTimeFromName(name) {
    if (!name || typeof name !== 'string') {
      return null;
    }
    const m = name.trim().match(/^(\d{1,2}):(\d{2})$/);
    if (!m) {
      return null;
    }
    const h = Number(m[1]);
    const min = Number(m[2]);
    if (h < 0 || h > 23 || min < 0 || min > 59) {
      return null;
    }
    return { h, m: min };
  }

  function dateOnlyFromTransportDate(val) {
    if (!val) {
      return null;
    }
    const s = String(val).split('T')[0];
    if (!/^\d{4}-\d{2}-\d{2}$/.test(s)) {
      return null;
    }
    return s;
  }

  /** Wall time in South Africa (SAST, UTC+2, no DST) */
  function zonedDateTimeSa(dateStr, hour, minute) {
    const pad = (n) => String(n).padStart(2, '0');
    return new Date(`${dateStr}T${pad(hour)}:${pad(minute)}:00+02:00`);
  }

  function formatSaDateTime(dt) {
    if (!dt || !(dt instanceof Date) || Number.isNaN(dt.getTime())) {
      return null;
    }
    return dt.toLocaleString('en-ZA', {
      timeZone: 'Africa/Johannesburg',
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  function dateInSa(dt) {
    if (!dt || !(dt instanceof Date)) {
      return null;
    }
    return dt.toLocaleDateString('en-CA', { timeZone: 'Africa/Johannesburg' });
  }

  const origin = computed(() => parseCoords(props.collectionAddress));
  const destination = computed(() => parseCoords(props.deliveryAddress));

  const hasCollectionObject = computed(
    () => props.collectionAddress != null && typeof props.collectionAddress === 'object'
  );
  const hasDeliveryObject = computed(
    () => props.deliveryAddress != null && typeof props.deliveryAddress === 'object'
  );

  /**
   * ETD from collection: end of loading window if set, else start of loading window.
   * Uses transport_date_earliest + loading hour option names from the Transport tab.
   */
  const departureDateTime = computed(() => {
    const dateStr = dateOnlyFromTransportDate(props.transportDateEarliest);
    if (!dateStr) {
      return null;
    }
    const toOpt = optionById(props.loadingHoursToId, props.loadingHourOptions);
    const fromOpt = optionById(props.loadingHoursFromId, props.loadingHourOptions);
    const t =
      parseTimeFromName(toOpt?.name) ||
      parseTimeFromName(fromOpt?.name);
    if (!t) {
      return null;
    }
    return zonedDateTimeSa(dateStr, t.h, t.m);
  });

  const departureScenarioLabel = computed(() => {
    const toOpt = optionById(props.loadingHoursToId, props.loadingHourOptions);
    const fromOpt = optionById(props.loadingHoursFromId, props.loadingHourOptions);
    if (toOpt?.name && parseTimeFromName(toOpt.name)) {
      return `End of loading window (${toOpt.name} SAST)`;
    }
    if (fromOpt?.name && parseTimeFromName(fromOpt.name)) {
      return `Start of loading window (${fromOpt.name} SAST)`;
    }
    return null;
  });

  const loading = ref(false);
  const matrixErrorStatus = ref(null);
  const straightLineKm = ref(null);
  const drivingKm = ref(null);
  const averageKm = ref(null);
  const drivingDurationSeconds = ref(null);
  const drivingDurationText = ref(null);
  const usesTrafficEstimate = ref(false);
  const etaAtDelivery = ref(null);
  const showModal = ref(false);
  const mapContainer = ref(null);

  let map = null;
  let directionsRenderer = null;
  let directionsService = null;

  function ensureGoogleMaps() {
    return loadGoogleMaps();
  }

  function haversineKm(a, b) {
    const R = 6371;
    const toRad = (d) => (d * Math.PI) / 180;
    const dLat = toRad(b.lat - a.lat);
    const dLon = toRad(b.lng - a.lng);
    const x =
      Math.sin(dLat / 2) ** 2 +
      Math.cos(toRad(a.lat)) * Math.cos(toRad(b.lat)) * Math.sin(dLon / 2) ** 2;
    const c = 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
    return R * c;
  }

  function hintForGoogleStatus(status) {
    const s = (status || '').toString();
    if (s.includes('REQUEST_DENIED')) {
      return 'The key or project blocked this request. In Google Cloud Console: (1) Enable billing on the project. (2) Enable APIs: Maps JavaScript API, Distance Matrix API, and Directions API. (3) Under Credentials → your API key → Application restrictions: use HTTP referrers and include the exact URL you use in the browser, e.g. http://localhost:8080/* and http://127.0.0.1:8080/* if you use port 8080. (4) API restrictions: either don’t restrict, or restrict to those three APIs.';
    }
    if (s.includes('OVER_QUERY_LIMIT') || s.includes('OVER_DAILY_LIMIT')) {
      return 'Quota or rate limit exceeded. Check quotas in Google Cloud Console → APIs & Services → Distance Matrix API.';
    }
    if (s.includes('ZERO_RESULTS')) {
      return 'No driving route was returned between these coordinates. You can still use the straight-line figure below.';
    }
    if (s.includes('NOT_FOUND')) {
      return 'Google could not resolve one of the points for driving directions.';
    }
    if (s.includes('INVALID_REQUEST')) {
      return 'The request was rejected as invalid. Check that latitude and longitude are correct.';
    }
    return 'Ensure Maps JavaScript API, Distance Matrix API, and Directions API are enabled, billing is active, and referrer restrictions match how you open the app (including port, e.g. :8080).';
  }

  const offloadingWindowHint = computed(() => {
    const eta = etaAtDelivery.value;
    const dateStr = dateOnlyFromTransportDate(props.transportDateEarliest);
    if (!eta || !dateStr) {
      return null;
    }
    const fromOpt = optionById(props.offloadingHoursFromId, props.loadingHourOptions);
    const toOpt = optionById(props.offloadingHoursToId, props.loadingHourOptions);
    const fromT = parseTimeFromName(fromOpt?.name);
    const toT = parseTimeFromName(toOpt?.name);
    if (!fromT || !toT) {
      return null;
    }
    const offFromMs = zonedDateTimeSa(dateStr, fromT.h, fromT.m).getTime();
    const offToMs = zonedDateTimeSa(dateStr, toT.h, toT.m).getTime();
    const etaMs = eta.getTime();
    if (etaMs < offFromMs) {
      return 'Estimated arrival is before the offloading window.';
    }
    if (etaMs > offToMs) {
      return 'Estimated arrival is after the offloading window.';
    }
    return 'Estimated arrival falls within the offloading window.';
  });

  const etaCrossesCalendarDay = computed(() => {
    const eta = etaAtDelivery.value;
    const dateStr = dateOnlyFromTransportDate(props.transportDateEarliest);
    if (!eta || !dateStr) {
      return false;
    }
    return dateInSa(eta) !== dateStr;
  });

  async function fetchDistances() {
    matrixErrorStatus.value = null;
    straightLineKm.value = null;
    drivingKm.value = null;
    averageKm.value = null;
    drivingDurationSeconds.value = null;
    drivingDurationText.value = null;
    usesTrafficEstimate.value = false;
    etaAtDelivery.value = null;

    if (!mapsApiKey || !origin.value || !destination.value) {
      return;
    }

    loading.value = true;
    const o = origin.value;
    const d = destination.value;
    straightLineKm.value = haversineKm(o, d);

    const dep = departureDateTime.value;
    const now = Date.now();
    const useTraffic =
      dep instanceof Date &&
      !Number.isNaN(dep.getTime()) &&
      dep.getTime() > now - 30 * 60 * 1000;

    try {
      const google = await ensureGoogleMaps();
      await new Promise((resolve, reject) => {
        const service = new google.maps.DistanceMatrixService();
        const request = {
          origins: [o],
          destinations: [d],
          travelMode: google.maps.TravelMode.DRIVING,
          unitSystem: google.maps.UnitSystem.METRIC,
        };
        if (useTraffic) {
          request.drivingOptions = {
            departureTime: dep,
            trafficModel: google.maps.TrafficModel.BEST_GUESS,
          };
        }

        service.getDistanceMatrix(request, (response, status) => {
          if (status !== 'OK' || !response?.rows?.[0]?.elements?.[0]) {
            const extra = response?.error_message ? ` — ${response.error_message}` : '';
            reject(new Error(`${status || 'UNKNOWN'}${extra}`));
            return;
          }
          const el = response.rows[0].elements[0];
          if (el.status !== 'OK') {
            reject(new Error(el.status));
            return;
          }
          drivingKm.value = el.distance.value / 1000;
          averageKm.value = (straightLineKm.value + drivingKm.value) / 2;

          const trafficDur = el.duration_in_traffic;
          const normalDur = el.duration;
          const chosen =
            trafficDur && typeof trafficDur.value === 'number'
              ? trafficDur
              : normalDur;
          usesTrafficEstimate.value = !!(trafficDur && typeof trafficDur.value === 'number');

          if (chosen) {
            drivingDurationSeconds.value = chosen.value;
            drivingDurationText.value = chosen.text;
          }

          if (dep && chosen?.value) {
            etaAtDelivery.value = new Date(dep.getTime() + chosen.value * 1000);
          }

          resolve();
        });
      });
    } catch (e) {
      const msg = e?.message || String(e);
      matrixErrorStatus.value = msg;
      drivingKm.value = null;
      averageKm.value = null;
      drivingDurationSeconds.value = null;
      drivingDurationText.value = null;
      usesTrafficEstimate.value = false;
      etaAtDelivery.value = null;
    } finally {
      loading.value = false;
    }
  }

  watch(
    () => [
      props.collectionAddress,
      props.deliveryAddress,
      props.transportDateEarliest,
      props.loadingHoursFromId,
      props.loadingHoursToId,
      props.offloadingHoursFromId,
      props.offloadingHoursToId,
      props.loadingHourOptions,
    ],
    () => {
      fetchDistances();
    },
    { deep: true, immediate: true }
  );

  const hasStraightLine = computed(
    () => straightLineKm.value != null && Number.isFinite(straightLineKm.value)
  );

  const hasFullGoogleDistance = computed(
    () => averageKm.value != null && drivingKm.value != null
  );

  const showDistanceRow = computed(
    () =>
      Boolean(
        mapsApiKey && origin.value && destination.value && !loading.value && hasStraightLine.value
      )
  );

  const showSuccessContent = computed(() => hasFullGoogleDistance.value && !loading.value);

  const showEtaBlock = computed(
    () =>
      hasFullGoogleDistance.value &&
      drivingDurationText.value &&
      !loading.value
  );

  const userNotice = computed(() => {
    if (showSuccessContent.value) {
      return null;
    }

    if (!mapsApiKey) {
      return {
        variant: 'amber',
        title: 'Map distance unavailable',
        text: 'Add VITE_GOOGLE_MAPS_API_KEY to your .env file and restart the Vite dev server (or Docker node container).',
      };
    }

    if (!hasCollectionObject.value) {
      return {
        variant: 'slate',
        title: 'Collection address needed',
        text: 'Choose a collection address on the Supplier account tab. Route distance appears once it is set.',
      };
    }

    if (!hasDeliveryObject.value) {
      return {
        variant: 'slate',
        title: 'Delivery address needed',
        text: 'Choose a delivery address on the Customer tab. Route distance appears once it is set.',
      };
    }

    if (!origin.value) {
      return {
        variant: 'slate',
        title: 'Collection address has no map point',
        text: 'Edit the collection address and use the Google address search so latitude and longitude are saved.',
      };
    }

    if (!destination.value) {
      return {
        variant: 'slate',
        title: 'Delivery address has no map point',
        text: 'Edit the delivery address and use the Google address search so latitude and longitude are saved.',
      };
    }

    if (loading.value) {
      return null;
    }

    if (matrixErrorStatus.value && hasStraightLine.value) {
      return {
        variant: 'amber',
        title: 'Google could not return driving distance',
        text: `Status: ${matrixErrorStatus.value}. ${hintForGoogleStatus(matrixErrorStatus.value)} Below is straight-line distance only (not road km).`,
      };
    }

    if (matrixErrorStatus.value) {
      return {
        variant: 'amber',
        title: 'Google Maps could not calculate distance',
        text: `Status: ${matrixErrorStatus.value}. ${hintForGoogleStatus(matrixErrorStatus.value)}`,
      };
    }

    return null;
  });

  const noticeBoxClass = computed(() => {
    const v = userNotice.value?.variant;
    if (v === 'amber') {
      return 'border-amber-200 bg-amber-50 text-amber-950';
    }
    return 'border-slate-200 bg-slate-50 text-slate-800';
  });

  const showSeeMoreButton = computed(
    () => !loading.value && hasStraightLine.value && mapsApiKey && origin.value && destination.value
  );

  function disposeRouteMap() {
    if (directionsRenderer) {
      directionsRenderer.setMap(null);
      directionsRenderer = null;
    }
    map = null;
    directionsService = null;
  }

  async function renderRouteOnMap() {
    if (!mapContainer.value || !origin.value || !destination.value) {
      return;
    }
    try {
      const google = await ensureGoogleMaps();
      if (!map) {
        map = new google.maps.Map(mapContainer.value, {
          zoom: 7,
          center: origin.value,
          mapTypeControl: false,
        });
        directionsRenderer = new google.maps.DirectionsRenderer({
          map,
          suppressMarkers: false,
        });
        directionsService = new google.maps.DirectionsService();
      }
      directionsService.route(
        {
          origin: origin.value,
          destination: destination.value,
          travelMode: google.maps.TravelMode.DRIVING,
        },
        (result, status) => {
          if (status === 'OK' && result && map && directionsRenderer) {
            directionsRenderer.setDirections(result);
            google.maps.event.trigger(map, 'resize');
          }
        }
      );
    } catch (e) {
      console.error(e);
    }
  }

  function openModal() {
    showModal.value = true;
  }

  function closeModal() {
    showModal.value = false;
  }

  watch(showModal, (open) => {
    if (open) {
      nextTick(() => {
        renderRouteOnMap();
      });
    } else {
      nextTick(() => {
        disposeRouteMap();
      });
    }
  });

  onUnmounted(() => {
    disposeRouteMap();
  });
</script>

<template>
  <div class="border-t border-gray-100 pt-3 mt-1 space-y-3">
    <div
      v-if="userNotice"
      :class="noticeBoxClass"
      class="rounded-md border px-3 py-2 text-xs leading-relaxed"
      role="status">
      <p class="font-medium">{{ userNotice.title }}</p>
      <p class="mt-0.5 opacity-90">{{ userNotice.text }}</p>
    </div>

    <div v-if="showDistanceRow">
      <div class="flex flex-col gap-y-2">
        <div class="flex justify-between gap-x-4">
          <dt class="text-gray-500">Collection → delivery distance</dt>
          <dd class="text-right text-gray-700">
            <span v-if="hasFullGoogleDistance">
              ~{{ averageKm.toFixed(1) }} km avg
            </span>
            <span v-else-if="hasStraightLine">
              ~{{ straightLineKm.toFixed(1) }} km (straight-line)
            </span>
          </dd>
        </div>
        <p
          v-if="hasFullGoogleDistance"
          class="text-xs text-gray-500 leading-relaxed">
          Average of straight-line ({{ straightLineKm.toFixed(1) }} km) and driving distance ({{
            drivingKm.toFixed(1)
          }}
          km) via Google Maps.
        </p>
        <p
          v-else-if="matrixErrorStatus && hasStraightLine"
          class="text-xs text-gray-500 leading-relaxed">
          Straight-line distance between the saved coordinates. Fix the Google setup above to show
          driving distance and a more accurate average.
        </p>

        <div
          v-if="showEtaBlock"
          class="rounded-md border border-indigo-100 bg-indigo-50/80 px-3 py-2 text-xs text-gray-800 space-y-1">
          <p class="font-medium text-indigo-900">Drive time &amp; ETA</p>
          <p>
            <span class="text-gray-600">Typical driving time:</span>
            {{ drivingDurationText }}
            <span
              v-if="usesTrafficEstimate"
              class="text-gray-600">
              (traffic-aware estimate)
            </span>
            <span
              v-else
              class="text-gray-600">
              (typical duration, no traffic data for this departure time)
            </span>
          </p>
          <template v-if="departureDateTime && departureScenarioLabel">
            <p class="text-gray-600">
              Assumes departure from collection at
              <strong>{{ departureScenarioLabel }}</strong>
              on
              <strong>{{ dateOnlyFromTransportDate(transportDateEarliest) }}</strong>
              (SAST), matching the Transport tab loading hours and earliest transport date.
            </p>
            <p v-if="formatSaDateTime(departureDateTime)">
              <span class="text-gray-600">ETD (collection):</span>
              {{ formatSaDateTime(departureDateTime) }}
            </p>
            <p v-if="etaAtDelivery && formatSaDateTime(etaAtDelivery)">
              <span class="text-gray-600">ETA (delivery):</span>
              {{ formatSaDateTime(etaAtDelivery) }}
              <span
                v-if="etaCrossesCalendarDay"
                class="text-amber-800">
                — next calendar day vs transport date
              </span>
            </p>
            <p
              v-if="offloadingWindowHint"
              :class="
                offloadingWindowHint.includes('within')
                  ? 'text-green-800'
                  : 'text-amber-900'
              ">
              {{ offloadingWindowHint }}
            </p>
          </template>
          <p
            v-else
            class="text-gray-600">
            Set <strong>transport date (earliest)</strong> and <strong>loading hours</strong> on the
            Transport tab to see ETD/ETA and how they compare to the offloading window.
          </p>
        </div>

        <div v-if="showSeeMoreButton">
          <SecondaryButton
            type="button"
            class="text-xs"
            @click="openModal">
            See more
          </SecondaryButton>
        </div>
      </div>

      <DialogModal
        :show="showModal"
        max-width="5xl"
        @close="closeModal">
        <template #title> Route: collection to delivery </template>
        <template #content>
          <div
            ref="mapContainer"
            class="w-full rounded-md border border-gray-200 bg-gray-100"
            style="height: 420px; min-height: 320px" />
          <p
            v-if="matrixErrorStatus"
            class="mt-2 text-xs text-amber-800">
            If the map is blank, Directions may also be denied for the same API key or referrer
            settings as Distance Matrix.
          </p>
        </template>
        <template #footer>
          <SecondaryButton
            type="button"
            @click="closeModal">
            Close
          </SecondaryButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
