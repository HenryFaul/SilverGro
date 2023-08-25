<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeRule extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['name','min_trade_value','max_trade_value','is_active'];

    public function PolyRuleRoles(): MorphMany
    {
        return $this->morphMany(TradeRuleRole::class, 'poly_rule');
    }


   /* public function TradeRuleRoles(): HasMany
    {
        return $this->hasMany(TradeRuleRole::class,'trade_rule_id');
    }*/
}
