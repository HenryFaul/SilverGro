<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeRuleRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['poly_rule_type','poly_rule_id','role'];

    public function PolyRuleRoles(): MorphTo
    {
        return $this->morphTo();
    }
}
