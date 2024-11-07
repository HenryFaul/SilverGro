<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Transport Order Confirmation</title>
    <style>
        @page {
            margin: 100px 40px;
            font-family: sans-serif;
        }

        body {

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
            height: 30px;
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
            font-size: 12px;
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

        /* Add this CSS to your stylesheet */
        .indented-list {
            list-style-type: decimal;
            padding-left: 30px;
        }

        .indented-list li {
            margin: 0;
            padding-left: 5px;
            text-indent: -20px;
        }

        .indented-list li::marker {
            font-weight: bold;
        }


    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    <table style="width:100%;">
        <tr>
            <td>
                <img style="float: left;" src="{{ $logo }}" width="225" height="105" />
            </td>
            <td style="float: right; text-align: right; font-size: 10px; padding-top: 7px;"><span>P.O. Box 71658, Rink Street<br>Port Elizabeth, 6001<br>Tel : +27 82 897 5966<br>+27 41 582 1952<br>Email : <a>documents@silvergro.co.za</a></span><br><br>
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
                        <b><span>Transport Order Confirmation:</span>
                            <span>{{$transport_trans->a_mq}}</span>
                        </b>
                    </td>

                </tr>

                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px">
                        @if($final_transport_order)
                            <span>Final Version</span>

                        @else
                            <span style="color:red">Working Document Version</span>
                        @endif

                    </td>
                </tr>

            </table>

            <ol type="1">
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:25%;">Transporter</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
                        </tr>
                       <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:25%;">Business address</td>
                            <td class="table_sections table_row_value" colspan="3">
                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Att</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Transporter->contactable=="[]")
                                    <span>No contact loaded</span>
                                @else
                                    @foreach($transport_trans->Transporter->contactable as $contact)
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

                </div>
                    <br>
                <li class="section_heading">Transport Details</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Date Earliest
                            </td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_earliest->format('D d/M/Y')}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Date Latest</td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">No. Of Loads</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->number_loads}}</td>
                            <td class="table_sections table_row_heading">Vehicle Type</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->VehicleType->name}}</td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Vehicle Reg</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                            <td class="table_sections table_row_heading">Trailer #1 Reg.</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1)
                                    <span>#1 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</span>
                                @endif

                            </td>
                            <td class="table_sections table_row_heading">Trailer #2 Reg.</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2)
                                    <span>#2 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</span>
                                @endif
                            </td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Driver</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}
                            </td>
                            <td class="table_sections table_row_heading">Driver ID:</td>
                            <td class="table_sections table_row_value">
                                <span> {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->comment}}</span>
                            </td>

                            <td class="table_sections table_row_heading">Driver Cell:</td>
                            <td class="table_sections table_row_value">
                                <span> {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}}</span>
                            </td>

                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Product & Tariff Detail</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                            <td class="table_sections table_row_value">{{$transport_trans->product->name}}</td>
                            <td class="table_sections table_row_heading">Weight planned (tons)</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Package Incoming</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportLoad->PackagingIncoming->name}}
                            </td>
                            <td class="table_sections table_row_heading">Package Outgoing</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Rate Basis</td>
                            <td class="table_sections table_row_value" style="background-color: #62FD473F">
                                {{$transport_trans->TransportFinance->TransportRateBasis->name}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Rate / Ton</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Rate per load</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Load Insurance / Ton</td>
                            <td  class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}}
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Terms of payment</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->Transporter->TermsOfPayment->value}}
                            </td>
                        </tr>

                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Loading Detail</li>
                <div class="">
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier name
                            </td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;">{{$transport_trans->Supplier->last_legal_name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Collection Address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                {{$transport_trans->TransportLoad->CollectionAddress->line_1}}
                                @if($transport_trans->TransportLoad->CollectionAddress->line_2)
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_2}}
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->line_3)
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_3}}
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->country)
                                    {{$transport_trans->TransportLoad->CollectionAddress->country}}
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->code)
                                    {{$transport_trans->TransportLoad->CollectionAddress->code}}
                                @endif

                            </td>
                        </tr>

                        <tr class="table_sections">

                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier loading number
                            </td>

                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->supplier_loading_number}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Loading hours from</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->LoadingHoursFrom->name}} HRS
                            </td>

                            <td class="table_sections table_row_heading" style="width: 25%;">Loading hours to</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->LoadingHoursTo->name}} HRS
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier contact:
                            </td>
                            <td class="table_sections table_row_value" colspan="3">
                                <span>{{$transport_trans->TransportJob->loading_contact}} {{$transport_trans->TransportJob->loading_contact_no}}</span>
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Loading instructions
                            </td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->loading_instructions}}</td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Offloading Detail</li>

                <div class="">
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Customer name
                            </td>
                        </tr>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Delivery Address</td>
                            <td class="table_sections table_row_value" colspan="3">
                               {{$transport_trans->TransportLoad->DeliveryAddress->line_1}}

                                @if($transport_trans->TransportLoad->DeliveryAddress->line_2)
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_2}}
                                @endif

                                @if($transport_trans->TransportLoad->DeliveryAddress->line_3)
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_3}}
                                @endif

                                @if($transport_trans->TransportLoad->DeliveryAddress->country)
                               {{$transport_trans->TransportLoad->DeliveryAddress->country}}
                                @endif

                                @if($transport_trans->TransportLoad->DeliveryAddress->code)
                                  {{$transport_trans->TransportLoad->DeliveryAddress->code}}
                                @endif

                            </td>
                        </tr>

                        <tr class="table_sections">

                            <td class="table_sections table_row_heading" style="width: 25%;">Customer loading number
                            </td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->customer_order_number}}
                            </td>

                            <td class="table_sections table_row_heading" style="width: 25%;">Customer offloading number
                            </td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_numbe}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Offloading hours from</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->OffloadingHoursFrom->name}} HRS
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Offloading hours from</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->OffloadingHoursTo->name}} HRS
                            </td>
                        </tr>



                        <tr class="table_sections">

                            <td class="table_sections table_row_heading" style="width: 25%;">Customer contact:
                            </td>
                            <td class="table_sections table_row_value" colspan="3">
                                <span>{{$transport_trans->TransportJob->offloading_contact}} {{$transport_trans->TransportJob->offloading_contact_no}}</span>
                            </td>
                        </tr>

                        <tr class="table_sections">

                            <td class="table_sections table_row_heading" style="width: 25%;">Offloading instructions
                            </td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->offloading_instructions}}</td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                    <br>

                    <li class="section_heading">Standard Terms and Conditions</li>
                <div class="table_row_value">

                    <div class="table_row_value">
                        <div class="table_row_value">
                            <ol class="indented-list">
                                <li>
                                    <strong>Transport Rate:</strong> The rate is valid for the completion of this
                                    transport order on the tonnage
                                    agreed upon.
                                </li>

                                <li>
                                    <strong>Payment Terms:</strong> Payment will be made on receiving the transporter's
                                    invoice with original
                                    documentation, normally within 14 working days from the date of receipt of the
                                    original invoice(s), and
                                    based on upload weight.
                                </li>

                                <li>
                                    <strong>Original Documentation:</strong> Payment will only be made upon receiving
                                    the transporter’s PODs,
                                    the original invoice with all original loading documentation, as well as original
                                    offloading documentation.
                                </li>

                                <li>
                                    <strong>Load Insurance:</strong> The transporter confirms that the value of the
                                    load(s), calculated at the
                                    selling value of the commodity, that is <strong>R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}}</strong>
                                    per mt, is fully insured by the Transport Company / CC / Other for all possible
                                    risks.
                                </li>

                                <li>
                                    <strong>Delivery Note Requirements:</strong> Transporter to provide the customer
                                    ONLY with a delivery note
                                    containing the following information: Supplier Name - SilverGro Feed and Grain,
                                    Customer Name as per the
                                    Transport Order, Transport Order Number, Product Description, Upload Weighbridge
                                    Weight and Weighbridge No,
                                    Truck Registration, Driver Name, Delivery Date, Delivery Time, and Customer
                                    Signature.
                                </li>

                                <li>
                                    <strong>Contract Stipulations:</strong> Please ensure you are familiar with
                                    SilverGro Feed & Grain's
                                    standard transport contract.
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
                    <li class="section_heading">Special Notes</li>
                    <div class="table_row_value">

                        <div class="table_row_value">
                            <div class="table_row_value">
                                <ol class="indented-list">
                                    @if(str_contains(strtolower($transport_trans->product->name), 'bagged'))
                                        <li>
                                            Customer to check for broken or wet bags and make comments with qualities on delivery documentation, and bring this to the transporters attention.
                                        </li>
                                        <li>
                                            If any bags are broken or goods defective, kindly contact Silvergro Feed & Grain immediately.

                                        </li>
                                    <li>
                                        Driver and Customer to do a bag count and sign for goods on the transporters delivery documentation / Proof of Delivery (POD).
                                    </li>

                                    @endif

                                        @if(str_contains(strtolower($transport_trans->product->name), 'bagged') && str_contains(strtolower($transport_trans->product->name), 'chop'))
                                            <li>
                                                Customer to use with 7 days of delivery.
                                            </li>
                                        @endif

                                        @if(str_contains(strtolower($transport_trans->TransportLoad->ProductSource->name), 'import'))
                                            <li>
                                                In the event that goods described in this contract are to be delivered out of an African territory, such as Malawi, Zimbabwe, Zambia or Mozambique, no warranty is given in regard to delivery or delivery time.  It is hereby agreed that there can be no claim for late, or non delivery by the Seller.

                                            </li>
                                        @endif
                                </ol>
                            </div>

                        </div>
                    </div>

                    <div>




                    </div>



                    <br>
                    <p class="section_heading">Prepared for sivergro Feed & Grain by {{$user_name}} at {{$now}} <span></span></p>
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
    <div style="width: 100%; position: relative;">
        <div style="float: left;">
            <span>SilverGro Feed & Grain</span>
            <span>| Generated by {{$user_name}}</span>
            <span>| version {{$app_version}}</span>
        </div>
        <div style="float: right;">

            <script type="text/php">
                if (isset($pdf)) {
                    $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                    $size = 8;
                    $font = $fontMetrics->getFont("sans-serif");
                    $width = $fontMetrics->get_text_width($text, $font, $size);

                    // Set x position closer to the right side
                    $x = 500; // Adjust 30 to a suitable value if needed
                    $y = $pdf->get_height() - 45; // Position from the bottom

                    $pdf->page_text($x, $y, $text, $font, $size);
                }

            </script>
        </div>
    </div>
</footer>


</body>
</html>
