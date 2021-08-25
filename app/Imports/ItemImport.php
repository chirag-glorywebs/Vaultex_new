<?php

namespace App\Imports;

use App\Models\products;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new products([
            'sku'=>$row['sku'],
            'short_description'=>$row['short_description'],
            'regular_price'=>$row['regular_price'],
            'in_stock'=>$row['in_stock'],
            'brand_id'=>$row['brand_id'],
        ]);
    }
}
