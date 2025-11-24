<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Sales Order Confirmation</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px; /* Adjusted bottom margin for footer */
            font-family: sans-serif;
        }

        body {
            margin-top: 5px;
            padding: 5px;
            margin-right: 10px;

        }

        header {
            position: fixed;
            top: -85px;
            left: 0px;
            right: 0px;
            height: 200px;
            line-height: 11px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 10px;
            color: #0f0f0f;
            line-height: 25px;
            text-align: center;
        }

        .page_num:before {
            content: counter(page);
        }

        .table_heading {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
            padding-bottom: 3px;
        }

        .table_row_heading {
            font-size: 10px;
            font-weight: bold;
        }

        .table_row_value {
            font-size: 10px;
        }

        /* table, th, td {
             border: 0px solid black;
             border-collapse: collapse;
         }

         .table_sections {
             border: 1px solid black;
             border-collapse: collapse;
         }*/

        table, th, td {
            border: none; /* Remove border from all tables, table headers, and table data cells */
            border-collapse: collapse;
        }

        .table_sections {
            border: none; /* Remove the border around each table section */
        }

        th, td {
            padding: 1px;
        }

        .section_heading {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 1px;
            padding-bottom: 0px;
            margin-top: 1px;
        }

        .page-break {
            page-break-after: always;
        }

        ol, ul {
            margin: 0;
            padding: 0;
            list-style-position: inside;
        }


        hr {
            margin-top: 1px;
            margin-bottom: 1px
        }


    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    <table style="width:100%;">
        <tr>
            <td>
                <img style="float: left;" src="{{ $logo }}" width="202" height="95" alt="Company Logo" />
            </td>
            <td style="float: right; text-align: right; font-size: 10px; padding-top: 7px;">
                <span>
                    @if(isset($pdfSettings))
                        @if($pdfSettings->po_box)
                            {{ $pdfSettings->po_box }}<br>
                        @endif
                        @if($pdfSettings->street_address)
                            {{ $pdfSettings->street_address }}<br>
                        @endif
                        @if($pdfSettings->city)
                            {{ $pdfSettings->city }}@if($pdfSettings->postal_code)
                                , {{ $pdfSettings->postal_code }}
                            @endif<br>
                        @endif
                        @if($pdfSettings->phone)
                            {{ $pdfSettings->phone }}<br>
                        @endif
                        @if($pdfSettings->fax)
                            {{ $pdfSettings->fax }}<br>
                        @endif
                        @if($pdfSettings->email)
                            Email : <a>{{ $pdfSettings->email }}</a>
                        @endif
                    @else
                        P.O. Box 71658, Rink Street<br>Port Elizabeth, 6001<br>Tel : +27 82 897 5966<br>+27 41 582 1952
                        <br>Email : <a>documents@silvergro.co.za</a>
                    @endif
                </span><br><br>
            </td>
        </tr>

    </table>
</header>


