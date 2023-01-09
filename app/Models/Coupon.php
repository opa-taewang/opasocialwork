<?php

namespace App\Models;


class Coupon extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'coupons';
    protected $guarded = [];
    public $timestamps = true;

    public function users()
    {
        return $this->hasOne('App\CouponUser', 'coupon_id', 'id');
    }
}
