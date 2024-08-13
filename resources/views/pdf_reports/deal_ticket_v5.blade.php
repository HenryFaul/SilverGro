<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Deal Ticket</title>
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
    <table style="width:100%;">
        <tr>
            <td>
                <img style="float: left;" src="{{ $logo }}" width="188" height="88"/>
            </td>
            <td style="float: right; text-align: right; font-size: 10px; padding-top: 7px;"><span>P.O. Box 71658, Rink Street<br>Port Elizabeth, 6001<br>Tel : +27 82 897 5966<br>+27 41 582 1952<br>Email : <a>documents@silvergro.co.za</a></span><br><br>
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
                        <b><span>Approved Deal Ticket:</span> <span>MQ</span>

                            @if($transport_trans->is_split_load)
                                <span>{{$split_data['primary_linked_trans_split']->a_mq}}</span>
                            @else
                                <span>{{$transport_trans->a_mq}}</span>
                            @endif


                        </b>
                    </td>

                </tr>

                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px">
                        @if($final_deal_ticket)
                            <span>Final Version</span>
                        @else
                            <span style="color:red">Working Document Version</span>
                        @endif
                    </td>
                </tr>

            </table>

            <ol type="1">
                @if($transport_trans->is_split_load)
                    <h3>SPLIT LOAD DEAL
                        @if(isset($split_data))
                            +{{count($split_data['linked_trans_split'])}}
                        @endif
                    </h3>

                    @if(isset($split_data))

                        <li class="section_heading">Split Check</li>
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        is_transporter_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">

                                        @if($split_data['is_transporter_same'])
                                            Yes
                                        @else
                                            No
                                        @endif

                                    </td>
                                    <td class="table_sections table_row_heading" style="width: 25%;">is_supplier_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">
                                        @if($split_data['is_supplier_same'])
                                            Yes
                                        @else
                                            No
                                        @endif

                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">is_customer_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">
                                        @if($split_data['is_customer_same'])
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                    <td class="table_sections table_row_heading" style="width: 25%;">is_product_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">
                                        @if($split_data['is_product_same'])
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        is_product_billing_units_outgoing_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">@if($split_data['is_product_billing_units_outgoing_same'])
                                            Yes
                                        @else
                                            No
                                        @endif</td>
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        is_product_billing_units_incoming_same
                                    </td>
                                    <td class="table_sections table_row_value" style="width: 25%;">
                                        @if($split_data['is_product_billing_units_incoming_same'])
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        is_transport_rate_basis_same
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">@if($split_data['is_transport_rate_basis_same'])
                                            Yes
                                        @else
                                            No
                                        @endif</td>
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        is_transport_rate_same
                                    </td>
                                    <td class="table_sections table_row_value" style="width: 25%;">
                                        @if($split_data['is_transport_rate_same'])
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        sum_weight_ton_incoming
                                    </td>
                                    <td class="table_sections table_row_value"
                                        style="width: 25%;">
                                        {{$split_data['sum_weight_ton_incoming']}} tons
                                    </td>
                                    <td class="table_sections table_row_heading" style="width: 25%;">
                                        sum_weight_ton_outgoing
                                    </td>
                                    <td class="table_sections table_row_value" style="width: 25%;">
                                        {{$split_data['sum_weight_ton_outgoing']}} tons
                                    </td>
                                </tr>

                                </tbody>

                            </table>
                        </div>

                        <br>

                        <li class="section_heading">Split Summary Overview</li>
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Deal Ticket</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Supplier Order
                                        #
                                    </td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Cust Order #</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Supplier</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Customer</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Transporter</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Product</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading"></td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)

                                    <tr class="table_sections" style="width:100%;">
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->supplier_loading_number}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->customer_order_number}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Supplier->last_legal_name}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Transporter->last_legal_name}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                                        <td style="width: 12.5%;" class="table_sections table_row_value"></td>
                                    </tr>

                                @endforeach

                                </tbody>

                            </table>
                        </div>

                        <br>

                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Deal Ticket</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Tons In</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Cost/Ton</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Load basis</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Rate/Ton</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Tons Out</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Price/Ton</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">GP/Ton</td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)

                                    <tr class="table_sections" style="width:100%;">
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_incoming_actual}}</td>
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_ton_actual,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->RateBasis->name}}</td>
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_rate_per_ton_actual,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_outgoing_actual}}</td>
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton_actual,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_per_ton_actual,2), 2, '.', ' ')}}</td>
                                    </tr>

                                @endforeach

                                </tbody>

                            </table>
                        </div>

                        <br>

                        @foreach ($split_data['linked_trans_split'] as $deal)

                            <li class="section_heading">Product Information [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->product->name }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Grade</td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportLoad->product_grade_perc }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Source</td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportLoad->ProductSource->name }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Weight in Tons
                                            Outgoing
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportFinance->weight_ton_outgoing }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Packaging
                                            outgoing
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportLoad->PackagingOutgoing->name }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;"></td>
                                        <td class="table_sections table_row_value" style="width: 25%;"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <li class="section_heading">Customer Information and Deal Pricing [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Customer
                                            parent
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{ $deal->TransportTransaction->Customer->CustomerParent->last_legal_name }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading">Customer</td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{ $deal->TransportTransaction->Customer->last_legal_name }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Business
                                            Address
                                        </td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            @if($deal->TransportTransaction->Customer->addressablePhysical == "[]")
                                                <span>No physical address loaded...</span>
                                            @else
                                                <span>{{ $deal->TransportTransaction->Customer->addressablePhysical[0]->line_1 }}</span>
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->line_2)
                                                    ,
                                                    <span>{{ $deal->TransportTransaction->Customer->addressablePhysical[0]->line_2 }}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->line_3)
                                                    ,
                                                    <span>{{ $deal->TransportTransaction->Customer->addressablePhysical[0]->line_3 }}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->country)
                                                    ,
                                                    <span>{{ $deal->TransportTransaction->Customer->addressablePhysical[0]->country }}</span>
                                                @endif
                                                @if($deal->TransportTransaction->Customer->addressablePhysical[0]->code)
                                                    ,
                                                    <span>{{ $deal->TransportTransaction->Customer->addressablePhysical[0]->code }}</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading">Delivery Address</td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            <span>{{ $deal->TransportTransaction->TransportLoad->DeliveryAddress->line_1 }}</span>
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->line_2),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->DeliveryAddress->line_2 }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->line_3),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->DeliveryAddress->line_3 }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->country),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->DeliveryAddress->country }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->DeliveryAddress->code),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->DeliveryAddress->code }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Selling Price /
                                            Metric Ton
                                        </td>
                                        <td class="table_sections table_row_value" style="width: 25%;">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton, 2), 2, '.', ' ') }}
                                        </td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">VAT Exempt</td>
                                        <td class="table_sections table_row_value" style="width: 25%;">
                                            @if($deal->TransportTransaction->TransportJob->is_product_zero_rated === 1)
                                                <span>Yes</span>
                                            @else
                                                <span>No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Selling Price /
                                            Unit
                                        </td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_unit, 2), 2, '.', ' ') }}
                                        </td>
                                        <td class="table_sections table_row_heading">VAT Cert. Received</td>
                                        <td class="table_sections table_row_value">
                                            @if($deal->TransportTransaction->Customer->is_vat_cert_received === 1)
                                                <span>Yes</span>
                                            @else
                                                <span>No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Total Selling
                                            Price (A)
                                        </td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->selling_price, 2), 2, '.', ' ') }}
                                        </td>
                                        <td class="table_sections table_row_heading">Product Zero Rated</td>
                                        <td class="table_sections table_row_value">
                                            @if($deal->TransportTransaction->TransportJob->is_product_zero_rated === 1)
                                                <span>Yes</span>
                                            @else
                                                <span>No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Invoice Basis
                                        </td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->Customer->InvoiceBasis->value }}</td>
                                        <td class="table_sections table_row_heading">Transport Costs Included</td>
                                        <td class="table_sections table_row_value">
                                            @if($deal->TransportTransaction->TransportJob->is_transport_costs_inc_price === 1)
                                                <span>Yes</span>
                                            @else
                                                <span>No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Billing Units
                                        </td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->TransportLoad->BillingUnitsOutgoing->name }}</td>
                                        <td class="table_sections table_row_heading">Terms of payment</td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->Customer->TermsOfPayment->value }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Customer
                                            Notes
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{!! nl2br($deal->TransportTransaction->customer_notes) !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <li class="section_heading">Transport [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transporter
                                            Name
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{ $deal->TransportTransaction->Transporter->last_legal_name }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Order Confirmed
                                            By
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportLoad->ConfirmedByType->name }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Weight Tons
                                            Incoming
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->TransportFinance->weight_ton_incoming }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transport Date
                                            Earliest
                                        </td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->transport_date_earliest->format('D d/M/Y') }}</td>
                                        <td class="table_sections table_row_heading">Rate Basis</td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->TransportFinance->TransportRateBasis->name }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transport Date
                                            Latest
                                        </td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->transport_date_latest->format('D d/M/Y') }}</td>
                                        <td class="table_sections table_row_heading">Tarrif / Metric Ton</td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->transport_rate_per_ton, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">No. Of Loads
                                        </td>
                                        <td class="table_sections table_row_value">{{ $deal->TransportTransaction->TransportJob->number_loads }}</td>
                                        <td class="table_sections table_row_heading">Load Tarrif (B)</td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->transport_cost, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Transport
                                            Notes
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{!! nl2br($deal->TransportTransaction->transport_notes) !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <li class="section_heading">Supplier Information and Deal Cost [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading">Supplier Name</td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{{ $deal->TransportTransaction->Supplier->last_legal_name }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Collection
                                            Address
                                        </td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            <span>{{ $deal->TransportTransaction->TransportLoad->CollectionAddress->line_1 }}</span>
                                            @if($deal->TransportTransaction->TransportLoad->CollectionAddress->line_2),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->CollectionAddress->line_2 }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->CollectionAddress->line_3),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->CollectionAddress->line_3 }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->CollectionAddress->country),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->CollectionAddress->country }}</span>
                                            @endif
                                            @if($deal->TransportTransaction->TransportLoad->CollectionAddress->code),
                                            <span>{{ $deal->TransportTransaction->TransportLoad->CollectionAddress->code }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Cost Price /
                                            Unit
                                        </td>
                                        <td class="table_sections table_row_value" style="width: 25%;">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit, 2), 2, '.', ' ') }}
                                        </td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Terms of
                                            Payment
                                        </td>
                                        <td class="table_sections table_row_value"
                                            style="width: 25%;">{{ $deal->TransportTransaction->Supplier->TermsOfPayment->value }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading">Cost Price / Metric Ton</td>
                                        <td class="table_sections table_row_value" colspan="3">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_ton, 2), 2, '.', ' ') }}
                                        </td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 20%;">Supplier
                                            Notes
                                        </td>
                                        <td class="table_sections table_row_value"
                                            colspan="3">{!! nl2br($deal->TransportTransaction->suppliers_notes) !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <li class="section_heading">Margin Calculation [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit
                                            A-B-C
                                        </td>
                                        <td class="table_sections table_row_value" style="width: 25%;">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->gross_profit, 2), 2, '.', ' ') }}</td>
                                        <td class="table_sections table_row_heading" style="width: 25%;">Additional Cost
                                            1
                                        </td>
                                        <td class="table_sections table_row_value" style="width: 25%;">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_1, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit /
                                            Metric Ton
                                        </td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_per_ton, 2), 2, '.', ' ') }}</td>
                                        <td class="table_sections table_row_heading">Additional Cost 2</td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_2, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit
                                            %
                                        </td>
                                        <td class="table_sections table_row_value">{{ number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_percent, 2), 2, '.', ' ') }}
                                            %
                                        </td>
                                        <td class="table_sections table_row_heading">Additional Cost 3</td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_3, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" style="width: 25%;"></td>
                                        <td class="table_sections table_row_value"></td>
                                        <td class="table_sections table_row_heading">Total Cost</td>
                                        <td class="table_sections table_row_value">
                                            R {{ number_format(round($deal->TransportTransaction->TransportFinance->total_cost_price, 2), 2, '.', ' ') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <li class="section_heading">Approvals [MQ{{$deal->TransportTransaction->a_mq}}]</li>
                            <div>
                                <table class="table_sections" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th class="table_sections table_row_heading">Rule</th>
                                        <th class="table_sections table_row_heading">Role</th>
                                        <th class="table_sections table_row_heading">Approved by</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" colspan="3">Trade Rules:</td>
                                    </tr>
                                    @if(isset($rules_with_approvals))
                                        @if(isset($rules_with_approvals['TradeRule']))
                                            @foreach($rules_with_approvals['TradeRule'] as $rule)
                                                <tr class="table_sections">
                                                    <td class="table_sections table_row_value">{{ $rule["rule"] }}</td>
                                                    <td class="table_sections table_row_value">{{ $rule["role"] }}</td>
                                                    <td class="table_sections table_row_value">
                                                        @if(isset($rule["approvals"]))
                                                            @foreach($rule["approvals"] as $approval)
                                                                <span>{{ $approval->User->name }} - </span>
                                                                <span>{{ $approval->created_at }}</span>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @else
                                        <tr class="table_sections">
                                            <td class="table_sections table_row_value">xxx</td>
                                            <td class="table_sections table_row_value">xxx</td>
                                            <td class="table_sections table_row_value">xxx</td>
                                        </tr>
                                    @endif
                                    <tr class="table_sections">
                                        <td class="table_sections table_row_heading" colspan="3">Trade Operation
                                            Rules:
                                        </td>
                                    </tr>
                                    @if(isset($rules_with_approvals))
                                        @if(isset($rules_with_approvals['TradeRuleOpp']))
                                            @foreach($rules_with_approvals['TradeRuleOpp'] as $rule)
                                                <tr class="table_sections">
                                                    <td class="table_sections table_row_value">{{ $rule["rule"] }}</td>
                                                    <td class="table_sections table_row_value">{{ $rule["role"] }}</td>
                                                    <td class="table_sections table_row_value">
                                                        @if(isset($rule["approvals"]))
                                                            @foreach($rule["approvals"] as $approval)
                                                                <span>{{ $approval->User->name }} - </span>
                                                                <span>{{ $approval->created_at }}</span>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @else
                                        <tr class="table_sections">
                                            <td class="table_sections table_row_value">xxx</td>
                                            <td class="table_sections table_row_value">xxx</td>
                                            <td class="table_sections table_row_value">xxx</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                        @endforeach
                            <br>
                            <li class="section_heading">Signatures [all MQ]</li>
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
                                    </tr>
                                    <tr style="margin-top: 4px;">
                                        <td class=" table_row_heading">Trader</td>
                                        <td class=" table_row_heading">Trading Director</td>
                                        <td class=" table_row_heading">Financial Director</td>
                                    </tr>

                                    </tbody>

                                </table>
                            </div>


                    @endif

                @else

                    <li class="section_heading">Product Information</li>
                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->product->name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Grade</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportLoad->product_grade_perc}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Source</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportLoad->ProductSource->name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Weight in Tons Outgoing
                                </td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportFinance->weight_ton_outgoing}}</td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Packaging outgoing</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;"></td>
                                <td class="table_sections table_row_value" style="width: 25%;"></td>
                            </tr>

                            </tbody>

                        </table>
                    </div>
                    <br>
                    <li class="section_heading">Customer Information and Deal Pricing</li>
                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Customer parent</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">{{$transport_trans->Customer->CustomerParent->last_legal_name}}</td>
                            </tr>
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
                                        @if($transport_trans->Customer->addressablePhysical[0]->line_2),
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->line_2}}</span>
                                        @endif
                                        @if($transport_trans->Customer->addressablePhysical[0]->line_3),
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->line_3}}</span>
                                        @endif
                                        @if($transport_trans->Customer->addressablePhysical[0]->country),
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->country}}</span>
                                        @endif
                                        @if($transport_trans->Customer->addressablePhysical[0]->code),
                                        <span>{{$transport_trans->Customer->addressablePhysical[0]->code}}</span>
                                        @endif
                                    @endif

                                </td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading">Delivery Address</td>
                                <td class="table_sections table_row_value" colspan="3">

                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_1}}</span>
                                    @if($transport_trans->TransportLoad->DeliveryAddress->line_2),
                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_2}}</span>
                                    @endif
                                    @if($transport_trans->TransportLoad->DeliveryAddress->line_3),
                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_3}}</span>
                                    @endif
                                    @if($transport_trans->TransportLoad->DeliveryAddress->country),
                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->country}}</span>
                                    @endif
                                    @if($transport_trans->TransportLoad->DeliveryAddress->code),
                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->code}}</span>
                                    @endif

                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Selling Price / Metric
                                    Ton
                                </td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    R {{number_format(round($transport_trans->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">VAT Exempt</td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    @if($transport_trans->TransportJob->is_product_zero_rated === 1)
                                        <span>Yes</span>
                                    @else
                                        <span>No</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Selling Price / Unit
                                </td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->selling_price_per_unit,2), 2, '.', ' ')}}
                                </td>
                                <td class="table_sections table_row_heading">VAT Cert. Received</td>
                                <td class="table_sections table_row_value">
                                    @if($transport_trans->Customer->is_vat_cert_received === 1)
                                        <span>Yes</span>
                                    @else
                                        <span>No</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Total Selling Price (A)
                                </td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->selling_price,2), 2, '.', ' ')}}</td>
                                <td class="table_sections table_row_heading">Product Zero Rated</td>
                                <td class="table_sections table_row_value">
                                    @if($transport_trans->TransportJob->is_product_zero_rated === 1)
                                        <span>Yes</span>
                                    @else
                                        <span>No</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Invoice Basis</td>
                                <td class="table_sections table_row_value">{{$transport_trans->Customer->InvoiceBasis->value}}</td>
                                <td class="table_sections table_row_heading">Transport Costs Included</td>
                                <td class="table_sections table_row_value">
                                    @if($transport_trans->TransportJob->is_transport_costs_inc_price === 1)
                                        <span>Yes</span>
                                    @else
                                        <span>No</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Billing Units</td>
                                <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->BillingUnitsOutgoing->name}}</td>
                                <td class="table_sections table_row_heading">Terms of payment</td>
                                <td class="table_sections table_row_value">{{$transport_trans->Customer->TermsOfPayment->value}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Customer Notes</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">
                                    {!!nl2br($transport_trans->customer_notes)!!}
                                </td>

                            </tr>

                            </tbody>

                        </table>
                    </div>
                    <br>
                    <li class="section_heading">Transport</li>
                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Transporter Name</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Order Confirmed By</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportLoad->ConfirmedByType->name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Weight Tons Incoming
                                </td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Earliest
                                </td>
                                <td class="table_sections table_row_value">{{ $transport_trans->transport_date_earliest->format('D d/M/Y')}}</td>
                                <td class="table_sections table_row_heading">Rate Basis</td>
                                <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->TransportRateBasis->name}}
                                    <
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Latest
                                </td>
                                <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                                <td class="table_sections table_row_heading">Tarrif / Metric Ton</td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">No. Of Loads</td>
                                <td class="table_sections table_row_value">{{$transport_trans->TransportJob->number_loads}}</td>
                                <td class="table_sections table_row_heading">Load Tarrif (B)</td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Transport Notes</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">
                                    {!!nl2br($transport_trans->transport_notes)!!}
                                </td>

                            </tr>

                            </tbody>

                        </table>
                    </div>
                    <br>
                    <li class="section_heading">Supplier Information and Deal Cost</li>
                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading">Supplier Name</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">{{$transport_trans->Supplier->last_legal_name}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Collection Address</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->line_1}}</span>
                                    @if($transport_trans->TransportLoad->CollectionAddress->line_2),
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_2}}
                                    </span>
                                    @endif
                                    @if($transport_trans->TransportLoad->CollectionAddress->line_3),
                                    <span>
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_3}}
                                </span>
                                    @endif
                                    @if($transport_trans->TransportLoad->CollectionAddress->country),
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->country}}</span>
                                    @endif
                                    @if($transport_trans->TransportLoad->CollectionAddress->code),
                                    <span>{{$transport_trans->TransportLoad->CollectionAddress->code}}</span>
                                    @endif

                                </td>

                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Cost Price / Unit</td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Terms of Payment</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$transport_trans->Supplier->TermsOfPayment->value}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading">Cost Price / Metric Ton</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    R {{number_format(round($transport_trans->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 20%;">Supplier Notes</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">
                                    {!!nl2br($transport_trans->suppliers_notes)!!}
                                </td>
                            </tr>

                            </tbody>

                        </table>
                    </div>
                    <br>
                    <li class="section_heading">Margin Calculation</li>
                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit A-B-C</td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    R {{number_format(round($transport_trans->TransportFinance->gross_profit,2), 2, '.', ' ')}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Additional Cost 1</td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    R {{number_format(round($transport_trans->TransportFinance->additional_cost_1,2), 2, '.', ' ')}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit / Metric
                                    Ton
                                </td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->gross_profit_per_ton,2), 2, '.', ' ')}}</td>
                                <td class="table_sections table_row_heading">Additional Cost 2</td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->additional_cost_2,2), 2, '.', ' ')}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Gross Profit %</td>
                                <td class="table_sections table_row_value">{{number_format(round($transport_trans->TransportFinance->gross_profit_percent,2), 2, '.', ' ')}}
                                    %
                                </td>
                                <td class="table_sections table_row_heading">Additional Cost 3</td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->additional_cost_3,2), 2, '.', ' ')}}</td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;"></td>
                                <td class="table_sections table_row_value"></td>
                                <td class="table_sections table_row_heading">Total Cost</td>
                                <td class="table_sections table_row_value">
                                    R {{number_format(round($transport_trans->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                            </tr>


                            </tbody>

                        </table>
                    </div>
                    <br>
                    <div style="">
                    </div>
                    <li class="section_heading">Approvals</li>
                    <div>
                        <table class="table_sections" style="width:100%;">

                            <thead>
                            <th class="table_sections table_row_heading">Rule</th>
                            <th class="table_section table_row_heading">Role</th>
                            <th class="table_sections table_row_heading">Approved by</th>
                            </thead>
                            <tbody>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" colspan="3">Trade Rules:</td>
                            </tr>

                            @if(isset($rules_with_approvals))
                                @if(isset($rules_with_approvals['TradeRule']))

                                    @foreach($rules_with_approvals['TradeRule'] as $rule)
                                        <tr class="table_sections">
                                            <td class="table_sections table_row_value">{{$rule["rule"]}}</td>
                                            <td class="table_sections table_row_value">{{$rule["role"]}}</td>
                                            <td class="table_sections table_row_value">

                                                @if(isset($rule["approvals"]))

                                                    @foreach($rule["approvals"] as $approval)

                                                        <span>{{$approval->User->name}} - </span>
                                                        <span>{{$approval->created_at}}</span>



                                                    @endforeach

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif

                            @else
                                <tr class="table_sections">
                                    <td class="table_sections table_row_value">xxx</td>
                                    <td class="table_sections table_row_value">xxx</td>
                                    <td class="table_sections table_row_value">xxx</td>
                                </tr>
                            @endif

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" colspan="3">Trade Operation Rules:</td>
                            </tr>

                            @if(isset($rules_with_approvals))
                                @if(isset($rules_with_approvals['TradeRuleOpp']))

                                    @foreach($rules_with_approvals['TradeRuleOpp'] as $rule)
                                        <tr class="table_sections">
                                            <td class="table_sections table_row_value">{{$rule["rule"]}}</td>
                                            <td class="table_sections table_row_value">{{$rule["role"]}}</td>
                                            <td class="table_sections table_row_value">

                                                @if(isset($rule["approvals"]))

                                                    @foreach($rule["approvals"] as $approval)
                                                        <span>{{$approval->User->name}} - </span>
                                                        <span>{{$approval->created_at}}</span>
                                                    @endforeach

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif

                            @else
                                <tr class="table_sections">
                                    <td class="table_sections table_row_value">xxx</td>
                                    <td class="table_sections table_row_value">xxx</td>
                                    <td class="table_sections table_row_value">xxx</td>
                                </tr>
                            @endif


                            </tbody>

                        </table>
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
                            </tr>
                            <tr style="margin-top: 4px;">
                                <td class=" table_row_heading">Trader</td>
                                <td class=" table_row_heading">Trading Director</td>
                                <td class=" table_row_heading">Financial Director</td>
                            </tr>

                            </tbody>

                        </table>
                    </div>


                @endif


            </ol>


        </div>
    </div>
</main>


<footer>

    <div>
        <span>SilverGro Feed & Grain</span>
<!--        <span>| Generated by {{$user_name}}</span>
        <span>on {{$now}}</span>
        <span>| version {{$app_version}}</span>
        <span>| Page: </span> <span class="page_num"> </span>-->
    </div>
    <div></div>
</footer>


</body>
</html>
