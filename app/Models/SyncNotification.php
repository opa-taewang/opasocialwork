<?php

/**
 * Indusrabbit - SMM Panel script
 * Domain: https://indusrabbit.com/
 * Codecanyon Item: https://codecanyon.net/item/indusrabbit-smm-panel/19821624
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SyncNotification extends Model
{
    protected $guarded = [];

    public function getCreatedAtAttribute($date)
    {
        return is_null($date)
            ? ''
            : Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone(config('app.timezone'))->toDateTimeString();
    }
}
