<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OldTransaction extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['old_id','contract_type','contract_no','deal_ticket','is_deal_ticket_printed',
        'is_vat_exempt','created_by','customer_id', 'supplier_id','transporter_id','no_units_incoming','billing_units_incoming','packaging_incoming',
        'packaging_incoming', 'product_id', 'product_source','no_units_outgoing','billing_units_outgoing','packaging_outgoing', 'cost_price_per_unit',
        'selling_price_per_unit','total_cost_price', 'total_cost_price_per_ton', 'total_cost_price', 'selling_price_per_ton',
        'transport_rate','transport_cost_price','comms_due_per_ton','weight_in_tons_incoming', 'weight_in_tons_outgoing', 'gross_profit',
        'gross_profit_perc', 'gross_profit_per_ton','purchase_order_confirmed_by','packaging_outgoing','sales_confirmation_sent',
        'sales_confirmation_received','weight_bridge_certificate_received','transport_order_received','invoice_paid',
        'invoice_amount','invoice_amount_paid','cost_price','selling_price',
        'supplier_notes','product_notes','customer_notes','transport_notes','pricing_notes','process_notes','transaction_notes',
        'approved','transaction_done','total_load_insurance','load_instructions','offload_instructions','transport_included_in_price',
        'loaded','on_road','paid','payment_overdue','delivered','traders_notes','weight_bridge_variance',
        'cancelled','transport_delayed','transport_breakdown','transport_cancelled','transport_load_slipped',
        'transport_weight_control','transport_driver','mill_slow','mill_breakdown','mill_stopped_demand_side',
        'quality_wet','quality_moisture_content','quality_contamination','quality_grade_specification',
        'quality_visual','general_variance_detected','include_in_calculations','original_date'
    ];

    public function Customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function Transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {

        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) => $query->whereDate('original_date',"=" ,$value)->orderBy('original_date', 'desc')
        );

    }

    public function scopeWeek(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) =>
            $query->whereBetween('original_date', [Carbon::parse($value)->startOfWeek(),Carbon::parse($value)->endOfWeek()])->orderBy('original_date', 'asc')
        );

    }
}
