<?php

namespace App\Models;


class RedeemPoints extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = array("id", "amount", "status", "user_id");

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
