<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW transaction_summary_flat_view AS
            SELECT
                -- Transaction Core Fields
                tt.id as transaction_id,
                tt.old_id,
                tt.a_mq,
                tt.a_sc,
                tt.a_pc,
                tt.sl_id,
                tt.sl_global_id,
                tt.contract_no,
                tt.old_deal_ticket,
                tt.transport_date_earliest,
                tt.transport_date_latest,
                tt.include_in_calculations,
                tt.is_transaction_done,
                tt.is_split_load,
                tt.is_split_load_primary,
                tt.is_split_load_member,
                tt.created_at as transaction_created_at,
                tt.updated_at as transaction_updated_at,

                -- Contract Type
                ct.name as contract_type,

                -- Supplier Information
                tt.supplier_id,
                CONCAT_WS(' ', s.first_name, s.last_legal_name) as supplier_name,
                s.id_reg_no as supplier_id_reg_no,

                -- Customer Information (Main)
                tt.customer_id,
                CONCAT_WS(' ', c.first_name, c.last_legal_name) as customer_name,
                c.id_reg_no as customer_id_reg_no,

                -- Additional Customers (Combined)
                CASE WHEN tt.customer_id_2 != 1 THEN CONCAT_WS(' ', c2.first_name, c2.last_legal_name) ELSE NULL END as customer_2_name,
                CASE WHEN tt.customer_id_3 != 1 THEN CONCAT_WS(' ', c3.first_name, c3.last_legal_name) ELSE NULL END as customer_3_name,
                CASE WHEN tt.customer_id_4 != 1 THEN CONCAT_WS(' ', c4.first_name, c4.last_legal_name) ELSE NULL END as customer_4_name,
                CASE WHEN tt.customer_id_5 != 1 THEN CONCAT_WS(' ', c5.first_name, c5.last_legal_name) ELSE NULL END as customer_5_name,

                -- All Customers Combined (Pipe-separated)
                CONCAT_WS(' | ',
                    CONCAT_WS(' ', c.first_name, c.last_legal_name),
                    CASE WHEN tt.customer_id_2 != 1 THEN CONCAT_WS(' ', c2.first_name, c2.last_legal_name) END,
                    CASE WHEN tt.customer_id_3 != 1 THEN CONCAT_WS(' ', c3.first_name, c3.last_legal_name) END,
                    CASE WHEN tt.customer_id_4 != 1 THEN CONCAT_WS(' ', c4.first_name, c4.last_legal_name) END,
                    CASE WHEN tt.customer_id_5 != 1 THEN CONCAT_WS(' ', c5.first_name, c5.last_legal_name) END
                ) as all_customers,

                -- Transporter Information
                tt.transporter_id,
                CONCAT_WS(' ', tr.first_name, tr.last_legal_name) as transporter_name,
                tr.id_reg_no as transporter_id_reg_no,

                -- Product Information
                tt.product_id,
                p.name as product_name,

                -- Transaction Notes (All combined with labels)
                tt.suppliers_notes,
                tt.traders_notes,
                tt.delivery_notes,
                tt.product_notes,
                tt.customer_notes,
                tt.transport_notes,
                tt.pricing_notes,
                tt.process_notes,
                tt.document_notes,
                tt.transaction_notes,
                tt.traders_notes_supplier,
                tt.traders_notes_customer,
                tt.traders_notes_transport,

                -- Combined Notes
                CONCAT_WS(' | ',
                    CASE WHEN tt.suppliers_notes IS NOT NULL THEN CONCAT('Supplier: ', tt.suppliers_notes) END,
                    CASE WHEN tt.traders_notes IS NOT NULL THEN CONCAT('Trader: ', tt.traders_notes) END,
                    CASE WHEN tt.delivery_notes IS NOT NULL THEN CONCAT('Delivery: ', tt.delivery_notes) END,
                    CASE WHEN tt.product_notes IS NOT NULL THEN CONCAT('Product: ', tt.product_notes) END,
                    CASE WHEN tt.customer_notes IS NOT NULL THEN CONCAT('Customer: ', tt.customer_notes) END,
                    CASE WHEN tt.transport_notes IS NOT NULL THEN CONCAT('Transport: ', tt.transport_notes) END
                ) as all_notes,

                -- Transport Job Information
                tj.customer_order_number,
                tj.supplier_loading_number,
                tj.is_multi_loads,
                tj.is_approved as job_is_approved,
                tj.is_transport_costs_inc_price,
                tj.is_product_zero_rated,
                tj.number_loads,
                tj.loading_instructions,
                tj.offloading_instructions,
                tj.loading_contact,
                tj.loading_contact_no,
                tj.offloading_contact,
                tj.offloading_contact_no,

                -- Customer Order Numbers (Combined)
                CONCAT_WS(' | ',
                    tj.customer_order_number,
                    tj.customer_order_number_2,
                    tj.customer_order_number_3,
                    tj.customer_order_number_4,
                    tj.customer_order_number_5
                ) as all_customer_order_numbers,

                -- Supplier Loading Numbers (Combined)
                CONCAT_WS(' | ',
                    tj.supplier_loading_number,
                    tj.supplier_loading_number_2,
                    tj.supplier_loading_number_3,
                    tj.supplier_loading_number_4,
                    tj.supplier_loading_number_5
                ) as all_supplier_loading_numbers,

                -- Transport Load Information
                tl.no_units_incoming,
                tl.no_units_outgoing,
                tl.product_grade_perc,
                tl.delivery_note,
                tl.calculated_route_distance,
                tl.is_weighbridge_certificate_received,

                -- Packaging
                pkg_in.name as packaging_incoming,
                pkg_out.name as packaging_outgoing,

                -- Billing Units
                bu_in.name as billing_units_incoming,
                bu_out.name as billing_units_outgoing,

                -- Product Source
                ps.name as product_source,

                -- Collection Address
                tl.collection_address_id,
                CONCAT_WS(', ',
                    addr_coll.line_1,
                    addr_coll.line_2,
                    addr_coll.line_3,
                    addr_coll.code,
                    addr_coll.country
                ) as collection_address,

                -- Delivery Address (Main)
                tl.delivery_address_id,
                CONCAT_WS(', ',
                    addr_del.line_1,
                    addr_del.line_2,
                    addr_del.line_3,
                    addr_del.code,
                    addr_del.country
                ) as delivery_address,

                -- All Delivery Addresses (Combined)
                CONCAT_WS(' | ',
                    CONCAT_WS(', ', addr_del.line_1, addr_del.line_2, addr_del.code),
                    CASE WHEN tl.delivery_address_id_2 IS NOT NULL THEN CONCAT_WS(', ', addr_del2.line_1, addr_del2.line_2, addr_del2.code) END,
                    CASE WHEN tl.delivery_address_id_3 IS NOT NULL THEN CONCAT_WS(', ', addr_del3.line_1, addr_del3.line_2, addr_del3.code) END,
                    CASE WHEN tl.delivery_address_id_4 IS NOT NULL THEN CONCAT_WS(', ', addr_del4.line_1, addr_del4.line_2, addr_del4.code) END,
                    CASE WHEN tl.delivery_address_id_5 IS NOT NULL THEN CONCAT_WS(', ', addr_del5.line_1, addr_del5.line_2, addr_del5.code) END
                ) as all_delivery_addresses,

                -- Transport Finance Information
                tf.cost_price,
                tf.cost_price_per_unit,
                tf.cost_price_per_ton,
                tf.selling_price,
                tf.selling_price_per_unit,
                tf.selling_price_per_ton,
                tf.transport_rate,
                tf.transport_rate_per_ton,
                tf.transport_price,
                tf.transport_cost,
                tf.weight_ton_incoming,
                tf.weight_ton_outgoing,
                tf.total_cost_price,
                tf.gross_profit,
                tf.gross_profit_percent,
                tf.gross_profit_per_ton,
                tf.additional_cost_1,
                tf.additional_cost_2,
                tf.additional_cost_3,
                tf.additional_cost_desc_1,
                tf.additional_cost_desc_2,
                tf.additional_cost_desc_3,
                tf.total_supplier_comm,
                tf.total_customer_comm,
                tf.total_comm,
                tf.adjusted_gp,

                -- Actual Values
                tf.cost_price_actual,
                tf.selling_price_actual,
                tf.cost_price_per_ton_actual,
                tf.selling_price_per_ton_actual,
                tf.transport_cost_actual,
                tf.weight_ton_incoming_actual,
                tf.weight_ton_outgoing_actual,
                tf.total_cost_price_actual,
                tf.gross_profit_actual,
                tf.gross_profit_percent_actual,
                tf.gross_profit_per_ton_actual,

                -- Transport Rate Basis
                trb.name as transport_rate_basis,

                -- Deal Ticket Information
                dt.old_id as deal_ticket_old_id,
                dt.trade_value,
                dt.type as deal_ticket_type,
                dt.is_approved as deal_ticket_is_approved,
                dt.is_printed as deal_ticket_is_printed,
                dt.stamp_printed as deal_ticket_stamp_printed,

                -- Invoice Information
                ti.type as transport_invoice_type,
                ti.is_printed as transport_invoice_is_printed,

                -- Invoice Details
                tid.invoice_no,
                tid.invoice_date,
                tid.invoice_pay_by_date,
                tid.invoice_paid_date,
                tid.invoice_amount,
                tid.invoice_amount_paid,
                tid.outstanding,
                tid.overdue,
                tid.is_invoiced,
                tid.is_invoice_paid,

                -- Invoice Status
                istat.name as invoice_status,

                -- Purchase Order
                po.old_id as purchase_order_old_id,
                po.is_printed as purchase_order_is_printed,
                po.is_po_sent as purchase_order_is_po_sent,
                po.is_po_received as purchase_order_is_po_received,

                -- Transport Order
                tord.old_id as transport_order_old_id,
                tord.is_printed as transport_order_is_printed,

                -- Sales Order
                so.old_id as sales_order_old_id,
                so.is_printed as sales_order_is_printed,

                -- Driver/Vehicle Info (Aggregated - pipe-separated)
                (
                    SELECT STRING_AGG(
                        CONCAT_WS(' - ',
                            CONCAT(rd.first_name, ' ', rd.last_name),
                            rv.reg_no,
                            tdv.weighbridge_upload_weight,
                            tdv.weighbridge_offload_weight
                        ),
                        ' | '
                    )
                    FROM transport_driver_vehicles tdv
                    LEFT JOIN regular_drivers rd ON tdv.regular_driver_id = rd.id
                    LEFT JOIN regular_vehicles rv ON tdv.regular_vehicle_id = rv.id
                    WHERE tdv.transport_trans_id = tt.id
                    AND tdv.deleted_at IS NULL
                ) as drivers_vehicles_info,

                -- Transport Status (Aggregated - pipe-separated)
                (
                    SELECT STRING_AGG(
                        CONCAT_WS(': ',
                            se.entity,
                            stype.type
                        ),
                        ' | '
                    )
                    FROM transport_statuses ts
                    LEFT JOIN status_entities se ON ts.status_entity_id = se.id
                    LEFT JOIN status_types stype ON ts.status_type_id = stype.id
                    WHERE ts.transport_trans_id = tt.id
                    AND ts.deleted_at IS NULL
                ) as status_history

            FROM transport_transactions tt

            -- Contract Type
            LEFT JOIN contract_types ct ON tt.contract_type_id = ct.id

            -- Supplier
            LEFT JOIN suppliers s ON tt.supplier_id = s.id

            -- Customers
            LEFT JOIN customers c ON tt.customer_id = c.id
            LEFT JOIN customers c2 ON tt.customer_id_2 = c2.id
            LEFT JOIN customers c3 ON tt.customer_id_3 = c3.id
            LEFT JOIN customers c4 ON tt.customer_id_4 = c4.id
            LEFT JOIN customers c5 ON tt.customer_id_5 = c5.id

            -- Transporter
            LEFT JOIN transporters tr ON tt.transporter_id = tr.id

            -- Product
            LEFT JOIN products p ON tt.product_id = p.id

            -- Transport Job
            LEFT JOIN transport_jobs tj ON tt.id = tj.transport_trans_id AND tj.deleted_at IS NULL

            -- Transport Load
            LEFT JOIN transport_loads tl ON tt.id = tl.transport_trans_id AND tl.deleted_at IS NULL

            -- Packaging
            LEFT JOIN packagings pkg_in ON tl.packaging_incoming_id = pkg_in.id
            LEFT JOIN packagings pkg_out ON tl.packaging_outgoing_id = pkg_out.id

            -- Billing Units
            LEFT JOIN billing_units bu_in ON tl.billing_units_incoming_id = bu_in.id
            LEFT JOIN billing_units bu_out ON tl.billing_units_outgoing_id = bu_out.id

            -- Product Source
            LEFT JOIN product_sources ps ON tl.product_source_id = ps.id

            -- Addresses
            LEFT JOIN addresses addr_coll ON tl.collection_address_id = addr_coll.id
            LEFT JOIN addresses addr_del ON tl.delivery_address_id = addr_del.id
            LEFT JOIN addresses addr_del2 ON tl.delivery_address_id_2 = addr_del2.id
            LEFT JOIN addresses addr_del3 ON tl.delivery_address_id_3 = addr_del3.id
            LEFT JOIN addresses addr_del4 ON tl.delivery_address_id_4 = addr_del4.id
            LEFT JOIN addresses addr_del5 ON tl.delivery_address_id_5 = addr_del5.id

            -- Transport Finance
            LEFT JOIN transport_finances tf ON tt.id = tf.transport_trans_id AND tf.deleted_at IS NULL

            -- Transport Rate Basis
            LEFT JOIN transport_rate_bases trb ON tf.transport_rate_basis_id = trb.id

            -- Deal Ticket
            LEFT JOIN deal_tickets dt ON tt.id = dt.transport_trans_id AND dt.deleted_at IS NULL

            -- Purchase Order
            LEFT JOIN purchase_orders po ON tt.id = po.transport_trans_id AND po.deleted_at IS NULL

            -- Transport Order
            LEFT JOIN transport_orders tord ON tt.id = tord.transport_trans_id AND tord.deleted_at IS NULL

            -- Sales Order
            LEFT JOIN sales_orders so ON tt.id = so.transport_trans_id AND so.deleted_at IS NULL

            -- Transport Invoice
            LEFT JOIN transport_invoices ti ON tt.id = ti.transport_trans_id AND ti.deleted_at IS NULL

            -- Transport Invoice Details
            LEFT JOIN transport_invoice_details tid ON tt.id = tid.transport_trans_id AND tid.deleted_at IS NULL

            -- Invoice Status
            LEFT JOIN invoice_statuses istat ON tid.status_id = istat.id

            WHERE tt.deleted_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS transaction_summary_flat_view');
    }
};

