<?php

namespace App\Http\Controllers;

use App\Models\TradeRuleOpp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TradeRuleOppController extends Controller
{
    public function update(Request $request, TradeRuleOpp $tradeRuleOpp)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $request->validate([
            'name'      => ['required', 'string', Rule::unique('trade_rule_opps', 'name')->ignore($tradeRuleOpp->id)->whereNull('deleted_at')],
            'is_active' => ['required', 'boolean'],
            'roles'     => ['present', 'array'],
            'roles.*'   => ['string', 'exists:roles,name'],
        ]);

        $tradeRuleOpp->update([
            'name'      => $request->name,
            'is_active' => $request->is_active,
        ]);

        $tradeRuleOpp->PolyRuleRoles()->delete();
        foreach ($request->roles as $roleName) {
            $tradeRuleOpp->PolyRuleRoles()->create(['role' => $roleName]);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Operation Rule saved');

        return redirect()->back();
    }
}
