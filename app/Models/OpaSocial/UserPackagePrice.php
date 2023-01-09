<?php


namespace App\Models\OpaSocial;

class UserPackagePrice extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["user_id", "package_id", "price_per_item"];
    public function user()
    {
        return $this->belongsTo("App\\User");
    }
    public function package()
    {
        return $this->belongsTo("App\\Package");
    }
}
