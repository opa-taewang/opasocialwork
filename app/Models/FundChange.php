<?php

namespace App\Models;


class FundChange extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'fundchange';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo("App\\User");
    }
}
