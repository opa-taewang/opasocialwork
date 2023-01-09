<?php


namespace App\Models\OpaSocial;

class AutoLike extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(Useer::class);
    }
    public function orders()
    {
        return $this->hasManyThrough(Order::class, "App\\AutoLikeOrder", "master_id", "id", "id", "slave_id");
    }
    public function dripfeeds()
    {
        return $this->hasManyThrough(DripFeed::class, "App\\AutoLikeOrder", "master_id", "id", "id", "slave_id");
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function getStatusAttribute($status)
    {
        return title_case($status);
    }
    public function getCreatedAtAttribute($date)
    {
        return is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString();
    }
    public function getUpdatedAtAttribute($date)
    {
        return is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString();
    }
}
