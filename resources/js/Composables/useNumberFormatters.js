/**
 * Number formatting utilities for Transaction Summary
 * Extracted from Index.vue to make code more maintainable
 */

/**
 * Format number with thousand separators (e.g., 1234567 -> "1,234,567")
 */
export function formatNiceNumber(number) {
  if (number === null || number === undefined || number === '') return '';
  return parseFloat(number).toLocaleString('en-US');
}

/**
 * Calculate variance between incoming and outgoing values
 * Returns formatted string with sign
 */
export function formatNiceVariance(valueIn, valueOut) {
  if (!valueIn || !valueOut) return '';

  const variance = parseFloat(valueIn) - parseFloat(valueOut);
  const sign = variance >= 0 ? '+' : '';
  return `${sign}${variance.toFixed(2)}`;
}

/**
 * Format currency (South African Rand)
 */
export function formatCurrency(amount) {
  if (amount === null || amount === undefined || amount === '') return 'R 0.00';
  return `R ${parseFloat(amount).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

/**
 * Format percentage
 */
export function formatPercentage(value) {
  if (value === null || value === undefined || value === '') return '0%';
  return `${parseFloat(value).toFixed(2)}%`;
}

/**
 * Format weight (kg)
 */
export function formatWeight(weight) {
  if (weight === null || weight === undefined || weight === '') return '0 kg';
  return `${parseFloat(weight).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} kg`;
}
