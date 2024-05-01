<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\AssignedUserComm;
use App\Models\BillingUnits;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\DealTicket;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\ProductSource;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\TradeRule;
use App\Models\TransLink;
use App\Models\TransportApproval;
use App\Models\TransportDriverVehicle;
use App\Models\Transporter;
use App\Models\TransportFinance;
use App\Models\TransportInvoice;
use App\Models\TransportInvoiceDetails;
use App\Models\TransportJob;
use App\Models\TransportLoad;
use App\Models\TransportOrder;
use App\Models\TransportStatus;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 100000);

class NewTransaction implements ToCollection, WithHeadingRow
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */


    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            if ($row != null) {

                if ($row['id'] != '') {

                    // $orig_date = Carbon::createFromTimestamp($timestamp)->toDateTimeString();

                    $conv_old_id = is_numeric($row['id']) ? $row['id'] : 0;
                    $conv_old_deal_ticket = trim($row['dealticket']) === '' || trim($row['dealticket']) === 'NULL' ? null : trim($row['dealticket']);

                    $date_created = Carbon::createFromTimestamp($row['ts_datecreated'])->toDateTimeString();
                    $conv_contract_type_id = $row['contracttype'] === 'PC' ? 2 : ($row['contracttype'] === 'SC' ? 3 : ($row['contracttype'] === 'MQ' ? 4 : 1));
                    $conv_contract_no = $row['contractno'] != 'NULL' && $row['contractno'] != '' ? $row['contractno'] : null;

                    //supplierid
                    $supplierid = trim($row['supplierid']);
                    if ($supplierid === "" || $supplierid === 0 || $supplierid === "0") {
                        $supplierid = 1;
                    }
                    $found_supplier = Supplier::where('id', $supplierid)->first();
                    $found_supplier_id = $found_supplier != null ? $found_supplier->id : 1;

                    //customerid
                    $customerid = trim($row['customerid']);
                    if ($customerid === "" || $customerid === 0 || $customerid === "0") {
                        $customerid = 1;
                    }
                    $found_customer = Customer::where('id', $customerid)->first();
                    $found_customer_id = $found_customer != null ? $found_customer->id : 1;

                    //transporterid
                    $transporterid = trim($row['transporterid']);
                    if ($transporterid === "" || $transporterid === 0 || $transporterid === "0") {
                        $transporterid = 1;
                    }
                    $found_transporter = Transporter::where('id', $transporterid)->first();
                    $found_transporter_id = $found_transporter != null ? $found_transporter->id : 1;

                    //productid
                    $productid = trim($row['productid']);
                    if ($productid === "" || $productid === 0 || $productid === "0") {
                        $productid = 1;
                    }
                    $found_product = Product::where('old_id', $productid)->first();
                    $found_product_id = $found_product != null ? $found_product->id : 1;

                    // include_in_calculations
                    $inc_calcs = !(trim($row['include_in_calculations']) == 0);
                    //Trans done
                    $is_trans_done = trim($row['transactiondone']) == 1 ? true : false;

                    //Notes

                    $conv_delivery_notes = trim($row['deliverynote']) === '' || trim($row['deliverynote']) === 'NULL' ? null : trim($row['deliverynote']);
                    $conv_product_notes = trim($row['productnotes']) === '' || trim($row['productnotes']) === 'NULL' ? null : trim($row['productnotes']);
                    $conv_customer_notes = trim($row['customernotes']) === '' || trim($row['customernotes']) === 'NULL' ? null : trim($row['customernotes']);
                    $conv_transport_notes = trim($row['transportnotes']) === '' || trim($row['transportnotes']) === 'NULL' ? null : trim($row['transportnotes']);
                    $conv_pricing_notes = trim($row['pricingnotes']) === '' || trim($row['pricingnotes']) === 'NULL' ? null : trim($row['pricingnotes']);
                    $conv_process_notes = trim($row['processnotes']) === '' || trim($row['processnotes']) === 'NULL' ? null : trim($row['processnotes']);

                    $conv_supplier_notes = trim($row['suppliernotes']) === '' || trim($row['suppliernotes']) === 'NULL' ? null : trim($row['suppliernotes']);
                    $conv_document_notes = trim($row['documentsnotes']) === '' || trim($row['documentsnotes']) === 'NULL' ? null : trim($row['documentsnotes']);
                    $conv_transaction_notes = trim($row['transactionnotes']) === '' || trim($row['transactionnotes']) === 'NULL' ? null : trim($row['transactionnotes']);
                    $conv_traders_notes_supplier = trim($row['tradersnotessupply']) === '' || trim($row['tradersnotessupply']) === 'NULL' ? null : trim($row['tradersnotessupply']);
                    $conv_traders_notes_customer = trim($row['tradersnotescustomer']) === '' || trim($row['tradersnotescustomer']) === 'NULL' ? null : trim($row['tradersnotescustomer']);
                    $conv_traders_notes_transport = trim($row['tradersnotestransport']) === '' || trim($row['tradersnotestransport']) === 'NULL' ? null : trim($row['tradersnotestransport']);
                    $traders_notes = trim($row['tradersnotes']) != '' || trim($row['tradersnotes']) != null || trim($row['tradersnotes']) != 'NULL' ? null : trim($row['tradersnotes']);

                    $transport_date_earliest = is_numeric(trim($row['ts_transportdateearliest'])) ? Carbon::createFromTimestamp($row['ts_transportdateearliest'])->toDateTimeString() : null;
                    $transport_date_latest = is_numeric(trim($row['ts_transportdatelatest'])) ? Carbon::createFromTimestamp($row['ts_transportdatelatest'])->toDateTimeString() : null;

                    $transport_rate_basis_id = match (trim($row['transportratebasis'])) {
                        "Per Ton" => 2,
                        "Per Load" => 3,
                        default => 1,
                    };

                    //deal_ticket
                    $old_id_or_deal_ticket = $conv_contract_type_id === 4? $conv_old_deal_ticket : $conv_old_id;
                    $old_id_or_deal_ticket = is_numeric($old_id_or_deal_ticket)? $old_id_or_deal_ticket:0;

                    $exists = TransportTransaction::where('old_id', '=', $old_id_or_deal_ticket)->exists();


                    if(!$exists){
                        //create transaction
                        $transport_trans = TransportTransaction::create([
                            'old_id' => $old_id_or_deal_ticket,
                            'contract_type_id' => $conv_contract_type_id,
                            'contract_no' => $conv_contract_no,
                            'supplier_id' => $found_supplier_id,
                            'customer_id' => $found_customer_id,
                            'transporter_id' => $found_transporter_id,
                            'product_id' => $found_product_id,
                            'include_in_calculations' => $inc_calcs,
                            'transport_date_earliest' => $transport_date_earliest,
                            'transport_date_latest' => $transport_date_latest,
                            'transport_rate_basis_id' => $transport_rate_basis_id,
                            'delivery_notes' => $conv_delivery_notes,
                            'suppliers_notes' => $conv_supplier_notes,
                            'traders_notes' => $traders_notes,
                            'product_notes' => $conv_product_notes,
                            'customer_notes' => $conv_customer_notes,
                            'transport_notes' => $conv_transport_notes,
                            'pricing_notes' => $conv_pricing_notes,
                            'process_notes' => $conv_process_notes,
                            'document_notes' => $conv_document_notes,
                            'transaction_notes' => $conv_transaction_notes,
                            'traders_notes_supplier' => $conv_traders_notes_supplier,
                            'traders_notes_customer' => $conv_traders_notes_customer,
                            'traders_notes_transport' => $conv_traders_notes_transport,
                            'is_transaction_done' => $is_trans_done
                        ]);

                        $transport_trans->created_at = $date_created;

                        $transport_trans->save();

                        //Link

                        //if MQ
                        if ($conv_contract_type_id == 4) {

                            $linkedpc = trim($row['linkedpc']);
                            $linkedsc = trim($row['linkedsc']);

                            if (is_numeric($linkedpc)) {

                                //find PC
                                $found_pc = TransportTransaction::where('old_id', '=', $linkedpc)->where('contract_type_id', '=', 2)->first();
                                if ($found_pc != null) {

                                    //'trans_link_type_id','transport_trans_id','linked_transport_trans_id'
                                    TransLink::create([
                                        'trans_link_type_id' => 3,
                                        'transport_trans_id' => $found_pc->id,
                                        'linked_transport_trans_id' => $transport_trans->id
                                    ]);


                                }

                            }

                            if (is_numeric($linkedsc)) {

                                //find PC
                                $found_sc = TransportTransaction::where('old_id', '=', $linkedsc)->where('contract_type_id', '=', 3)->first();
                                if ($found_sc != null) {

                                    //'trans_link_type_id','transport_trans_id','linked_transport_trans_id'
                                    TransLink::create([
                                        'trans_link_type_id' => 4,
                                        'transport_trans_id' => $found_sc->id,
                                        'linked_transport_trans_id' => $transport_trans->id
                                    ]);
                                }

                            }

                        }

                        //Purchase order
                        $linkedpc = (trim($row['linkedpc']) === '' || trim($row['linkedpc']) === 'NULL' ? null : is_numeric(trim($row['linkedpc']))) ? trim($row['linkedpc']) : null;
                        $confirmed_by_type_id = (((trim($row['purchaseorderconfirmedby']) === '' ? 1 : trim($row['purchaseorderconfirmedby']) === 'Fax') ? 2 : trim($row['purchaseorderconfirmedby']) === 'Telephone') ? 3 : trim($row['purchaseorderconfirmedby']) === 'Email') ? 4 : 1;

                        //unallocated,  Fax, Telephone, Email

                        //purchaseordersent, purchaseorderreceived,

                        $purchaseordersent = !(trim($row['purchaseordersent']) == 0);
                        $purchaseorderreceived = !(trim($row['purchaseorderreceived']) == 0);

                        $purchase_order = PurchaseOrder::create([
                            'old_id' => $linkedpc,
                            'transport_trans_id' => $transport_trans->id,
                            'confirmed_by_type_id' => $confirmed_by_type_id,
                            'confirmed_by_id' => 1,
                            'is_po_sent' => $purchaseordersent,
                            'is_po_received' => $purchaseorderreceived
                        ]);

                        //Sales Oder

                        $sales_order = SalesOrder::create([
                            'transport_trans_id' => $transport_trans->id,
                            'confirmed_by_id'=>1,
                            'confirmed_by_type_id'=>1,
                            'is_printed' => false,
                            'is_active' => false
                        ]);

                        //Transport order

                        $t_o_confirmed_by_type_id = (((trim($row['transportorderconfirmedby']) === '' ? 1 : trim($row['transportorderconfirmedby']) === 'Fax') ? 2 : trim($row['transportorderconfirmedby']) === 'Telephone') ? 3 : trim($row['transportorderconfirmedby']) === 'Email') ? 4 : 1;
                        $is_to_sent = !(trim($row['transportordersent']) == 0);
                        $is_to_received = (trim($row['transportorderreceived']) == 0);

                        $transport_order = TransportOrder::create([
                            'transport_trans_id' => $transport_trans->id,
                            'confirmed_by_type_id' => $t_o_confirmed_by_type_id,
                            'confirmed_by_id' => 1,
                            'is_to_sent' => $is_to_sent,
                            'is_to_received' => $is_to_received
                        ]);

                        //invoice

                        //public $fillable = ['transport_trans_id','type','comment','is_active','is_printed'];

                        $invoiceno = (trim($row['invoiceno']) === '' || trim($row['invoiceno']) === 'NULL' ? null : is_numeric(trim($row['invoiceno']))) ? trim($row['invoiceno']) : null;

                        $transport_invoice = TransportInvoice::create([
                            'old_id' => $invoiceno,
                            'transport_trans_id' => $transport_trans->id,
                            'customer_id'=>$found_customer_id,
                            'is_active' => 0,
                            'is_printed' => 0
                        ]);

                        //invoice details


                        // public $fillable = ['invoice_id','transport_trans_id','is_invoiced','is_invoice_paid','invoice_no', 'invoice_paid_date','invoice_pay_by_date','invoice_date','invoice_amount','cost_price','invoice_amount_paid','cost_price','selling_price','status','notes'];

                        /*   'Closed'
          'In Process'
          'Cancelled'
          'Requires Immediate Attention'
          'Requires Attention'
          'Contracted Client'
          'Customer Required'*/

                        $is_invoiced = !(trim($row['invoiced']) == 0);
                        $is_invoice_paid = !(trim($row['invoicepaid']) == 0);
                        $invoice_paid_date = is_numeric(trim($row['ts_invoicepaiddate'])) ? Carbon::createFromTimestamp($row['ts_invoicepaiddate'])->toDateTimeString() : null;
                        $invoice_pay_by_date = is_numeric(trim($row['ts_invoicepaybydate'])) ? Carbon::createFromTimestamp($row['ts_invoicepaybydate'])->toDateTimeString() : null;
                        $invoice_date = is_numeric(trim($row['ts_invoicedate'])) ? Carbon::createFromTimestamp($row['ts_invoicedate'])->toDateTimeString() : null;
                        $invoice_amount = is_numeric(trim($row['invoiceamount'])) ? trim($row['invoiceamount']) : 0;
                        $invoice_amount_paid = is_numeric(trim($row['invoiceamountpaid'])) ? trim($row['invoiceamountpaid']) : 0;


                        $cost_price = is_numeric(trim($row['costprice'])) ? trim($row['costprice']) : 0;
                        $selling_price = is_numeric(trim($row['sellingprice'])) ? trim($row['sellingprice']) : 0;


                        $status_id = 1;

                        $status_id = match (trim($row['status'])) {
                            "Closed" => 2,
                            "In Process" => 3,
                            "Cancelled" => 4,
                            "Requires Immediate Attention" => 5,
                            "Requires Attention" => 6,
                            "Contracted Client" => 7,
                            "Customer Required" => 8,
                            default => 1,
                        };


                        $notes = trim($row['notes']) === '' || trim($row['notes']) === 'NULL' ? null : trim($row['notes']);

                        $is_before_ded_23 = $transport_date_earliest < date('2023-12-01');

                        $transport_invoice_details = TransportInvoiceDetails::create([
                            'transport_trans_id' => $transport_trans->id,
                            'invoice_id' => $transport_invoice->id,
                            'is_invoiced' => $is_invoiced,
                            'is_invoice_paid' => $is_invoice_paid,
                            'invoice_no' => trim($row['invoiceno']),
                            'invoice_paid_date' => $invoice_paid_date,
                            'invoice_pay_by_date' => $invoice_pay_by_date,
                            'invoice_date' => $invoice_date,
                            'invoice_amount' => $is_before_ded_23?0: $invoice_amount,
                            'invoice_amount_paid'=>$is_before_ded_23?0: $invoice_amount_paid,
                            'cost_price' => $cost_price,
                            'selling_price' => $selling_price,
                            'status_id' => $status_id,
                            'notes' => $notes
                        ]);


                        //TransportLoad

                        /* ['transport_trans_id','confirmed_by_id',
                             'product_id','packaging_incoming_id','packaging_outgoing_id','product_source_id','product_grade_perc','no_units_incoming'
                             ,'billing_units_incoming','no_units_outgoing','billing_units_outgoing','is_weighbridge_certificate_received'
                             ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id'];*/


                        $incoming_name = trim($row['packagingincoming']) != '' || trim($row['packagingincoming']) != null || trim($row['packagingincoming']) != 'NULL' ? trim($row['packagingincoming']) : "Unallocated";
                        $incoming_package = Packaging::where('name', $incoming_name)->first();
                        $packaging_incoming_id = $incoming_package != null ? $incoming_package->id : 1;

                        $outgoing_name = trim($row['packagingoutgoing']) != '' || trim($row['packagingoutgoing']) != null || trim($row['packagingoutgoing']) != 'NULL' ? trim($row['packagingoutgoing']) : "Unallocated";
                        $outgoing_package = Packaging::where('name', $outgoing_name)->first();
                        $packaging_outgoing_id = $outgoing_package != null ? $outgoing_package->id : 1;

                        $product_source_name = trim($row['productsource']) != '' || trim($row['productsource']) != null || trim($row['productsource']) != 'NULL' ? trim($row['productsource']) : "Unallocated";
                        $product_source = ProductSource::where('name', $product_source_name)->first();
                        $product_source_id = $product_source != null ? $product_source->id : 1;
                        $product_grade_perc = trim($row['productgradeperc']) != '' || trim($row['productgradeperc']) != null || trim($row['productgradeperc']) != 'NULL' ? trim($row['productgradeperc']) : null;

                        $no_units_incoming = is_numeric(trim($row['noofunitsincoming'])) ? trim($row['noofunitsincoming']) : 0;


                        $billing_units_incoming_id = match (trim($row['billingunitsincoming'])) {
                            "Tons" => 2,
                            "25KG Bags" => 3,
                            "30KG Bags" => 4,
                            "35KG Bags" => 5,
                            "40KG Bags" => 6,
                            default => 1,
                        };

                        $billing_units_outgoing_id = match (trim($row['billingunitsoutgoing'])) {
                            "Tons" => 2,
                            "25KG Bags" => 3,
                            "30KG Bags" => 4,
                            "35KG Bags" => 5,
                            "40KG Bags" => 6,
                            default => 1,
                        };

                        $no_units_outgoing = is_numeric(trim($row['noofunitsoutgoing'])) ? trim($row['noofunitsoutgoing']) : 0;
                        $is_weighbridge_certificate_received = !(trim($row['weighbridgecertificatereceived']) == 0);
                        $product_id = trim($row['productid']) != '' || trim($row['productid']) != null || trim($row['productid']) != 'NULL' ? trim($row['productid']) : 1;

                        $product_found = Product::where('old_id', $product_id)->first();
                        $product_found_id = $product_found != null ? $product_found->id : 1;


                        $transport_load = TransportLoad::create([
                            'transport_trans_id' => $transport_trans->id,
                            'confirmed_by_id' => 1,
                            'confirmed_by_type_id' => 1,
                            'product_id' => $product_found_id,
                            'packaging_incoming_id' => $packaging_incoming_id,
                            'packaging_outgoing_id' => $packaging_outgoing_id,
                            'product_source_id' => $product_source_id,
                            'product_grade_perc' => $product_grade_perc,
                            'no_units_incoming' => $no_units_incoming,
                            'billing_units_incoming_id' => $billing_units_incoming_id,
                            'no_units_outgoing' => $no_units_outgoing,
                            'billing_units_outgoing_id' => $billing_units_outgoing_id,
                            'is_weighbridge_certificate_received' => $is_weighbridge_certificate_received,
                            'delivery_note' => (trim($row['deliverynote'])),
                            'calculated_route_distance' => 0
                        ]);

                        /*  deliveryaddress1,
                          deliveryaddress2,
                          deliveryaddress3,
                          deliveryaddress4,*/


                        /*   'supplier_id'=>$found_supplier_id,
                               'customer_id'=>$found_customer_id,*/


                        $found_customer = Customer::where('id', $found_customer_id)->first();
                        $found_del_address = Address::where('address_type_id', 1)->where('poly_address_id', $found_customer->id)->where('poly_address_type', get_class($found_customer))->first();
                        if ($found_del_address === null) {
                            //Delivery
                            if (trim($row['deliveryaddress1']) != '' || trim($row['deliveryaddress1']) != 'as specified') {
                                $address = Address::create([
                                    'line_1' => str_replace(',', '', trim($row['deliveryaddress1'])),
                                    'line_2' => str_replace(',', '', trim($row['deliveryaddress2'])),
                                    'line_3' => str_replace(',', '', trim($row['deliveryaddress3'])),
                                    'code' => str_replace(',', '', trim($row['deliveryaddress4'])),
                                    'address_type_id' => 1,
                                    'poly_address_type' => get_class($found_customer),
                                    'poly_address_id' => $found_customer->id,
                                ]);

                                $transport_load->delivery_address_id = $address->id;
                            }
                        } else {

                            //'collection_address_id','delivery_address_id'
                            $transport_load->delivery_address_id = $found_del_address->id;
                        }


                        $found_supplier = Supplier::where('id', $found_supplier_id)->first();
                        $found_col_address = Address::where('address_type_id', 1)->where('poly_address_id', $found_supplier->id)->where('poly_address_type', get_class($found_supplier))->first();
                        if ($found_col_address === null) {
                            //Delivery
                            if (trim($row['collectionaddress1']) != '' || trim($row['collectionaddress1']) != 'as specified') {
                                $address = Address::create([
                                    'line_1' => str_replace(',', '', trim($row['collectionaddress1'])),
                                    'line_2' => str_replace(',', '', trim($row['collectionaddress2'])),
                                    'line_3' => str_replace(',', '', trim($row['collectionaddress3'])),
                                    'code' => str_replace(',', '', trim($row['collectionaddress4'])),
                                    'address_type_id' => 1,
                                    'poly_address_type' => get_class($found_supplier),
                                    'poly_address_id' => $found_supplier->id,
                                ]);

                                $transport_load->collection_address_id = $address->id;
                            }
                        } else {

                            //'','delivery_address_id'
                            $transport_load->collection_address_id = $found_col_address->id;
                        }


                        $transport_load->save();

                        /*   costpriceperunit,
                           sellingpriceperunit,
                           totalcostprice,
                           costpriceperton,
                           sellingpriceperton,
                           transportrate,
                           transportcostprice,
                           commsdueperton,
                           weightintonsincoming,
                           weightintonsoutgoing,
                           grossprofit,
                           grossprofitpercent,
                           grossprofitperton,
                            transportrateperton

                            additionalcost1,
                           additionalcost1desc,
                           additionalcost2,
                           additionalcost2desc,
                           additionalcost3,
                           additionalcost3desc,

                            total_supplier_comm,
                           total_customer_comm,
                           total_comm,

                        adjustedgp,
                       adjustedgpnotes



                        */


                        $selling_price_per_unit = is_numeric(trim($row['sellingpriceperunit'])) ? trim($row['sellingpriceperunit']) : 0;
                        $transport_rate_per_ton = is_numeric(trim($row['transportrateperton'])) ? trim($row['transportrateperton']) : 0;
                        $cost_price_per_unit = is_numeric(trim($row['costpriceperunit'])) ? trim($row['costpriceperunit']) : 0;


                        $cost_price_per_ton = is_numeric(trim($row['costpriceperton'])) ? trim($row['costpriceperton']) : 0;
                        $selling_price_per_ton = is_numeric(trim($row['sellingpriceperton'])) ? trim($row['sellingpriceperton']) : 0;

                        $transport_rate = is_numeric(trim($row['transportrate'])) ? trim($row['transportrate']) : 0;
                        $transport_price = is_numeric(trim($row['transportcostprice'])) ? trim($row['transportcostprice']) : 0;
                        $comms_due_per_ton = is_numeric(trim($row['commsdueperton'])) ? trim($row['commsdueperton']) : 0;
                        $weight_ton_incoming = is_numeric(trim($row['weightintonsincoming'])) ? trim($row['weightintonsincoming']) : 0;
                        $weight_ton_outgoing = is_numeric(trim($row['weightintonsoutgoing'])) ? trim($row['weightintonsoutgoing']) : 0;
                        $additional_cost_1 = is_numeric(trim($row['additionalcost1'])) ? trim($row['additionalcost1']) : 0;
                        $additional_cost_2 = is_numeric(trim($row['additionalcost2'])) ? trim($row['additionalcost2']) : 0;
                        $additional_cost_3 = is_numeric(trim($row['additionalcost3'])) ? trim($row['additionalcost3']) : 0;
                        $additional_cost_desc_1 = trim($row['additionalcost1desc']) != '' || trim($row['additionalcost1desc']) != null || trim($row['additionalcost1desc']) != 'NULL' ? null : trim($row['additionalcost1desc']);
                        $additional_cost_desc_2 = trim($row['additionalcost2desc']) != '' || trim($row['additionalcost2desc']) != null || trim($row['additionalcost2desc']) != 'NULL' ? null : trim($row['additionalcost2desc']);
                        $additional_cost_desc_3 = trim($row['additionalcost3desc']) != '' || trim($row['additionalcost3desc']) != null || trim($row['additionalcost3desc']) != 'NULL' ? null : trim($row['additionalcost3desc']);
                        $gross_profit = is_numeric(trim($row['grossprofit'])) ? trim($row['grossprofit']) : 0;
                        $gross_profit_percent = is_numeric(trim($row['grossprofitpercent'])) ? trim($row['grossprofitpercent']) : 0;
                        $gross_profit_per_ton = is_numeric(trim($row['grossprofitperton'])) ? trim($row['grossprofitperton']) : 0;
                        $total_supplier_comm = is_numeric(trim($row['total_supplier_comm'])) ? trim($row['total_supplier_comm']) : 0;
                        $total_customer_comm = is_numeric(trim($row['total_customer_comm'])) ? trim($row['total_customer_comm']) : 0;

                        //totalcostprice
                        $total_cost_price = is_numeric(trim($row['totalcostprice'])) ? trim($row['totalcostprice']) : 0;

                        $total_comm = is_numeric(trim($row['total_comm'])) ? trim($row['total_comm']) : 0;
                        $adjusted_gp = is_numeric(trim($row['adjustedgp'])) ? trim($row['adjustedgp']) : 0;
                        $adjusted_gp_notes = trim($row['adjustedgpnotes']) != '' || trim($row['adjustedgpnotes']) != null || trim($row['adjustedgpnotes']) != 'NULL' ? null : trim($row['adjustedgpnotes']);

                        $cost_price = is_numeric(trim($row['costprice'])) ? trim($row['costprice']) : 0;
                        $selling_price = is_numeric(trim($row['sellingprice'])) ? trim($row['sellingprice']) : 0;

                        $load_insurance_per_ton = is_numeric(trim($row['loadinsuranceperton'])) ? trim($row['loadinsuranceperton']) : 0;
                        $is_transport_costs_inc_price = trim($row['transportcostsincludedinprice']) == "Yes";


                        $transport_cost = ($transport_rate * $weight_ton_incoming);


                        //TransportFinance
                        $transport_finance = TransportFinance::create([
                            'transport_trans_id' => $transport_trans->id,
                            'transport_load_id' => $transport_load->id,
                            'transport_rate_basis_id' => $transport_rate_basis_id,
                            'cost_price_per_unit' => $cost_price_per_unit,
                            'cost_price_per_ton' => $cost_price_per_ton,
                            'total_cost_price' => $total_cost_price,
                            'cost_price' => $cost_price,
                            'selling_price' => $selling_price,
                            'selling_price_per_unit' => $selling_price_per_unit,
                            'selling_price_per_ton' => $selling_price_per_ton,
                            'is_transport_costs_inc_price' => $is_transport_costs_inc_price,
                            'transport_cost' => $transport_cost,
                            'transport_rate_per_ton' => $transport_rate_per_ton,
                            'transport_rate' => $transport_rate,
                            'transport_price' => $transport_price,
                            'load_insurance_per_ton' => $load_insurance_per_ton,
                            'comms_due_per_ton' => $comms_due_per_ton,
                            'weight_ton_incoming' => $weight_ton_incoming,
                            'weight_ton_outgoing' => $weight_ton_outgoing,
                            'additional_cost_1' => $additional_cost_1,
                            'additional_cost_2' => $additional_cost_2,
                            'additional_cost_3' => $additional_cost_3,
                            'additional_cost_desc_1' => $additional_cost_desc_1,
                            'additional_cost_desc_2' => $additional_cost_desc_2,
                            'additional_cost_desc_3' => $additional_cost_desc_3,
                            'gross_profit' => $gross_profit,
                            'gross_profit_percent' => $gross_profit_percent,
                            'gross_profit_per_ton' => $gross_profit_per_ton,
                            'total_supplier_comm' => $total_supplier_comm,
                            'total_customer_comm' => $total_customer_comm,
                            'total_comm' => $total_comm,
                            'adjusted_gp' => $adjusted_gp,
                            'adjusted_gp_notes' => $adjusted_gp_notes
                        ]);


                        //Deal ticket

                        // dealticket, dealticketprinted, ts_dealticketprinted,
                        //dealticket
                        //'transport_trans_id','old_id','trade_rule_id','trade_value','type','comment','is_active','is_printed','stamp_printed'

                        $deal_ticket = is_numeric(trim($row['dealticket'])) ? trim($row['dealticket']) : null;
                        $dealticketprinted = !(trim($row['dealticketprinted']) == 0);
                        $ts_dealticketprinted = trim($row['ts_dealticketprinted']) === '' || trim($row['ts_dealticketprinted']) === 'NULL' ? null : Carbon::createFromTimestamp($row['ts_dealticketprinted'])->toDateTimeString();

                        // $trade_rule = TradeRule::where('max_trade_value','>=',$cost_price)->where('min_trade_value','<=',$cost_price)->with('PolyRuleRoles')->first();

                        $deal_ticket = DealTicket::create([
                            'transport_trans_id' => $transport_trans->id,
                            'old_id' => $deal_ticket,
                            'is_printed' => $dealticketprinted,
                            'trade_value'=>$cost_price,
                            'stamp_printed' => $ts_dealticketprinted,
                            'is_active'=>false
                        ]);

                        //Trade Rule


                        //

                        //user comm
                        /* assigned_user_1_supplier,
                         assigned_user_1_supplier_comm,
                         assigned_user_2_supplier,
                         assigned_user_2_supplier_comm,
                         assigned_user_1_customer,
                         assigned_user_1_customer_comm,
                         assigned_user_2_customer,
                         assigned_user_2_customer_comm,
                         */

                        //user 1
                        if (true) {

                            $assigned_user_supplier_id = match (trim($row['assigned_user_1_supplier'])) {
                                "3" => 2,
                                "6" => 6,
                                "7" => 4,
                                "9" => 5,
                                "10" => 3,
                                default => 1,
                            };

                            $assigned_user_customer_id = match (trim($row['assigned_user_1_customer'])) {
                                "3" => 2,
                                "6" => 6,
                                "7" => 4,
                                "9" => 5,
                                "10" => 3,
                                default => 1,
                            };

                            $supplier_comm = is_numeric(trim($row['assigned_user_1_supplier_comm'])) ? trim($row['assigned_user_1_supplier_comm']) : 0;
                            $customer_comm = is_numeric(trim($row['assigned_user_1_customer_comm'])) ? trim($row['assigned_user_1_customer_comm']) : 0;

                            $assigned_user_comm_1 = AssignedUserComm::create([
                                'transport_trans_id' => $transport_trans->id,
                                'transport_finance_id' => $transport_finance->id,
                                'assigned_user_supplier_id' => $assigned_user_supplier_id,
                                'assigned_user_customer_id' => $assigned_user_customer_id,
                                'supplier_comm' => $supplier_comm,
                                'customer_comm' => $customer_comm,
                            ]);

                        }

                        //user 2
                        if (false) {

                            $assigned_user_supplier_id = match (trim($row['assigned_user_2_supplier'])) {
                                "3" => 2,
                                "6" => 6,
                                "7" => 4,
                                "9" => 5,
                                "10" => 3,
                                default => 1,
                            };

                            $assigned_user_customer_id = match (trim($row['assigned_user_2_customer'])) {
                                "3" => 2,
                                "6" => 6,
                                "7" => 4,
                                "9" => 5,
                                "10" => 3,
                                default => 1,
                            };

                            $supplier_comm = is_numeric(trim($row['assigned_user_2_supplier_comm'])) ? trim($row['assigned_user_2_supplier_comm']) : 0;
                            $customer_comm = is_numeric(trim($row['assigned_user_2_customer_comm'])) ? trim($row['assigned_user_2_customer_comm']) : 0;

                            $assigned_user_comm_2 = AssignedUserComm::create([
                                'transport_trans_id' => $transport_trans->id,
                                'transport_finance_id' => $transport_finance->id,
                                'assigned_user_supplier_id' => $assigned_user_supplier_id,
                                'assigned_user_customer_id' => $assigned_user_customer_id,
                                'supplier_comm' => $supplier_comm,
                                'customer_comm' => $customer_comm,
                            ]);

                        }

                        //transport Job


                        $customer_order_number = trim($row['customer_order_number']) != '' || trim($row['customer_order_number']) != null || trim($row['customer_order_number']) != 'NULL' ? null : trim($row['customer_order_number']);

                        $is_multi_loads = !(trim($row['multipleloads']) == 0);
                        $is_approved = !(trim($row['approved']) == 0);


                        //$date = Carbon::createFromFormat('m/d/Y', $myDate)->format('Y-m-d');
                        //2018-06-01
                        //Carbon::createFromFormat('yyyy-MM-dd',$row['ts_invoicepaybydate'])->toDateTimeString()


                        $transport_date_earliest = is_numeric(trim($row['ts_transportdateearliest'])) ? Carbon::createFromTimestamp($row['ts_transportdateearliest'])->toDateTimeString() : null;
                        $transport_date_latest = is_numeric(trim($row['ts_transportdatelatest'])) ? Carbon::createFromTimestamp($row['ts_transportdatelatest'])->toDateTimeString() : null;


                        $is_transport_costs_inc_price = trim($row['transportcostsincludedinprice']) == "Yes";
                        $is_product_zero_rated = trim($row['productzerorated']) == "Yes";
                        $total_load_insurance = is_numeric(trim($row['totalloadinsurance'])) ? trim($row['totalloadinsurance']) : 0;
                        $number_loads = is_numeric(trim($row['numberofloads'])) ? trim($row['numberofloads']) : 0;
                        $loading_instructions = trim($row['loadinginstructions']) != '' || trim($row['loadinginstructions']) != null || trim($row['loadinginstructions']) != 'NULL' ? null : trim($row['loadinginstructions']);
                        $offloading_instructions = trim($row['offloadinginstructions']) != '' || trim($row['offloadinginstructions']) != null || trim($row['offloadinginstructions']) != 'NULL' ? null : trim($row['offloadinginstructions']);

                        $transport_job = TransportJob::create([
                            'transport_trans_id' => $transport_trans->id,
                            'transport_rate_basis_id' => $transport_rate_basis_id,
                            'customer_order_number' => $customer_order_number,
                            'is_multi_loads' => $is_multi_loads,
                            'is_approved' => $is_approved,
                            'transport_date_earliest' => $transport_date_earliest,
                            'transport_date_latest' => $transport_date_latest,
                            'is_transport_costs_inc_price' => $is_transport_costs_inc_price,
                            'is_product_zero_rated' => $is_product_zero_rated,
                            'loading_hours_from_id' => 1,
                            'loading_hours_to_id' => 1,
                            'offloading_hours_from_id' => 1,
                            'offloading_hours_to_id' => 1,
                            'load_insurance_per_ton' => $load_insurance_per_ton,
                            'total_load_insurance' => $total_load_insurance,
                            'number_loads' => $number_loads,
                            'loading_instructions' => $loading_instructions,
                            'offloading_instructions' => $offloading_instructions,
                        ]);

                        //approvals


                        /*   approvaldesiree,
                           approvalhennie,
                           approvalpetro,
                           approvalandi,
                           approvalmarelize,
                           approvalallan, */

                        /*  if (trim($row['approvaldesiree']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 6
                              ]);
                          }

                          if (trim($row['approvalhennie']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 7
                              ]);
                          }

                          if (trim($row['approvalpetro']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 8
                              ]);
                          }

                          if (trim($row['approvalandi']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 9
                              ]);
                          }

                          if (trim($row['approvalmarelize']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 3
                              ]);
                          }

                          if (trim($row['approvalallan']) == "Yes") {
                              $transport_approval = TransportApproval::create([
                                  'transport_trans_id' => $transport_trans->id,
                                  'transport_job_id' => $transport_job->id,
                                  'user_id' => 2
                              ]);
                          }*/


                        //TransportDriverVehicle


                        $weighbridge_upload_weight = is_numeric(trim($row['weighbridgeuploadweight'])) ? trim($row['weighbridgeuploadweight']) : 0;
                        $weighbridge_offload_weight = is_numeric(trim($row['weighbridgeoffloadweight'])) ? trim($row['weighbridgeoffloadweight']) : 0;
                        $is_cancelled = trim($row['cancelled']) == 1;
                        $is_loaded = trim($row['loaded']) == 1;

                        $date_loaded = is_numeric(trim($row['ts_dateloaded'])) ? Carbon::createFromTimestamp($row['ts_dateloaded'])->toDateTimeString() : null;
                        $is_onroad = trim($row['onroad']) == 1;
                        $is_delivered = trim($row['delivered']) == 1;

                        $is_transport_scheduled = trim($row['transportscheduled']) == 1;
                        $is_paid = trim($row['paid']) == 1;
                        $is_payment_overdue = trim($row['paymentoverdue']) == 1;


                        /*  loaded,
                          dateloaded,
                          , */


                        $traders_notes = trim($row['tradersnotes']) != '' || trim($row['tradersnotes']) != null || trim($row['tradersnotes']) != 'NULL' ? null : trim($row['tradersnotes']);
                        $operations_alert_notes = trim($row['operations_alertnotes']) != '' || trim($row['operations_alertnotes']) != null || trim($row['operations_alertnotes']) != 'NULL' ? null : trim($row['operations_alertnotes']);

                        $transport_driver_vehicle = TransportDriverVehicle::create([
                            'transport_trans_id' => $transport_trans->id,
                            'transport_job_id' => $transport_job->id,
                            'regular_driver_id' => 1,
                            'regular_vehicle_id' => 1,
                            'weighbridge_upload_weight' => $weighbridge_upload_weight,
                            'weighbridge_offload_weight' => $weighbridge_offload_weight,
                            'is_weighbridge_variance' => false,
                            'is_cancelled' => $is_cancelled,
                            'date_cancelled' => null,
                            'is_loaded' => $is_loaded,
                            'date_loaded' => $date_loaded,
                            'is_onroad' => $is_onroad,
                            'date_onroad' => null,
                            'is_delivered' => $is_delivered,
                            'date_delivered' => null,
                            'is_transport_scheduled' => $is_transport_scheduled,
                            'date_scheduled' => null,
                            'is_paid' => $is_paid,
                            'date_paid' => null,
                            'is_payment_overdue' => $is_payment_overdue,
                            'traders_notes' => $traders_notes,
                            'operations_alert_notes' => $operations_alert_notes

                        ]);

                        //TransportStatus


                        /*,
                        ,

                        ,

                        */

                        //calculate margins
                        $transport_finance->calculateFields();


                        if (trim($row['transport_delayed']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 2
                            ]);
                        }


                        if (trim($row['transport_breakdown']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 3
                            ]);
                        }

                        if (trim($row['transport_cancelled']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 4
                            ]);
                        }

                        if (trim($row['transport_loadslipped']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 5
                            ]);
                        }

                        if (trim($row['transport_overweight_control']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 7
                            ]);
                        }

                        if (trim($row['transport_driver']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 2,
                                'status_type_id' => 6
                            ]);
                        }


                        if (trim($row['mill_slow']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 3,
                                'status_type_id' => 8
                            ]);
                        }

                        if (trim($row['mill_breakdown']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 3,
                                'status_type_id' => 3
                            ]);
                        }


                        if (trim($row['mill_stopped_demandside']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 3,
                                'status_type_id' => 9
                            ]);
                        }


                        if (trim($row['quality_wet']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 10
                            ]);
                        }

                        if (trim($row['quality_moisture_content']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 10
                            ]);
                        }


                        if (trim($row['quality_moisture_content']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 11
                            ]);
                        }

                        if (trim($row['quality_contamination']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 12
                            ]);
                        }

                        if (trim($row['quality_grade_specification']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 13
                            ]);
                        }


                        if (trim($row['quality_visual']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 5,
                                'status_type_id' => 14
                            ]);
                        }

                        if (trim($row['general_variance_detected']) == 1) {
                            $transport_status
                                = TransportStatus::create([
                                'transport_trans_id' => $transport_trans->id,
                                'status_entity_id' => 6,
                                'status_type_id' => 15
                            ]);
                        }


                    }




                }


            }


        }
    }


}
