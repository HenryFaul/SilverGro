<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Split Purchase Order Confirmation</title>
    <style>
        @page {
            margin: 100px 30px 60px 30px;
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
            list-style: none;
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
    @if($transport_trans->is_split_load && isset($split_data))

    @php
        $primary = $transport_trans; // fully loaded with all relationships
        $summary_total_units = 0;
        $summary_total_cost  = 0;
        $summary_total_tons  = 0;
        foreach ($split_data['linked_trans_split'] as $d) {
            $summary_total_units += $d->TransportTransaction->TransportLoad->no_units_incoming;
            $summary_total_cost  += $d->TransportTransaction->TransportFinance->total_cost_price;
            $summary_total_tons  += $d->TransportTransaction->TransportFinance->weight_ton_incoming;
        }
    @endphp

    {{-- ================================================================ --}}
    {{-- REUSABLE MACRO: Supplier Details block (always from primary)     --}}
    {{-- ================================================================ --}}
    @php
        function renderSupplierDetails($supplier) { return ''; } // placeholder — rendered inline below
    @endphp

    {{-- ============================================================ --}}
    {{-- PAGE 1: Supplier Details + Split Summary + Supplier Notes    --}}
    {{-- ============================================================ --}}
    <div style="margin-top: 1px;">
        <table style="width:100%">
            <tr>
                <td></td>
                <td style="float: right; text-align: right; font-size: 14px;">
                    <b>Split Purchase Order Confirmation: MQ{{$split_data['primary_linked_trans_split']->a_mq ?? $primary->a_mq}}</b>
                    <p><span>Created at: {{$now}}</span></p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="float: right; text-align: right; font-size: 12px;">
                    @if($final_purchase_order)
                        <span>Final Version</span>
                    @else
                        <span style="color:red;">Working Document Version</span>
                    @endif
                </td>
            </tr>
        </table>

        <h3 style="margin-top: 8px; margin-bottom: 8px;">SPLIT PURCHASE ORDER MASTER +{{count($split_data['linked_trans_split'])}}</h3>

        {{-- Supplier Details --}}
        <li class="section_heading" style="margin-top: 12px;">1. Supplier Details</li>
        <hr style="margin-top: 3px; margin-bottom: 6px;">
        <div style="margin-bottom: 12px;">
            <table class="table_sections" style="width:100%;">
                <tbody>
                <tr class="table_sections">
                    <td class="table_sections table_row_heading" style="width:25%;">Supplier</td>
                    <td class="table_sections table_row_value" colspan="3">{{$primary->Supplier->last_legal_name}}</td>
                </tr>
                <tr class="table_sections">
                    <td class="table_sections table_row_heading" style="width:25%;">Business Address</td>
                    <td class="table_sections table_row_value" colspan="3">
                        @if($primary->Supplier->addressablePhysical == "[]" || empty($primary->Supplier->addressablePhysical))
                            <span>No physical address loaded...</span>
                        @else
                            <span>{{$primary->Supplier->addressablePhysical[0]->line_1}}</span>
                            @if($primary->Supplier->addressablePhysical[0]->line_2), <span>{{$primary->Supplier->addressablePhysical[0]->line_2}}</span>@endif
                            @if($primary->Supplier->addressablePhysical[0]->line_3), <span>{{$primary->Supplier->addressablePhysical[0]->line_3}}</span>@endif
                            @if($primary->Supplier->addressablePhysical[0]->country), <span>{{$primary->Supplier->addressablePhysical[0]->country}}</span>@endif
                            @if($primary->Supplier->addressablePhysical[0]->code), <span>{{$primary->Supplier->addressablePhysical[0]->code}}</span>@endif
                        @endif
                    </td>
                </tr>
                <tr class="table_sections">
                    <td class="table_sections table_row_heading" style="width:25%;">Att</td>
                    <td class="table_sections table_row_value" colspan="3">
                        @if($primary->Supplier->contactable == "[]" || $primary->Supplier->contactable->isEmpty())
                            <span>No contact loaded</span>
                        @else
                            @foreach($primary->Supplier->contactable as $contact)
                                <div>
                                    <span>{{$contact->first_name}} {{$contact->last_legal_name}}</span>
                                    @if($contact->numberable != "[]" && $contact->numberable->isNotEmpty())
                                        <span> | T: @foreach($contact->numberable as $phone){{$phone->value}} @endforeach</span>
                                    @endif
                                    @if($contact->emailable != "[]" && $contact->emailable->isNotEmpty())
                                        <span> | E: @foreach($contact->emailable as $email){{$email->value}} @endforeach</span>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="table_row_value" style="margin-top: 8px;">
                Herewith our confirmation of the PURCHASE ORDER for the following products, including the specific terms
                and conditions of this order. This document is a confirmation of our telephone order.
            </p>
        </div>

        {{-- Split Summary Overview --}}
        <li class="section_heading" style="margin-top: 12px;">2. Split Summary Overview</li>
        <hr style="margin-top: 3px; margin-bottom: 6px;">
        <div style="margin-bottom: 12px;">
            <table class="table_sections_bordered" style="width:100%;">
                <tbody>
                <tr>
                    <td style="width:10%;" class="table_sections_bordered table_row_heading">Purchase Order</td>
                    <td style="width:12%;" class="table_sections_bordered table_row_heading">Supplier Loading #</td>
                    <td style="width:16%;" class="table_sections_bordered table_row_heading">Product</td>
                    <td style="width:12%;" class="table_sections_bordered table_row_heading">Packaging</td>
                    <td style="width:12%;" class="table_sections_bordered table_row_heading">Billing Units</td>
                    <td style="width:10%;" class="table_sections_bordered table_row_heading">Qty Units</td>
                    <td style="width:12%;" class="table_sections_bordered table_row_heading">Cost / Unit</td>
                    <td style="width:10%;" class="table_sections_bordered table_row_heading">Total Cost</td>
                    <td style="width:10%;" class="table_sections_bordered table_row_heading">Planned Tons</td>
                </tr>
                @foreach ($split_data['linked_trans_split'] as $deal)
                    <tr>
                        <td class="table_sections_bordered table_row_value">MQ{{$deal->TransportTransaction->a_mq}}</td>
                        <td class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportJob->supplier_loading_number}}</td>
                        <td class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->Product->name}}</td>
                        <td class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->PackagingIncoming->name ?? ''}}</td>
                        <td class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->BillingUnitsIncoming->name ?? ''}}</td>
                        <td class="table_sections_bordered table_row_value">{{$deal->TransportTransaction->TransportLoad->no_units_incoming}}</td>
                        <td class="table_sections_bordered table_row_value">R {{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit,2),2,'.', ' ')}}</td>
                        <td class="table_sections_bordered table_row_value">R {{number_format(round($deal->TransportTransaction->TransportFinance->total_cost_price,2),2,'.',' ')}}</td>
                        <td class="table_sections_bordered table_row_value">{{round($deal->TransportTransaction->TransportFinance->weight_ton_incoming,2)}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="table_sections_bordered table_row_heading" colspan="5">TOTALS</td>
                    <td class="table_sections_bordered table_row_heading">{{number_format($summary_total_units,0,'.',' ')}}</td>
                    <td class="table_sections_bordered table_row_heading"></td>
                    <td class="table_sections_bordered table_row_heading">R {{number_format(round($summary_total_cost,2),2,'.',' ')}}</td>
                    <td class="table_sections_bordered table_row_heading">{{number_format(round($summary_total_tons,2),2,'.',' ')}}</td>
                </tr>
                </tbody>
            </table>
        </div>

        {{-- Supplier Notes Summary --}}
        <li class="section_heading" style="margin-top: 12px;">3. Supplier Notes</li>
        <hr style="margin-top: 3px; margin-bottom: 6px;">
        <div style="margin-bottom: 16px;">
            <table class="table_sections_bordered" style="width:100%;">
                <thead>
                <tr>
                    <th class="table_sections_bordered table_row_heading" style="width:15%; text-align:left;">Order #</th>
                    <th class="table_sections_bordered table_row_heading" style="text-align:left;">Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($split_data['linked_trans_split'] as $note_deal)
                    <tr>
                        <td class="table_sections_bordered table_row_value">MQ{{$note_deal->TransportTransaction->a_mq}}</td>
                        <td class="table_sections_bordered table_row_value">{!! nl2br($note_deal->TransportTransaction->suppliers_notes) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- PER-DEAL PAGES                                               --}}
    {{-- ============================================================ --}}
    @foreach ($split_data['linked_trans_split'] as $deal)
        <div class="page-break"></div>

        <div style="margin-top: 1px;">
            {{-- Page header: MQ reference --}}
            <table style="width:100%;">
                <tr>
                    <td></td>
                    <td style="float:right; text-align:right; font-size:14px;">
                        <b>Purchase Order: MQ{{$deal->TransportTransaction->a_mq}}</b>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="float:right; text-align:right; font-size:12px;">
                        @if($final_purchase_order)
                            <span>Final Version</span>
                        @else
                            <span style="color:red;">Working Document Version</span>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Supplier Details (primary — same on every page) --}}
            <li class="section_heading" style="margin-top: 12px;">1. Supplier Details</li>
            <hr style="margin-top: 3px; margin-bottom: 6px;">
            <div style="margin-bottom: 12px;">
                <table class="table_sections" style="width:100%;">
                    <tbody>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Supplier</td>
                        <td class="table_sections table_row_value" colspan="3">{{$primary->Supplier->last_legal_name}}</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Business Address</td>
                        <td class="table_sections table_row_value" colspan="3">
                            @if($primary->Supplier->addressablePhysical == "[]" || empty($primary->Supplier->addressablePhysical))
                                <span>No physical address loaded...</span>
                            @else
                                <span>{{$primary->Supplier->addressablePhysical[0]->line_1}}</span>
                                @if($primary->Supplier->addressablePhysical[0]->line_2), <span>{{$primary->Supplier->addressablePhysical[0]->line_2}}</span>@endif
                                @if($primary->Supplier->addressablePhysical[0]->line_3), <span>{{$primary->Supplier->addressablePhysical[0]->line_3}}</span>@endif
                                @if($primary->Supplier->addressablePhysical[0]->country), <span>{{$primary->Supplier->addressablePhysical[0]->country}}</span>@endif
                                @if($primary->Supplier->addressablePhysical[0]->code), <span>{{$primary->Supplier->addressablePhysical[0]->code}}</span>@endif
                            @endif
                        </td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Att</td>
                        <td class="table_sections table_row_value" colspan="3">
                            @if($primary->Supplier->contactable == "[]" || $primary->Supplier->contactable->isEmpty())
                                <span>No contact loaded</span>
                            @else
                                @foreach($primary->Supplier->contactable as $contact)
                                    <div>
                                        <span>{{$contact->first_name}} {{$contact->last_legal_name}}</span>
                                        @if($contact->numberable != "[]" && $contact->numberable->isNotEmpty())
                                            <span> | T: @foreach($contact->numberable as $phone){{$phone->value}} @endforeach</span>
                                        @endif
                                        @if($contact->emailable != "[]" && $contact->emailable->isNotEmpty())
                                            <span> | E: @foreach($contact->emailable as $email){{$email->value}} @endforeach</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Collection and Transport (from primary transaction) --}}
            <li class="section_heading" style="margin-top: 12px;">2. Collection and Transport</li>
            <hr style="margin-top: 3px; margin-bottom: 6px;">
            <div style="margin-bottom: 12px;">
                <table class="table_sections" style="width:100%;">
                    <tbody>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Transport Date Earliest</td>
                        <td class="table_sections table_row_value" style="width:25%; white-space:nowrap;">
                            {{ $primary->transport_date_earliest ? $primary->transport_date_earliest->format('D d/M/Y') : 'No date selected' }}
                        </td>
                        <td class="table_sections table_row_heading" style="width:25%;">Loading Time From</td>
                        <td class="table_sections table_row_value">{{$primary->TransportJob->LoadingHoursFrom->name}} HRS</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Transport Date Latest</td>
                        <td class="table_sections table_row_value" style="width:25%; white-space:nowrap;">
                            {{$primary->transport_date_latest->format('D d/M/Y')}}
                        </td>
                        <td class="table_sections table_row_heading" style="width:25%;">Loading Time To</td>
                        <td class="table_sections table_row_value">{{$primary->TransportJob->LoadingHoursTo->name}} HRS</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">No. Of Loads</td>
                        <td class="table_sections table_row_value" colspan="3">{{$primary->TransportJob->number_loads}}</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading">Collection Address</td>
                        <td class="table_sections table_row_value" colspan="3">
                            <span>{{$primary->TransportLoad->CollectionAddress->line_1}}</span>
                            @if($primary->TransportLoad->CollectionAddress->line_2), <span>{{$primary->TransportLoad->CollectionAddress->line_2}}</span>@endif
                            @if($primary->TransportLoad->CollectionAddress->line_3), <span>{{$primary->TransportLoad->CollectionAddress->line_3}}</span>@endif
                            @if($primary->TransportLoad->CollectionAddress->country), <span>{{$primary->TransportLoad->CollectionAddress->country}}</span>@endif
                            @if($primary->TransportLoad->CollectionAddress->code), <span>{{$primary->TransportLoad->CollectionAddress->code}}</span>@endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Product & Pricing for this specific deal --}}
            <li class="section_heading" style="margin-top: 12px;">3. Product &amp; Pricing</li>
            <hr style="margin-top: 3px; margin-bottom: 6px;">
            <div style="margin-bottom: 12px;">
                <table class="table_sections" style="width:100%;">
                    <tbody>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Product</td>
                        <td class="table_sections table_row_value" colspan="3">{{$deal->TransportTransaction->Product->name}}</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Packaging</td>
                        <td class="table_sections table_row_value" style="width:25%;">{{$deal->TransportTransaction->TransportLoad->PackagingIncoming->name ?? ''}}</td>
                        <td class="table_sections table_row_heading" style="width:25%;">Billing Units</td>
                        <td class="table_sections table_row_value">{{$deal->TransportTransaction->TransportLoad->BillingUnitsIncoming->name ?? ''}}</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Quantity of Units</td>
                        <td class="table_sections table_row_value" style="width:25%;">{{$deal->TransportTransaction->TransportLoad->no_units_incoming}}</td>
                        <td class="table_sections table_row_heading" style="width:25%;">Planned Tons</td>
                        <td class="table_sections table_row_value">{{round($deal->TransportTransaction->TransportFinance->weight_ton_incoming,2)}}</td>
                    </tr>
                    <tr class="table_sections">
                        <td class="table_sections table_row_heading" style="width:25%;">Cost per Unit</td>
                        <td class="table_sections table_row_value" style="width:25%;">R {{number_format(round($deal->TransportTransaction->TransportFinance->cost_price_per_unit,2),2,'.',' ')}}</td>
                        <td class="table_sections table_row_heading" style="width:25%;">Total Cost</td>
                        <td class="table_sections table_row_value" style="background-color:#62FD473F;">R {{number_format(round($deal->TransportTransaction->TransportFinance->total_cost_price,2),2,'.',' ')}}</td>
                    </tr>
                    </tbody>
                </table>

                <div class="table_row_value" style="margin-top: 10px;">
                    The seller accepts the conditions as set out in this "PURCHASE ORDER", unless changes are presented
                    in writing within 24 hours after receiving this document, for acceptance by the buyer. Please sign
                    this document and email a scanned copy to documents@silvergro.co.za. If the seller does not sign
                    this document and return it as per the above, the transaction will still be considered as legal and
                    binding. This transaction is subject to the terms, conditions and rules, including the arbitration
                    clause and rules in contract form known as Sagos 1 (Current Version) with which the parties are
                    fully familiar with and which will be applicable to the extent in which it will not be contradictory
                    to what the parties agreed upon. FORCE MAJEURE: To be applied as per the SAGOS 1 (Version 09),
                    section 11. We thank you for the opportunity to do business with you.
                </div>
            </div>
        </div>
    @endforeach

    {{-- Signatures — always last, kept together on one page --}}
    <div style="page-break-inside: avoid; margin-top: 20px;">
        <li class="section_heading">4. Signatures</li>
        <hr style="margin-top: 3px; margin-bottom: 20px;">
        <table style="width:100%;">
            <tbody>
            <tr>
                <td style="width:25%;" class="table_row_value"><br><hr></td>
                <td style="width:25%;" class="table_row_value"><br><hr></td>
                <td style="width:25%;" class="table_row_value"><br><hr></td>
                <td style="width:25%;" class="table_row_value"><br><hr></td>
            </tr>
            <tr>
                <td class="table_row_heading">Supplier Signature</td>
                <td class="table_row_heading">Supplier Name</td>
                <td class="table_row_heading">Date</td>
                <td class="table_row_heading">Stamp</td>
            </tr>
            </tbody>
        </table>
    </div>

    @endif
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

