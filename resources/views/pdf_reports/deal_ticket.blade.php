<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Deal Ticket</title>
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
                        <b><span>Deal Ticket:</span> <span>MQ</span>
                            <span>{{$transport_trans->id}}</span>
                        </b>
                    </td>

                </tr>

                <tr>
                    <td></td>
                    <td style="float: right; text-align: right; font-size: 14px">
                        @if($final_deal_ticket)
                            <span>Final Version</span>

                        @else
                            <span style="color:red">Working Document Version</span>
                        @endif

                    </td>
                </tr>

            </table>

            <ol type="1">
                <li class="section_heading">Product Information</li>
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
                            <td class="table_sections table_row_heading">Weight in Tons Outgoing</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_outgoing}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Packaging outgoing</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->PackagingOutgoing->name}}</td>
                            <td class="table_sections table_row_heading"></td>
                            <td class="table_sections table_row_value"></td>
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
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Selling Price / Metric
                                Ton
                            </td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">VAT Exempt</td>
                            <td class="table_sections table_row_value">
                                @if($transport_trans->TransportJob->is_product_zero_rated === 1)
                                    <span>Yes</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Selling Price / Unit</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->sell,2), 2, '.', ' ')}}
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
                            <td class="table_sections table_row_heading" style="width: 20%;">Total Selling Price (A)
                            </td>
                            <td class="table_sections table_row_value"> R {{number_format(round($transport_trans->TransportFinance->selling_price_per_unit,2), 2, '.', ' ')}}</td>
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
                            <td class="table_sections table_row_heading" style="width: 20%;">Invoice Basis</td>
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
                            <td class="table_sections table_row_heading" style="width: 20%;">Billing Units outgoing</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->BillingUnitsOutgoing->name}}</td>
                            <td class="table_sections table_row_heading">Confirmed P/O By</td>
                            <td class="table_sections table_row_value">{{$purchase_order->ConfirmedByType->name}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Terms of payment basis</td>
                            <td class="table_sections table_row_value">{{$transport_trans->Customer->TermsOfPaymentBasis->value}}</td>
                            <td class="table_sections table_row_heading">Terms of payment</td>
                            <td class="table_sections table_row_value">{{$transport_trans->Customer->TermsOfPayment->value}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Customer Notes</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->customer_notes}}</td>

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
                            <td class="table_sections table_row_heading">Transporter Name</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->Transporter->last_legal_name}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Order Confirmed By</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportLoad->ConfirmedByType->name}}</td>
                            <td class="table_sections table_row_heading">Weight Tons Incoming</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->weight_ton_incoming}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Transport Date Earliest
                            </td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_earliest}}</td>
                            <td class="table_sections table_row_heading">Rate Basis</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportFinance->TransportRateBasis->name}}<</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Transport Date Latest</td>
                            <td class="table_sections table_row_value">{{$transport_trans->transport_date_latest}}</td>
                            <td class="table_sections table_row_heading">Tarrif / Metric Ton</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->selling_price_per_ton,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">No. Of Loads</td>
                            <td class="table_sections table_row_value">{{$transport_trans->TransportJob->number_loads}}</td>
                            <td class="table_sections table_row_heading">Load Tarrif (B)</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->transport_cost,2), 2, '.', ' ')}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Transport Notes</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->transport_notes}}</td>

                        </tr>

                        </tbody>

                    </table>
                </div>
                <br>
                <li class="section_heading">Supplier Information and Deal Cost</li>
                <div class="page-break">
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading">Supplier Name</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->Supplier->last_legal_name}}</td>
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
                            <td class="table_sections table_row_heading">Cost Price / Metric Ton</td>
                            <td class="table_sections table_row_value">
                                R {{number_format(round($transport_trans->TransportFinance->cost_price_per_ton,2), 2, '.', ' ')}}
                            </td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Cost Price / Unit</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->cost_price_per_unit,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading" style="width: 20%;">Terms of Payment</td>
                            <td class="table_sections table_row_value">{{$transport_trans->Supplier->TermsOfPayment->value}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Supplier Notes</td>
                            <td class="table_sections table_row_value" colspan="3">{{$transport_trans->supplier_notes}}</td>

                        </tr>

                        </tbody>

                    </table>
                </div>
                <li class="section_heading">Margin Calculation</li>
                <div>
                    <table class="table_sections" style="width:100%;">
                        <tbody>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Gross Profit A-B-C</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->gross_profit,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">Additional Cost 1</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->additional_cost_1,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Gross Profit / Metric Ton
                            </td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->gross_profit_per_ton,2), 2, '.', ' ')}}</td>
                            <td class="table_sections table_row_heading">Additional Cost 2</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->additional_cost_2,2), 2, '.', ' ')}}</td>
                        </tr>

                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;">Gross Profit %</td>
                            <td class="table_sections table_row_value">{{number_format(round($transport_trans->TransportFinance->gross_profit_percent,2), 2, '.', ' ')}} %</td>
                            <td class="table_sections table_row_heading">Additional Cost 3</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->additional_cost_3,2), 2, '.', ' ')}}</td>
                        </tr>
                        <tr class="table_sections">
                            <td class="table_sections table_row_heading" style="width: 20%;"></td>
                            <td class="table_sections table_row_value"></td>
                            <td class="table_sections table_row_heading">Total Cost</td>
                            <td class="table_sections table_row_value">R {{number_format(round($transport_trans->TransportFinance->total_cost_price,2), 2, '.', ' ')}}</td>
                        </tr>


                        </tbody>

                    </table>
                </div>
                <br>
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

            </ol>


        </div>

    </div>
</main>
</body>
</html>
