<?php


namespace App\Models\OpaSocial;

class Subscription extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["quantity", "user_id", "package_id", "posts", "price", "status", "link"];
    public function package()
    {
        return $this->belongsTo("App\\Package");
    }
    public function user()
    {
        return $this->belongsTo("App\\User");
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
