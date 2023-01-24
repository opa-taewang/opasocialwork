<?php


namespace App\Models;

class Broadcast extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["MsgTitle", "MsgText", "MsgStatus", "StartTime", "ExpireTime", "Icon"];

    protected $dates = ['StartTime', 'ExpireTime'];

    public function type()
    {
        return title_case($this->Icon);
    }
}
