<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
   protected  $table="contactus";
   protected $fillable=['email','name','company_name','message','contact'];
}