<!-- Wrap the content of your PDF inside a main tag -->
<main>
    <div style="">

        <div style="margin-top: 5px;">
            <table style="width:100%">
                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px;">
                        <b><span>Sales Order Confirmation:</span> <span>MQ</span>
                            <span>{{$transport_trans->a_mq}}</span>
                        </b>
                    </td>

                </tr>

                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px">
                        @if($final_sales_order)
                            <span>Final Version</span>

                        @else
                            <span style="color:red">Working Document Version</span>
                        @endif

                    </td>
                </tr>

            </table>

            <ol type="1">
                <li class="section_heading">Customer Details</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Customer</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->Customer->last_legal_name}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Business Address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Customer->addressablePhysical == "[]")
                                    <span>No physical address loaded...</span>
                                @else
                                    <span>{{$transport_trans->Customer->addressablePhysical[0]->line_1}}</span>
                                    @if($transport_trans->Customer->addressablePhysical[0]->line_2)
                                        ,
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->line_2}}</span>
                                    @endif
                                    @if($transport_trans->Customer->addressablePhysical[0]->line_3)
                                        ,
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->line_3}}</span>
                                    @endif
                                    @if($transport_trans->Customer->addressablePhysical[0]->country)
                                        ,
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->country}}</span>
                                    @endif
                                    @if($transport_trans->Customer->addressablePhysical[0]->code)
                                        ,
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->code}}</span>
                                    @endif
                                @endif

                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Att</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Customer->contactable=="[]")
                                    <span>No contact loaded</span>
                                @else
                                    @foreach($transport_trans->Customer->contactable as $contact)
                                        <div>
                                            <span>{{$contact->first_name}}</span>
                                            <span>{{$contact->last_legal_name}}</span>

                                            @if($contact->numberable=="[]")
                                                <span>T: none loaded</span>
                                            @else
                                                <span>T:</span>
                                                @foreach($contact->numberable as $phone)
                                                    <span>{{$phone->value}}</span>
                                                @endforeach
                                            @endif

                                            @if($contact->emailable=="[]")
                                                <span>E: none loaded</span>
                                            @else
                                                <span>E:</span>
                                                @foreach($contact->emailable as $email)
                                                    <span>{{$email->value}}</span>
                                                @endforeach
                                            @endif

                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        </tbody>

                    </table>

                    <p class="table_row_value">
                        Herewith our confirmation of the <strong>SALES ORDER</strong> for the following product,
                        including the specific terms and
                        conditions of this order. This document is a confirmation of our telephone order.
                    </p>
                </div>
                <li class="section_heading">
                    Product and Payment Details
                </li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;">{{$transport_trans->product->name}}</td>
                            <td class="table_sections table_row_heading">Grade</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->product_grade_perc}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Billing units</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->BillingUnitsOutgoing->name}}</td>

                            <td class="table_sections table_row_heading" style="width: 20%;">Packaging incoming</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Units incoming</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->no_units_outgoing}}</td>

                            <td class="table_sections table_row_heading" style="width: 20%;"></td>
                            <td class="table_sections table_row_value"></td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Cost per unit</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}
                            </td>

                            <td class="table_sections table_row_heading" style="width: 20%;">Tons</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportFinance->weight_ton_outgoing}}
                            </td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Total Cost</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->cost_price,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading" style="width: 20%;">Cost Price / Metric Ton
                            </td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">VAT (Zero rated)</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->TransportJob->is_product_zero_rated)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td class="table_sections table_row_heading">Vat (Exempt)</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->Customer->is_vat_exempt)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>


                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Payment Terms</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->Customer->TermsOfPayment->value}}
                            </td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Collection and Transport</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Collection address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->line_1}}</span>

                                @if($transport_trans->TransportLoad->CollectionAddress->line_2)
                                    ,
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->line_2}}</span>
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->line_3)
                                    ,
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->line_3}}</span>
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->country)
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->country}}</span>
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->code)
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->code}}</span>
                                @endif


                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Customer order number</td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->TransportJob->customer_order_number}}
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Customer offloading
                                number
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}
                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Supplier loading no</td>
                            <td class="table_sections table_row_value" colspan="3">
                                {{$transport_trans->TransportJob->supplier_loading_number}}
                            </td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 50%;">Vehicle Registration</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 50%;">Trailer #1</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Trailer #2</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 50%;">Driver Name</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 50%;">Driver cell</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}} </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Date Earliest
                            </td>
                            <td class="table_sections table_row_value"
                                style="width: 25%; white-space: nowrap;">{{ $transport_trans->transport_date_earliest ? $transport_trans->transport_date_earliest->format('D d/M/Y') : 'No date Selected' }}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Date Latest</td>
                            <td class="table_sections table_row_value"
                                style="white-space: nowrap;">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;"></td>
                            <td class="table_sections table_row_value"></td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Offloading time from</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->OffloadingHoursFrom->name}}
                                HRS
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Offloading time to</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->OffloadingHoursTo->name}}
                                HRS
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;"></td>
                            <td class="table_sections table_row_value"></td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">No loads</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->number_loads}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;"></td>
                            <td class="table_sections table_row_value"></td>

                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Customer Notes</li>
                <div class="table_row_value">
                    {!!nl2br($transport_trans->customer_notes)!!}
                </div>

                <br>

                <div class="table_row_value">
                    The buyer accepts the conditions as set out in this "SALES ORDER", unless changes are presented in
                    writing within 24 hours after receiving this document, for acceptance by the buyer. Please sign this
                    document and email a scanned copy to documents@silvergro.co.za. If the seller does not sign this
                    document and return it as per the above, the transaction will still be considered as legal and
                    binding. In the event the buyer does dispute, in the event of immediate direct resolution efforts
                    failing, an independent professional will be appointed by the seller at the expense of the default
                    party. This transaction is subject to the terms, conditions and rules, including the arbitration
                    clause and rules in contract form known as Sagos 1 (Current Version) with which the parties are
                    fully familiar with and which will be applicable to the extent in which it will not be contradictory
                    to what the parties agreed upon. FORCE MAJEURE: To be applied as per the SAGOS 1 (Version 09),
                    section 11. We thank you for the opportunity to do business with you.
                </div>


                @if(str_contains(strtolower($transport_trans->product->name), 'lucerne'))
                    <br>
                    <div class="table_row_value">
                        Ensure load is fully covered with tarps and protected from rain.
                    </div>
                @endif

                @if(str_contains(strtolower($transport_trans->product->name), 'bagged'))
                    <br>
                    <div class="table_row_value">
                        Supplier to check for broken or wet bags and remove from loading.
                    </div>
                    <div class="table_row_value">
                        If any bags are broken or goods defective, kindly contact Silvergro Feed & Grain
                        immediately.
                    </div>
                    <div class="table_row_value">
                        Driver and Customer to do a bag count and sign for goods on the sales and transporter's
                        delivery documentation.
                    </div>
            @endif

        </div>
        <br>
        <br>
        <p class="section_heading">Prepared for Silvergro Feed & Grain by {{$user_name}} at {{$now}}
            <span></span>
        </p>
        <div>
            <table class="" style="width:100%;">
                <tbody>
                <tr class="">
                    <td class=" table_row_value" style="width:25%;">
                        <br>
                        <br>
                        <br>
                        <hr>
                    </td>
                    <td class=" table_row_value" style="width:25%;">
                        <br>
                        <br>
                        <br>

                    </td>
                    <td class=" table_row_value" style="width:25%;">
                        <br>
                        <br>
                        <br>

                    </td>
                    <td class=" table_row_value" style="width:25%;">
                        <br>
                        <br>
                        <br>
                        <hr>
                    </td>
                </tr>
                <tr style="margin-top: 4px;">
                    <td class=" table_row_heading">Transporter Signature</td>
                    <td class=" table_row_heading"></td>
                    <td class=" table_row_heading"></td>
                    <td class=" table_row_heading">Transporter Name</td>
                </tr>

                </tbody>

            </table>
        </div>

        </ol>


    </div>

    </div>
</main>


<footer>
    <script type="text/php">
        // Define default values if not set
        if (!isset($user_name)) {
            $user_name = 'Unknown User';
        }

        if (!isset($app_version)) {
            $app_version = '1.0';
        }

        if (isset($pdf) && $PAGE_NUM > 1) {
            // Set up footer text
            $left_text = "SilverGro Feed & Grain | Generated by {$user_name} | version {$app_version}";
            $page_text = "Page {$PAGE_NUM} of {$PAGE_COUNT}";

            // Set font properties
            $font = $fontMetrics->getFont("sans-serif");
            $size = 8;

            // Positioning
            $y = $pdf->get_height() - 45;
            $pdf->text(50, $y, $left_text, $font, $size);
            $pdf->text($pdf->get_width() - 100, $y, $page_text, $font, $size);
        }
    </script>
</footer>


</body>
</html>
