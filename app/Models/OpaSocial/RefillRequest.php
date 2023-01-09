<?php

namespace App\Models\OpaSocial;


class RefillRequest extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = array();

    public function order()
    {
        return $this->belongsTo("App\\Order");
    }

    public function getStatusAttribute($status)
    {
        return title_case($status);
    }
}
