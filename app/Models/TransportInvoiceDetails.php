<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportInvoiceDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['invoice_id','transport_trans_id','is_invoiced','is_invoice_paid','invoice_no', 'invoice_paid_date','invoice_pay_by_date','invoice_date','invoice_amount','invoice_amount_paid','cost_price','selling_price','status_id','notes'];


}
