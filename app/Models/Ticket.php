<?php

namespace App\Models;


class Ticket extends \Illuminate\Database\Eloquent\Model
{
    protected $appends = array("unread_message_count");
    protected $fillable = array("topic", "request", "paymentmode", "transaction", "email", "amount", "orderids", "subject", "status", "description", "user_id", "is_read");


    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUnreadMessageCountAttribute()
    {
        return $this->messages()->where(array("is_read" => false))->whereNotIn("user_id", array(\Illuminate\Support\Facades\Auth::user()->id))->count();
    }

    public function getStatusAttribute($status)
    {
        return title_case($status);
    }

    public function getCreatedAtAttribute($date)
    {
        return (is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString());
    }

    public function getUpdatedAtAttribute($date)
    {
        return (is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->toDateTimeString());
    }
}
