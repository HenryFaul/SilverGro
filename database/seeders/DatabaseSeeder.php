<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AddressType;
use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\CustomerRating;
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
use App\Models\SystemPlayer;
use App\Models\ContactType;
use App\Models\SystemPlayerType;
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
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //Standard
/*        Add new line
    Update supplier
    Update product
    Update customer
    Update transport
    Update pricing
    Update process
    Delete system documents
    Delete user documents
    Manage users
    Manage products
    Create dealtickerts
    Create purchase contracts
    Create sales contracts*/


        Permission::create(['name' => 'view_only']);
        Permission::create(['name' => 'update_supplier']);
        Permission::create(['name' => 'update_product']);
        Permission::create(['name' => 'update_customer']);
        Permission::create(['name' => 'update_transporter']);
        Permission::create(['name' => 'update_contact']);
        Permission::create(['name' => 'update_pricing']);
        Permission::create(['name' => 'update_process']);
        Permission::create(['name' => 'delete_system_document']);
        Permission::create(['name' => 'delete_user_document']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'create_pc']);
        Permission::create(['name' => 'create_sc']);
        Permission::create(['name' => 'create_mq']);
        Permission::create(['name' => 'link_mq']);
        Permission::create(['name' => 'approve_deal_ticket']);
        Permission::create(['name' => 'override_deal_ticket']);
        Permission::create(['name' => 'edit_adjusted_gp']);
        Permission::create(['name' => 'edit_transport_trans']);

     /*   System Tester
        Admin
        Trader
        Logistics
        Trading Director
        Financial Director
        Super Admin*/

        $viewOnlyRole = Role::create(['name' => 'ViewOnlyRole']);
        $SystemTesterRole = Role::create(['name' => 'SystemTesterRole']);
        $AdminRole = Role::create(['name' => 'AdminRole']);
        $TraderRole = Role::create(['name' => 'TraderRole']);
        $LogisticsRole = Role::create(['name' => 'LogisticsRole']);
        $TradeDirectorRole = Role::create(['name' => 'TradeDirectorRole']);
        $FinancialDirectorRole = Role::create(['name' => 'FinancialDirectorRole']);
        $OpsAdminManger = Role::create(['name' => 'OpsAdmin Manager']);


        $viewOnlyRole->givePermissionTo([
            'view_only',
        ]);

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
            'edit_adjusted_gp',
            'edit_transport_trans'
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
            'edit_transport_trans'
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
            'edit_transport_trans'
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
            'edit_transport_trans'
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
            'edit_transport_trans'
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
            'edit_transport_trans'
        ]);

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
            'edit_transport_trans'
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
            'edit_transport_trans'
        ]);

        //Staff

        $unallocated_user =  User::create([
            'name' => 'Unallocated',
            'email' => 'unallocated@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Unallocated',
            'user_id'=> $unallocated_user->id
        ]);


        $allan_user =  User::create([
            'name' => 'Allan',
            'email' => 'allan@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

       Staff::create([
            'first_name' => 'Allan',
            'user_id'=> $allan_user->id
        ]);

        $maralize_user =  User::create([
            'name' => 'Marlize',
            'email' => 'maralize@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Marlize',
            'user_id'=> $maralize_user->id
        ]);

        $harry_user =  User::create([
            'name' => 'Harry',
            'email' => 'harry@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Harry',
            'user_id'=> $harry_user->id
        ]);


        $lize_user =  User::create([
            'name' => 'Liza-Marie',
            'email' => 'lize@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Liza-Marie',
            'user_id'=> $lize_user->id
        ]);



        $desire_user =  User::create([
            'name' => 'Desiree',
            'email' => 'desiree@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Desiree',
            'user_id'=> $desire_user->id
        ]);

        $hennie_user =  User::create([
            'name' => 'Hennie',
            'email' => 'hennie@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Hennie',
            'user_id'=> $hennie_user->id
        ]);

        $petro_user =  User::create([
            'name' => 'Petro',
            'email' => 'petro@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Petro',
            'user_id'=> $petro_user->id
        ]);

        $landi_user =  User::create([
            'name' => 'Landi',
            'email' => 'Landi@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Landi',
            'user_id'=> $landi_user->id
        ]);


        $henry_user =  User::create([
            'name' => 'Henry',
            'email' => 'rhfaul@gmail.com',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'Henry',
            'user_id'=> $henry_user->id
        ]);


        $simone_user =  User::create([
            'name' => 'simone',
            'email' => 'simone@silvergro.co.za',
            'password' => bcrypt('test1234')
        ]);

        Staff::create([
            'first_name' => 'simone',
            'user_id'=> $simone_user->id
        ]);





        $henry_user->assignRole('SystemTesterRole');
        $henry_user->assignRole('TraderRole');
        $henry_user->assignRole('AdminRole');

        /*Staff::create([
            'first_name' => 'Allan',
        ]);

        Staff::create([
            'first_name' => 'Marlize',
        ]);

        Staff::create([
            'first_name' => 'Harry',
        ]);

        Staff::create([
            'first_name' => 'Liza-Marie',
        ]);

        Staff::create([
            'first_name' => 'Desire',
        ]);

        Staff::create([
            'first_name' => 'Hennie',
        ]);

        Staff::create([
            'first_name' => 'Petro',
        ]);

        Staff::create([
            'first_name' => 'Landi',
        ]);*/



        //$view->assignRole('ViewOnly');


        //TradeRules

    /*    'name','min_trade_value','max_trade_vale'*/

        //'poly_rule_type','poly_rule_id','role'

       $rule_1 = TradeRule::create([
            'name' => 'R0 to  R199,999.00',
            'min_trade_value' => 0,
            'max_trade_value' =>  199999.00 ,
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_1->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'TraderRole',
        ]);

        $rule_2 = TradeRule::create([
            'name' => 'R200 000 to  R3,999,999.00',
            'min_trade_value' => 200000,
            'max_trade_value' =>   3999999.00,
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_2->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'TradeDirectorRole',
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_2->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'TraderRole',
        ]);


        $rule_3 = TradeRule::create([
            'name' => 'R4 000 000 to  R9,999,999.00',
            'min_trade_value' => 4000000,
            'max_trade_value' =>   9999999.00,
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'TradeDirectorRole',
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'FinancialDirectorRole',
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_3->id,
            'poly_rule_type' => get_class($rule_1),
            'role' => 'TraderRole',
        ]);

        $rule_opp_1 = TradeRuleOpp::create([
            'name' => 'Purchase is C.O.D',
        ]);

        $rule_opp_2 = TradeRuleOpp::create([
            'name' => 'Supplier Suspended',
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_opp_1->id,
            'poly_rule_type' => get_class($rule_opp_1),
            'role' => 'FinancialDirectorRole',
        ]);

        TradeRuleRole::create([
            'poly_rule_id' => $rule_opp_2->id,
            'poly_rule_type' => get_class($rule_opp_1),
            'role' => 'FinancialDirectorRole',
        ]);

        //TransLinkTypes

/*        •	sc_to_pc
            •	pc_to_sc
            •	mq_to_pc
            •	mq_to_sc
*/

        TransLinkType::create([
            'name' => 'sc_to_pc'
        ]);

        TransLinkType::create([
            'name' => 'pc_to_sc'
        ]);

        TransLinkType::create([
            'name' => 'mq_to_pc'
        ]);

        TransLinkType::create([
            'name' => 'mq_to_sc'
        ]);

        //AddressType
        //delivery 1, physical 2, postal 3
        AddressType::create([
            'type' => 'Delivery'
        ]);

        AddressType::create([
            'type' => 'Physical'
        ]);

        AddressType::create([
            'type' => 'Postal'
        ]);

        AddressType::create([
            'type' => 'Collection'
        ]);

        //CustomerRating
        CustomerRating::create([
            'value' => 'A'
        ]);
        CustomerRating::create([
            'value' => 'B'
        ]);
        CustomerRating::create([
            'value' => 'C'
        ]);
        CustomerRating::create([
            'value' => 'D'
        ]);
        CustomerRating::create([
            'value' => 'E'
        ]);
        CustomerRating::create([
            'value' => 'F'
        ]);

        //InvoiceBasis

        InvoiceBasis::create([
            'value' => 'Upload Weight'
        ]);

        InvoiceBasis::create([
            'value' => 'Offload Weight'
        ]);


        //ContactType

        ContactType::create([
            'type' => 'Telephone'
        ]);

        ContactType::create([
            'type' => 'Cellphone'
        ]);

        ContactType::create([
            'type' => 'Fax number'
        ]);

        ContactType::create([
            'type' => 'Email'
        ]);


        //TermsOfPaymentBasis

        TermsOfPaymentBasis::create([
            'value' => 'Days from Invoice Date'
        ]);

        TermsOfPaymentBasis::create([
            'value' => 'Days from Statement'
        ]);

        //TermsOfPayment

        TermsOfPayment::create([
            'value' => 'C.O.D',
            'days' => 0
        ]);

        TermsOfPayment::create([
            'value' => 'Unallocated',
            'days' => 0
        ]);

        TermsOfPayment::create([
            'value' => '3 Days',
            'days' => 3

        ]);

        TermsOfPayment::create([
            'value' => '7 Days',
            'days' => 7
        ]);

        TermsOfPayment::create([
            'value' => '14 Days',
            'days' => 14
        ]);

        TermsOfPayment::create([
            'value' => '30 Days',
            'days' => 30
        ]);

        TermsOfPayment::create([
            'value' => 'F.C.A',
            'days' => 0
        ]);

        TermsOfPayment::create([
            'value' => 'Prepaid',
            'days' => 0
        ]);



        //Unallocated types

        Supplier::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1]);
        Transporter::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1]);
        Customer::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1,
            'invoice_basis_id'=>1,
            'terms_of_payment_basis_id'=>1,
            'customer_rating_id'=>1]);


        //TransportRateBasis

