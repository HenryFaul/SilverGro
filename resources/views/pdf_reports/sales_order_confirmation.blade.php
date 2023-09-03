<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Sales Order Confirmation </title>
    <style>
        @page {
            margin: 90px 25px;
            font-family: sans-serif;
        }

        body {
            margin-top: 50px;
            padding: 10px;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0px;
            right: 0px;
            height: 70px;
            line-height: 15px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 12px;
            background-color: #0f0f0f;
            color: white;
            line-height: 25px;
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
            font-size: 11px;
            font-weight: bold;
        }

        .table_row_value {
            font-size: 11px;
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
            padding: 3px;
        }

        .section_heading {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 1px;
            padding-bottom: 2px;
            margin-top: 2px;
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
                <img style="float: left" src="{{ $logo }}" width="235" height="110"/>
            </td>
            <td style="float: right; text-align: right; font-size: 12px"><span><br><br>P.O. Box 71658, Rink Street<br>Port Elizabeth, 6001<br>Tel : +27 82 897 5966<br>+27 41 582 1952<br>Email : <a>documents@silvergro.co.za</a></span><br><br>
            </td>
        </tr>

    </table>

</header>

<footer>
    <div style="text-align: center">
        <span>SilverGro Feed & Grain</span>
        <span>| Generated by {{$user_name}}</span>
        <span>on {{$now}}</span>
        <span>| version {{$app_version}}</span>
        <span>| Page: </span> <span class="page_num"> </span>

    </div>
</footer>

<!-- Wrap the content of your PDF inside a main tag -->
<main>
    <div style="">

        <div style="margin-top: 5px;">
            <table style="width:100%">
                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 20px;">
                        <b><span>Sales Order Confirmation:</span> <span>SOC</span>
                            <span>{{$transport_trans->id}}</span>
                        </b>
                    </td>

                </tr>

                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 14px">
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
                            <td class="table_sections table_row_heading" style="width: 20%;">Collection Address</td>
                            <td class="table_sections table_row_value">
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->line_1}}</span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_2}}
                                </span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_3}}
                                </span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->country}}</span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->code}}</span>

                            </td>
                            <td class="table_sections table_row_heading">Delivery Address</td>
                            <td class="table_sections table_row_value">
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_1}}</span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_2}}
                                </span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_3}}
                                </span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->country}}</span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->code}}</span>
                            </td>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Customer Notes</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->customer_notes}}</td>
                        </tr>

                        </tbody>

                    </table>

                    <p class="table_row_value">
                        Herewith our confirmation of the SALES ORDER for the following product, including the specific terms and conditions of
                        this order. This document is a confirmation of our telephone order.
                    </p>
                </div>
                <li class="section_heading">
                    Product and Payment Details
                </li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Product</td>
                            <td class="table_sections table_row_value">{{$transport_trans->product->name}}</td>
                            <td class="table_sections table_row_heading">Grade</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->product_grade_perc}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Source</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->ProductSource->name}}</td>
                            <td class="table_sections table_row_heading">Weight in Tons Incoming</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
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
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading"></td>
                            <td class="table_sections table_row_value"></td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Delivery and Transport</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Transport Date Earliest
                            </td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_earliest}}</td>
                            <td class="table_sections table_row_heading">Delivery from</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->OffloadingHoursFrom->name}}<</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Transport Date Latest</td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest}}</td>
                            <td class="table_sections table_row_heading">Delivery to</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->OffloadingHoursTo->name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">No. Of Loads</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->number_loads}}</td>
                            <td class="table_sections table_row_heading">Delivery address</td>
                            <td class="table_sections table_row_value">
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_1}}</span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_2}}
                                </span>
                                <br>
                                <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_3}}
                                </span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->country}}</span>
                                <br>
                                <span>{{$transport_trans->TransportLoad->DeliveryAddress->code}}</span>
                            </td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Customer Notes</li>
                <div class="page-break">
                    <table class="table_sections" style="width:100%;">
                        <tbody>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Notes</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->customer_notes}}</td>

                        </tr>

                        </tbody>

                    </table>

                    <br>

                    <div class="table_row_value">
                        The buyer accepts the conditions as set out in this "SALES ORDER", unless changes are presented in writing within 24 hours after receiving this
                        document, for acceptance by the seller. Please sign this document and email a scanned copy to documents@silvergro.co.za. If the buyer does
                        not sign this document and return it as per the above, the transaction will still be considered as legal and binding. In the event of a quality
                        dispute, in the event of immediate direct resolution efforts failing, an independent professional will be appointed by the seller at the expense of
                        the default party. This transaction is subject to the terms, conditions and rules, including the arbitration clause and rules in contract form known
                        as Sagos 1 (Current Version) with which the parties are fully familiar with and which will be applicable to the extent in which it will not be
                        contradictory to what the parties agreed upon. FORCE MAJEURE : To be applied as per the SAGOS 1 (Version 09), section 11. We thank you for
                        the opportunity to do business with you.
                    </div>

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
                            <td class=" table_row_heading">Customer</td>
                        </tr>

                        </tbody>

                    </table>
                </div>

            </ol>


        </div>

    </div>
</main>
</body>
</html>
