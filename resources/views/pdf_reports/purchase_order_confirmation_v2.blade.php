<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Purchase order confirmation</title>
    <style>
        @page {
            margin: 100px 40px;
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
            height: 30px;
            font-size: 10px;
            background-color: #0f0f0f;
            color: white;
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
            border: 0px solid black;
            border-collapse: collapse;
        }

        .table_sections {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th, td {
            padding: 1px;
        }

        .section_heading {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 1px;
            padding-bottom: 0px;
            margin-top: 1px;
        }

        .page-break {
            page-break-after: always;
        }


    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    <table style="width:100%">
        <tr>
            <td>
                <img style="float: left" src="{{ $logo }}" width="188" height="88"/>
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
                        <b><span>Purchase Order Confirmation:</span> <span>MQ</span>
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
                <li class="section_heading">Supplier Details</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Supplier</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->Supplier->last_legal_name}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Business Address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Supplier->addressablePhysical == "[]")
                                    <span>No physical address loaded...</span>
                                @else
                                    <span>{{$transport_trans->Supplier->addressablePhysical[0]->line_1}}</span>
                                    @if($transport_trans->Supplier->addressablePhysical[0]->line_2),
                                    <span>{{$transport_trans->Supplier->addressablePhysical[0]->line_2}}</span>
                                    @endif
                                    @if($transport_trans->Supplier->addressablePhysical[0]->line_3),
                                    <span>{{$transport_trans->Supplier->addressablePhysical[0]->line_3}}</span>
                                    @endif
                                    @if($transport_trans->Supplier->addressablePhysical[0]->country),
                                    <span>{{$transport_trans->Supplier->addressablePhysical[0]->country}}</span>
                                    @endif
                                    @if($transport_trans->Supplier->addressablePhysical[0]->code),
                                    <span>{{$transport_trans->Supplier->addressablePhysical[0]->code}}</span>
                                    @endif
                                @endif

                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Att</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Supplier->contactable=="[]")
                                    <span>No contact loaded</span>
                                @else
                                    @foreach($transport_trans->Supplier->contactable as $contact)
                                        <div>
                                            <span>{{$contact->first_name}}</span> <span>{{$contact->last_legal_name}}</span>

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
                        Herewith our confirmation of the PURCHASE ORDER for the following product, including the specific terms and
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
                            <td class="table_sections table_row_value" style="width: 25%;">{{$transport_trans->product->name}}</td>
                            <td class="table_sections table_row_heading">Grade</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->product_grade_perc}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Packaging outgoing</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                            <td class="table_sections table_row_heading">Confirm Purchase Order By</td>
                            <td class="table_sections table_row_value">{{$purchase_order->ConfirmedByType->name}}</td>
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
                            <td class="table_sections table_row_heading" style="width: 20%;">Cost Price / Metric Ton</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">Cost Price / Unit</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Total Cost </td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->cost_price,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">Weight in Tons Incoming</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
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
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Earliest
                            </td>
                            <td class="table_sections table_row_value"  style="width: 25%;">{{$transport_trans->transport_date_earliest->format('D d/M/Y')}}</td>
                            <td class="table_sections table_row_heading"  style="width: 25%;">Loading time from</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->LoadingHoursFrom->name}} HRS<</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Latest</td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                            <td class="table_sections table_row_heading"  style="width: 25%;">Loading time to</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->LoadingHoursTo->name}} HRS</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">No. Of Loads</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->TransportJob->number_loads}}</td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Collection address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->line_1}}</span>

                                @if($transport_trans->TransportLoad->CollectionAddress->line_2),
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->line_2}}</span>
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->line_3),
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

                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Supplier Notes</li>
                <div class="">
                    <table class="table_sections" style="width:100%;">
                        <tbody>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Notes</td>
                            <td class="table_sections table_row_value" colspan="3">

                                {!!nl2br($transport_trans->supplier_notes)!!}
                            </td>

                        </tr>

                        </tbody>

                    </table>

                    <br>

                    <div class="table_row_value">
                        The seller accepts the conditions as set out in this "PURCHASE ORDER", unless changes are presented in writing within 24 hours after receiving
                        this document, for acceptance by the seller. Please sign this document and email a scanned copy to documents@silvergro.co.za. If the buyer
                        does not sign this document and return it as per the above, the transaction will still be considered as legal and binding. FORCE MAJEURE : To be
                        applied as per the SAGOS 1 (Version 09), section 11. We thank you for the opportunity to do business with you.
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
                            If any bags are broken or goods defective, kindly contact Silvergro Feed & Grain immediately.
                        </div>
                        <div class="table_row_value">
                            Driver and Customer to do a bag count and sign for goods on the sales and transporter's delivery documentation.
                        </div>
                    @endif

                </div>
                <br>
                <li class="section_heading">Signatures</li>
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
                                <hr>
                            </td>
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
                                <hr>
                            </td>
                        </tr>
                        <tr style="margin-top: 4px;">
                            <td class=" table_row_heading">Trader</td>
                            <td class=" table_row_heading">Trading Director</td>
                            <td class=" table_row_heading">Financial Director</td>
                            <td class=" table_row_heading">Supplier</td>
                        </tr>

                        </tbody>

                    </table>
                </div>

            </ol>


        </div>

    </div>
</main>

<footer>
    <div>
        <span>SilverGro Feed & Grain</span>
        <span>| Generated by {{$user_name}}</span>
        <span>on {{$now}}</span>
        <span>| version {{$app_version}}</span>
        <span>| Page: </span> <span class="page_num"> </span>
    </div>
</footer>


</body>
</html>
