<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        'branch','department','comment','poly_contact_type','poly_contact_id'];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function emailable(): MorphMany
    {
        return $this->morphMany(EmailContactDetail::class, 'poly_email');
    }

    public function numberable(): MorphMany
    {
        return $this->morphMany(NumberContactDetail::class, 'poly_number');
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