/*                'Per Ton'
        'Per Load'
        'Unknown'
        ''
        NULL*/

        TransportRateBasis::create([
            'name' => 'Unallocated',
        ]);

        TransportRateBasis::create([
            'name' => 'Per Ton',
        ]);

        TransportRateBasis::create([
            'name' => 'Per Load',
        ]);


        //Billing Units

    /*    'Tons'
        '30KG Bags'
        '35KG Bags'
        '25KG Bags'
        '40KG Bags'*/


/*            '1','25KG Bags','25'
    '2','Tons','1000'
    '3','25KG Bags','25'
    '5','30KG Bags','30'
    '6','35KG Bags','35'
    '7','50KG Bags','50'
    '8','40KG Bags','40'*/


        BillingUnits::create([
            'name' => 'Unallocated',
            'kgs'=>0
        ]);

        BillingUnits::create([
            'name' => 'Tons',
            'kgs'=>1000
        ]);

        BillingUnits::create([
            'name' => '25KG Bags',
            'kgs'=>25
        ]);

        BillingUnits::create([
            'name' => '30KG Bags',
            'kgs'=>30
        ]);

        BillingUnits::create([
            'name' => '35KG Bags',
            'kgs'=>35
        ]);

        BillingUnits::create([
            'name' => '40KG Bags',
            'kgs'=>40
        ]);

        BillingUnits::create([
            'name' => '50KG Bags',
            'kgs'=>50
        ]);

        //Packaging

        Packaging::create([
            'old_id' => 0,
            'name' => 'Unallocated',
        ]);

        Packaging::create([
            'old_id' => 1,
            'name' => '25KG Bags',
        ]);
        Packaging::create([
            'old_id' => 2,
            'name' => '30KG Bags',
        ]);
        Packaging::create([
            'old_id' => 3,
            'name' => '35KG Bags',
        ]);
        Packaging::create([
            'old_id' => 4,
            'name' => 'Bales (Small Pack)',
        ]);
        Packaging::create([
            'old_id' => 5,
            'name' => '4 Wire Bales (Big Pack)',
        ]);
        Packaging::create([
            'old_id' => 6,
            'name' => '6 Wire Bales (Big Pack)',
        ]);
        Packaging::create([
            'old_id' => 7,
            'name' => 'Bulk',
        ]);
        Packaging::create([
            'old_id' => 8,
            'name' => '50KG Bags',
        ]);
        Packaging::create([
            'old_id' => 9,
            'name' => '40KG Bags',
        ]);

        //Product sources

        ProductSource::create([
            'name' => 'Unallocated',
        ]);

        ProductSource::create([
            'name' => 'Other',
        ]);

        ProductSource::create([
            'name' => 'Mill',
        ]);

        ProductSource::create([
            'name' => 'Farm',
        ]);

        ProductSource::create([
            'name' => 'Import',
        ]);

        ProductSource::create([
            'name' => 'Warehouse',
        ]);

        ProductSource::create([
            'name' => 'Silo',
        ]);

        //Vehicle type

        VehicleType::create([
            'name' => 'Unallocated',
        ]);

        VehicleType::create([
            'name' => 'Flatbed',
        ]);

        VehicleType::create([
            'name' => 'Taut Liner',
        ]);

        VehicleType::create([
            'name' => 'Tri-Axle',
        ]);

        VehicleType::create([
            'name' => 'Dropside link',
        ]);

        VehicleType::create([
            'name' => 'Walking Floor',
        ]);

        //regular vehicle

        RegularVehicle::create([
            'vehicle_type_id'=>1,
            'reg_no' => 'N/A',
            'comment'=>'Unallocated vehicle.'
        ]);

        //regular driver

        RegularDriver::create([
           'first_name'=>'Unallocated',
            'last_name'=>'Unallocated',
        ]);


        //Loading Time

        LoadingHourOption::create([
            'name' => '00:00',
        ]);
        LoadingHourOption::create([
            'name' => '01:00',
        ]);
        LoadingHourOption::create([
            'name' => '02:00',
        ]);

        LoadingHourOption::create([
            'name' => '03:00',
        ]);

        LoadingHourOption::create([
            'name' => '04:00',
        ]);

        LoadingHourOption::create([
            'name' => '05:00',
        ]);

        LoadingHourOption::create([
            'name' => '06:00',
        ]);

        LoadingHourOption::create([
            'name' => '07:00',
        ]);

        LoadingHourOption::create([
            'name' => '08:00',
        ]);

        LoadingHourOption::create([
            'name' => '09:00',
        ]);

        LoadingHourOption::create([
            'name' => '10:00',
        ]);

        LoadingHourOption::create([
            'name' => '11:00',
        ]);

        LoadingHourOption::create([
            'name' => '12:00',
        ]);

        LoadingHourOption::create([
            'name' => '13:00',
        ]);

        LoadingHourOption::create([
            'name' => '14:00',
        ]);

        LoadingHourOption::create([
            'name' => '15:00',
        ]);

        LoadingHourOption::create([
            'name' => '16:00',
        ]);

        LoadingHourOption::create([
            'name' => '17:00',
        ]);

        LoadingHourOption::create([
            'name' => '18:00',
        ]);

        LoadingHourOption::create([
            'name' => '19:00',
        ]);

        LoadingHourOption::create([
            'name' => '20:00',
        ]);

        LoadingHourOption::create([
            'name' => '21:00',
        ]);

        LoadingHourOption::create([
            'name' => '22:00',
        ]);

        LoadingHourOption::create([
            'name' => '23:00',
        ]);

        //Confirmation type

        ConfirmationTypes::create([
            'name' => 'Unallocated',
        ]);

        ConfirmationTypes::create([
            'name' => 'Fax',
        ]);

        ConfirmationTypes::create([
            'name' => 'Telephone',
        ]);

        ConfirmationTypes::create([
            'name' => 'Email',
        ]);

        //unallocated,  Fax, Telephone, Email

        //Contract types

        //Unallocated
        //'PC'
        //'SC'
        //'MQ'



        ContractType::create([
            'name' => 'Unallocated',
        ]);

        ContractType::create([
            'name' => 'PC',
        ]);

        ContractType::create([
            'name' => 'SC',
        ]);

        ContractType::create([
            'name' => 'MQ',
        ]);

        //Status entity
        //transport
        //mill
        //driver
        //quality

        StatusEntity::create([
            'entity' => 'unallocated',
        ]);

        StatusEntity::create([
            'entity' => 'transport',
        ]);

        StatusEntity::create([
            'entity' => 'mill',
        ]);

        StatusEntity::create([
            'entity' => 'driver',
        ]);

        StatusEntity::create([
            'entity' => 'quality',
        ]);

        StatusEntity::create([
            'entity' => 'general',
        ]);

        //Status types
        //transport_delayed,
        //transport_breakdown,
        //transport_cancelled,
        //transport_loadslipped,
        //transport_overweight_control,
        //transport_driver,
        //mill_slow,
        //mill_breakdown,
        //mill_stopped_demandside,
        //quality_wet,
        //quality_moisture_content,
        //quality_contamination,
        //quality_grade_specification,
        //quality_visual,
        //general_variance_detected

        StatusType::create([
            'type' => 'unallocated',
        ]);

        StatusType::create([
            'type' => 'delayed',
        ]);

        StatusType::create([
            'type' => 'breakdown',
        ]);

        StatusType::create([
            'type' => 'cancelled',
        ]);

        StatusType::create([
            'type' => 'load_slipped',
        ]);

        StatusType::create([
            'type' => 'driver',
        ]);

        StatusType::create([
            'type' => 'overweight_control',
        ]);


        StatusType::create([
            'type' => 'slow',
        ]);


        StatusType::create([
            'type' => 'stopped_demand_side',
        ]);

        StatusType::create([
            'type' => 'wet',
        ]);

        StatusType::create([
            'type' => 'moisture_content',
        ]);

        StatusType::create([
            'type' => 'contamination',
        ]);

        StatusType::create([
            'type' => 'grade_specification',
        ]);

        StatusType::create([
            'type' => 'visual',
        ]);

        StatusType::create([
            'type' => 'variance_detected',
        ]);

        //invoice status
     /*
    1 unallocated
    2 'Closed'
      3  'In Process'
       4 'Cancelled'
       5 'Requires Immediate Attention'
       6 'Requires Attention'
       7 'Contracted Client'
       8 'Customer Required'*/

        InvoiceStatus::create([
            'name' => 'Unallocated',
        ]);

        InvoiceStatus::create([
            'name' => 'Closed',
        ]);

        InvoiceStatus::create([
            'name' => 'In Process',
        ]);

        InvoiceStatus::create([
            'name' => 'Cancelled',
        ]);

        InvoiceStatus::create([
            'name' => 'Requires Immediate Attention',
        ]);

        InvoiceStatus::create([
            'name' => 'Requires Attention',
        ]);

        InvoiceStatus::create([
            'name' => 'Contracted Client',
        ]);

        InvoiceStatus::create([
            'name' => 'Customer Required',
        ]);

    }
}
