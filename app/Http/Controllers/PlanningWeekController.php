<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OldTransaction;
use App\Models\TransportDriverVehicle;
use App\Models\TransportTransaction;
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
            $cur_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
            $filters['date'] = $cur_date;
        }

        $paginate = $request['show'] ?? 1;


        $transport_trans = TransportTransaction::with('Customer')->with('Supplier')->with('Transporter')->with('ContractType')->with('Product')->with('DealTicket')
            ->with('TransportFinance')->with('TransportLoad')->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle'))->with('TransportInvoiceDetails')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))
            ->week($filters)
            ->orderBy('transport_date_earliest', 'asc')
            ->paginate($paginate)
            ->withQueryString();


        //start of week & end of week
        $start_of_week = (Carbon::parse($request->date)->tz('Africa/Johannesburg'))->startOfWeek();
        $end_of_week = (Carbon::parse($request->date)->tz('Africa/Johannesburg'))->endOfWeek();


        //total variable using the actual data

        //Number of Loads	Planned Tons	Weight Uploaded	Weight Offloaded	Cost Price	Transport Cost	Other Costs	Selling Price	Gross Profit	GP %
        $no_loads = 0;
        $planned_tons = 0;
        $weight_uploaded = 0;
        $weight_offloaded = 0;
        $cost_price = 0;
        $trans_cost = 0;
        $other_costs = 0;
        $selling_price = 0;
        $gp = 0;
        $gp_perc = 0;

        $no_loads = TransportTransaction::where('include_in_calculations', '=', 1)->where('contract_type_id', '=', 4)->week($filters)->count('id');
        $trans_data = TransportTransaction::where('include_in_calculations', '=', 1)->where('contract_type_id', '=', 4)->week($filters)->with('TransportFinance')->with('TransportDriverVehicle')->with('TransportJob')->with('Customer')->with('Transporter')->get();

        foreach ($trans_data as $trans) {
            $transport_finance = $trans->TransportFinance;
            $transport_job = $trans->TransportJob;
            $customer = $trans->Customer;
            $transporter = $trans->Transporter;
            $transport_driver_vehicles = $trans->TransportDriverVehicle;

            $cur_weight_uploaded=0;
            $cur_weight_offloaded=0;

            foreach ($transport_driver_vehicles as $driver_vehicle) {
                $cur_weight_uploaded += $driver_vehicle->weighbridge_upload_weight;
                $cur_weight_offloaded += $driver_vehicle->weighbridge_offload_weight;
            }

            //weight bridge offloaded > 0
            if ($cur_weight_offloaded > 0) {
                //Invoice basis == Upload Weight
                if ($customer->invoice_basis_id == 1) {
                    $selling_price += ($transport_finance->selling_price_per_ton * $cur_weight_uploaded);
                } else {
                    $selling_price += ($transport_finance->selling_price_per_ton * $cur_weight_offloaded);
                }
                //$totalcostprice = $totalcostprice + ($plan['costpriceperton'] * $plan['weighbridgeuploadweight']);
                $cost_price += ($transport_finance->cost_price_per_ton * $cur_weight_uploaded);

            } else {
                //$totalcostprice = $totalcostprice + $plan['costprice'];
                $cost_price += $transport_finance->cost_price;
                $selling_price += $transport_finance->selling_price;
            }

            //Transporter rate basis = 'Per Load'
            if ($transport_finance->transport_rate_basis_id == 3) {
                $trans_cost += $transport_finance->transport_rate;

            }else{

                //weighbridge upload weight > 0

                if ($cur_weight_uploaded > 0) {
                    $trans_cost += ($transport_finance->transport_rate * $cur_weight_uploaded);
                }
                else{
                    $trans_cost += ($transport_finance->transport_rate *  $transport_finance->weight_ton_outgoing);
                }
            }

            $weight_uploaded += $cur_weight_uploaded;
            $weight_offloaded += $cur_weight_offloaded;
            $planned_tons += $transport_finance->weight_ton_incoming;
            $other_costs += ($transport_finance->additional_cost_1 + $transport_finance->additional_cost_2 + $transport_finance->additional_cost_3 + $transport_finance->adjusted_gp);

        }

        $gp = $selling_price - $cost_price - $trans_cost - $other_costs;

        if ($selling_price > 0) {
            $gp_perc = ( round($gp) / round($selling_price)) * 100;
        }

        return inertia(
            'Planning/WeekPlan',
            [
                'transport_trans' => $transport_trans,
                'filters' => $filters,
                'start_of_week' => $start_of_week,
                'end_of_week' => $end_of_week,
                'no_loads' => $no_loads,
                'weight_uploaded' => round($weight_uploaded, 1),
                'planned_tons' => round($planned_tons, 1),
                'weight_offloaded' => round($weight_offloaded, 1),
                'cost_price' => round($cost_price, 0),
                'trans_cost' => round($trans_cost, 0),
                'other_costs' => round($other_costs, 0),
                'selling_price' => round($selling_price, 0),
                'gp' => round($gp, 0),
                'gp_perc' => round($gp_perc, 1),
            ]
        );
    }
}
