<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DebtorStanding;
use App\Models\TransportInvoice;
use Carbon\Carbon;
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
                        $customer_total_outstanding += $invoice_balance;

                        $day_to_add = $customer->TermsOfPayment->days;
                        $invoice_date = Carbon::create($invoice_detail->invoice_date);
                        $adjusted_date = $invoice_date->addDays($day_to_add);

                        if($adjusted_date < $cur_date){
                            $customer_total_overdue += $invoice_balance;
                        }

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
            'hasBalance'
        ]);

        $paginate = $request['show'] ?? 10;

        $debtors_standings = DebtorStanding::filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        $max_date = DebtorStanding::max('updated_at');


        return inertia(
            'Customer/DebtorStanding',
            [
                'filters' => $filters,
                'debtors_standings' => $debtors_standings,
                'max_date'=>$max_date

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
