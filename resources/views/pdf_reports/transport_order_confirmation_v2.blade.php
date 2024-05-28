<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Transport order confirmation</title>
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
                        <b><span>Transport Order Confirmation:</span> <span>TOC</span>
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
                @if($transport_trans->is_split_load)
                    <h4>SPLIT LOAD DEAL</h4>
                @endif

                <li class="section_heading">Transporter Details</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width:25%;">Transporter</td>
                            <td class="table_sections table_row_value"
                                colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Att</td>
                            <td class="table_sections table_row_value" colspan="3">
                                @if($transport_trans->Transporter->contactable=="[]")
                                    <span>No contact loaded</span>
                                @else
                                    @foreach($transport_trans->Transporter->contactable as $contact)
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

                </div>
                <br>
                <li class="section_heading">Product & Tariff Detail</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Product</td>
                            <td class="table_sections table_row_value">{{$transport_trans->product->name}}</td>
                            <td class="table_sections table_row_heading">Weight (tons)</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Rate Basis</td>
                            <td class="table_sections table_row_value" style="background-color: #fde047">
                                {{$transport_trans->TransportFinance->TransportRateBasis->name}}
                            </td>
                            <td class="table_sections table_row_heading">Rate / Ton</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->transport_rate_per_ton,2), 2, '.', ' ')}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Load Insurance / Ton</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}}
                            </td>
                            <td class="table_sections table_row_heading">Confirmed By</td>
                            <td class="table_sections table_row_value">
                                {{$transport_order->ConfirmedByType->name}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Terms of payment</td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->Transporter->TermsOfPayment->value}}
                            </td>
                            <td class="table_sections table_row_heading">Package</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Rate per load</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}
                            </td>
                            <td class="table_sections table_row_heading"></td>
                            <td class="table_sections table_row_value"></td>
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
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Earliest
                            </td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_earliest->format('D d/M/Y')}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Transport Date Latest</td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest->format('D d/M/Y')}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">No. Of Loads</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->number_loads}}</td>
                            <td class="table_sections table_row_heading">Driver</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->first_name}} {{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->last_name}}</td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Driver Cell</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Driver->cell_no}}</td>
                            <td class="table_sections table_row_heading">Vehicle Reg:</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->reg_no}}</td>

                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Vehicle Type</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->TransportDriverVehicle[0]->Vehicle->VehicleType->name}}</td>
                            <td class="table_sections table_row_heading">Trailers:</td>
                            <td class="table_sections table_row_value">

                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1)
                                    <span >#1 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_1}}</span>
                                @endif

                                @if($transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2)
                                        <span>#2 {{$transport_trans->TransportJob->TransportDriverVehicle[0]->trailer_reg_2}}</span>
                                 @endif
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
                            <td class="table_sections table_row_value" style="width: 25%;">{{$transport_trans->Supplier->last_legal_name}}</td>
                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier loading number
                            </td>
                            <td class="table_sections table_row_value">
                                {{$transport_trans->TransportJob->supplier_loading_number}}
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Collection from</td>
                            <td class="table_sections table_row_value" >
                                {{$transport_trans->TransportJob->LoadingHoursFrom->name}} HRS
                            </td>

                            <td class="table_sections table_row_heading" style="width: 25%;">Collection to</td>
                            <td class="table_sections table_row_value" >
                                {{$transport_trans->TransportJob->LoadingHoursTo->name}} HRS
                            </td>
                        </tr>


                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Collection Address</td>
                            <td class="table_sections table_row_value" colspan="3">
                                <span>{{$transport_trans->TransportLoad->CollectionAddress->line_1}}</span>
                                <br>
                                @if($transport_trans->TransportLoad->CollectionAddress->line_2)
                                    <span>
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_2}}
                                </span>
                                @endif

                                @if($transport_trans->TransportLoad->CollectionAddress->line_3)
                                    <span>
                                    {{$transport_trans->TransportLoad->CollectionAddress->line_3}}
                                </span>
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
                            <td class="table_sections table_row_heading" style="width: 25%;">Loading instructions
                            </td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->TransportJob->loading_instructions}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 25%;">Supplier contact:
                            </td>
                            <td class="table_sections table_row_value" colspan="3">
                               <span>{{$transport_trans->TransportJob->loading_contact}} {{$transport_trans->TransportJob->loading_contact_no}}</span>
                            </td>
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
                                <td class="table_sections table_row_value" style="width: 25%;">{{$transport_trans->Customer->last_legal_name}}</td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Customer loading number
                                </td>
                                <td class="table_sections table_row_value">
                                    {{$transport_trans->TransportJob->customer_order_number}}
                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Delivery from</td>
                                <td class="table_sections table_row_value">
                                    {{$transport_trans->TransportJob->OffloadingHoursFrom->name}} HRS
                                </td>
                                <td class="table_sections table_row_heading" style="width: 25%;">Delivery to</td>
                                <td class="table_sections table_row_value">
                                    {{$transport_trans->TransportJob->OffloadingHoursTo->name}} HRS
                                </td>
                            </tr>


                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Delivery Address</td>
                                <td class="table_sections table_row_value" colspan="3">
                                    <span>{{$transport_trans->TransportLoad->DeliveryAddress->line_1}}</span>
                                    <br>
                                    @if($transport_trans->TransportLoad->DeliveryAddress->line_2)
                                        <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_2}}
                                </span>
                                    @endif

                                    @if($transport_trans->TransportLoad->DeliveryAddress->line_3)
                                        <span>
                                    {{$transport_trans->TransportLoad->DeliveryAddress->line_3}}
                                </span>
                                    @endif

                                    @if($transport_trans->TransportLoad->DeliveryAddress->country)
                                        <span>{{$transport_trans->TransportLoad->DeliveryAddress->country}}</span>
                                    @endif

                                    @if($transport_trans->TransportLoad->DeliveryAddress->code)
                                        <span>{{$transport_trans->TransportLoad->DeliveryAddress->code}}</span>
                                    @endif

                                </td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Offloading instructions
                                </td>
                                <td class="table_sections table_row_value" colspan="3">{{$transport_trans->TransportJob->offloading_instructions}}</td>
                            </tr>

                            <tr class="table_sections">
                                <td class="table_sections table_row_heading" style="width: 25%;">Customer contact:
                                </td>
                                <td class="table_sections table_row_value" colspan="3">
                                    <span>{{$transport_trans->TransportJob->offloading_contact}} {{$transport_trans->TransportJob->offloading_contact_no}}</span>
                                </td>
                            </tr>


                            </tbody>

                        </table>
                    </div>

                    @if($transport_trans->is_split_load)
                        <br>
                    <li class="section_heading">Split Load Details</li>
                    @if($transport_trans->Customer_2->last_legal_name != 'Unallocated')
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width:25%;">Split Customer 2</td>
                                    <td class="table_sections table_row_value"
                                        colspan="3">{{$transport_trans->Customer_2->last_legal_name}}</td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading">Delivery Address</td>
                                    <td class="table_sections table_row_value" colspan="3">
                                    <span>
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            {{$transport_trans->TransportLoad->DeliveryAddress_2->line_1}}
                                        @endif
                                    </span>

                                         @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                             <span>{{$transport_trans->TransportLoad->DeliveryAddress_2->line_2}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_2->line_3}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_2->country}}</span>
                                            @endif
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_2->code}}</span>
                                            @endif
                                    </td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Selling Price Split
                                    </td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->selling_price_2,2), 2, '.', ' ')}}
                                    </td>
                                    <td class="table_sections table_row_heading">Transport Cost Split</td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->transport_cost_2,2), 2, '.', ' ')}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Unit Split</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportLoad->no_units_outgoing_2}}
                                    </td>
                                    <td class="table_sections table_row_heading">Customer order no</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->customer_order_number_2}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Supplier loading no
                                    </td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->supplier_loading_number_2}}
                                    </td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_value">

                                    </td>
                                </tr>

                                </tbody>

                            </table>
                        </div>
                        <br>
                    @endif
                    @if($transport_trans->Customer_3->last_legal_name != 'Unallocated')
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width:25%;">Split Customer 3</td>
                                    <td class="table_sections table_row_value"
                                        colspan="3">{{$transport_trans->Customer_3->last_legal_name}}</td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading">Delivery Address</td>
                                    <td class="table_sections table_row_value" colspan="3">
                                    <span>
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_3))
                                            {{$transport_trans->TransportLoad->DeliveryAddress_3->line_1}}
                                        @endif
                                    </span>
                                         @if(isset($transport_trans->TransportLoad->DeliveryAddress_3))
                                             <span> {{$transport_trans->TransportLoad->DeliveryAddress_3->line_2}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            <span> {{$transport_trans->TransportLoad->DeliveryAddress->line_3}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                                <span> {{$transport_trans->TransportLoad->DeliveryAddress->country}} </span>
                                            @endif
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_2))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress->code}}</span>
                                            @endif
                                    </td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Selling Price
                                        Split
                                    </td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->selling_price_2,2), 2, '.', ' ')}}
                                    </td>
                                    <td class="table_sections table_row_heading">Transport Cost Split</td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->transport_cost_2,2), 2, '.', ' ')}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Unit Split</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportLoad->no_units_outgoing_2}}
                                    </td>
                                    <td class="table_sections table_row_heading">Customer order no</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->customer_order_number_2}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Supplier
                                        loading no
                                    </td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->supplier_loading_number_2}}
                                    </td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_value">

                                    </td>
                                </tr>


                                </tbody>

                            </table>
                        </div>
                        <br>
                    @endif
                    @if($transport_trans->Customer_4->last_legal_name != 'Unallocated')
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading"  style="width:25%;">Split Customer 4</td>
                                    <td class="table_sections table_row_value"
                                        colspan="3">{{$transport_trans->Customer_4->last_legal_name}}</td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading">Delivery Address</td>
                                    <td class="table_sections table_row_value" colspan="3">
                                    <span>
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_4))
                                            {{$transport_trans->TransportLoad->DeliveryAddress_4->line_1}}
                                        @endif
                                    </span>

                                         @if(isset($transport_trans->TransportLoad->DeliveryAddress_4))
                                             <span>{{$transport_trans->TransportLoad->DeliveryAddress_4->line_2}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_4))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_4->line_3}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_4))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_4->country}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_4))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_4->code}}</span>
                                            @endif
                                    </td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Selling Price
                                        Split
                                    </td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->selling_price_4,2), 2, '.', ' ')}}
                                    </td>
                                    <td class="table_sections table_row_heading">Transport Cost Split</td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->transport_cost_4,2), 2, '.', ' ')}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Unit Split</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportLoad->no_units_outgoing_4}}
                                    </td>
                                    <td class="table_sections table_row_heading">Customer order no</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->customer_order_number_4}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Supplier
                                        loading no
                                    </td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->supplier_loading_number_4}}
                                    </td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_value">

                                    </td>
                                </tr>


                                </tbody>

                            </table>
                        </div>
                        <br>
                    @endif
                    @if($transport_trans->Customer_5->last_legal_name != 'Unallocated')
                        <div>
                            <table class="table_sections" style="width:100%;">
                                <tbody>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width:25%;" >Split Customer 5</td>
                                    <td class="table_sections table_row_value"
                                        colspan="3">{{$transport_trans->Customer_5->last_legal_name}}</td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading">Delivery Address</td>
                                    <td class="table_sections table_row_value" colspan="3">
                                    <span>
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_5))
                                            {{$transport_trans->TransportLoad->DeliveryAddress_5->line_1}}
                                        @endif
                                    </span>

                                         @if(isset($transport_trans->TransportLoad->DeliveryAddress_5))
                                             <span>{{$transport_trans->TransportLoad->DeliveryAddress_5->line_2}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_5))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_5->line_3}}</span>
                                            @endif
                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_5))
                                            <span>{{$transport_trans->TransportLoad->DeliveryAddress_5->country}}</span>
                                            @endif

                                        @if(isset($transport_trans->TransportLoad->DeliveryAddress_5))
                                            <span> {{$transport_trans->TransportLoad->DeliveryAddress_5->code}}</span>
                                            @endif

                                    </td>
                                </tr>
                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Selling Price
                                        Split
                                    </td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->selling_price_5,2), 2, '.', ' ')}}
                                    </td>
                                    <td class="table_sections table_row_heading">Transport Cost Split</td>
                                    <td class="table_sections table_row_value">
                                        R {{number_format(round($transport_trans->TransportFinance->transport_cost_5,2), 2, '.', ' ')}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Unit Split</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportLoad->no_units_outgoing_5}}
                                    </td>
                                    <td class="table_sections table_row_heading">Customer order no</td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->customer_order_number_5}}
                                    </td>
                                </tr>

                                <tr class="table_sections">
                                    <td class="table_sections table_row_heading" style="width: 25%;">Supplier
                                        loading no
                                    </td>
                                    <td class="table_sections table_row_value">
                                        {{$transport_trans->TransportJob->supplier_loading_number_5x}}
                                    </td>
                                    <td class="table_sections table_row_heading"></td>
                                    <td class="table_sections table_row_value">

                                    </td>
                                </tr>


                                </tbody>

                            </table>
                        </div>
                        <br>
                    @endif
                @endif


                    <br>
                <div class="table_row_value">
                    <ol>
                        <li>
                            Transport Rate:- The rate is valid for the completion of this transport order on the tonnage agreed upon.
                        </li>

                        <li>
                            Payment Terms:- will be made on receiving of transport's invoice with original documentation, normally within 14 working days from date of receipt of the original invoice/s,
                            and based on upload weight.
                        </li>

                        <li>
                            Original Documentation:- Payment will only be on receiving of the transporter's POD's, the original invoice with all original loading documentation as well as original
                            offloading documentation.
                        </li>

                        <li>
                            Load Insurance: The transporter confirms that the value of the load(s), calculated at the selling value of the commodity, that is R {{number_format(round($transport_trans->TransportFinance->load_insurance_per_ton,2), 2, '.', ' ')}} per mt, is fully insured by the
                            Transport Company / CC / Other for all possible risks.
                        </li>

                        <li>
                            Transporter to provide customer ONLY a delivery note with the following information: Supplier Name - SilverGro Feed and Grain, Customer Name as per the Transport Order,
                            Transport Order Number, The Product Description, Upload Weighbridge Weight and Weighbridge No, Truck Registration, Driver Name, Delivery Date, Delivery Time, Customer
                            Signature.
                        </li>

                        <li>
                            Contract Stipulations:- Please ensure you are familiar with SilverGro Feed & Grain's standard transport contract
                        </li>

                        @if(str_contains(strtolower($transport_trans->product->name), 'bagged'))

                            <li>
                                Transporter to check for broken or wet bags and make comments with qualities on delivery documentation, and bring this to the suppliers attention.
                            </li>

                            <li>
                                If any bags are found broken, wet, or goods defective at any time, transporter to contact Silvergro Feed & Grain immediately.
                            </li>

                            <li>
                                Driver and Supplier to do a bag count and sign for goods on the transporters delivery documentation / Proof of Delivery (POD).
                            </li>

                        @endif

                        @if(str_contains(strtolower($transport_trans->product->name), 'lucerne'))
                            <li>
                                Ensure load is fully covered with tarps and protected from rain.
                            </li>
                            <li>
                                Ensure load is strapped and safe.
                            </li>

                        @endif


                    </ol>
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
                            <td class=" table_row_heading">Transporter</td>
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
