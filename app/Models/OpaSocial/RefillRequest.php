<?php

namespace App\Models\OpaSocial;

use App\Models\OpaSocial\Order;


class RefillRequest extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = array();

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusAttribute($status)
    {
        return title_case($status);
    }
}
