<?php

namespace App\Http\Controllers;

use App\Charts\MonthlyGpChart;
use App\Charts\MonthlyPcChart;
use App\Models\ContractSummary;
use App\Models\Customer;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class
DashboardController extends Controller
{
    //

    public function index(MonthlyPcChart $chart)
    {

        $this->doSummary();
        $today_date = Carbon::now();
        $month = ($today_date)->monthName;
        $filters['date'] = $today_date->toDateString();

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

        $trans_data = TransportTransaction::where('include_in_calculations', '=', 1)->where('contract_type_id', '=', 4)->month($filters)->with('TransportFinance')->with('TransportDriverVehicle')->with('TransportJob')->with('Customer')->with('Transporter')->get();

    /*    foreach ($trans_data as $trans) {
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


        $stats = array(
            array('planned_tons_in' => 'bar','value'=>$planned_tons_in,'change'=>'+4.75%','changeType'=>'positive'),
            array('planned_tons_out' => 'bar','value'=>$planned_tons_out,'change'=>'+4.75%','changeType'=>'positive'),
            array('weight_uploaded' => 'bar','value'=>$weight_uploaded,'change'=>'+4.75%','changeType'=>'positive'),
            array('weight_offloaded' => 'bar','value'=>$weight_offloaded,'change'=>'+4.75%','changeType'=>'positive'),
            array('cost_price' => 'bar','value'=>$cost_price,'change'=>'+4.75%','changeType'=>'positive'),
            array('trans_cost' => 'bar','value'=>$trans_cost,'change'=>'+4.75%','changeType'=>'positive'),
            array('other_costs' => 'bar','value'=>$other_costs,'change'=>'+4.75%','changeType'=>'positive'),
            array('selling_price' => 'bar','value'=>$selling_price,'change'=>'+4.75%','changeType'=>'positive'),
            array('gp' => 'bar','value'=>'bar','change'=>$gp,'changeType'=>'positive'),
            array('gp_perc' => 'bar','value'=>'bar','change'=>$gp_perc,'changeType'=>'positive'),

        );
        //chart



        //{ name: 'Revenue', value: '$405,091.00', change: '+4.75%', changeType: 'positive' },
        return inertia(
            'Dashboard',
            [
                'month'=>$month,
                'planned_tons_in'=> round($planned_tons_in,2),
                'planned_tons_out'=> round($planned_tons_out,2),
                'weight_uploaded'=> round($weight_uploaded,2),
                'weight_offloaded'=> round($weight_offloaded,2),
                'cost_price'=> round($cost_price,2),
                'trans_cost'=> round($trans_cost,2),
                'other_costs'=> round($other_costs,2),
                'selling_price'=> round($selling_price,2),
                'gp'=> round($gp,2),
                'gp_perc'=> round($gp_perc),
                'stats'=>$stats,
                'chart' => $chart->build(),
            ]
        );
    }

    public function doSummary(){

        $today_date = Carbon::now();
        $month = ($today_date)->monthName;
        $filters['date'] = $today_date->toDateString();

        $trans_data = TransportTransaction::where('contract_type_id', '=', 2)->with('TransportFinance')->with('TransportDriverVehicle')->with('TransportJob')->get();

        foreach ($trans_data as $trans) {
            $transport_finance = $trans->TransportFinance;
            $transport_job = $trans->TransportJob;
            $transport_load= $trans->TransportLoad;
            $transport_driver_vehicles = $trans->TransportDriverVehicle;

            $cur_weight_uploaded = 0;
            $cur_weight_offloaded = 0;

            foreach ($transport_driver_vehicles as $driver_vehicle) {
                $cur_weight_uploaded += $driver_vehicle->weighbridge_upload_weight;
                $cur_weight_offloaded += $driver_vehicle->weighbridge_offload_weight;
            }

            $contract_summary = ContractSummary::where('transport_trans_id',$trans->id)->first();

            if($contract_summary != null){

                $contract_summary->update(
                  [
                      'transport_date_earliest'=>$trans->transport_date_earliest,
                      'contract_type_id'=>$trans->contract_type_id,
                      'contract_number'=>'PC'.$trans->id,
                      'planned_tons_in'=> $transport_load->no_units_incoming??0,
                      'planned_tons_out'=> $transport_load->no_units_outgoing,
                      'actual_tons_in'=>$cur_weight_uploaded,
                      'actual_tons_out'=>$cur_weight_offloaded,
                      'variance_in'=>$transport_load->no_units_incoming-$cur_weight_uploaded,
                      'variance_out'=>$transport_load->no_units_outgoing-$cur_weight_offloaded
                  ]
                );

            }
            else {
                $contract_summary = ContractSummary::create([
                    'transport_trans_id' => $trans->id,
                    'transport_date_earliest'=>$trans->transport_date_earliest,
                    'contract_type_id'=>$trans->contract_type_id,
                    'contract_number'=>'PC'.$trans->id,
                    'planned_tons_in'=> $transport_load->no_units_incoming??0,
                    'planned_tons_out'=> $transport_load->no_units_outgoing,
                    'actual_tons_in'=>$cur_weight_uploaded,
                    'actual_tons_out'=>$cur_weight_offloaded,
                    'variance_in'=>$transport_load->no_units_incoming-$cur_weight_uploaded,
                    'variance_out'=>$transport_load->no_units_outgoing-$cur_weight_offloaded

                ]);
            }


        }


    }
}
