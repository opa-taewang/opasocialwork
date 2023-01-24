<?php

namespace App\Models\OpaSocial;

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = [];
    static public function boot()
    {

        parent::boot();
        static::created(
            function ($model) {
                $cost = $model->package->cost_per_item * $model->quantity;
                Order::where('id', '=', $model->id)->update(['cost' => $cost]);
            }
        );
        static::updated(
            function ($model) {
                if ((strtoupper($model->status) == 'CANCELLED') || (strtoupper($model->status) == 'REFUNDED')) {
                    $cost = $model->price;
                    Order::where('id', '=', $model->id)->update(['cost' => $cost]);
                } else if (strtoupper($model->status) == 'PARTIAL') {
                    $cost = $model->package->cost_per_item * ($model->quantity - $model->remains);
                    Order::where('id', '=', $model->id)->update(['cost' => $cost]);
                }
                if ((strtoupper($model->status) == 'COMPLETED') || (strtoupper($model->status) == 'PARTIAL')) {
                    $user = User::find($model->user_id);
                }
            }
        );
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function api()
    {
        return $this->belongsTo(API::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function getStatusAttribute($status)
    // {
    //     return title_case($status);
    // }

    public function getCreatedAtAttribute($date)
    {
        return is_null($date) ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone(config('app.timezone'))->toDateTimeString();
    }

    public function getUpdatedAtAttribute($date)
    {
        return is_null($date) ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone(config('app.timezone'))->toDateTimeString();
    }

    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values)->where('status', '!=', 'COMPLETED')->sum('price');
    }
}
