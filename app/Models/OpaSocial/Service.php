<?php

namespace App\Models\OpaSocial;


class Service extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = array("name", "slug", "description", "is_subscription_allowed", "status", "servicetype", "position");

    public function packages()
    {
        return $this->hasMany("App\\Package");
    }
}
