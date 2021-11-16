<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Mail;
use Carbon\Carbon;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
  /*   protected $fillable = [
        'vendor_code',
        'price_list_no',
        'vendor_credit_limit',
        'name',
        'email',
        //'password',
        'mobile',
        'phone',
        'user_role',
    ]; */

    use HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        
    ];

    public function linkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }

    public static function sendNotificationForCron($responseData)
    {     
        
        $message = $responseData['message'];
        if($responseData['error']){
            $message .= $responseData['error'];
        }
        $cronLog = new CronLog;
        $cronLog->message = $responseData['message'];
        $cronLog->module = $responseData['function'];
        $cronLog->status = 1;
        $cronLog->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $cronLog->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $cronLog->save();
        
        $emailAddress = 'ranjitsinh@glorywebsdev.com';
        Mail::send('API.email.cron-notification', [
            'email' => $emailAddress,
            'data' => $responseData
        ], function ($message) use ($emailAddress) {
            $message->subject('Notification for cron.');
            $message->to($emailAddress);
        });
    }
}
