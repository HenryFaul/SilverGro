<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class TransactionSummaryFlatView extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_summary_flat_view';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transport_date_earliest' => 'date',
        'transport_date_latest' => 'date',
        'transaction_created_at' => 'datetime',
        'transaction_updated_at' => 'datetime',
        'invoice_date' => 'date',
        'invoice_pay_by_date' => 'date',
        'invoice_paid_date' => 'date',
        'include_in_calculations' => 'boolean',
        'is_transaction_done' => 'boolean',
        'is_split_load' => 'boolean',
        'is_split_load_primary' => 'boolean',
        'is_split_load_member' => 'boolean',
        'is_multi_loads' => 'boolean',
        'job_is_approved' => 'boolean',
        'is_transport_costs_inc_price' => 'boolean',
        'is_product_zero_rated' => 'boolean',
        'is_weighbridge_certificate_received' => 'boolean',
        'deal_ticket_is_approved' => 'boolean',
        'deal_ticket_is_printed' => 'boolean',
        'purchase_order_is_approved' => 'boolean',
        'purchase_order_is_printed' => 'boolean',
        'transport_order_is_approved' => 'boolean',
        'transport_order_is_printed' => 'boolean',
        'sales_order_is_approved' => 'boolean',
        'sales_order_is_printed' => 'boolean',
    ];

    /**
     * Since this is a view, prevent any insert/update/delete operations
     */
    public function save(array $options = [])
    {
        throw new Exception('Cannot save to a database view');
    }

    public function delete()
    {
        throw new Exception('Cannot delete from a database view');
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new Exception('Cannot update a database view');
    }
}

