<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerParent extends Model
{
    use SoftDeletes;

    public $fillable = ['id','first_name','last_legal_name','nickname','title','id_reg_no','is_active','terms_of_payment_basis_id','terms_of_payment_id','invoice_basis_id','customer_rating_id','is_vat_exempt','is_vat_cert_received',
        'credit_limit','credit_limit_hard','comment','days_overdue_allowed_id'];


    public function Customer(): HasMany
    {
        return $this->hasMany(Customer::class);
    }


    public function TermsOfPayment(): BelongsTo
    {
        return $this->belongsTo(TermsOfPayment::class);
    }

    public function TermsOfPaymentBasis(): BelongsTo
    {
        return $this->belongsTo(TermsOfPaymentBasis::class);
    }

    public function InvoiceBasis(): BelongsTo
    {
        return $this->belongsTo(InvoiceBasis::class);
    }

    public function CustomerRating(): BelongsTo
    {
        return $this->belongsTo(CustomerRating::class);
    }


    public function addressable(): MorphMany
    {
        return $this->morphMany(Address::class, 'poly_address');
    }

    public function contactable(): MorphMany
    {
        return $this->morphMany(Contact::class, 'poly_contact');
    }

    public function staff(): MorphToMany
    {
        return $this->morphToMany(Staff::class, 'staff_assigned');
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
            fn ($query, $value) => $query->where('first_name', 'like', '%'.$value.'%')
                ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                ->orWhere('id_reg_no', 'like', '%'.$value.'%')
        )->when(
            $filters['isActive'] ?? false,
            fn ($query, $value) => $query->where('is_active', $value == 'active' ? 1:0)
        )->when(
            $filters['field'] ?? false,
            fn ($query, $value) =>
            $query->orderBy($value, $filters['direction'] ?? 'asc')
        );

    }
}
