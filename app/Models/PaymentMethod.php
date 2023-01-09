<?php


namespace App\Models;

class PaymentMethod extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["name", "status", "slug", "config_key", "config_value", "is_disabled_default"];
    public function getStatusAttribute($status)
    {
        return title_case($status);
    }
}
