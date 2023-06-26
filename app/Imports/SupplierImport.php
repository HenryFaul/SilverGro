<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport  implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {


       // public $fillable = ['first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active','terms_of_payment_id','account_number','comment'];

        //suppliername	legal_no	address_type	address1	address2	address3	address4	country	postaladdress1	postaladdress2	postaladdress3	postaladdress4	postalprovince	postalcountry	collectionaddress1	collectionaddress2	collectionaddress3	collectionaddress4	collectionprovince	collectioncountry	province	contactperson	telno	alttelno	faxno	cellno	email	accountno	paymentterms	staff_assigned_to_supplier1
        foreach ($rows as $row)
        {
            if ($row != null){

                if ($row['suppliername'] != ''){


                    $comment_combined = trim($row['contactperson']).' '.trim($row['telno']).' '.trim($row['alttelno']).' '.trim($row['cellno']).' '.trim($row['telno']).' '.trim($row['email']);

                    $terms_of_payment_id = trim($row['paymentterms']) == '' ? 2 : doubleval(trim($row['paymentterms']));


                    $supplier = Supplier::create([
                        'id'=>trim($row['id']),
                        'last_legal_name'=>trim($row['suppliername']),
                        'id_reg_no'=>trim($row['legal_no']),
                        'is_active'=>1,
                        'terms_of_payment_id'=>$terms_of_payment_id,
                        'account_number'=>trim($row['accountno']),
                        'comment'=>$comment_combined

                    ]);



                    $staff_id = trim($row['staff_assigned_to_supplier1']) == '' ? 1 : trim($row['staff_assigned_to_supplier1']) ;

                    $staff = Staff::find($staff_id);

                    if ($staff != null){

                        $supplier->staff()->attach($staff);
                    }


                    //delivery 1, physical 2, postal 3
                    $address_type_id = trim($row['address_type']) == '3' ? 3 : 2;



                    if (trim($row['address1']) != ''){
                        $address = Address::create([
                            'line_1' => str_replace(',', '', trim($row['address1'])),
                            'line_2' => str_replace(',', '', trim($row['address2'])),
                            'line_3' => str_replace(',', '', trim($row['address3'])).$row['province'] ==''?'':', '.str_replace(',', '', trim($row['province'])),
                            'country' => str_replace(',', '', trim($row['country'])),
                            'code' => str_replace(',', '', trim($row['address4'])),
                            'address_type_id' =>  $address_type_id,
                            'poly_address_type' =>  get_class($supplier),
                            'poly_address_id' => $supplier->id,
                        ]);
                    }

                    //paddress1	paddress2	paddress3	paddress4	pprovince	pcountry

                    //postal
                    if (trim($row['postaladdress1']) != ''){
                        $address = Address::create([
                            'line_1' => str_replace(',', '', trim($row['postaladdress1'])),
                            'line_2' => str_replace(',', '', trim($row['postaladdress2'])),
                            'line_3' => str_replace(',', '', trim($row['postaladdress3'])).$row['postalprovince'] ==''?'':', '.str_replace(',', '', trim($row['postalprovince'])),
                            'country' => str_replace(',', '', trim($row['postalcountry'])),
                            'code' => str_replace(',', '', trim($row['postaladdress4'])),
                            'address_type_id' =>  2,
                            'poly_address_type' =>  get_class($supplier),
                            'poly_address_id' => $supplier->id,
                        ]);
                    }

                    //collectionaddress1	collectionaddress2	collectionaddress3	collectionaddress4	collectionprovince	collectioncountry	province
                    //collection
                    if (trim($row['collectionaddress1']) != ''){
                        $address = Address::create([
                            'line_1' => str_replace(',', '', trim($row['collectionaddress1'])),
                            'line_2' => str_replace(',', '', trim($row['collectionaddress2'])),
                            'line_3' => str_replace(',', '', trim($row['collectionaddress3'])).$row['collectionprovince'] ==''?'':', '.str_replace(',', '', trim($row['collectionprovince'])),
                            'country' => str_replace(',', '', trim($row['collectioncountry'])),
                            'code' => str_replace(',', '', trim($row['collectionaddress4'])),
                            'address_type_id' =>  1,
                            'poly_address_type' =>  get_class($supplier),
                            'poly_address_id' => $supplier->id,
                        ]);
                    }


                }


            }


        }
    }
}
