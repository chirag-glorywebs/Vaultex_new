<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageOrderStatus extends Model
{
    use HasFactory;
    protected $table = 'manage_order_status';
    protected $primaryKey= 'id';
    public $timestamps = false;
}
