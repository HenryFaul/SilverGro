<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OldTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanningWeekController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $filters = $request->only([
            'date',
            'show'
        ]);

        if (!$filters) {
            $cur_date = (Carbon::now())->toDateString();
            $filters['date'] = $cur_date;
        }

        $paginate = $request['show'] ?? 1;

        $old_trans = OldTransaction::with('Customer')->with('Supplier')->with('Transporter')
            ->week($filters)
            ->paginate($paginate)
            ->withQueryString();

        //start of week & end of week


        $start_of_week = (Carbon::parse($request->date))->startOfWeek();
        $end_of_week = (Carbon::parse($request->date))->endOfWeek();


        //TONS IN	TONS OUT	WEIGHT UPLOADED	WEIGHT OFFLOADED	COST PRICE	TRANS COST	OTHER COSTS	SELL PRICE	GP	GP %

        //weight_in_tons_incoming

        $tons_in = round(OldTransaction::week($filters)->sum('weight_in_tons_incoming'), 4);
        $tons_out = round(OldTransaction::week($filters)->sum('weight_in_tons_outgoing'), 4);
        $cost_price = round(OldTransaction::week($filters)->sum('total_cost_price'), 4);
        $trans_cost = round(OldTransaction::week($filters)->sum('transport_cost_price'), 4);
        $selling_price = round(OldTransaction::week($filters)->sum('selling_price'), 4);
        $gp = round(OldTransaction::week($filters)->sum('gross_profit'), 4);
        $gp_perc = round(OldTransaction::week($filters)->sum('gross_profit_perc'), 4);


        //

        return inertia(
            'Planning/WeekPlan',
            [
                'old_trans' => $old_trans,
                'filters' => $filters,
                'tons_in' => $tons_in,
                'tons_out' => $tons_out,
                'cost_price' => $cost_price,
                'trans_cost' => $trans_cost,
                'selling_price' => $selling_price,
                'gp' => $gp,
                'gp_perc' => $gp_perc,
                'start_of_week' => $start_of_week,
                'end_of_week' => $end_of_week,
            ]
        );
    }
}
