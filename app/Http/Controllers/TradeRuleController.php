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
            'min_trade_value' => ['required', 'numeric', 'min:0'],
            'max_trade_value' => ['required', 'numeric', 'gt:min_trade_value'],
            'is_active'       => ['required', 'boolean'],
            'roles'           => ['present', 'array', 'min:1'],
            'roles.*'         => ['string', 'exists:roles,name'],
        ], [
            'roles.min' => 'At least one approver role is required.',
        ]);

        $this->validateNoOverlap($request->min_trade_value, $request->max_trade_value);

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
            'min_trade_value' => ['required', 'numeric', 'min:0'],
            'max_trade_value' => ['required', 'numeric', 'gt:min_trade_value'],
            'is_active'       => ['required', 'boolean'],
            'roles'           => ['present', 'array', 'min:1'],
            'roles.*'         => ['string', 'exists:roles,name'],
        ], [
            'roles.min' => 'At least one approver role is required.',
        ]);

        $this->validateNoOverlap($request->min_trade_value, $request->max_trade_value, $tradeRule->id);

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

    /**
     * Abort with a validation error if [min, max] overlaps any existing trade rule range.
     * Excludes $ignoreId (used on update to ignore the rule being edited).
     */
    private function validateNoOverlap(float $min, float $max, ?int $ignoreId = null): void
    {
        $query = TradeRule::whereNull('deleted_at')
            ->where('min_trade_value', '<', $max)   // existing starts before new ends
            ->where('max_trade_value', '>', $min);  // existing ends after new starts

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        $overlapping = $query->first();

        if ($overlapping) {
            $formatter = fn($v) => 'R ' . number_format($v, 0, '.', ' ');
            $msg = sprintf(
                'This range (%s – %s) overlaps with the existing rule "%s" (%s – %s). Ranges must not overlap.',
                $formatter($min),
                $formatter($max),
                $overlapping->name,
                $formatter($overlapping->min_trade_value),
                $formatter($overlapping->max_trade_value)
            );
            throw \Illuminate\Validation\ValidationException::withMessages([
                'min_trade_value' => $msg,
                'max_trade_value' => $msg,
            ]);
        }
    }
}
