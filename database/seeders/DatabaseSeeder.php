<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Address;
use App\Models\AddressType;
use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\CustomerRating;
use App\Models\CustomReport;
use App\Models\CustomReportModel;
use App\Models\InvoiceBasis;
use App\Models\InvoiceStatus;
use App\Models\LoadingHourOption;
use App\Models\Packaging;
use App\Models\ProductSource;
use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\Staff;
use App\Models\StatusEntity;
use App\Models\StatusType;
use App\Models\Supplier;
use App\Models\ContactType;
use App\Models\TermsOfPayment;
use App\Models\TermsOfPaymentBasis;
use App\Models\TradeRule;
use App\Models\TradeRuleOpp;
use App\Models\TradeRuleRole;
use App\Models\TransLinkType;
use App\Models\Transporter;
use App\Models\TransportRateBasis;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Idempotent: safe to re-run against an existing database.
     * Uses firstOrCreate throughout so only missing records are inserted.
     */
    public function run(): void
    {
        // ─── Permissions ─────────────────────────────────────────────────────────

        Permission::firstOrCreate(['name' => 'view_only']);
        Permission::firstOrCreate(['name' => 'update_supplier']);
        Permission::firstOrCreate(['name' => 'update_product']);
        Permission::firstOrCreate(['name' => 'update_customer']);
        Permission::firstOrCreate(['name' => 'update_transporter']);
        Permission::firstOrCreate(['name' => 'update_contact']);
        Permission::firstOrCreate(['name' => 'update_pricing']);
        Permission::firstOrCreate(['name' => 'update_process']);
        Permission::firstOrCreate(['name' => 'delete_system_document']);
        Permission::firstOrCreate(['name' => 'delete_user_document']);
        Permission::firstOrCreate(['name' => 'manage_users']);
        Permission::firstOrCreate(['name' => 'create_pc']);
        Permission::firstOrCreate(['name' => 'create_sc']);
        Permission::firstOrCreate(['name' => 'create_mq']);
        Permission::firstOrCreate(['name' => 'link_mq']);
        Permission::firstOrCreate(['name' => 'approve_deal_ticket']);
        Permission::firstOrCreate(['name' => 'override_deal_ticket']);
        Permission::firstOrCreate(['name' => 'edit_adjusted_gp']);
        Permission::firstOrCreate(['name' => 'edit_transport_trans']);

        // ─── Roles ───────────────────────────────────────────────────────────────

        $viewOnlyRole         = Role::firstOrCreate(['name' => 'ViewOnlyRole']);
        $SystemTesterRole     = Role::firstOrCreate(['name' => 'SystemTesterRole']);
        $AdminRole            = Role::firstOrCreate(['name' => 'AdminRole']);
        $TraderRole           = Role::firstOrCreate(['name' => 'TraderRole']);
        $LogisticsRole        = Role::firstOrCreate(['name' => 'LogisticsRole']);
        $TradeDirectorRole    = Role::firstOrCreate(['name' => 'TradeDirectorRole']);
        $FinancialDirectorRole = Role::firstOrCreate(['name' => 'FinancialDirectorRole']);
        $OpsAdminManger       = Role::firstOrCreate(['name' => 'OpsAdmin Manager']);

        // givePermissionTo uses syncWithoutDetaching internally — idempotent.

        $viewOnlyRole->givePermissionTo(['view_only']);

        $AdminRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'approve_deal_ticket',
            'edit_adjusted_gp',
            'edit_transport_trans',
        ]);

        $SystemTesterRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'approve_deal_ticket',
            'edit_adjusted_gp',
            'edit_transport_trans',
        ]);

        $TraderRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'edit_transport_trans',
        ]);

        $LogisticsRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'edit_transport_trans',
        ]);

        $TradeDirectorRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'approve_deal_ticket',
            'edit_adjusted_gp',
            'edit_transport_trans',
        ]);

        $FinancialDirectorRole->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'approve_deal_ticket',
            'edit_adjusted_gp',
            'edit_transport_trans',
        ]);

        $OpsAdminManger->givePermissionTo([
            'update_supplier',
            'update_product',
            'update_customer',
            'update_transporter',
            'update_contact',
            'update_pricing',
            'update_process',
            'delete_system_document',
            'delete_user_document',
            'manage_users',
            'create_pc',
            'create_sc',
            'create_mq',
            'link_mq',
            'approve_deal_ticket',
            'edit_transport_trans',
        ]);

        // ─── Staff / Users ────────────────────────────────────────────────────────

        $unallocated_user = User::firstOrCreate(
            ['email' => 'unallocated@silvergro.co.za'],
            ['name' => 'Unallocated', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $unallocated_user->id], ['first_name' => 'Unallocated']);

        $allan_user = User::firstOrCreate(
            ['email' => 'allan@silvergro.co.za'],
            ['name' => 'Allan', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $allan_user->id], ['first_name' => 'Allan']);

        $maralize_user = User::firstOrCreate(
            ['email' => 'maralize@silvergro.co.za'],
            ['name' => 'Marlize', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $maralize_user->id], ['first_name' => 'Marlize']);

        $harry_user = User::firstOrCreate(
            ['email' => 'harry@silvergro.co.za'],
            ['name' => 'Harry', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $harry_user->id], ['first_name' => 'Harry']);

        $lize_user = User::firstOrCreate(
            ['email' => 'lize@silvergro.co.za'],
            ['name' => 'Liza-Marie', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $lize_user->id], ['first_name' => 'Liza-Marie']);

        $desire_user = User::firstOrCreate(
            ['email' => 'desiree@silvergro.co.za'],
            ['name' => 'Desiree', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $desire_user->id], ['first_name' => 'Desiree']);

        $hennie_user = User::firstOrCreate(
            ['email' => 'hennie@silvergro.co.za'],
            ['name' => 'Hennie', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $hennie_user->id], ['first_name' => 'Hennie']);

        $petro_user = User::firstOrCreate(
            ['email' => 'petro@silvergro.co.za'],
            ['name' => 'Petro', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $petro_user->id], ['first_name' => 'Petro']);

        $landi_user = User::firstOrCreate(
            ['email' => 'Landi@silvergro.co.za'],
            ['name' => 'Landi', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $landi_user->id], ['first_name' => 'Landi']);

        $henry_user = User::firstOrCreate(
            ['email' => 'rhfaul@gmail.com'],
            ['name' => 'Henry', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $henry_user->id], ['first_name' => 'Henry']);

        $simone_user = User::firstOrCreate(
            ['email' => 'simone@silvergro.co.za'],
            ['name' => 'simone', 'password' => bcrypt('test1234')]
        );
        Staff::firstOrCreate(['user_id' => $simone_user->id], ['first_name' => 'simone']);

        // assignRole uses syncWithoutDetaching — idempotent.
        $henry_user->assignRole('SystemTesterRole');
        $henry_user->assignRole('TraderRole');
        $henry_user->assignRole('AdminRole');

        // ─── Trade Rules ──────────────────────────────────────────────────────────

        $rule_1 = TradeRule::firstOrCreate(
            ['name' => 'R0 to  R199,999.00'],
            ['min_trade_value' => 0, 'max_trade_value' => 199999.00]
        );
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_1->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'TraderRole',
        ]);

        $rule_2 = TradeRule::firstOrCreate(
            ['name' => 'R200 000 to  R3,999,999.00'],
            ['min_trade_value' => 200000, 'max_trade_value' => 3999999.00]
        );
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_2->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'TradeDirectorRole',
        ]);
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_2->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'TraderRole',
        ]);

        $rule_3 = TradeRule::firstOrCreate(
            ['name' => 'R4 000 000 to  R9,999,999.00'],
            ['min_trade_value' => 4000000, 'max_trade_value' => 9999999.00]
        );
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'TradeDirectorRole',
        ]);
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'FinancialDirectorRole',
        ]);
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role'           => 'TraderRole',
        ]);

        $rule_opp_1 = TradeRuleOpp::firstOrCreate(['name' => 'Purchase is C.O.D']);
        $rule_opp_2 = TradeRuleOpp::firstOrCreate(['name' => 'Supplier Suspended']);

        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_opp_1->id,
            'poly_rule_type' => get_class($rule_opp_1),
            'role'           => 'FinancialDirectorRole',
        ]);
        TradeRuleRole::firstOrCreate([
            'poly_rule_id'   => $rule_opp_2->id,
            'poly_rule_type' => get_class($rule_opp_1),
            'role'           => 'FinancialDirectorRole',
        ]);

        // ─── TransLinkTypes ───────────────────────────────────────────────────────

        TransLinkType::firstOrCreate(['name' => 'sc_to_pc']);
        TransLinkType::firstOrCreate(['name' => 'pc_to_sc']);
        TransLinkType::firstOrCreate(['name' => 'mq_to_pc']);
        TransLinkType::firstOrCreate(['name' => 'mq_to_sc']);
        TransLinkType::firstOrCreate(['name' => 'pc_to_pc']);

        // ─── Address Types ────────────────────────────────────────────────────────

        // delivery=1, physical=2, postal=3, collection=4
        AddressType::firstOrCreate(['type' => 'Delivery']);
        AddressType::firstOrCreate(['type' => 'Physical']);
        AddressType::firstOrCreate(['type' => 'Postal']);
        AddressType::firstOrCreate(['type' => 'Collection']);

        // ─── Customer Ratings ─────────────────────────────────────────────────────

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $rating) {
            CustomerRating::firstOrCreate(['value' => $rating]);
        }

        // ─── Invoice Basis ────────────────────────────────────────────────────────

        InvoiceBasis::firstOrCreate(['value' => 'Upload Weight']);
        InvoiceBasis::firstOrCreate(['value' => 'Offload Weight']);

        // ─── Contact Types ────────────────────────────────────────────────────────

        ContactType::firstOrCreate(['type' => 'Telephone']);
        ContactType::firstOrCreate(['type' => 'Cellphone']);
        ContactType::firstOrCreate(['type' => 'Fax number']);
        ContactType::firstOrCreate(['type' => 'Email']);

        // ─── Terms of Payment ─────────────────────────────────────────────────────

        TermsOfPaymentBasis::firstOrCreate(['value' => 'Days from Invoice Date']);
        TermsOfPaymentBasis::firstOrCreate(['value' => 'Days from Statement']);

        TermsOfPayment::firstOrCreate(['value' => 'C.O.D'],         ['days' => 0]);
        TermsOfPayment::firstOrCreate(['value' => 'Unallocated'],   ['days' => 0]);
        TermsOfPayment::firstOrCreate(['value' => '3 Days'],        ['days' => 3]);
        TermsOfPayment::firstOrCreate(['value' => '7 Days'],        ['days' => 7]);
        TermsOfPayment::firstOrCreate(['value' => '14 Days'],       ['days' => 14]);
        TermsOfPayment::firstOrCreate(['value' => '30 Days'],       ['days' => 30]);
        TermsOfPayment::firstOrCreate(['value' => '60 Days'],       ['days' => 60]);
        TermsOfPayment::firstOrCreate(['value' => '90 Days'],       ['days' => 90]);
        TermsOfPayment::firstOrCreate(['value' => 'F.C.A'],         ['days' => 0]);
        TermsOfPayment::firstOrCreate(['value' => 'Prepaid'],       ['days' => 0]);

        // ─── Unallocated Entity Placeholders ─────────────────────────────────────

        $customer_parent = CustomerParent::firstOrCreate(
            ['id' => 1],
            [
                'last_legal_name'          => 'Unallocated',
                'terms_of_payment_id'      => 1,
                'invoice_basis_id'         => 1,
                'terms_of_payment_basis_id'=> 1,
                'days_overdue_allowed_id'  => 1,
                'customer_rating_id'       => 1,
            ]
        );

        $customer_parent_2 = CustomerParent::firstOrCreate(
            ['id' => 2],
            [
                'last_legal_name'          => 'Split Parent',
                'terms_of_payment_id'      => 1,
                'invoice_basis_id'         => 1,
                'terms_of_payment_basis_id'=> 2,
                'days_overdue_allowed_id'  => 1,
                'customer_rating_id'       => 1,
            ]
        );

        Address::firstOrCreate(
            ['poly_address_id' => $customer_parent_2->id, 'poly_address_type' => 'App\\Models\\CustomerParent'],
            [
                'line_1'           => 'Split Address',
                'line_2'           => 'See split customer',
                'line_3'           => '',
                'country'          => 'none',
                'code'             => 1234,
                'address_type_id'  => 1,
                'is_primary'       => 1,
                'longitude'        => 0,
                'latitude'         => 0,
                'directions'       => 'Address per split customer',
            ]
        );

        Supplier::firstOrCreate(
            ['id' => 1],
            ['last_legal_name' => 'Unallocated', 'terms_of_payment_id' => 1]
        );

        Transporter::firstOrCreate(
            ['id' => 1],
            ['last_legal_name' => 'Unallocated', 'terms_of_payment_id' => 1]
        );

        Customer::firstOrCreate(
            ['id' => 1],
            [
                'last_legal_name'          => 'Unallocated',
                'terms_of_payment_id'      => 1,
                'invoice_basis_id'         => 1,
                'terms_of_payment_basis_id'=> 1,
                'days_overdue_allowed_id'  => 1,
                'customer_rating_id'       => 1,
                'customer_parent_id'       => $customer_parent->id,
            ]
        );

        // ─── Transport Rate Basis ─────────────────────────────────────────────────

        TransportRateBasis::firstOrCreate(['name' => 'Unallocated']);
        TransportRateBasis::firstOrCreate(['name' => 'Per Ton']);
        TransportRateBasis::firstOrCreate(['name' => 'Per Load']);

        // ─── Billing Units ────────────────────────────────────────────────────────

        BillingUnits::firstOrCreate(['name' => 'Unallocated'], ['kgs' => 0]);
        BillingUnits::firstOrCreate(['name' => 'Tons'],        ['kgs' => 1000]);
        BillingUnits::firstOrCreate(['name' => '25KG Bags'],   ['kgs' => 25]);
        BillingUnits::firstOrCreate(['name' => '30KG Bags'],   ['kgs' => 30]);
        BillingUnits::firstOrCreate(['name' => '35KG Bags'],   ['kgs' => 35]);
        BillingUnits::firstOrCreate(['name' => '40KG Bags'],   ['kgs' => 40]);
        BillingUnits::firstOrCreate(['name' => '50KG Bags'],   ['kgs' => 50]);

        // ─── Packaging ────────────────────────────────────────────────────────────

        Packaging::firstOrCreate(['name' => 'Unallocated'],           ['old_id' => 0]);
        Packaging::firstOrCreate(['name' => '25KG Bags'],             ['old_id' => 1]);
        Packaging::firstOrCreate(['name' => '30KG Bags'],             ['old_id' => 2]);
        Packaging::firstOrCreate(['name' => '35KG Bags'],             ['old_id' => 3]);
        Packaging::firstOrCreate(['name' => 'Bales (Small Pack)'],    ['old_id' => 4]);
        Packaging::firstOrCreate(['name' => '4 Wire Bales (Big Pack)'],['old_id' => 5]);
        Packaging::firstOrCreate(['name' => '6 Wire Bales (Big Pack)'],['old_id' => 6]);
        Packaging::firstOrCreate(['name' => 'Bulk'],                  ['old_id' => 7]);
        Packaging::firstOrCreate(['name' => '50KG Bags'],             ['old_id' => 8]);
        Packaging::firstOrCreate(['name' => '40KG Bags'],             ['old_id' => 9]);

        // ─── Product Sources ──────────────────────────────────────────────────────

        ProductSource::firstOrCreate(['name' => 'Unallocated']);
        ProductSource::firstOrCreate(['name' => 'Other']);
        ProductSource::firstOrCreate(['name' => 'Mill']);
        ProductSource::firstOrCreate(['name' => 'Farm']);
        ProductSource::firstOrCreate(['name' => 'Import']);
        ProductSource::firstOrCreate(['name' => 'Warehouse']);
        ProductSource::firstOrCreate(['name' => 'Silo']);

        // ─── Vehicle Types ────────────────────────────────────────────────────────

        VehicleType::firstOrCreate(['name' => 'Unallocated']);
        VehicleType::firstOrCreate(['name' => 'Flatbed']);
        VehicleType::firstOrCreate(['name' => 'Taut Liner']);
        VehicleType::firstOrCreate(['name' => 'Tri-Axle']);
        VehicleType::firstOrCreate(['name' => 'Dropside link']);
        VehicleType::firstOrCreate(['name' => 'Walking Floor']);

        // ─── Regular Vehicle & Driver (Unallocated placeholders) ─────────────────

        RegularVehicle::firstOrCreate(
            ['id' => 1],
            ['vehicle_type_id' => 1, 'reg_no' => 'N/A', 'comment' => 'Unallocated vehicle.']
        );

        RegularDriver::firstOrCreate(
            ['id' => 1],
            ['first_name' => 'Unallocated', 'last_name' => 'Unallocated']
        );

        // ─── Loading Hour Options ─────────────────────────────────────────────────

        foreach (range(0, 23) as $hour) {
            LoadingHourOption::firstOrCreate(['name' => sprintf('%02d:00', $hour)]);
        }

        // ─── Confirmation Types ───────────────────────────────────────────────────

        ConfirmationTypes::firstOrCreate(['name' => 'Unallocated']);
        ConfirmationTypes::firstOrCreate(['name' => 'Fax']);
        ConfirmationTypes::firstOrCreate(['name' => 'Telephone']);
        ConfirmationTypes::firstOrCreate(['name' => 'Email']);

        // ─── Contract Types ───────────────────────────────────────────────────────

        ContractType::firstOrCreate(['name' => 'Unallocated']);
        ContractType::firstOrCreate(['name' => 'PC']);
        ContractType::firstOrCreate(['name' => 'SC']);
        ContractType::firstOrCreate(['name' => 'MQ']);

        // ─── Status Entities ──────────────────────────────────────────────────────

        StatusEntity::firstOrCreate(['entity' => 'unallocated']);
        StatusEntity::firstOrCreate(['entity' => 'transport']);
        StatusEntity::firstOrCreate(['entity' => 'mill']);
        StatusEntity::firstOrCreate(['entity' => 'driver']);
        StatusEntity::firstOrCreate(['entity' => 'quality']);
        StatusEntity::firstOrCreate(['entity' => 'general']);

        // ─── Status Types ─────────────────────────────────────────────────────────

        StatusType::firstOrCreate(['type' => 'unallocated']);
        StatusType::firstOrCreate(['type' => 'delayed']);
        StatusType::firstOrCreate(['type' => 'breakdown']);
        StatusType::firstOrCreate(['type' => 'cancelled']);
        StatusType::firstOrCreate(['type' => 'load_slipped']);
        StatusType::firstOrCreate(['type' => 'driver']);
        StatusType::firstOrCreate(['type' => 'overweight_control']);
        StatusType::firstOrCreate(['type' => 'slow']);
        StatusType::firstOrCreate(['type' => 'stopped_demand_side']);
        StatusType::firstOrCreate(['type' => 'wet']);
        StatusType::firstOrCreate(['type' => 'moisture_content']);
        StatusType::firstOrCreate(['type' => 'contamination']);
        StatusType::firstOrCreate(['type' => 'grade_specification']);
        StatusType::firstOrCreate(['type' => 'visual']);
        StatusType::firstOrCreate(['type' => 'variance_detected']);

        // ─── Invoice Statuses ─────────────────────────────────────────────────────

        InvoiceStatus::firstOrCreate(['name' => 'Unallocated']);
        InvoiceStatus::firstOrCreate(['name' => 'Closed']);
        InvoiceStatus::firstOrCreate(['name' => 'In Process']);
        InvoiceStatus::firstOrCreate(['name' => 'Cancelled']);
        InvoiceStatus::firstOrCreate(['name' => 'Requires Immediate Attention']);
        InvoiceStatus::firstOrCreate(['name' => 'Requires Attention']);
        InvoiceStatus::firstOrCreate(['name' => 'Contracted Client']);
        InvoiceStatus::firstOrCreate(['name' => 'Customer Required']);

        // ─── Custom Reports ───────────────────────────────────────────────────────

        $this->call(CustomReportFlatViewSeeder::class);

        $demoReport = CustomReport::firstOrCreate(
            ['name' => 'Demo Report'],
            ['created_by_id' => 1, 'comment' => 'Demo Report to test']
        );

        $reportModels = [
            ['class_name' => 'App\Models\TransportTransaction', 'attribute_name' => 'id'],
            ['class_name' => 'App\Models\TransportTransaction', 'attribute_name' => 'old_id'],
            ['class_name' => 'App\Models\TransportTransaction', 'attribute_name' => 'old_deal_ticket'],
            ['class_name' => 'App\Models\TransportTransaction', 'attribute_name' => 'a_mq'],
            ['class_name' => 'App\Models\TransportTransaction', 'attribute_name' => 'contract_type_id'],
        ];

        foreach ($reportModels as $model) {
            CustomReportModel::firstOrCreate(
                ['report_id' => $demoReport->id, 'class_name' => $model['class_name'], 'attribute_name' => $model['attribute_name']],
                ['created_by_id' => 1]
            );
        }
    }
}
