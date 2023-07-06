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


        Permission::create(['name' => 'view-only']);

        $viewOnlyRole = Role::create(['name' => 'ViewOnly']);

        $viewOnlyRole->givePermissionTo([
            'view-only',
        ]);

        $henry=  User::create([
            'name' => 'Henry',
            'email' => 'rhfaul@gmail.com',
            'password' => bcrypt('test1234')
        ]);

        $view=  User::create([
            'name' => 'View_Only',
            'email' => 'view@only.com',
            'password' => bcrypt('test1234')
        ]);


        $view->assignRole('ViewOnly');





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

        //TermsOfPayment

        TermsOfPayment::create([
            'value' => 'C.O.D'
        ]);

        TermsOfPayment::create([
            'value' => 'Unallocated'
        ]);

        TermsOfPayment::create([
            'value' => '3 Days'
        ]);

        TermsOfPayment::create([
            'value' => '7 Days'
        ]);

        TermsOfPayment::create([
            'value' => '14 Days'
        ]);

        TermsOfPayment::create([
            'value' => '30 Days'
        ]);

        TermsOfPayment::create([
            'value' => 'F.C.A'
        ]);

        TermsOfPayment::create([
            'value' => 'Prepaid'
        ]);

        //Staff

        Staff::create([
            'first_name' => 'Unallocated',
        ]);

        Staff::create([
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
        ]);

        //Unallocated types

        Supplier::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1]);
        Transporter::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1]);
        Customer::create(['id'=>1,'last_legal_name'=>'Unallocated','terms_of_payment_id'=>1,
            'invoice_basis_id'=>1,
            'customer_rating_id'=>1,'days_overdue_allowed_id'=>1]);


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
            'name' => 'Unallocated',
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
