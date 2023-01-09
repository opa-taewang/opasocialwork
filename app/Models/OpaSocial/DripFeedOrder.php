<?php


namespace App\Models\OpaSocial;

class DripFeedOrder extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = [];
    public $timestamps = false;
    public function master()
    {
        return $this->belongsTo(DripFeed::class, "id", "master_id");
    }
    public function slaves()
    {
        return $this->belongsTo(Order::class, "id", "slave_id");
    }
}
