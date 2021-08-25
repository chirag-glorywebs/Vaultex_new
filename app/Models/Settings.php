<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    public $table='settings';
    public $timestamps = true;
    public const UPDATED_AT =  'settings_updated_at';
    protected $fillable = ['name','value','settings_updated_at'] ;


}
