<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtorStanding extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['customer_id','total_outstanding','total_overdue','updated_at'];

    public function Customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
             fn ($query, $value) => $query->whereHas('Customer', function ($q) use ($value){
                 $q->where('first_name', 'like', '%'.$value.'%')
                     ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                     ->orWhere('id_reg_no', 'like', '%'.$value.'%');
             })
        )->when(
            $filters['isActive'] ?? false,
            fn ($query, $value) => $query->where('is_active', $value == 'active' ? 1:0)
        )->when(
            $filters['field'] ?? false,
            fn ($query, $value) =>
            $query->orderBy($value, $filters['direction'] ?? 'asc')
        )->with('Customer',fn($query) => $query->with('CustomerRating')->with('TermsOfPayment')->with('TermsOfPaymentBasis'))
            ->orderByDesc('total_outstanding');

    }
}
