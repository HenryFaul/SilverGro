<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Split Sales Order Confirmation</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
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

        table, th, td {
            border: none;
            border-collapse: collapse;
        }

        .table_sections {
            border: none;
        }

        .table_sections_bordered {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table_sections_bordered th,
        .table_sections_bordered td {
            border: 1px solid black;
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

        p {
            margin: 2px 0;
            padding: 0;
        }

        div {
            margin: 0;
            padding: 0;
        }

        li {
            margin-top: 2px;
            margin-bottom: 2px;
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
        <div style="margin-top: 1px;">
            <table style="width:100%">
                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px;">
                        <b style="font-size: 16px"><span>Split Sales Order Confirmation:</span>
                            @if($transport_trans->is_split_load)
                                <span>{{$split_data['primary_linked_trans_split']->a_mq}}</span>
                            @else
                                <span>{{$transport_trans->a_mq}}</span>
                            @endif
                        </b>
                        <div><span>Created at: {{$now}}</span></div>
                    </td>
                </tr>


                <tr>
                    <td>
                        <h5 style="margin: 0; padding: 0;">SPLIT LOAD DEAL
                            @if(isset($split_data))
                                +{{count($split_data['linked_trans_split'])}}
                            @endif
                        </h5>
                    </td>
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
                @if($transport_trans->is_split_load)

                    @if(isset($split_data))
                        <li class="section_heading">Split Summary Overview</li>
                        <div>
                            <table class="table_sections_bordered" style="width:100%;">
                                <tbody>
                                <tr>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Sales
                                        Order
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Customer
                                        Order #
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Supplier
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Customer
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Transporter
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Product
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Tons
                                        Out
                                    </td>
                                    <td style="width: 12.5%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Price/Ton
                                    </td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)
                                    <tr style="width:100%;">
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportJob->customer_order_number}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Supplier->last_legal_name}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Transporter->last_legal_name}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{round($deal->TransportTransaction->TransportFinance->weight_ton_outgoing,2)}}</td>
                                        <td style="width: 12.5%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @foreach ($split_data['linked_trans_split'] as $deal)
                            <hr style="margin-top: 5px; margin-bottom: 5px;">
                            <li class="section_heading">Customer Details [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width:25%;">Customer</td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Business
                                            Address
                                        </td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            @if($deal->TransportTransaction->Customer->addressablePhysical == "[]")
                                                <span>No physical address loaded...</span>
                                            @else
                                                <span>{{$deal->TransportTransaction->Customer->addressablePhysical[0]->line_1}}</span>
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->line_2)
                                                    ,
                                                    <span>{{$deal->TransportTransaction->Customer->addressablePhysical[0]->line_2}}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->line_3)
                                                    ,
                                                    <span>{{$deal->TransportTransaction->Customer->addressablePhysical[0]->line_3}}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->country)
                                                    ,
                                                    <span>{{$deal->TransportTransaction->Customer->addressablePhysical[0]->country}}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->code)
                                                    ,
                                                    <span>{{$deal->TransportTransaction->Customer->addressablePhysical[0]->code}}</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Att</td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            @if($deal->TransportTransaction->Customer->contactable=="[]")
                                                <span>No contact loaded</span>
                                            @else
                                                @foreach($deal->TransportTransaction->Customer->contactable as $contact)
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

                                <p class="table_row_value" style="margin-top: 4px;">
                                    Herewith our confirmation of the SALES ORDER for the following product, including
                                    the specific terms and conditions of this order. This document is a confirmation of
                                    our telephone order.
                                </p>
                            </div>

                            <li class="section_heading">Product and Payment Details
                                [MQ{{$deal->TransportTransaction->a_mq}}]
                            </li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                                        <td class="table_sections table_row_value"
                                            style="width:25%;">{{$deal->TransportTransaction->product->name}}</td>
                                        <td class="table_sections table_row_heading">Grade</td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->product_grade_perc}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Source</td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->ProductSource->name}}</td>
                                        <td class="table_sections table_row_heading">Weight in Tons Outgoing</td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_outgoing}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Packaging
                                            outgoing
                                        </td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->PackagingOutgoing->name}}</td>
                                        <td class="table_sections table_row_heading">Billing Units</td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->BillingUnitsOutgoing->name}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">VAT (Zero
                                            rated)
                                        </td>
                                        <td class="table_sections table_row_value">
                                            @if($deal->TransportTransaction->TransportJob->is_product_zero_rated)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td class="table_sections table_row_heading">Vat (Exempt)</td>
                                        <td class="table_sections table_row_value">
                                            @if($deal->TransportTransaction->Customer->is_vat_exempt)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Selling Price /
                                            Metric Ton
                                        </td>
                                        <td class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                        <td class="table_sections table_row_heading">Selling Price / Unit</td>
                                        <td class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_unit,2), 2, '.', ' ')}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Total Selling
                                            Price
                                        </td>
                                        <td class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price,2), 2, '.', ' ')}}</td>
                                        <td class="table_sections table_row_heading">Terms of payment</td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->Customer->TermsOfPayment->value}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <li class="section_heading">Delivery and Transport [MQ{{$deal->TransportTransaction->a_mq}}
                                ]
                            </li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transport Date
                                            Earliest
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%; white-space: nowrap;">{{ $deal->TransportTransaction->transport_date_earliest ? $deal->TransportTransaction->transport_date_earliest->format('D d/M/Y') : 'No date Selected' }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Offloading time
                                            from
                                        </td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->OffloadingHoursFrom->name}}
                                            HRS
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transport Date
                                            Latest
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%; white-space: nowrap;">{{$deal->TransportTransaction->transport_date_latest->format('D d/M/Y')}}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Offloading time
                                            to
                                        </td>
                                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->OffloadingHoursTo->name}}
                                            HRS
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">No. Of Loads
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{$deal->TransportTransaction->TransportJob->number_loads}}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading">Delivery Address</td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            <span>{{$deal->TransportTransaction->TransportLoad->DeliveryAddress->line_1}}</span>
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->line_2)
                                                ,
                                                <span>{{$deal->TransportTransaction->TransportLoad->DeliveryAddress->line_2}}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->line_3)
                                                ,
                                                <span>{{$deal->TransportTransaction->TransportLoad->DeliveryAddress->line_3}}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->country)
                                                ,
                                                <span>{{$deal->TransportTransaction->TransportLoad->DeliveryAddress->country}}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->code)
                                                ,
                                                <span>{{$deal->TransportTransaction->TransportLoad->DeliveryAddress->code}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <li class="section_heading">Customer Notes [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div class="">
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 20%;">Notes</td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{!!nl2br($deal->TransportTransaction->customer_notes)!!}</td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div class="table_row_value" style="margin-top: 4px;">
                                    The buyer accepts the conditions as set out in this "SALES ORDER", unless changes
                                    are presented in writing within 24 hours after receiving this document, for
                                    acceptance by the seller. Please sign this document and email a scanned copy to
                                    documents@silvergro.co.za. If the buyer does not sign this document and return it as
                                    per the above, the transaction will still be considered as legal and binding. In the
                                    event of a quality dispute, in the event of immediate direct resolution efforts
                                    failing, an independent professional will be appointed by the seller at the expense
                                    of the default party. This transaction is subject to the terms, conditions and
                                    rules, including the arbitration clause and rules in contract form known as Sagos 1
                                    (Current Version) with which the parties are fully familiar with and which will be
                                    applicable to the extent in which it will not be contradictory to what the parties
                                    agreed upon. FORCE MAJEURE : To be applied as per the SAGOS 1 (Version 09), section
                                    11. We thank you for the opportunity to do business with you.
                                </div>

                                @if(str_contains(strtolower($deal->TransportTransaction->product->name), 'bagged'))
                                    <div class="table_row_value" style="margin-top: 4px;">
                                        Customer to check for broken or wet bags and make comments with qualities on
                                        delivery documentation, and bring this to the transporters attention.
                                    </div>
                                    <div class="table_row_value">
                                        If any bags are broken or goods defective, kindly contact Silvergro Feed & Grain
                                        immediately.
                                    </div>
                                    <div class="table_row_value">
                                        Driver and Customer to do a bag count and sign for goods on the transporters
                                        delivery documentation / Proof of Delivery (POD).
                                    </div>
                                @endif

                                @if(str_contains(strtolower($deal->TransportTransaction->product->name), 'bagged') && str_contains(strtolower($deal->TransportTransaction->product->name), 'chop'))
                                    <div class="table_row_value" style="margin-top: 4px;">
                                        Customer to use with 7 days of delivery.
                                    </div>
                                @endif

                                @if(str_contains(strtolower($deal->TransportTransaction->TransportLoad->ProductSource->name), 'import'))
                                    <div class="table_row_value" style="margin-top: 4px;">
                                        In the event that goods described in this contract are to be delivered out of an
                                        African territory, such as Malawi, Zimbabwe, Zambia or Mozambique, no warranty
                                        is given in regard to delivery or delivery time. It is hereby agreed that there
                                        can be no claim for late, or non delivery by the Seller.
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <li class="section_heading">Signatures [all MQ]</li>
                        <div>
                            <table class="" style="width:100%;">
                                <tbody>
                                <tr class="">
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <hr>
                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <hr>
                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <hr>
                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <hr>
                                    </td>
                                </tr>
                                <tr style="margin-top: 4px;">
                                    <td class=" table_row_heading">Customer Signature</td>
                                    <td class=" table_row_heading">Customer Name</td>
                                    <td class=" table_row_heading">Date</td>
                                    <td class=" table_row_heading">Stamp</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            </ol>
        </div>
    </div>
</main>

<footer>
    <script type="text/php">
        if (isset($pdf) && $PAGE_NUM > 1) {
            // Set up footer text with values from Blade
            $user_name = "{{ $user_name ?? 'Unknown User' }}";
            $app_version = "{{ $app_version ?? '1.0' }}";
            $left_text = "SilverGro Feed & Grain | Generated by " . $user_name . " | version " . $app_version;
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

