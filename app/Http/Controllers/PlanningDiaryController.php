<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use App\Models\Customer;
use App\Models\OldTransaction;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanningDiaryController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $filters = $request->only([
            'date',
            'show',
            'contract_type_id',
        ]);

        if (!$filters) {
            $cur_date = (Carbon::now())->toDateString();
            $filters['date'] = $cur_date;
        }

        $paginate = $request['show'] ?? 25;

        $transport_trans = TransportTransaction::with('Customer')->with('Supplier')->with('Transporter')->with('ContractType')->with('Product')->with('DealTicket')->with('TransportInvoice')
            ->with('TransportFinance')->with('TransportLoad')->with('TransportInvoiceDetails')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle')))
            ->with('TransportLoad')->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle'))
            ->filter($filters)
            ->orderBy('transport_date_earliest', 'asc')
            ->paginate($paginate)
            ->withQueryString();

        $contract_types = ContractType::all();


        //TONS IN	TONS OUT	WEIGHT UPLOADED	WEIGHT OFFLOADED	COST PRICE	TRANS COST	OTHER COSTS	SELL PRICE	GP	GP %

        //Tons In
        //Tons Out
        //Weight Uploaded
        //Weight Offloaded
        //Cost Price
        //Trans Cost
        //Other Costs
        //Sell Price
        //GP
        //GP %

        $planned_tons_in = 0;
        $planned_tons_out = 0;
        $weight_uploaded = 0;
        $weight_offloaded = 0;
        $cost_price = 0;
        $trans_cost = 0;
        $other_costs = 0;
        $selling_price = 0;
        $gp = 0;
        $gp_perc = 0;

        $trans_data = TransportTransaction::where('include_in_calculations', '=', 1)->where('contract_type_id', '=', 4)->filter($filters)->with('TransportFinance')->with('TransportDriverVehicle')->with('TransportJob')->with('Customer')->with('Transporter')->get();

   /*     foreach ($trans_data as $trans) {
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

            //dump($transport_job->transport_rate_basis_id);
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
            $planned_tons_in += $transport_finance->weight_ton_incoming;
            $planned_tons_out += $transport_finance->weight_ton_outgoing;
            $other_costs += ($transport_finance->additional_cost_1 + $transport_finance->additional_cost_2 + $transport_finance->additional_cost_3 + $transport_finance->adjusted_gp);

        }*/

        foreach ($trans_data as $trans) {
            $transport_finance = $trans->TransportFinance;

            $cur_weight_uploaded=$transport_finance->weight_ton_incoming_actual;
            $cur_weight_offloaded=$transport_finance->weight_ton_outgoing_actual;

            $weight_uploaded += $cur_weight_uploaded;
            $weight_offloaded += $cur_weight_offloaded;
            $planned_tons_in += $transport_finance->weight_ton_incoming;
            $planned_tons_out += $transport_finance->weight_ton_outgoing;

            $cost_price+=$transport_finance->cost_price;
            $trans_cost+=$transport_finance->transport_cost;
            $selling_price+=$transport_finance->selling_price;

            $other_costs += ($transport_finance->additional_cost_1 + $transport_finance->additional_cost_2 + $transport_finance->additional_cost_3 + $transport_finance->adjusted_gp);

        }

        $gp = $selling_price - $cost_price - $trans_cost - $other_costs;

        if ($selling_price > 0) {
            $gp_perc = ( round($gp) / round($selling_price)) * 100;
        }

        return inertia(
            'Planning/Diary',
            [
                'transport_trans'=>$transport_trans,
                'filters' => $filters,
                'weight_uploaded' => round($weight_uploaded, 4),
                'planned_tons_in' => round($planned_tons_in, 4),
                'planned_tons_out' => round($planned_tons_out, 4),
                'weight_offloaded' => round($weight_offloaded, 4),
                'cost_price' => round($cost_price, 0),
                'trans_cost' => round($trans_cost, 0),
                'other_costs' => round($other_costs, 0),
                'selling_price' => round($selling_price, 0),
                'gp' => round($gp, 0),
                'gp_perc' => round($gp_perc, 1),
                'contract_types'=>$contract_types
            ]
        );
    }
}
