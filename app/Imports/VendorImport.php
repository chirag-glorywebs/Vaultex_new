<?php

namespace App\Imports;

use App\Models\user;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class VendorImport implements ToModel,WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new user([
            'vendor_code' => $row['vendor_code'],
            'price_list_no' => $row['price_list_no'],
            'vendor_credit_limit' => $row['vendor_credit_limit'],
            'name' => $row['name'],
            'email' => $row['email'],
            'mobile' => $row['mobile'],
            'phone' => $row['phone'],
            'user_role' => $row['user_role'],
        ]);
    }
}
