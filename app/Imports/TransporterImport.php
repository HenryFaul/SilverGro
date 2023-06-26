<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\Transporter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransporterImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {

        //public $fillable = ['first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active','terms_of_payment_id','account_number','comment','is_git'];

        //id	transportername	legal_no	address_type	address1	address2	address3	address4	province	postaladdress1	postaladdress2	postaladdress3	postaladdress4	postalprovince	postalcountry	country	contactperson	telno	alttelno	faxno	cellno	email	termsofpayment	status	accountno	git

        foreach ($rows as $row)
        {
            if ($row != null){

                if ($row['transportername'] != ''){


                    $comment_combined = trim($row['contactperson']).' '.trim($row['telno']).' '.trim($row['alttelno']).' '.trim($row['cellno']).' '.trim($row['telno']).' '.trim($row['email']);

                    $terms_of_payment_id = trim($row['termsofpayment']) == '' ? 2 : doubleval(trim($row['termsofpayment']));


                    $transporter = Transporter::create([
                        'id'=>trim($row['id']),
                        'last_legal_name'=>trim($row['transportername']),
                        'id_reg_no'=>trim($row['legal_no']),
                        'is_active'=>1,
                        'terms_of_payment_id'=>$terms_of_payment_id,
                        'account_number'=>trim($row['accountno']),
                        'comment'=>$comment_combined,
                        'is_git'=>true

                    ]);



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
                            'poly_address_type' =>  get_class($transporter),
                            'poly_address_id' => $transporter->id,
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
                            'poly_address_type' =>  get_class($transporter),
                            'poly_address_id' => $transporter->id,
                        ]);
                    }



                }


            }


        }
    }
}
