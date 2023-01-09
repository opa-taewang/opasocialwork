<?php


namespace App\Models\OpaSocial;

class AutoLikeOrder extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = [];
    public $timestamps = false;
    public function master()
    {
        return $this->belongsTo(AutoLike::class, "id", "master_id");
    }
    public function orderslaves()
    {
        return $this->belongsTo(Order::class, "id", "slave_id");
    }
    public function dripfeedslaves()
    {
        return $this->belongsTo(DripFeed::class, "id", "slave_id");
    }
}
