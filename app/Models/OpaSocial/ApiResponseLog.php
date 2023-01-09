<?php


namespace App\Models\OpaSocial;

class ApiResponseLog extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["order_id", "api_id", "response"];
    public function api()
    {
        return $this->belongsTo(API::class);
    }
}
