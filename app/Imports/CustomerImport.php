<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */


    public function collection(Collection $rows)
    {

         $fillable = ['first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active'
        ,'terms_of_payment_id','invoice_basis_id','customer_rating_id','days_overdue_allowed_id','is_vat_exempt','is_vat_cert_received',
        'credit_limit','credit_limit_hard','comment'];

         //customername	legal_no	address_type	address1	address2	address3	address4	province	country	paddress1	paddress2	paddress3	paddress4	pprovince	pcountry	deliveryaddress1	deliveryaddress2	deliveryaddress3	deliveryaddress4	deliveryprovince	deliverycountry		termsofpayment	vatexempt	vatcertificatereceived	invoicebasis	staff_assigned_to_customer1	customer_rating	credit_limit	hard_credit_limit	days_overdue_allowed

        foreach ($rows as $row)
        {
            if ($row != null){

                if ($row['customername'] != ''){


                    $comment_combined = trim($row['contactperson']).' '.trim($row['telno']).' '.trim($row['alttelno']).' '.trim($row['cellno']).' '.trim($row['telno']).' '.trim($row['email']);
                    $terms_of_payment_id = trim($row['termsofpayment']) == '' ? 2 : doubleval(trim($row['termsofpayment']));
                    $invoice_basis_id = trim($row['invoicebasis']) == '' ? 1 : doubleval(trim($row['invoicebasis']));
                    $customer_rating_id = trim($row['customer_rating']) == '' ? 6 : doubleval(trim($row['customer_rating']));
                    $days_overdue_allowed_id = trim($row['days_overdue_allowed']) == '' ? 2 : doubleval(trim($row['days_overdue_allowed']));
                    $is_vat_exempt = trim($row['vatexempt']) == '1';
                    $is_vat_cert_received = trim($row['vatcertificatereceived']) == '1';
                    $credit_limit = trim($row['credit_limit']) == '' ? 0 : doubleval(trim($row['credit_limit']));
                    $credit_limit_hard = trim($row['hard_credit_limit']) == '' ? 0 : doubleval(trim($row['hard_credit_limit']));

                    $customer_parent= CustomerParent::first();


                    $customer = Customer::create([
                        'id'=>trim($row['id']),
                        'last_legal_name'=>trim($row['customername']),
                        'id_reg_no'=>trim($row['legal_no']),
                        'terms_of_payment_id'=>$terms_of_payment_id,
                        'invoice_basis_id'=>$invoice_basis_id,
                        'customer_rating_id'=>$customer_rating_id,
                        'terms_of_payment_basis_id'=>1,
                        'is_vat_exempt'=>$is_vat_exempt,
                        'is_vat_cert_received'=>$is_vat_cert_received,
                        'credit_limit'=>$credit_limit,
                        'credit_limit_hard'=>$credit_limit_hard,
                        'comment'=>$comment_combined,
                        'customer_parent_id'=>$customer_parent->id
                    ]);

                    $staff_id = trim($row['staff_assigned_to_customer1']) == '' ? 1 : trim($row['staff_assigned_to_customer1']) ;

                    $staff = Staff::find($staff_id);

                    if ($staff != null){

                        $customer->staff()->attach($staff);
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
                            'poly_address_type' =>  get_class($customer),
                            'poly_address_id' => $customer->id,
                        ]);
                    }

                    //paddress1	paddress2	paddress3	paddress4	pprovince	pcountry

                    //postal
                    if (trim($row['paddress1']) != ''){
                        $address = Address::create([
                            'line_1' => str_replace(',', '', trim($row['paddress1'])),
                            'line_2' => str_replace(',', '', trim($row['paddress2'])),
                            'line_3' => str_replace(',', '', trim($row['paddress3'])).$row['pprovince'] ==''?'':', '.str_replace(',', '', trim($row['pprovince'])),
                            'country' => str_replace(',', '', trim($row['pcountry'])),
                            'code' => str_replace(',', '', trim($row['paddress4'])),
                            'address_type_id' =>  2,
                            'poly_address_type' =>  get_class($customer),
                            'poly_address_id' => $customer->id,
                        ]);
                    }

                    //deliveryaddress1	deliveryaddress2	deliveryaddress3	deliveryaddress4 deliveryprovince	deliverycountry
                    //delivery
                    if (trim($row['deliveryaddress1']) != ''){
                        $address = Address::create([
                            'line_1' => str_replace(',', '', trim($row['deliveryaddress1'])),
                            'line_2' => str_replace(',', '', trim($row['deliveryaddress2'])),
                            'line_3' => str_replace(',', '', trim($row['deliveryaddress3'])).$row['deliveryprovince'] ==''?'':', '.str_replace(',', '', trim($row['deliveryprovince'])),
                            'country' => str_replace(',', '', trim($row['deliverycountry'])),
                            'code' => str_replace(',', '', trim($row['deliveryaddress4'])),
                            'address_type_id' =>  1,
                            'poly_address_type' =>  get_class($customer),
                            'poly_address_id' => $customer->id,
                        ]);
                    }


                }


            }


        }
    }
}
