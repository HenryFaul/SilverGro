/**
 * useTransactionDateFormatters
 *
 * Date formatting functions specific to transaction summary
 * Extracts date formatting logic from Index.vue
 */

import { formatShortDate } from '@/Composables/useDateFormatters.js';

export function useTransactionDateFormatters(filterForm, combined_Form) {
  // Filter date formatters
  const format = () => formatShortDate(filterForm.end_date);
  const formatStart = () => formatShortDate(filterForm.start_date);

  // Transport date formatters
  const formatEarly = () => formatShortDate(combined_Form.transport_date_earliest);
  const formatLate = () => formatShortDate(combined_Form.transport_date_latest);

  // Invoice date formatters
  const formatInvoicePdDay = () => formatShortDate(combined_Form.invoice_paid_date);
  const formatInvoicePayByDay = () => formatShortDate(combined_Form.invoice_pay_by_date);
  const formatInvoiceDate = () => formatShortDate(combined_Form.invoice_date);

  return {
    format,
    formatStart,
    formatEarly,
    formatLate,
    formatInvoicePdDay,
    formatInvoicePayByDay,
    formatInvoiceDate,
  };
}
