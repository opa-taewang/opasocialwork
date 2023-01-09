<?php

namespace App\Models;


class PaymentLog extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = array("details", "currency_code", "amountconversion", "total_amount", "payment_method_id", "user_id");

    public function paymentMethod()
    {
        return $this->belongsTo("App\\PaymentMethod");
    }

    public function user()
    {
        return $this->belongsTo("App\\User");
    }

    public function getCreatedAtAttribute($date)
    {
        return (is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString());
    }

    public function getUpdatedAtAttribute($date)
    {
        return (is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString());
    }
}
