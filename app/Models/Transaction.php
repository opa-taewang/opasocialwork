<?php

namespace App\Models;


class Transaction extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = array("amount", "amountconversion", "payment_method_id", "details", "user_id");

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
