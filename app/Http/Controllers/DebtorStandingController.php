<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DebtorStanding;
use App\Models\TransportInvoice;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\ResponseFactory;
use Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DebtorStandingController extends Controller
{

    public function calculateDebtors(): RedirectResponse
    {

        $customers = Customer::with([
            'CustomerRating',
            'TermsOfPayment',
            'TermsOfPaymentBasis',
            'InvoiceBasis'
        ])->where('is_active', 1)->get();

        $cur_date = Carbon::now()->tz('Africa/Johannesburg');

        $counter = 0;

        foreach ($customers as $customer) {

            $customer_total_outstanding = 0;
            $customer_total_overdue = 0;

            // Check if customer has required payment terms
            if (!$customer->TermsOfPayment) {
                Log::warning("Customer {$customer->id} missing TermsOfPayment");
                continue;
            }

            // Get invoices for the customer with eager loading
            $invoices = TransportInvoice::where('customer_id', $customer->id)
                ->with([
                    'TransportInvoiceDetails',
                    'TransportTransaction.DealTicket'
                ])
                ->get();

            // Loop over the invoices
            foreach ($invoices as $invoice) {

                // Skip if invoice details don't exist
                if (!$invoice->TransportInvoiceDetails) {
                    Log::warning("Invoice {$invoice->id} missing TransportInvoiceDetails");
                    continue;
                }

                $invoice_detail = $invoice->TransportInvoiceDetails;

                // Skip if transport transaction doesn't exist
                if (!$invoice->TransportTransaction) {
                    Log::warning("Invoice {$invoice->id} missing TransportTransaction");
                    // Reset outstanding/overdue since we can't calculate
                    $invoice_detail->outstanding = 0;
                    $invoice_detail->overdue = 0;
                    $invoice_detail->save();
                    continue;
                }

                $transport_transaction = $invoice->TransportTransaction;
                $contract_type_id = $transport_transaction->contract_type_id;

                // Only process contract type 4
                if ($contract_type_id === 4) {
                    // Treat as unpaid only if not flagged as paid AND amount not fully settled
                    if (!$invoice_detail->is_invoice_paid && $invoice_detail->invoice_amount_paid < $invoice_detail->invoice_amount) {
                        $counter++;

                        $invoice_balance = ($invoice_detail->invoice_amount - $invoice_detail->invoice_amount_paid);
                        $invoice_detail->outstanding = $invoice_balance;
                        $customer_total_outstanding += $invoice_balance;

                        $day_to_add = $customer->TermsOfPayment->days;
                        $invoice_date = Carbon::create($invoice_detail->invoice_date);
                        $adjusted_date = $invoice_date->copy()->addDays($day_to_add);

                        if ($adjusted_date < $cur_date) {
                            $customer_total_overdue += $invoice_balance;
                            $invoice_detail->overdue = $invoice_balance;
                        } else {
                            // Reset overdue if not overdue anymore
                            $invoice_detail->overdue = 0;
                        }

                        $invoice_detail->save();

                    } else {
                        // Invoice is fully paid - reset outstanding and overdue
                        $invoice_detail->outstanding = 0;
                        $invoice_detail->overdue = 0;
                        $invoice_detail->save();
                    }
                } else {
                    // Not contract type 4 - reset outstanding and overdue
                    $invoice_detail->outstanding = 0;
                    $invoice_detail->overdue = 0;
                    $invoice_detail->save();
                }
            }

            $debtor_standing = DebtorStanding::firstOrCreate(
                ['customer_id' => $customer->id]
            );

            $debtor_standing->total_outstanding = round($customer_total_outstanding, 2);
            $debtor_standing->total_overdue = round($customer_total_overdue, 2);
            $debtor_standing->updated_at = $cur_date->toDayDateTimeString();

            $debtor_standing->save();
        }

        return to_route('debtor.index');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): Response|ResponseFactory
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
            $invoices = TransportInvoice::where('customer_id',$selected_client_id)
                ->with([
                    'TransportInvoiceDetails',
                    'TransportTransaction:id,a_mq,customer_id',
                    'TransportTransaction.Customer:id,last_legal_name'
                ])
                ->whereHas('TransportInvoiceDetails', function (Builder $query) {
                    $query->where('outstanding', '>', 0)
                          ->where('is_invoice_paid', false);
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

    /**
     * Export invoices to Excel for a specific customer
     */
    public function exportInvoices(Request $request)
    {
        $customer_id = $request->input('customer_id');

        if (!$customer_id) {
            return back()->with('error', 'No customer selected');
        }

        $customer = Customer::find($customer_id);

        if (!$customer) {
            return back()->with('error', 'Customer not found');
        }

        // Get invoices for the customer
        $invoices = TransportInvoice::where('customer_id', $customer_id)
            ->with([
                'TransportInvoiceDetails',
                'TransportTransaction:id,a_mq,customer_id',
                'TransportTransaction.Customer:id,last_legal_name'
            ])
            ->whereHas('TransportInvoiceDetails', function (Builder $query) {
                $query->where('outstanding', '>', 0);
            })
            ->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No outstanding invoices found for this customer');
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle('Invoices');

            // Style arrays
            $headerStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'font' => [
                    'bold' => true,
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ];

            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 14
                ]
            ];

            // Set title
            $sheet->setCellValue('A1', 'Outstanding Invoices Report');
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->applyFromArray($titleStyle);

            $sheet->setCellValue('A2', 'Customer: ' . $customer->last_legal_name);
            $sheet->mergeCells('A2:K2');

            $sheet->setCellValue('A3', 'Generated: ' . Carbon::now()->format('Y-m-d H:i:s'));
            $sheet->mergeCells('A3:K3');

            // Set headers in row 5
            $headers = [
                'A5' => 'TRADE',
                'B5' => 'MQ',
                'C5' => 'Invoice No',
                'D5' => 'Customer',
                'E5' => 'Invoice Amount',
                'F5' => 'Paid Amount',
                'G5' => 'Invoice Date',
                'H5' => 'Due Date',
                'I5' => 'Paid Date',
                'J5' => 'Outstanding',
                'K5' => 'Overdue'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            $sheet->getStyle('A5:K5')->applyFromArray($headerStyle);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);

            // Populate data
            $row = 6;
            $totalOutstanding = 0;
            $totalOverdue = 0;

            foreach ($invoices as $invoice) {
                $details = $invoice->TransportInvoiceDetails;

                if (!$details) {
                    continue;
                }

                $sheet->setCellValue('A' . $row, $invoice->transport_trans_id);
                $sheet->setCellValue('B' . $row, $invoice->TransportTransaction->a_mq ?? 'N/A');
                $sheet->setCellValue('C' . $row, $details->invoice_no);
                $sheet->setCellValue('D' . $row, $invoice->TransportTransaction->Customer->last_legal_name ?? 'N/A');
                $sheet->setCellValue('E' . $row, $details->invoice_amount ?? 0);
                $sheet->setCellValue('F' . $row, $details->invoice_amount_paid ?? 0);
                $sheet->setCellValue('G' . $row, $details->invoice_date ?? 'N/A');
                $sheet->setCellValue('H' . $row, $details->invoice_pay_by_date ?? 'N/A');
                $sheet->setCellValue('I' . $row, $details->invoice_paid_date ?? 'Not Paid');
                $sheet->setCellValue('J' . $row, $details->outstanding);
                $sheet->setCellValue('K' . $row, $details->overdue);

                // Format currency columns
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $totalOutstanding += $details->outstanding;
                $totalOverdue += $details->overdue;

                $row++;
            }

            // Add totals row
            $sheet->setCellValue('I' . $row, 'TOTALS:');
            $sheet->setCellValue('J' . $row, $totalOutstanding);
            $sheet->setCellValue('K' . $row, $totalOverdue);

            $sheet->getStyle('I' . $row . ':K' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);

            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Generate filename
            $datestamp = time();
            $file_name = 'invoices_' . $customer->id . '_' . $datestamp . '.xlsx';

            // Save to temporary file
            $resource = tmpfile();
            $writer = new Xlsx($spreadsheet);
            $writer->save($resource);

            // Store in storage
            Storage::put('reports/excel/' . $file_name, $resource);

            // Return download URL
            return response()->json([
                'success' => true,
                'download_url' => $file_name,
                'message' => 'Export generated successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel file
     */
    public function download($file_name): StreamedResponse
    {
        return Storage::download('/reports/excel/' . $file_name);
    }
}
