/**
 * Date formatting and utility functions
 * Common date operations used across the application
 */

/**
 * Get the day of the week from a date (0 = Sunday, 1 = Monday, etc.)
 * @param {string|Date} date - The date to get the day from
 * @returns {number} The day of the week (0-6)
 */
export function getDay(date) {
  return new Date(date).getDay();
}

/**
 * Check if a date falls on a day that is included in the day flags
 * @param {string|Date} date - The date to check
 * @param {Object} dayFlags - Object with boolean flags for each day
 * @param {boolean} dayFlags.mon - Monday flag
 * @param {boolean} dayFlags.tue - Tuesday flag
 * @param {boolean} dayFlags.wed - Wednesday flag
 * @param {boolean} dayFlags.thu - Thursday flag
 * @param {boolean} dayFlags.fri - Friday flag
 * @param {boolean} dayFlags.sat - Saturday flag
 * @param {boolean} dayFlags.sun - Sunday flag
 * @returns {boolean} True if the date's day is included
 */
export function isDayIncluded(date, dayFlags) {
  const day = getDay(date);

  switch (day) {
    case 1:
      return dayFlags.mon;
    case 2:
      return dayFlags.tue;
    case 3:
      return dayFlags.wed;
    case 4:
      return dayFlags.thu;
    case 5:
      return dayFlags.fri;
    case 6:
      return dayFlags.sat;
    case 0:
      return dayFlags.sun;
    default:
      return false;
  }
}

/**
 * Format a date as a short date string (YYYY-MM-DD)
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string or empty string if no date
 */
export function formatShortDate(date) {
  if (!date) return '';

  const d = new Date(date);
  if (isNaN(d.getTime())) return '';

  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}

/**
 * Format a date as a readable string (e.g., "Jan 15, 2024")
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string or empty string if no date
 */
export function formatReadableDate(date) {
  if (!date) return '';

  const d = new Date(date);
  if (isNaN(d.getTime())) return '';

  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

/**
 * Format a date in a nice readable format (e.g., "15 Jan 2024")
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string or empty string if no date
 */
export function formatNiceDate(date) {
  if (!date) return '';

  const d = new Date(date);
  if (isNaN(d.getTime())) return '';

  const day = d.getDate();
  const month = d.toLocaleString('en', {
    month: 'short',
    timeZone: 'Africa/Johannesburg',
  });
  const year = d.getFullYear();

  return `${day} ${month} ${year}`;
}

/**
 * Get the name of the day of the week
 * @param {string|Date} date - The date to get the day name from
 * @returns {string} The day name (e.g., "Monday", "Tuesday")
 */
export function getDayName(date) {
  const dayNames = [
    'Sunday',
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
  ];
  const day = getDay(date);
  return dayNames[day];
}

/**
 * Get the short name of the day of the week
 * @param {string|Date} date - The date to get the day name from
 * @returns {string} The short day name (e.g., "Mon", "Tue")
 */
export function getShortDayName(date) {
  const shortDayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const day = getDay(date);
  return shortDayNames[day];
}
