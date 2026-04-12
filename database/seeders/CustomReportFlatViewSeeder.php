<?php

namespace Database\Seeders;

use App\Models\CustomReport;
use App\Models\CustomReportModel;
use Illuminate\Database\Seeder;

class CustomReportFlatViewSeeder extends Seeder
{
    public function run(): void
    {
        $report = CustomReport::firstOrCreate(
            ['name' => 'FlatView Report'],
            ['created_by_id' => 1, 'comment' => 'Comprehensive report listing all transaction summary properties from the flat view']
        );

        $columns = [
            // ── Transaction Core ──────────────────────────────────────────────
            'transaction_id',
            'old_id',
            'a_mq',
            'a_sc',
            'a_pc',
            'sl_id',
            'sl_global_id',
            'contract_no',
            'old_deal_ticket',
            'transport_date_earliest',
            'transport_date_latest',
            'include_in_calculations',
            'is_transaction_done',
            'is_split_load',
            'is_split_load_primary',
            'is_split_load_member',
            'transaction_created_at',
            'transaction_updated_at',

            // ── Contract ──────────────────────────────────────────────────────
            'contract_type',

            // ── Supplier ──────────────────────────────────────────────────────
            'supplier_id',
            'supplier_name',
            'supplier_id_reg_no',

            // ── Customer ──────────────────────────────────────────────────────
            'customer_id',
            'customer_name',
            'customer_id_reg_no',
            'customer_2_name',
            'customer_3_name',
            'customer_4_name',
            'customer_5_name',
            'all_customers',

            // ── Transporter ───────────────────────────────────────────────────
            'transporter_id',
            'transporter_name',
            'transporter_id_reg_no',

            // ── Product ───────────────────────────────────────────────────────
            'product_id',
            'product_name',

            // ── Notes ─────────────────────────────────────────────────────────
            'suppliers_notes',
            'traders_notes',
            'delivery_notes',
            'product_notes',
            'customer_notes',
            'transport_notes',
            'pricing_notes',
            'process_notes',
            'document_notes',
            'transaction_notes',
            'traders_notes_supplier',
            'traders_notes_customer',
            'traders_notes_transport',
            'all_notes',

            // ── Transport Job ─────────────────────────────────────────────────
            'customer_order_number',
            'supplier_loading_number',
            'is_multi_loads',
            'job_is_approved',
            'is_transport_costs_inc_price',
            'is_product_zero_rated',
            'number_loads',
            'loading_instructions',
            'offloading_instructions',
            'loading_contact',
            'loading_contact_no',
            'offloading_contact',
            'offloading_contact_no',
            'all_customer_order_numbers',
            'all_supplier_loading_numbers',

            // ── Transport Load ────────────────────────────────────────────────
            'no_units_incoming',
            'no_units_outgoing',
            'product_grade_perc',
            'delivery_note',
            'calculated_route_distance',
            'is_weighbridge_certificate_received',

            // ── Packaging & Units ─────────────────────────────────────────────
            'packaging_incoming',
            'packaging_outgoing',
            'billing_units_incoming',
            'billing_units_outgoing',
            'product_source',

            // ── Addresses ─────────────────────────────────────────────────────
            'collection_address_id',
            'collection_address',
            'delivery_address_id',
            'delivery_address',
            'all_delivery_addresses',

            // ── Financials (Planned) ──────────────────────────────────────────
            'cost_price',
            'cost_price_per_unit',
            'cost_price_per_ton',
            'selling_price',
            'selling_price_per_unit',
            'selling_price_per_ton',
            'transport_rate',
            'transport_rate_per_ton',
            'transport_price',
            'transport_cost',
            'weight_ton_incoming',
            'weight_ton_outgoing',
            'total_cost_price',
            'gross_profit',
            'gross_profit_percent',
            'gross_profit_per_ton',
            'additional_cost_1',
            'additional_cost_2',
            'additional_cost_3',
            'additional_cost_desc_1',
            'additional_cost_desc_2',
            'additional_cost_desc_3',
            'total_supplier_comm',
            'total_customer_comm',
            'total_comm',
            'adjusted_gp',

            // ── Financials (Actual) ───────────────────────────────────────────
            'cost_price_actual',
            'selling_price_actual',
            'cost_price_per_ton_actual',
            'selling_price_per_ton_actual',
            'transport_cost_actual',
            'weight_ton_incoming_actual',
            'weight_ton_outgoing_actual',
            'total_cost_price_actual',
            'gross_profit_actual',
            'gross_profit_percent_actual',
            'gross_profit_per_ton_actual',

            // ── Transport Rate Basis ──────────────────────────────────────────
            'transport_rate_basis',

            // ── Deal Ticket ───────────────────────────────────────────────────
            'deal_ticket_old_id',
            'trade_value',
            'deal_ticket_type',
            'deal_ticket_is_approved',
            'deal_ticket_is_printed',
            'deal_ticket_stamp_printed',

            // ── Invoice ───────────────────────────────────────────────────────
            'transport_invoice_type',
            'transport_invoice_is_printed',
            'invoice_no',
            'invoice_date',
            'invoice_pay_by_date',
            'invoice_paid_date',
            'invoice_amount',
            'invoice_amount_paid',
            'outstanding',
            'overdue',
            'is_invoiced',
            'is_invoice_paid',
            'invoice_status',

            // ── Purchase Order ────────────────────────────────────────────────
            'purchase_order_old_id',
            'purchase_order_is_printed',
            'purchase_order_is_po_sent',
            'purchase_order_is_po_received',

            // ── Transport Order ───────────────────────────────────────────────
            'transport_order_old_id',
            'transport_order_is_printed',

            // ── Sales Order ───────────────────────────────────────────────────
            'sales_order_old_id',
            'sales_order_is_printed',

            // ── Driver / Vehicle ──────────────────────────────────────────────
            'drivers_vehicles_info',

            // ── Status History ────────────────────────────────────────────────
            'status_history',
        ];

        foreach ($columns as $column) {
            CustomReportModel::firstOrCreate(
                [
                    'report_id'      => $report->id,
                    'class_name'     => 'App\Models\TransactionSummaryFlatView',
                    'attribute_name' => $column,
                ],
                ['created_by_id' => 1]
            );
        }
    }
}
