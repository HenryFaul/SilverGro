<?php

namespace App\Http\Controllers;

use App\Models\TradeRule;
use App\Models\TradeRuleOpp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TradeRuleController extends Controller
{
    public function index()
    {
        $trade_rules     = TradeRule::with('PolyRuleRoles')->orderBy('min_trade_value')->get();
        $trade_rule_opps = TradeRuleOpp::with('PolyRuleRoles')->orderBy('id')->get();
        $all_roles       = Role::orderBy('name')->get(['id', 'name']);

        return inertia('TradeRule/Index', [
            'trade_rules'     => $trade_rules,
            'trade_rule_opps' => $trade_rule_opps,
            'all_roles'       => $all_roles,
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $request->validate([
            'name'            => ['required', 'string', 'unique:trade_rules,name'],
            'min_trade_value' => ['required', 'numeric', 'min:0', 'unique:trade_rules,min_trade_value'],
            'max_trade_value' => ['required', 'numeric', 'gt:min_trade_value', 'unique:trade_rules,max_trade_value'],
            'is_active'       => ['required', 'boolean'],
            'roles'           => ['present', 'array'],
            'roles.*'         => ['string', 'exists:roles,name'],
        ]);

        $rule = TradeRule::create([
            'name'            => $request->name,
            'min_trade_value' => $request->min_trade_value,
            'max_trade_value' => $request->max_trade_value,
            'is_active'       => $request->is_active,
        ]);

        foreach ($request->roles as $roleName) {
            $rule->PolyRuleRoles()->create(['role' => $roleName]);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Trade Rule created');

        return redirect()->back();
    }

    public function update(Request $request, TradeRule $tradeRule)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $request->validate([
            'name'            => ['required', 'string', Rule::unique('trade_rules', 'name')->ignore($tradeRule->id)->whereNull('deleted_at')],
            'min_trade_value' => ['required', 'numeric', 'min:0', Rule::unique('trade_rules', 'min_trade_value')->ignore($tradeRule->id)->whereNull('deleted_at')],
            'max_trade_value' => ['required', 'numeric', 'gt:min_trade_value', Rule::unique('trade_rules', 'max_trade_value')->ignore($tradeRule->id)->whereNull('deleted_at')],
            'is_active'       => ['required', 'boolean'],
            'roles'           => ['present', 'array'],
            'roles.*'         => ['string', 'exists:roles,name'],
        ]);

        $tradeRule->update([
            'name'            => $request->name,
            'min_trade_value' => $request->min_trade_value,
            'max_trade_value' => $request->max_trade_value,
            'is_active'       => $request->is_active,
        ]);

        $tradeRule->PolyRuleRoles()->delete();
        foreach ($request->roles as $roleName) {
            $tradeRule->PolyRuleRoles()->create(['role' => $roleName]);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Trade Rule saved');

        return redirect()->back();
    }

    public function destroy(Request $request, TradeRule $tradeRule)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $tradeRule->PolyRuleRoles()->delete();
        $tradeRule->delete();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Trade Rule deleted');

        return redirect()->back();
    }
}
