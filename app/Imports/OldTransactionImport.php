<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Customer;
use App\Models\OldTransaction;
use App\Models\Supplier;
use App\Models\Transporter;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OldTransactionImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {

        //id	ts_datecreated	contracttype	contractno	linkedpc	linkedsc	dealticket	dealticketprinted	ts_dealticketprinted	createdby	supplierid	customerid	transporterid	noofunitsincoming	billingunitsincoming	packagingincoming	productid	productsource	productgradeperc	noofunitsoutgoing	billingunitsoutgoing	packagingoutgoing	costpriceperunit	sellingpriceperunit	totalcostprice	costpriceperton	sellingpriceperton	transportrate	transportcostprice	commsdueperton	weightintonsincoming	weightintonsoutgoing	grossprofit	grossprofitpercent	grossprofitperton	purchaseorder	purchaseorderconfirmedby	salesconfirmationsent	salesconfirmationreceived	purchaseordersent	purchaseorderreceived	transportordersent	transportorderreceived	transportorderconfirmedby	transportstatus	weighbridgecertificatereceived	deliverynote	invoiced	invoiceno	ts_invoicedate	invoicepaid	ts_invoicepaiddate	ts_invoicepaybydate	invoiceamount	invoicedate	invoicepaiddate	invoicepaybydate	invoiceamountpaid	invoicebalance	costprice	sellingprice	notes	status	suppliernotes	productnotes	customernotes	transportnotes	pricingnotes	processnotes	documentsnotes	transactionnotes	driverid	vehicleid	multipleloads	ts_transportdateearliest	ts_transportdatelatest	transportratebasis	transportdateearliest	transportdatelatest	approvaldesiree	approvalhennie	approvalpetro	approvalandi	approvalmarelize	approvalallan	approved	transactiondone	additionalcost1	additionalcost1desc	additionalcost2	additionalcost2desc	additionalcost3	additionalcost3desc	uploadweighbridgecertificate	uploadsupplierinvoice	uploadtransporterinvoice	confirmsalesconfirmationby	transportrateperton	weighbridgeuploadweight	weighbridgeoffloadweight	collectionaddress1	collectionaddress2	collectionaddress3	collectionaddress4	deliveryaddress1	deliveryaddress2	deliveryaddress3	deliveryaddress4	drivername	drivercellno	vehicletype	vehicleregistrationno	loadinghoursfrom	loadinghoursto	offloadinghoursfrom	offloadinghoursto	loadinsuranceperton	totalloadinsurance	numberofloads	loadinginstructions	offloadinginstructions	transportcostsincludedinprice	productzerorated	loadingcontact	loadingcontacttelno	offloadingcontact	offloadingcontacttelno	tradersnotessupply	tradersnotescustomer	tradersnotestransport	loaded	dateloaded	ts_dateloaded	onroad	paid	paymentoverdue	transportlogid	delivered	tradersnotes	operations_alertnotes	vehicleregistrationno1	vehicleregistrationno2	vehicleregistrationno3	statuses	weighbridgevariance	cancelled	transport_delayed	transport_breakdown	transport_cancelled	transport_loadslipped	transport_overweight_control	transport_driver	mill_slow	mill_breakdown	mill_stopped_demandside	quality_wet	quality_moisture_content	quality_contamination	quality_grade_specification	quality_visual	general_variance_detected	include_in_calculations	transportrouteid	assigned_user_1_supplier	assigned_user_1_supplier_comm	assigned_user_2_supplier	assigned_user_2_supplier_comm	assigned_user_1_customer	assigned_user_1_customer_comm	assigned_user_2_customer	assigned_user_2_customer_comm	total_supplier_comm	total_customer_comm	total_comm	transportscheduled	adjustedgp	adjustedgpnotes	customer_order_number	exclude_from_calculations	not_scheduled


        //['old_id','contract_type','contract_no', 'linked_pc','deal_ticket','is_deal_ticket_printed',
        //        'is_vat_exempt','created_by','customer_id', 'supplier_id','transporter_id','no_units_incoming','billing_units_incoming','packaging_incoming',
        //         'product_id', 'product_source','no_units_outgoing','billing_units_outgoing','packaging_outgoing', 'cost_price_per_unit',
        //        'selling_price_per_unit','total_cost_price', 'total_cost_price_per_ton', 'total_cost_price', 'selling_price_per_ton',
        //        'transport_rate','transport_cost_price','comms_due_per_ton','weight_in_tons_incoming', 'weight_in_tons_outgoing', 'gross_profit',
        //        'gross_profit_perc', 'gross_profit_per_ton','purchase_order_confirmed_by','packaging_outgoing','sales_confirmation_sent',
        //        'sales_confirmation_received','weight_bridge_certificate_received','transport_order_received','invoice_paid',
        //        'invoice_amount','invoice_amount_paid','cost_price','selling_price',
        //        'supplier_notes','product_notes','customer_notes','transport_notes','pricing_notes','process_notes','transaction_notes',
        //        'approved','transaction_done','total_load_insurance','load_instructions','offload_instructions','transport_included_in_price',
        //        'loaded','on_road','paid','payment_overdue','delivered','traders_notes','weight_bridge_variance',
        //        'cancelled','transport_delayed','transport_breakdown','transport_cancelled','transport_load_slipped',
        //        'transport_weight_control','transport_driver','mill_slow','mill_breakdown','mill_stopped_demand_side',
        //        'quality_wet','quality_moisture_content','quality_contamination','quality_grade_specification',
        //        'quality_visual','general_variance_detected','include_in_calculations',
        //    ];

        foreach ($rows as $row)
        {
            if ($row != null){

                if ($row['id'] != ''){



                    $timestamp = $row['ts_datecreated'];
                    $orig_date=  Carbon::createFromTimestamp($timestamp)->toDateTimeString();

                    $customerid = trim($row['customerid']);

                    if($customerid === "" || $customerid === 0 || $customerid === "0"){
                        $customerid = 1;
                    }


                    $supplierid = trim($row['supplierid']);

                    if($supplierid === "" || $supplierid === 0 || $supplierid === "0"){
                        $supplierid = 1;
                    }

                    $transporterid = trim($row['transporterid']);

                    if($transporterid === "" || $transporterid === 0 || $transporterid === "0"){
                        $transporterid = 1;
                    }

                    $is_customer =  Customer::where('id', $customerid)->exists();
                    $is_supplier =   Supplier::where('id',$supplierid)->exists();
                    $is_transporter =  Transporter::where('id',$transporterid)->exists();

                    $old_transaction = OldTransaction::create([
                        'old_id'=>trim($row['id']),
                        'contract_type'=>trim($row['contracttype']),
                        'contract_no'=>trim($row['contractno']),
                        'linked_pc'=>trim($row['linkedpc']),
                        'deal_ticket'=>trim($row['dealticket']),
                        'is_deal_ticket_printed'=> !($row['dealticket'] == '' || $row['dealticket'] == 0),
                        'created_by'=> trim($row['createdby']),
                        'customer_id'=>$is_customer? $customerid: 1,
                        'supplier_id'=>$is_supplier? $supplierid: 1,
                        'transporter_id'=>$is_transporter? $transporterid : 1,
                        'no_units_incoming'=>trim($row['noofunitsincoming']),
                        'billing_units_incoming'=>trim($row['billingunitsincoming']),
                        'packaging_incoming'=>trim($row['packagingincoming']),
                        'product_id'=>trim($row['productid']),
                        'product_source'=>trim($row['productsource']),
                        'no_units_outgoing'=>$row['noofunitsoutgoing'],
                        'billing_units_outgoing'=>$row['billingunitsoutgoing'],
                        'packaging_outgoing'=>$row['packagingoutgoing'],
                        'cost_price_per_unit'=>$row['costpriceperunit'],
                        'selling_price_per_unit'=>$row['sellingpriceperunit'],
                        'total_cost_price'=>$row['totalcostprice'],
                        'total_cost_price_per_ton'=>$row['costpriceperton'],
                        'selling_price_per_ton'=>$row['sellingpriceperton'],
                        'transport_rate'=>$row['transportrate'],
                        'transport_cost_price'=>$row['transportcostprice'],
                        'comms_due_per_ton'=>$row['commsdueperton'],
                        'weight_in_tons_incoming'=>$row['weightintonsincoming'],
                        'weight_in_tons_outgoing'=>$row['weightintonsoutgoing'],
                        'gross_profit'=>$row['grossprofit'],


                        'gross_profit_perc'=>trim($row['grossprofitpercent']),
                        'gross_profit_per_ton'=>$row['grossprofitperton'],
                        'purchase_order_confirmed_by'=>trim($row['purchaseorderconfirmedby']),
                        'sales_confirmation_sent'=>!($row['dealticket'] == '' || $row['dealticket'] == 0),
                        'sales_confirmation_received'=>!($row['salesconfirmationreceived'] == '' || $row['salesconfirmationreceived'] == 0),
                        'weight_bridge_certificate_received'=>!($row['weighbridgecertificatereceived'] == '' || $row['weighbridgecertificatereceived'] == 0),
                        'transport_order_received'=>!($row['transportorderreceived'] == '' || $row['transportorderreceived'] == 0),
                        'invoice_paid'=>!($row['invoicepaid'] == '' || $row['invoicepaid'] == 0),


                        'invoice_amount'=>$row['invoiceamount'] == ""? 0: $row['invoiceamount'],
                        'invoice_amount_paid'=>$row['invoiceamountpaid'] == "" ? 0: $row['invoiceamountpaid'],
                        'cost_price'=>$row['costprice'] == ""? 0:$row['costprice'],
                        'selling_price'=>$row['sellingprice'] == ""? 0: $row['sellingprice'],
                        'supplier_notes'=>trim($row['suppliernotes']),
                        'product_notes'=>trim($row['productnotes']),
                        'customer_notes'=>trim($row['customernotes']),
                        'transport_notes'=>trim($row['transportnotes']),
                        'pricing_notes'=>trim($row['pricingnotes']),
                        'process_notes'=>trim($row['processnotes']),
                        'transaction_notes'=>trim($row['transactionnotes']),
                        'approved'=>!($row['approved'] == '' || $row['approved'] == 0),

                        'transaction_done'=>!($row['transactiondone'] == '' || $row['transactiondone'] == 0),
                        'total_load_insurance'=>trim($row['loadinsuranceperton']),
                        'load_instructions'=>trim($row['loadinginstructions']),
                        'offload_instructions'=>trim($row['offloadinginstructions']),

                        'transport_included_in_price'=> $row['transportcostsincludedinprice'] == "Yes",
                        'loaded'=>!($row['loaded'] == '' || $row['loaded'] == 0),
                        'on_road'=>!($row['onroad'] == '' || $row['onroad'] == 0),
                        'paid'=>!($row['paid'] == '' || $row['paid'] == 0),
                        'payment_overdue'=>!($row['paymentoverdue'] == '' || $row['paymentoverdue'] == 0),
                        'delivered'=>!($row['delivered'] == '' || $row['delivered'] == 0),
                        'traders_notes'=>trim($row['tradersnotestransport']),
                        'weight_bridge_variance'=>$row['weighbridgevariance'] == ""?0:$row['weighbridgevariance'],
                        'cancelled'=>!($row['cancelled'] == '' || $row['cancelled'] == 0),
                        'transport_delayed'=>!($row['transport_delayed'] == '' || $row['transport_delayed'] == 0),
                        'transport_breakdown'=>!($row['transport_breakdown'] == '' || $row['transport_breakdown'] == 0),
                        'transport_cancelled'=>!($row['transport_cancelled'] == '' || $row['transport_cancelled'] == 0),
                        'transport_load_slipped'=>!($row['transport_loadslipped'] == '' || $row['transport_loadslipped'] == 0),
                        'transport_weight_control'=>!($row['transport_overweight_control'] == '' || $row['transport_overweight_control'] == 0),
                        'transport_driver'=>!($row['transport_driver'] == '' || $row['transport_driver'] == 0),
                        'mill_slow'=>!($row['mill_slow'] == '' || $row['mill_slow'] == 0),
                        'mill_breakdown'=>!($row['mill_breakdown'] == '' || $row['mill_breakdown'] == 0),
                        'mill_stopped_demand_side'=>!($row['mill_stopped_demandside'] == '' || $row['mill_stopped_demandside'] == 0),
                        'quality_wet'=>!($row['quality_wet'] == '' || $row['quality_wet'] == 0),
                        'quality_moisture_content'=>!($row['quality_moisture_content'] == '' || $row['quality_moisture_content'] == 0),
                        'quality_contamination'=>!($row['quality_contamination'] == '' || $row['quality_contamination'] == 0),
                        'quality_grade_specification'=>!($row['quality_grade_specification'] == '' || $row['quality_grade_specification'] == 0),
                        'quality_visual'=>!($row['quality_visual'] == '' || $row['quality_visual'] == 0),
                        'general_variance_detected'=>!($row['general_variance_detected'] == '' || $row['general_variance_detected'] == 0),
                        'include_in_calculations'=>!($row['include_in_calculations'] == '' || $row['include_in_calculations'] == 0),
                        'original_date'=>$orig_date
                    ]);





                }


            }


        }
    }
}
