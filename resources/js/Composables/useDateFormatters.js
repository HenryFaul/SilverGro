/**
 * Date formatting utilities for Transaction Summary
 * Extracted from Index.vue to make code more maintainable
 */

/**
 * Get day of week (1-7, Sunday is 7)
 */
export function getNiceDay(date) {
  const day = new Date(date).getDay();
  return day === 0 ? 7 : day; // Convert Sunday from 0 to 7
}

/**
 * Format date as "DAY DD/MMM/YYYY" (e.g., "MON 10/NOV/2025")
 */
export function formatNiceDate(date) {
  if (!date) return '';

  const _date = new Date(date);
  const day = _date.getDate();
  const month = _date
    .toLocaleString('en', {
      month: 'short',
      timeZone: 'Africa/Johannesburg',
    })
    .toUpperCase();
  const dayString = _date
    .toLocaleString('en', {
      weekday: 'short',
      timeZone: 'Africa/Johannesburg',
    })
    .toUpperCase();
  const year = _date.getFullYear();
  return `${dayString} ${day}/${month}/${year}`;
}

/**
 * Format date as "DD/MMM/YYYY"
 */
export function formatShortDate(date) {
  if (!date) return '';

  const _date = new Date(date);
  const day = _date.getDate();
  const month = _date
    .toLocaleString('en', {
      month: 'short',
      timeZone: 'Africa/Johannesburg',
    })
    .toUpperCase();
  const year = _date.getFullYear();
  return `${day}/${month}/${year}`;
}

/**
 * Check if a specific day is included based on day flags
 */
export function isDayIncluded(date, dayFlags) {
  const day = getNiceDay(date);
  const { mon, tue, wed, thu, fri, sat, sun } = dayFlags;

  switch (day) {
    case 1:
      return mon;
    case 2:
      return tue;
    case 3:
      return wed;
    case 4:
      return thu;
    case 5:
      return fri;
    case 6:
      return sat;
    case 7:
      return sun;
    default:
      return false;
  }
}

/**
 * Create a date formatter function for VueDatePicker
 */
export function createDateFormatter(formatFn) {
  return () => {
    return (date) => formatFn(date);
  };
}
