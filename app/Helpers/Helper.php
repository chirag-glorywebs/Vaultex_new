<?php 

namespace App\Helpers;

class Helper
{
    public static function getRollId(string $roleTitle='')
    {
        if($roleTitle){
            $rolesByUser = [
                'ADMIN' => 1,
                'SALES' => 2,
                'VENDOR' => 3,
                'CUSTOMER' => 4
            ];
            return $rolesByUser[$roleTitle];
        } 
        return false;       
    }
}


?>