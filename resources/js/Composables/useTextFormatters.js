/**
 * Text formatting utilities for Transaction Summary
 * Extracted from Index.vue to make code more maintainable
 */

/**
 * Truncate text to specified length with ellipsis
 */
export function truncateText(text, maxLength = 40) {
  if (!text) return '';
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text;
}

/**
 * Capitalize first letter of each word
 */
export function capitalizeWords(text) {
  if (!text) return '';
  return text
    .toLowerCase()
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

/**
 * Format name (Last, First -> First Last)
 */
export function formatName(lastName, firstName) {
  if (!lastName && !firstName) return '';
  if (!firstName) return lastName;
  if (!lastName) return firstName;
  return `${firstName} ${lastName}`;
}
