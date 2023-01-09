<?php

namespace App\Models;


class CouponHistory extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'coupon_history';
    protected $guarded = [];
    public $timestamps = true;

    public function coupon()
    {
        return $this->hasOne('App\Coupon', 'id', 'coupon_id');
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
