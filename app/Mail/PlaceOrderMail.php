<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlaceOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user_info,$cart_data,$payment_status,$order,$currancy;
    public function __construct($user_info,$cart_data,$order,$currancy)
    {
        $this->user_info = $user_info;
        // $this->payment_status = $payment_status;
        $this->cart_data = $cart_data;
        $this->currancy = $currancy;
        $this->order = $order;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('order placed successfully')->view('API.email.placeOrder');
       
    }
}
