<?php


namespace App\Models;

class AdminMessage extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo("App\\User");
    }
    public function admin()
    {
        return $this->belongsTo("App\\User", "admin_id", "user_id");
    }
    public function getStatusAttribute($status)
    {
        return title_case($status);
    }
}
