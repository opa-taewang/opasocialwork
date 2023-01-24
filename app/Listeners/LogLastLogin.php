<?php


namespace App\Listeners;

class LogLastLogin
{
    public function __construct()
    {
    }
    public function handle(\Illuminate\Auth\Events\Login $event)
    {
        $event->user->last_login = \Carbon\Carbon::now()->toDateTimeString();
        $event->user->save();
    }
}

?>