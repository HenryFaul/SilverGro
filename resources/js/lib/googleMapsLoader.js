/**
 * Single @googlemaps/js-api-loader instance for the whole app.
 * Loader must not be created with different options once the script has loaded
 * (e.g. AddressModal uses places; Distance Matrix / Directions use the same script).
 */
import { Loader } from '@googlemaps/js-api-loader';

let loadPromise = null;

export function getGoogleMapsApiKey() {
  return (import.meta.env.VITE_GOOGLE_MAPS_API_KEY || '').trim();
}

export function loadGoogleMaps() {
  const apiKey = getGoogleMapsApiKey();
  if (!apiKey) {
    return Promise.reject(new Error('No API key'));
  }
  if (!loadPromise) {
    const loader = new Loader({
      apiKey,
      version: 'weekly',
      libraries: ['places'],
    });
    loadPromise = loader.load();
  }
  return loadPromise;
}
