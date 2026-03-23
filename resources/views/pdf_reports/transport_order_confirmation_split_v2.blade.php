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
        <div style="margin-top: 0px;">
            <table style="width:100%">
                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 12px;">
                        <b style="font-size: 16px"><span>Split Transport Order Confirmation:</span>
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
                        <h5 style="margin: 0; padding: 0;">SPLIT LOAD MASTER
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
                                <td class="table_sections table_row_heading" style="width: 25%;">Transporter Name:</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    @if($split_data['is_transporter_same'])
                                        <strong style="font-size: 14px;">{{$split_data['primary_linked_trans_split']->Transporter->last_legal_name}}</strong>
                                    @else
                                        <strong style="font-size: 14px; color: red;">Different transporter - align in each trade</strong>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Business Address:</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    @if(!$split_data['split_transporter_address'])
                                        <span>No physical address loaded...</span>
                                    @else
                                        <span>{{$split_data['split_transporter_address']->line_1}}</span>
                                        @if($split_data['split_transporter_address']->line_2)
                                            ,
                                            <span>{{$split_data['split_transporter_address']->line_2}}</span>
                                        @endif
                                        @if($split_data['split_transporter_address']->line_3)
                                            ,
                                            <span>{{$split_data['split_transporter_address']->line_3}}</span>
                                        @endif
                                        @if($split_data['split_transporter_address']->country)
                                            ,
                                            <span>{{$split_data['split_transporter_address']->country}}</span>
                                        @endif
                                        @if($split_data['split_transporter_address']->code)
                                            ,
                                            <span>{{$split_data['split_transporter_address']->code}}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Att:</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    @if($split_data['primary_linked_trans_split']->Transporter->contactable=="[]")
                                        <span>No contact loaded</span>
                                    @else
                                        @foreach($split_data['primary_linked_trans_split']->Transporter->contactable as $contact)
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

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Supplier:</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    @if($split_data['is_supplier_same'])
                                        <strong>{{$split_data['primary_linked_trans_split']->Supplier->last_legal_name}}</strong>
                                    @else
                                        <strong style="color: red;">Different supplier - align in each trade</strong>
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Supplier Collection Address:
                                </td>
                                <td class="table_sections table_row_value" colspan="3">
                                    @if(!$split_data['is_supplier_same'])
                                        <strong style="color: red;">Different supplier - align in each trade</strong>
                                    @else
                                    <span>{{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_1}}</span>
                                    @if($split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_2)
                                        , {{$split_data['primary_linked_trans_split']->TransportLoad->CollectionAddress->line_2}}
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
                                    @endif
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Transport date
                                    earliest
                                </td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    {{ $split_data['primary_linked_trans_split']->transport_date_earliest ? $split_data['primary_linked_trans_split']->transport_date_earliest->format('D d/M/Y') : 'No date Selected' }}
                                </td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Transport date latest
                                </td>
                                <td class="table_sections table_row_value" style="width: 25%;">
                                    {{ $split_data['primary_linked_trans_split']->transport_date_latest->format('D d/M/Y')}}
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Vehicle reg</td>
                                <td class="table_sections table_row_value"
                                    colspan="3">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Trailer #1</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Trailer #2</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Driver</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Cell</td>
                                <td class="table_sections table_row_value"
                                    style="width: 25%;">{{$split_data['primary_linked_trans_split']->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}}</td>
                            </tr>

                            </tbody>

                        </table>
                    </div>

                    @if(isset($split_data))
                        <hr>
                        <li class="section_heading">Split Summary Overview</li>
                        <div>
                            <table class="table_sections_bordered" style="width:100%;">
                                <tbody>
                                <tr>
                                    <td style="width: 10%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Transport
                                        Order
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Supplier
                                    </td>
                                    <td style="width: 10%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Supplier
                                        Loading #
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Customer
                                    </td>
                                    <td style="width: 10%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Cust Order
                                        #
                                    </td>
                                    <td style="width: 10%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Cust
                                        Offloading #
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Transporter
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Product
                                    </td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)

                                    <tr style="width:100%;">
                                        <td style="text-align: left;" class="table_sections_bordered table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Supplier->last_legal_name}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportJob->supplier_loading_number}}</td>

                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Customer->last_legal_name}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportJob->customer_order_number}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Transporter->last_legal_name}}</td>
                                        <td style="text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
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

                                <!-- First Table: Product and Load Details -->
                            <br>
                            <table class="table_sections_bordered" style="width:100%; margin-bottom: 5px;">
                                <tbody>
                                <tr>
                                    <td style="width: 14%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Transport Order
                                    </td>
                                    <td style="width: 18%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Product
                                    </td>
                                    <td style="width: 16%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Billing Units
                                    </td>
                                    <td style="width: 18%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Packaging Incoming
                                    </td>
                                    <td style="width: 12%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">BU In
                                    </td>
                                    <td style="width: 11%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Cost PU
                                    </td>
                                    <td style="width: 11%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Cost PT
                                    </td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)
                                    <tr style="width:100%;">
                                        <td style="width: 14%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 18%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                                        <td style="width: 16%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->BillingUnitsIncoming->name}}</td>
                                        <td style="width: 18%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->PackagingIncoming->name }}</td>
                                        <td style="width: 12%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->no_units_incoming}}</td>
                                        <td style="width: 11%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R{{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                                        <td style="width: 11%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R{{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td style="text-align: left;" class="table_sections_bordered table_row_heading">
                                        TOTALS
                                    </td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading">{{ number_format($total_units_incoming, 0, '.', ' ') }}</td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                </tr>
                                </tbody>
                            </table>

                            <!-- Second Table: Transport and Pricing Details -->
                            <table class="table_sections_bordered" style="width:100%;">
                                <tbody>
                                <tr>
                                    <td style="width: 14%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">
                                        Transport Order
                                    </td>
                                    <td style="width: 12%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Planned TI
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Transport Rate
                                    </td>
                                    <td style="width: 15%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Load Rate
                                    </td>
                                    <td style="width: 16%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Effective Rate/Ton
                                    </td>
                                    <td style="width: 14%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Planned TO
                                    </td>
                                    <td style="width: 14%; text-align: left;"
                                        class="table_sections_bordered table_row_heading">Selling Ton
                                    </td>
                                </tr>

                                @foreach ($split_data['linked_trans_split'] as $deal)
                                    <tr style="width:100%;">
                                        <td style="width: 14%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            MQ{{$deal->TransportTransaction->a_mq}}</td>
                                        <td style="width: 12%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_incoming}}</td>
                                        <td style="width: 15%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}</td>
                                        <td style="width: 15%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportFinance->TransportRateBasis->name}}</td>
                                        <td style="width: 16%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                                        <td style="width: 14%; text-align: left;"
                                            class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportFinance->weight_ton_outgoing}}</td>
                                        <td style="width: 14%; text-align: left;"
                                            class="table_sections_bordered table_row_value">
                                            R {{number_format(round($deal->TransportTransaction->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td style="text-align: left;" class="table_sections_bordered table_row_heading">
                                        TOTALS
                                    </td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading">{{ number_format($total_weight_ton_incoming, 0, '.', ' ') }}</td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading">{{ number_format($total_weight_ton_outgoing, 0, '.', ' ') }}</td>
                                    <td style="text-align: left;"
                                        class="table_sections_bordered table_row_heading"></td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                        <hr>
                        <div style="margin-top: 2px; margin-bottom: 2px;" class="table_row_heading">Transport notes:
                        </div>

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

                    @endif

                    @if(isset($split_data))

                        <div>
                            @foreach ($split_data['linked_trans_split'] as $deal)

                                <div class="page-break"></div>

                                <div>
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
                                                            <td class="table_sections table_row_heading"
                                                                style="width:25%;">Transporter
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width:25%;">Business address
                                                            </td>
                                                            <td class="table_sections table_row_value" colspan="3">
                                                                @if($primaryPhysicalAddress)
                                                                    {{ $primaryPhysicalAddress->line_1 }}
                                                                    @if($primaryPhysicalAddress->line_2)
                                                                        , {{ $primaryPhysicalAddress->line_2 }}
                                                                    @endif
                                                                    @if($primaryPhysicalAddress->line_3)
                                                                        , {{ $primaryPhysicalAddress->line_3 }}
                                                                    @endif
                                                                    @if($primaryPhysicalAddress->country)
                                                                        , {{ $primaryPhysicalAddress->country }}
                                                                    @endif
                                                                    @if($primaryPhysicalAddress->code)
                                                                        , {{ $primaryPhysicalAddress->code }}
                                                                    @endif
                                                                @else
                                                                    <span>No Primary Physical Address Loaded</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Att
                                                            </td>
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
                                                <li class="section_heading" style="margin-top: 4px;">Transport Details
                                                </li>
                                                <div>
                                                    <table class="table_sections" style="width:100%;">
                                                        <tbody>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Date Earliest
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%; white-space: nowrap;">{{ $transport_trans->transport_date_earliest ? $transport_trans->transport_date_earliest->format('D d/M/Y') : 'No date Selected' }}</td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Date Latest
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%; white-space: nowrap;">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">No. Of Loads
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->number_loads}}</td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Vehicle Type
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->VehicleType->name}}</td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Vehicle Reg
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Driver
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Trailer #1 Reg.
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">
                                                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1)
                                                                    #1 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}
                                                                @endif
                                                            </td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Trailer #2 Reg.
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">
                                                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2)
                                                                    #2 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Driver Cell
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}}</td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Driver Comment
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->comment}}</td>
                                                        </tr>


                                                        </tbody>

                                                    </table>
                                                </div>
                                                <li class="section_heading" style="margin-top: 4px;">Product & Tariff
                                                    Detail
                                                </li>
                                                <div>
                                                    <table class="table_sections" style="width:100%;">
                                                        <tbody>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Product
                                                            </td>
                                                            <td class="table_sections table_row_value">{{$transport_trans->product->name}}</td>
                                                            <td class="table_sections table_row_heading">Weight planned
                                                                (tons)
                                                            </td>
                                                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Package Incoming
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportLoad->PackagingIncoming->name}}
                                                            </td>
                                                            <td class="table_sections table_row_heading">Package
                                                                Outgoing
                                                            </td>
                                                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Transport Rate Basis
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="background-color: #62FD473F">
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
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Rate per load
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Load Insurance / Ton
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}}
                                                            </td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Terms of payment
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->Transporter->TermsOfPayment->value}}
                                                            </td>
                                                        </tr>

                                                        </tbody>

                                                    </table>
                                                </div>
                                                <li class="section_heading" style="margin-top: 4px;">Loading Detail</li>
                                                <div class="">
                                                    <table class="table_sections" style="width:100%;">
                                                        <tbody>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Supplier name
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                style="width: 25%;">{{$transport_trans->Supplier->last_legal_name}}</td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Collection Address
                                                            </td>
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

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Supplier loading number
                                                            </td>

                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->supplier_loading_number}}
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Loading hours from
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->LoadingHoursFrom->name}}
                                                                HRS
                                                            </td>

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Loading hours to
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->LoadingHoursTo->name}}
                                                                HRS
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Supplier contact:
                                                            </td>
                                                            <td class="table_sections table_row_value" colspan="3">
                                                                <span>{{$transport_trans->TransportJob->loading_contact}} {{$transport_trans->TransportJob->loading_contact_no}}</span>
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Loading instructions
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                colspan="3">{{$transport_trans->TransportJob->loading_instructions}}</td>
                                                        </tr>


                                                        </tbody>

                                                    </table>
                                                </div>
                                                <li class="section_heading" style="margin-top: 4px;">Offloading Detail
                                                </li>
                                                <div class="">
                                                    <table class="table_sections" style="width:100%;">
                                                        <tbody>
                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Customer name
                                                            </td>
                                                        </tr>


                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Delivery Address
                                                            </td>
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

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Customer order number
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->customer_order_number}}
                                                            </td>

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Customer offloading number
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Offloading hours from
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->OffloadingHoursFrom->name}}
                                                                HRS
                                                            </td>
                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Offloading hours from
                                                            </td>
                                                            <td class="table_sections table_row_value">
                                                                {{$transport_trans->TransportJob->OffloadingHoursTo->name}}
                                                                HRS
                                                            </td>
                                                        </tr>


                                                        <tr class="table_sections">

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Customer contact:
                                                            </td>
                                                            <td class="table_sections table_row_value" colspan="3">
                                                                <span>{{$transport_trans->TransportJob->offloading_contact}} {{$transport_trans->TransportJob->offloading_contact_no}}</span>
                                                            </td>
                                                        </tr>

                                                        <tr class="table_sections">

                                                            <td class="table_sections table_row_heading"
                                                                style="width: 25%;">Offloading instructions
                                                            </td>
                                                            <td class="table_sections table_row_value"
                                                                colspan="3">{{$transport_trans->TransportJob->offloading_instructions}}</td>
                                                        </tr>


                                                        </tbody>

                                                    </table>
                                                </div>

                                            </ol>


                                        </div>

                                    </div>
                                </div>

                            @endforeach
                        </div>

                    @endif

                    <div class="page-break"></div>
                    <div>
                        <li class="section_heading" style="margin-top: 4px; margin-bottom: 2px;">Standard Terms and
                            Conditions
                        </li>
                        <div class="table_row_value">

                            <div class="table_row_value">
                                <div class="table_row_value">
                                    <ol class="indented-list">
                                        <li>
                                            <strong>Transport Rate:</strong> The rate is valid for the completion of
                                            this
                                            transport order on the tonnage
                                            agreed upon.
                                        </li>

                                        <li>
                                            <strong>Payment Terms:</strong> Payment will be made on receiving the
                                            transporter's
                                            invoice with original
                                            documentation, normally within 14 working days from the date of receipt of
                                            the
                                            original invoice(s), and
                                            based on upload weight.
                                        </li>

                                        <li>
                                            <strong>Original Documentation:</strong> Payment will only be made upon
                                            receiving
                                            the transporter’s PODs,
                                            the original invoice with all original loading documentation, as well as
                                            original
                                            offloading documentation.
                                        </li>

                                        <li>
                                            <strong>Load Insurance:</strong> The transporter confirms that the value of
                                            the
                                            load(s), calculated at the
                                            selling value of the commodity, that is
                                            <strong>R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}}</strong>
                                            per mt, is fully insured by the Transport Company / CC / Other for all
                                            possible
                                            risks.
                                        </li>

                                        <li>
                                            <strong>Delivery Note Requirements:</strong> Transporter to provide the
                                            customer
                                            ONLY with a delivery note
                                            containing the following information: Supplier Name - SilverGro Feed and
                                            Grain,
                                            Customer Name as per the
                                            Transport Order, Transport Order Number, Product Description, Upload
                                            Weighbridge
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
                        <li class="section_heading" style="margin-top: 4px; margin-bottom: 2px;">Special Notes</li>
                        <div class="table_row_value">

                            <div class="table_row_value">
                                <div class="table_row_value">
                                    <ol class="indented-list">
                                        @if($transport_trans->transport_notes)
                                            <li>
                                                <strong>Transport Notes:</strong> {{$transport_trans->transport_notes}}
                                            </li>
                                        @endif
                                        @if(str_contains(strtolower($transport_trans->product->name), 'bagged'))
                                            <li>
                                                Customer to check for broken or wet bags and make comments with
                                                qualities on delivery documentation, and bring this to the transporters
                                                attention.
                                            </li>
                                            <li>
                                                If any bags are broken or goods defective, kindly contact Silvergro Feed
                                                & Grain immediately.

                                            </li>
                                            <li>
                                                Driver and Customer to do a bag count and sign for goods on the
                                                transporters delivery documentation / Proof of Delivery (POD).
                                            </li>

                                        @endif

                                        @if(str_contains(strtolower($transport_trans->product->name), 'bagged') && str_contains(strtolower($transport_trans->product->name), 'chop'))
                                            <li>
                                                Customer to use with 7 days of delivery.
                                            </li>
                                        @endif

                                        @if(str_contains(strtolower($transport_trans->TransportLoad->ProductSource->name), 'import'))
                                            <li>
                                                In the event that goods described in this contract are to be delivered
                                                out of an African territory, such as Malawi, Zimbabwe, Zambia or
                                                Mozambique, no warranty is given in regard to delivery or delivery time.
                                                It is hereby agreed that there can be no claim for late, or non delivery
                                                by the Seller.

                                            </li>
                                        @endif
                                    </ol>
                                </div>

                            </div>
                        </div>

                        <p class="section_heading" style="margin-top: 4px; margin-bottom: 2px;">Prepared for Silvergro
                            Feed & Grain by {{$user_name}} at {{$now}}
                            <span></span></p>
                        <div>
                            <table class="" style="width:100%;">
                                <tbody>
                                <tr class="">
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <br>
                                        <hr>
                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <br>

                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
                                        <br>
                                        <br>

                                    </td>
                                    <td class=" table_row_value" style="width:25%;">
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
                    </div>

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
