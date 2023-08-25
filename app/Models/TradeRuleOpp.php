<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TradeRuleOpp extends Model
{
    use HasFactory;

    public $fillable = ['name','is_active'];

    public function PolyRuleRoles(): MorphMany
    {
        return $this->morphMany(TradeRuleRole::class, 'poly_rule');
    }

}
