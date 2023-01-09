<?php


namespace App\Models\OpaSocial;

class ApiRequestParam extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["param_key", "param_value", "param_type", "api_type", "api_id"];
}
