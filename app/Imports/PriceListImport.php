<?php

namespace App\Imports;

use App\Models\PriceList;
use Maatwebsite\Excel\Concerns\ToModel;

class PriceListImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|nullw
     */
    public function model(array $row)
    {
        return new PriceList([
            'item_no' => $row[0],
            'item_description' => $row[1],
        ]);
    }
}
