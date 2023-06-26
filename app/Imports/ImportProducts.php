<?php

namespace App\Imports;


use App\Models\Product;
use App\Models\Transporter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportProducts implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public function collection(Collection $rows)
    {

        $is_unallocated = Product::where('id',1)->exists();

        if (!$is_unallocated){

            Product::create([
                'old_id'=>1,
                'name'=>'unallocated'
            ]);
        }

        foreach ($rows as $row)
        {
            if ($row != null){

                if ($row['id'] != ''){

                    //'old_id','name','comment','is_active'

                    $product = Product::create([
                        'old_id'=>trim($row['id']),
                        'name'=>trim($row['productname']),
                    ]);

                }


            }


        }
    }
}
