<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\TransportInvoiceDetails;
use Carbon\Carbon;

/**
 * When an unpaid invoice counts as a late payment.
 *
 * Late = still unpaid, a balance left on it, and past its invoice date plus the
 * customer's payment terms plus their "days overdue allowed". The allowance is
 * the grace SilverGro gives each customer according to their behaviour and
 * credit rating, so a customer on 7-day terms with 3 days allowed is late from
 * day 11, not day 8.
 *
 * This is deliberately different from "overdue" on Debtor Standing, which stays
 * the plain accounting figure (past the due date, no grace) so it keeps matching
 * the ageing report. Late is what drives the debtor flags and the $ alert on the
 * trade lists - one definition for both, so they cannot disagree.
 */
class PaymentLateness
{
    public static function graceDays(?Customer $customer): int
    {
        if (!$customer) {
            return 0;
        }

        return (int) ($customer->TermsOfPayment?->days ?? 0)
            + (int) ($customer->DaysOverdueAllowed?->days ?? 0);
    }

    public static function isLate(?TransportInvoiceDetails $details, ?Customer $customer, ?Carbon $now = null): bool
    {
        if (!$details || !$customer || !$details->invoice_date || $details->is_invoice_paid) {
            return false;
        }

        if (round($details->invoice_amount - $details->invoice_amount_paid, 2) <= 0) {
            return false;
        }

        $lateFrom = Carbon::create($details->invoice_date)->addDays(self::graceDays($customer));

        return $lateFrom < ($now ?? Carbon::now()->tz('Africa/Johannesburg'));
    }

    /**
     * Mark each trade in a list with `payment_late`, for the $ alert.
     *
     * Only MQs are invoiced to a customer, so only they can be late. The trade's
     * invoice details may be loaded directly or under its invoice, depending on
     * the screen, so both are checked.
     */
    public static function flagTrades(iterable $trades): void
    {
        foreach ($trades as $trade) {
            $details = $trade->relationLoaded('TransportInvoiceDetails')
                ? $trade->TransportInvoiceDetails
                : $trade->TransportInvoice?->TransportInvoiceDetails;

            $trade->setAttribute(
                'payment_late',
                (int) $trade->contract_type_id === 4 && self::isLate($details, $trade->Customer)
            );
        }
    }

    /**
     * Eager loads the lists need so flagTrades() runs without extra queries.
     */
    public static function eagerLoads(): array
    {
        return [
            'Customer.TermsOfPayment',
            'Customer.DaysOverdueAllowed',
            'TransportInvoiceDetails',
        ];
    }
}
