<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Split Transport Order Confirmation</title>
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
                <img style="float: left;" src="{{ $logo }}" width="202" height="95" />
            </td>
            <td style="float: right; text-align: right; font-size: 10px; padding-top: 7px;"><span>P.O. Box 71658, Rink Street<br>Port Elizabeth, 6001<br>Tel : +27 82 897 5966<br>+27 41 582 1952<br>Email : <a>documents@silvergro.co.za</a></span><br><br>
            </td>
        </tr>

    </table>
</header>


<!-- Wrap the content of your PDF inside a main tag -->
<main>
    <div style="">
        <div style="margin-top: 0px;">
            <table style="width:100%">
                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px;">
                        <b style="font-size: 16px"><span>Approved Deal Ticket:</span> <span>MQ</span>
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
                        @if($final_transport_order)
                            <span>Final Version</span>
                        @else
                            <span style="color:red">Working Document Version</span>
                        @endif
                    </td>
                </tr>

            </table>

            <ol type="1">

                @if($transport_trans->is_split_load)

                    <div>
                        <table class="table_sections" style="width:100%;">
                            <tbody>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 15%;">Supplier Name:</td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">{{$split_data['primary_linked_trans_split']->Supplier->last_legal_name}}</td>
                                <td class="table_sections table_row_heading" style="width: 10%;">Att:</td>
                                <td class="table_sections table_row_value"
                                    style="width: 20%;">Split supplier...
                                </td>

                            </tr>

                            <tr class="table_sections">

                                <td class="table_sections table_row_heading" style="width: 10%;">Business Address:</td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">

                                    @if($split_data['primary_linked_trans_split']->Supplier->addressablePhysical == "[]")
                                        <span>No physical address loaded...</span>
                                    @else
                                        <span>{{$split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->line_1}}</span>
                                        @if($split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->line_2)
                                            ,
                                            <span>{{$split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->line_2}}</span>
                                        @endif
                                        @if($split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->line_3)
                                            ,
                                            <span>{{$split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->line_3}}</span>
                                        @endif
                                        @if($split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->country)
                                            ,
                                            <span>{{$split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->country}}</span>
                                        @endif
                                        @if($split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->code)
                                            ,
                                            <span>{{$split_data['primary_linked_trans_split']->Customer->addressablePhysical[0]->code}}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 10%;">Collection Address:
                                </td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">

                                    <span>{{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_1}}</span>
                                    @if($split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_2)
                                        ,
                                        {{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_2}}
                                        </span>
                                    @endif
                                    @if($split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_3)
                                        ,
                                        <span>{{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_3}}</span>
                                    @endif
                                    @if($split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->country)
                                        ,
                                        <span>{{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->country}}</span>
                                    @endif
                                    @if($split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->code)
                                        ,
                                        <span>{{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->code}}</span>
                                    @endif

                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 10%;">Transport date
                                    earliest
                                </td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">

                                    {{ $split_data['primary_linked_trans_split']->transport_date_earliest->format('D d/M/Y')}}
                                </td>
                                <td class="table_sections table_row_heading" style="width: 15%;">Transport date latest
                                </td>
                                <td class="table_sections table_row_value"
                                    style="width: 20%;">
                                    {{ $split_data['primary_linked_trans_split']->transport_date_latest->format('D d/M/Y')}}
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 10%;">Vehicle reg</td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                                <td class="table_sections table_row_heading" style="width: 15%;"></td>
                                <td class="table_sections table_row_value"
                                    style="width: 20%;"></td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 10%;">Trailer #1</td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</td>
                                <td class="table_sections table_row_heading" style="width: 15%;">Trailer #2</td>
                                <td class="table_sections table_row_value"
                                    style="width: 20%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</td>
                            </tr>


                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 10%;">Driver</td>
                                <td class="table_sections table_row_value"
                                    style="width: 55%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>
                                <td class="table_sections table_row_heading" style="width: 15%;">Cell</td>
                                <td class="table_sections table_row_value"
                                    style="width: 20%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}}</td>
                            </tr>

                            </tbody>

                        </table>
                    </div>

                    @if(isset($split_data))
                        <hr>
                        <li class="section_heading">Split Summary Overview</li>
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td style="width: 10%;" class="table_sections table_row_heading">Deal Ticket</td>
                                    <td style="width: 15%;" class="table_sections table_row_heading">Supplier</td>
                                    <td style="width: 10%;" class="table_sections table_row_heading">Supplier Order #
                                    </td>
                                    <td style="width: 15%;" class="table_sections table_row_heading">Customer</td>
                                    <td style="width: 10%;" class="table_sections table_row_heading">Cust Order #</td>
                                    <td style="width: 10%;" class="table_sections table_row_heading">Cust Offloading #
                                    </td>
                                    <td style="width: 15%;" class="table_sections table_row_heading">Transporter</td>
                                    <td style="width: 15%;" class="table_sections table_row_heading">Product</td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)

                                    <tr class="table_sections" style="width:100%;">
                                        <td class="table_sections table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Supplier->last_legal_name}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->supplier_loading_number}}</td>

                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->customer_order_number}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Transporter->last_legal_name}}</td>
                                        <td
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                                    </tr>

                                @endforeach

                                </tbody>

                            </table>
                        </div>

                        <div>
                            @php
                                $total_units_incoming = 0;
                                $total_weight_ton_incoming = 0;
                                $total_weight_ton_outgoing = 0;

                                foreach ($split_data['linked_trans_split'] as $deal) {
                                    $total_units_incoming += $deal->TransportTransaction->TransportLoad->no_units_incoming;
                                    $total_weight_ton_incoming += $deal->TransportTransaction->TransportFinance->weight_ton_incoming;
                                    $total_weight_ton_outgoing += $deal->TransportTransaction->TransportFinance->weight_ton_outgoing;
                                }
                            @endphp
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Deal Ticket</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Product</td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Billing Units
                                    </td>
                                    <td style="width: 12.5%;" class="table_sections table_row_heading">Packaging
                                        Incoming
                                    </td>
                                    <td style="width: 12.5%;  text-align: center;"
                                        class="table_sections table_row_heading">BU In
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Cost PU
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Cost PT
                                    </td>
                                    <td style="width: 12.5%;  text-align: center;"
                                        class="table_sections table_row_heading">Planned TI
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Transport Rate
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Load Rate
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Effective Rate/Ton
                                    </td>
                                    <td style="width: 12.5%;  text-align: center;"
                                        class="table_sections table_row_heading">Planned TO
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">Selling Ton
                                    </td>
                                    <td style="width: 12.5%; text-align: right;"
                                        class="table_sections table_row_heading">GP
                                    </td>

                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)

                                    <tr class="table_sections" style="width:100%;">
                                        <td style="width: 12.5%;" class="table_sections table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->BillingUnitsIncoming->name}}</td>
                                        <td style="width: 12.5%;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->PackagingIncoming->name }}</td>
                                        <td style="width: 12.5%;  text-align: center;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->no_units_incoming}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R{{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R{{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}</td>

                                        <td style="width: 12.5%;  text-align: center;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_incoming}}</td>

                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->TransportRateBasis->name}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%;  text-align: center;"
                                            class="table_sections table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_outgoing}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                        <td style="width: 12.5%; text-align: right;"
                                            class="table_sections table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_per_ton,2), 2, '.', ' ')}}</td>
                                    </tr>

                                @endforeach
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading">TOTALS</td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td style=" text-align: center;"
                                        class="table_sections table_row_heading">{{ number_format($total_units_incoming, 0, '.', ' ') }}</td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td style=" text-align: center;"
                                        class="table_sections table_row_heading">{{ number_format($total_weight_ton_incoming, 0, '.', ' ') }}</td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td style=" text-align: center;"
                                        class="table_sections table_row_heading">{{ number_format($total_weight_ton_outgoing, 0, '.', ' ') }}</td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_heading"></td>
                                </tr>

                                </tbody>

                            </table>
                        </div>
                        <hr>
                        <div style="margin-top: 1px"></div>

                        <div style="margin-top: 5px"></div>
                        <div class="table_row_heading">Transport notes:</div>

                        <div>
                            <table class="table_sections" style="width:100%; border: 1px solid black;">
                                <tbody>
                                @foreach ($split_data['linked_trans_split'] as $deal)
                                    <tr class="table_sections">
                                        <td style="border: 1px solid black;" class="table_sections table_row_heading"
                                            style="width: 25%; border: 1px solid black;">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="border: 1px solid black;" class="table_sections table_row_value"
                                            colspan="3">{!! nl2br($deal->TransportTransaction->transport_notes) !!}</td>
                                    </tr>

                                @endforeach

                                </tbody>

                            </table>


                        </div>
                        <div style="margin-top: 5px"></div>
                        <div class="table_row_heading">Customer notes:</div>
                        <div>
                            <table class="table_sections" style="width:100%; border: 1px solid black;">
                                <tbody>

                                @foreach ($split_data['linked_trans_split'] as $deal)
                                    <tr class="table_sections">
                                        <td style="border: 1px solid black;" class="table_sections table_row_heading"
                                            style="width: 25%; border: 1px solid black;">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="border: 1px solid black;" class="table_sections table_row_value"
                                            colspan="3">{!! nl2br($deal->TransportTransaction->customer_notes) !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(isset($split_data))

                        <div>
                            @foreach ($split_data['linked_trans_split'] as $deal)

                                <div class="page-break"></div>
                                <div>
                                    <br>
                                    <br>
                                    <li class="section_heading mt-2">Product info
                                        [MQ{{$deal->TransportTransaction->a_mq}}]
                                    </li>
                                    <div class="mt-2">
                                        <table class="table_sections" style="width:50%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Product
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="width: 25%;">{{$deal->TransportTransaction->product->name}}</td>

                                            </tr>

                                            <tr class="table_sections">

                                                <td class="table_sections table_row_heading" style="width: 25%;">Grade
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="width: 25%;">{{$deal->TransportTransaction->TransportLoad->product_grade_perc}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Source
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="width: 25%;">{{$deal->TransportTransaction->TransportLoad->ProductSource->name}}</td>
                                            </tr>

                                            </tbody>

                                        </table>
                                    </div>
                                    <li class="section_heading">Customer Information and Deal Pricing</li>
                                    <hr>
                                    <div>
                                        <table class="table_sections" style="width:100%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Customer
                                                    parent
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->Customer->CustomerParent->last_legal_name}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading">Customer</td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Business
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


                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Packaging
                                                    outgoing
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    {{$deal->TransportTransaction->TransportLoad->PackagingOutgoing->name }}
                                                </td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">VAT
                                                    Exempt
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    @if($deal->TransportTransaction->TransportJob->is_product_zero_rated === 1)
                                                        <span>Yes</span>
                                                    @else
                                                        <span>No</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Billing
                                                    units
                                                    outgoing
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;"
                                                    style="background-color: #62FD473F">
                                                    {{$deal->TransportTransaction->TransportLoad->BillingUnitsIncoming->name}}
                                                </td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">VAT
                                                    cert.
                                                    received
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    @if($deal->TransportTransaction->Customer->is_vat_cert_received === 1)
                                                        <span>Yes</span>
                                                    @else
                                                        <span>No</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Billing
                                                    units
                                                    outgoing
                                                    (qty)
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    {{$deal->TransportTransaction->TransportLoad->no_units_outgoing}}
                                                </td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">Product
                                                    Zero
                                                    Rated
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    @if($deal->TransportTransaction->TransportJob->is_product_zero_rated === 1)
                                                        <span>Yes</span>
                                                    @else
                                                        <span>No</span>
                                                    @endif
                                                </td>
                                            </tr>


                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Selling
                                                    Price /
                                                    Unit
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_unit,2), 2, '.', ' ')}}
                                                </td>
                                                <td class="table_sections table_row_heading">Transport Cost Included
                                                </td>
                                                <td class="table_sections table_row_value">
                                                    @if($deal->TransportTransaction->TransportJob->is_transport_costs_inc_price === 1)
                                                        <span>Yes</span>
                                                    @else
                                                        <span>No</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Total
                                                    Selling
                                                    Price (A)
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price,2), 2, '.', ' ')}}</td>
                                                <td class="table_sections table_row_heading">Terms of payment</td>
                                                <td class="table_sections table_row_value">
                                                    {{$deal->TransportTransaction->Customer->TermsOfPayment->value}}
                                                </td>
                                            </tr>


                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Selling
                                                    Price /
                                                    Metric
                                                    Ton
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">Invoice
                                                    Basis
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    {{$deal->TransportTransaction->Customer->InvoiceBasis->value}}
                                                </td>
                                            </tr>


                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Customer
                                                    offloading #
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}</td>
                                                <td class="table_sections table_row_heading">Customer order #</td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">{{$deal->TransportTransaction->TransportJob->customer_order_number}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Silvergro
                                                    Linked SC #
                                                </td>
                                                <td class="table_sections table_row_value">{{$deal->TransportTransaction->id ?? 'none'}}</td>
                                                <td class="table_sections table_row_heading"></td>
                                                <td class="table_sections table_row_value"></td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Customer
                                                    Notes
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3" style="border: 1px solid black;">
                                                    {!!nl2br($deal->TransportTransaction->customer_notes)!!}
                                                </td>

                                            </tr>

                                            </tbody>

                                        </table>
                                    </div>
                                    <li class="section_heading">Transport</li>
                                    <hr>
                                    <div>

                                        <table class="table_sections" style="width:50%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">
                                                    Transporter
                                                    Name
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->Transporter->last_legal_name}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">Vehicle
                                                    Registration
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">Trailer
                                                    #1
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Trailer
                                                    #2
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">Driver
                                                    Name
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">Driver
                                                    cell
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}} </td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">Weight
                                                    Tons
                                                    Incoming
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="width: 25%;"
                                                    colspan="3">{{$deal->TransportTransaction->TransportFinance->weight_ton_incoming}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 50%;">No. Of
                                                    Loads
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->TransportJob->number_loads}}</td>
                                            </tr>

                                            </tbody>

                                        </table>

                                        <table class="table_sections" style="width:100%;">
                                            <tbody>


                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Transport
                                                    Date
                                                    Earliest
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;"
                                                    style="background-color: #62FD473F">
                                                    {{ $deal->TransportTransaction->transport_date_earliest->format('D d/M/Y')}} </td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Transport
                                                    Date
                                                    Latest<
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    {{$deal->TransportTransaction->transport_date_latest->format('D d/M/Y')}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Rate
                                                    Basis
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;"
                                                    style="background-color: #62FD473F">
                                                    {{$deal->TransportTransaction->TransportFinance->TransportRateBasis->name}} </td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">Rate /
                                                    Ton
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Load
                                                    Tarrif
                                                    (B)
                                                </td>
                                                <td class="table_sections table_row_value">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                                                <td class="table_sections table_row_heading"></td>
                                                <td class="table_sections table_row_value">
                                                </td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Transporter
                                                    Notes
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3" style="border: 1px solid black;">
                                                    {!!nl2br($deal->TransportTransaction->transporter_notes)!!}
                                                </td>

                                            </tr>


                                            </tbody>

                                        </table>

                                    </div>
                                    <li class="section_heading">Supplier Information and Deal Cost</li>
                                    <hr>
                                    <div>

                                        <table class="table_sections" style="width:100%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading">Supplier Name</td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3">{{$deal->TransportTransaction->Supplier->last_legal_name}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Collection
                                                    Address
                                                </td>
                                                <td class="table_sections table_row_value" colspan="3">
                                                    <span>{{$deal->TransportTransaction->TransportLoad->CollectionAddress->line_1}}</span>
                                                    @if($deal->TransportTransaction->TransportLoad->CollectionAddress->line_2)
                                                        ,
                                                        {{$deal->TransportTransaction->TransportLoad->CollectionAddress->line_2}}
                                                        </span>
                                                    @endif
                                                    @if($deal->TransportTransaction->TransportLoad->CollectionAddress->line_3)
                                                        ,
                                                        <span>{{$deal->TransportTransaction->TransportLoad->CollectionAddress->line_3}}</span>
                                                    @endif
                                                    @if($deal->TransportTransaction->TransportLoad->CollectionAddress->country)
                                                        ,
                                                        <span>{{$deal->TransportTransaction->TransportLoad->CollectionAddress->country}}</span>
                                                    @endif
                                                    @if($deal->TransportTransaction->TransportLoad->CollectionAddress->code)
                                                        ,
                                                        <span>{{$deal->TransportTransaction->TransportLoad->CollectionAddress->code}}</span>
                                                    @endif

                                                </td>

                                            </tr>

                                            </tbody>

                                        </table>

                                        <table class="table_sections" style="width:50%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Supplier
                                                    Packaging
                                                    Incoming
                                                </td>
                                                <td class="table_sections table_row_value"
                                                >{{$deal->TransportTransaction->TransportLoad->PackagingIncoming->name }}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Billing
                                                    units
                                                    Incoming
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F"> {{$deal->TransportTransaction->TransportLoad->BillingUnitsOutgoing->name}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Billing
                                                    units
                                                    Incoming
                                                    (qty)
                                                </td>
                                                <td class="table_sections table_row_value"
                                                >  {{$deal->TransportTransaction->TransportLoad->no_units_outgoing}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Cost
                                                    Price /
                                                    Unit
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Cost
                                                    Price /
                                                    Ton
                                                </td>
                                                <td class="table_sections table_row_value"
                                                >
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width:50%;">Total
                                                    cost
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            </tbody>

                                        </table>


                                        <table class="table_sections" style="width:100%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Supplier
                                                    loading #
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;"
                                                    style="background-color: #62FD473F"></td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Silvergro
                                                    Linked PC #<
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    style="width: 25%;">{{$deal->TransportTransaction->id ?? 'none'}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 20%;">
                                                    Supplier
                                                    Notes
                                                </td>
                                                <td class="table_sections table_row_value"
                                                    colspan="3" style="border: 1px solid black;">
                                                    {!!nl2br($deal->TransportTransaction->suppliers_notes)!!}
                                                </td>
                                            </tr>


                                            </tbody>

                                        </table>
                                    </div>
                                    <li class="section_heading">Margin Calculation</li>
                                    <hr>
                                    <div>
                                        <table class="table_sections" style="width:100%;">
                                            <tbody>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Gross
                                                    Profit
                                                    A-B-C
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->gross_profit,2), 2, '.', ' ')}}</td>
                                                <td class="table_sections table_row_heading" style="width: 25%;">
                                                    Additional
                                                    Cost
                                                    1
                                                </td>
                                                <td class="table_sections table_row_value" style="width: 25%;">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_1,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Gross
                                                    Profit /
                                                    Metric
                                                    Ton
                                                </td>
                                                <td class="table_sections table_row_value">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_per_ton,2), 2, '.', ' ')}}</td>
                                                <td class="table_sections table_row_heading">Additional Cost 2</td>
                                                <td class="table_sections table_row_value">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_2,2), 2, '.', ' ')}}</td>
                                            </tr>

                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;">Gross
                                                    Profit
                                                    %
                                                </td>
                                                <td class="table_sections table_row_value">{{number_format(round($deal->TransportTransaction->TransportFinance->gross_profit_percent,2), 2, '.', ' ')}}
                                                    %
                                                </td>
                                                <td class="table_sections table_row_heading">Additional Cost 3</td>
                                                <td class="table_sections table_row_value">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->additional_cost_3,2), 2, '.', ' ')}}</td>
                                            </tr>
                                            <tr class="table_sections">
                                                <td class="table_sections table_row_heading" style="width: 25%;"></td>
                                                <td class="table_sections table_row_value"></td>
                                                <td class="table_sections table_row_heading">Total Cost</td>
                                                <td class="table_sections table_row_value"
                                                    style="background-color: #62FD473F">
                                                    R {{number_format(round($deal->TransportTransaction->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                                            </tr>


                                            </tbody>

                                        </table>
                                    </div>
                                    <div style="">
                                    </div>
                                </div>

                            @endforeach
                        </div>

                    @endif

                @endif

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
