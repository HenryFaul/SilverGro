<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DebtorStanding;
use App\Models\TransportInvoice;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DebtorStandingController extends Controller
{

    public function calculateDebtors(): \Illuminate\Http\RedirectResponse
    {

        $customers = Customer::with('CustomerRating')->with('TermsOfPayment')->with('TermsOfPaymentBasis')
            ->with('InvoiceBasis')->where('is_active',1)->get();

        $cur_date = (Carbon::now()->tz('Africa/Johannesburg'));

        $counter =0;

        foreach ($customers as $customer){

            $customer_total_outstanding = 0;
            $customer_total_overdue = 0;

            //Get invoices for the customer
            $invoices = TransportInvoice::where('customer_id',$customer->id)->get();

            //Loop over the invoices

            foreach ($invoices as $invoice){

                $invoice_detail = $invoice->TransportInvoiceDetails;
                (boolean) $invoice_active = $invoice->is_active;
                $transport_transaction = $invoice->TransportTransaction;
                $contract_type_id = $transport_transaction->contract_type_id;
                $deal_ticket = $transport_transaction->DealTicket;
                $deal_ticket_is_active = $deal_ticket->is_active;

               // $transport_trans = TransportTransaction::where('id', $deal_ticket->transport_trans_id)->first();

                if ($contract_type_id === 4){
                    //if amount paid less than  invoice amount

                    if ($invoice_detail->invoice_amount_paid < $invoice_detail->invoice_amount){
                        $counter++;

                        $invoice_balance = ($invoice_detail->invoice_amount - $invoice_detail->invoice_amount_paid);
                        $invoice_detail->outstanding = $invoice_balance;
                        $customer_total_outstanding += $invoice_balance;

                        $day_to_add = $customer->TermsOfPayment->days;
                        $invoice_date = Carbon::create($invoice_detail->invoice_date);
                        $adjusted_date = $invoice_date->addDays($day_to_add);

                        if($adjusted_date < $cur_date){
                            $customer_total_overdue += $invoice_balance;
                            $invoice_detail->overdue = $invoice_balance;
                        }

                        $invoice_detail->save();

                    }
                }


            }

            $debtor_standing = DebtorStanding::where('customer_id',$customer->id)->first();

            if($debtor_standing == null){
                $debtor_standing =  DebtorStanding::create(['customer_id'=>$customer->id]);
            }

           // $debtor_standing = DebtorStanding::firstOrNew(['customer_id' =>  $customer->id])->get();

            //$debtor_standing->customer_id = $customer->id;

            $debtor_standing->total_outstanding = round($customer_total_outstanding,2);
            $debtor_standing->total_overdue = round($customer_total_overdue,2);
            $debtor_standing->updated_at = $cur_date->toDayDateTimeString();

            $debtor_standing->save();


        }

        return to_route('debtor.index');


    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): \Inertia\Response|\Inertia\ResponseFactory
    {
        //$this->calculateDebtors();

        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show',
            'hasBalance',
            'selected_client_id'
        ]);

        if( !isset($filters['hasBalance']) ){
            $filters['hasBalance']=true;
        }


        $paginate = $request['show'] ?? 25;

        $debtors_standings = DebtorStanding::filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        $debtors_standings_totals = DebtorStanding::where('total_outstanding', '>',0)->get();
        $total_outstanding =0;
        $total_overdue=0;

        foreach ($debtors_standings_totals as $standing){
            $total_outstanding += $standing->total_outstanding;
            $total_overdue += $standing->total_overdue;
        }

        $max_date = DebtorStanding::max('updated_at');

        $selected_client_id = $filters['selected_client_id'] ?? null;

        $customer = null;

        if ($selected_client_id != null){
            $customer = Customer::find($selected_client_id);
        }

        $invoices=null;

        if($customer != null){


           // $invoices = TransportInvoice::where('customer_id',$selected_client_id)->with('TransportInvoiceDetails')->get();
            $invoices = TransportInvoice::where('customer_id',$selected_client_id)->with('TransportInvoiceDetails')
                ->whereHas('TransportInvoiceDetails', function (Builder $query) {
                    $query->where('outstanding', '>', 0);
                })
                ->get();
        }


        return inertia(
            'Customer/DebtorStanding',
            [
                'filters' => $filters,
                'debtors_standings' => $debtors_standings,
                'max_date'=>$max_date,
                'selected_client_id'=>$selected_client_id,
                'invoices'=>$invoices,
                'selected_client'=>$customer,
                'total_outstanding'=>$total_outstanding,
                'total_overdue'=>$total_overdue

            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DebtorStanding $debtorStanding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DebtorStanding $debtorStanding)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DebtorStanding $debtorStanding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DebtorStanding $debtorStanding)
    {
        //
    }
}
