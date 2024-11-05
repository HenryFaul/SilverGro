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


    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    <table style="width:100%;">
        <tr>
            <td>
                <img style="float: left;" src="{{ $logo }}" width="225" height="105"/>
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
                <br>
                <li class="section_heading">Product Information</li>

                <div>
                    <table class="table_sections" style="width:50%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;">{{$transport_trans->product->name}}</td>

                        </tr>

                        <tr class="table_sections">

                            <td class="table_sections table_row_heading" style="width: 25%;">Grade</td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;">{{$transport_trans->TransportLoad->product_grade_perc}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Source</td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;">{{$transport_trans->TransportLoad->ProductSource->name}}</td>
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
                            <td class="table_sections table_row_heading" style="width: 25%;">Packaging outgoing
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->TransportLoad->PackagingOutgoing->name }}
                            </td>
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
                            <td class="table_sections table_row_heading" style="width: 25%;">Billing units outgoing
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;"
                                style="background-color: #62FD473F">
                                {{$transport_trans->TransportLoad->BillingUnitsIncoming->name}}
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">VAT cert. received</td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                @if($transport_trans->Customer->is_vat_cert_received === 1)
                                    <span>Yes</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Billing units outgoing
                                (qty)
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->TransportLoad->no_units_outgoing}}
                            </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Product Zero Rated</td>
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
                            <td class="table_sections table_row_value" style="background-color: #62FD473F">
                                R {{number_format(round($transport_trans->TransportFinance->selling_price_per_unit,2), 2, '.', ' ')}}
                            </td>
                            <td class="table_sections table_row_heading">Transport Cost Included</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->TransportJob->is_transport_costs_inc_price === 1)
                                    <span>Yes</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Total Selling Price (A)
                            </td>
                            <td class="table_sections table_row_value" style="background-color: #62FD473F">
                                R {{number_format(round($transport_trans->TransportFinance->selling_price,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">Terms of payment</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->Customer->TermsOfPayment->value}}
                            </td>
                        </tr>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Selling Price / Metric
                                Ton
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                R {{number_format(round($transport_trans->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Invoice Basis</td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->Customer->InvoiceBasis->value}}
                            </td>
                        </tr>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Customer offloading #</td>
                            <td class="table_sections table_row_value"
                                style="background-color: #62FD473F">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->driver_vehicle_loading_number}}</td>
                            <td class="table_sections table_row_heading">Customer order #</td>
                            <td class="table_sections table_row_value"
                                style="background-color: #62FD473F">{{$transport_trans->TransportJob->customer_order_number}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Silvergro Linked SC #</td>
                            <td class="table_sections table_row_value">{{$linked_trans_sc->id ?? 'none'}}</td>
                            <td class="table_sections table_row_heading"></td>
                            <td class="table_sections table_row_value"></td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Customer Notes</td>
                            <td class="table_sections table_row_value"
                                colspan="3" style="border: 1px solid black;">
                                {!!nl2br($transport_trans->customer_notes)!!}
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
                            <td class="table_sections table_row_heading" style="width: 50%;">Transporter Name</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
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
                            <td class="table_sections table_row_heading" style="width: 50%;">Weight Tons Incoming
                            </td>
                            <td class="table_sections table_row_value"
                                style="width: 25%;"
                                colspan="3">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 50%;">No. Of Loads</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->TransportJob->number_loads}}</td>
                        </tr>

                        </tbody>

                    </table>

                    <table class="table_sections" style="width:100%;">
                        <tbody>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Earliest
                            </td>
                            <td class="table_sections table_row_value" style="width: 25%;"
                                style="background-color: #62FD473F">
                                {{ $transport_trans->transport_date_earliest->format('D d/M/Y')}} </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Latest<</td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                {{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Rate Basis</td>
                            <td class="table_sections table_row_value" style="width: 25%;"
                                style="background-color: #62FD473F">
                                {{$transport_trans->TransportFinance->TransportRateBasis->name}} </td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Rate / Ton<</td>
                            <td class="table_sections table_row_value" style="width: 25%;">
                                R {{number_format(round($transport_trans->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Load Tarrif (B)</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading"></td>
                            <td class="table_sections table_row_value">
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Transporter Notes</td>
                            <td class="table_sections table_row_value"
                                colspan="3" style="border: 1px solid black;">
                                {!!nl2br($transport_trans->transporter_notes)!!}
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

                        </tbody>

                    </table>

                    <table class="table_sections" style="width:50%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Supplier Packaging
                                Incoming
                            </td>
                            <td class="table_sections table_row_value"
                            >{{$transport_trans->TransportLoad->PackagingIncoming->name }}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Billing units Incoming</td>
                            <td class="table_sections table_row_value"
                                style="background-color: #62FD473F"> {{$transport_trans->TransportLoad->BillingUnitsOutgoing->name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Billing units Incoming
                                (qty)
                            </td>
                            <td class="table_sections table_row_value"
                            >  {{$transport_trans->TransportLoad->no_units_outgoing}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Cost Price / Unit</td>
                            <td class="table_sections table_row_value"
                                style="background-color: #62FD473F">
                                R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Cost Price / Ton</td>
                            <td class="table_sections table_row_value"
                            >
                                R {{number_format(round($transport_trans->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:50%;">Total cost</td>
                            <td class="table_sections table_row_value"
                                style="background-color: #62FD473F">
                                R {{number_format(round($transport_trans->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                        </tr>

                        </tbody>

                    </table>


                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier loading #</td>
                            <td class="table_sections table_row_value" style="width: 25%;"
                                style="background-color: #62FD473F"></td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Silvergro Linked PC #<</td>
                            <td class="table_sections table_row_value" style="width: 25%;">{{$linked_trans_pc->id ?? 'none'}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Supplier Notes</td>
                            <td class="table_sections table_row_value"
                                colspan="3" style="border: 1px solid black;">
                                {!!nl2br($transport_trans->suppliers_notes)!!}
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
                            <td class="table_sections table_row_value" style="background-color: #62FD473F">
                                R {{number_format(round($transport_trans->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <div style="">
                </div>
                <li class="section_heading">Approvals</li>
                <hr>
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
