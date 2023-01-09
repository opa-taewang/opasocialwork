<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function getPaymentMethods()
    {
        $enabled_payment_methods = array();
        if (!empty(\Auth::user()->enabled_payment_methods)) {
            $enabled_payment_methods = explode(",", \Auth::user()->enabled_payment_methods);
        }
        $paymentMethods = \App\PaymentMethod::where(array("config_key" => NULL, "status" => "ACTIVE"))->whereIn("id", $enabled_payment_methods)->groupBy("slug")->orderBy("id")->get();
        return view("payments.select-payment-method", compact("paymentMethods"));
    }
}
