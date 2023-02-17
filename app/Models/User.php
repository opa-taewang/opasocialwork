<?php

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

namespace App\Models;

use Carbon\Carbon;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'funds',
        'role',
        'status',
        'favorite_pkgs',
        'enabled_payment_methods',
        'api_token',
        'last_login',
        'group_id',
        'ip',
        'points',
        'reffund',
        'treffund',
        'verified'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->profile()->create([
                'enabled_payment_methods' => 'NULL'
            ]);
        });
    }

    public function getReferralAttribute()
    {
        $name = $this->name;
        $val = substr($name, 0, 3);
        $sname = $val[0] . $val[1] . $val[2];
        $id = $this->id;
        return env('APP_URL') . 'ref/' . $sname . '/' . $id;
    }

    public function orders()
    {
        return $this->hasMany(OpaSocial\Order::class);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    public function adminmessages()
    {
        return $this->hasMany('AdminMessage', 'user_id', 'id');
    }

    // public function getStatusAttribute($status)
    // {
    //     return title_case($status);
    // }

    public function getlastLoginAttribute($date)
    {
        return is_null($date) ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
    }

    public function getCreatedAtAttribute($date)
    {
        return is_null($date) ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone(config('app.timezone'))->toDateTimeString();
    }

    public function getUpdatedAtAttribute($date)
    {
        return is_null($date) ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone(config('app.timezone'))->toDateTimeString();
    }
    public function transactions()
    {
        return $this->hasMany('App\AffiliatTransaction');
    }
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
    public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }
}
