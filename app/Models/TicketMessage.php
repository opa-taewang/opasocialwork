<?php


namespace App\Models;

class TicketMessage extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["content", "user_id", "ticket_id", "is_read"];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getCreatedAtAttribute($date)
    {
        return is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->diffForHumans();
    }
    public function getUpdatedAtAttribute($date)
    {
        return is_null($date) ? "" : \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->timezone(config("app.timezone"))->diffForHumans();
    }
}
