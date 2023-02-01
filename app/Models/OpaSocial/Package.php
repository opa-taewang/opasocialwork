<?php

namespace App\Models\OpaSocial;


class Package extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = array("name", "price_per_item", "position_id", "minimum_quantity", "maximum_quantity", "performance", "description", "slug", "status", "service_id", "custom_comments", "features", "preferred_api_id", "license_codes", "script", "refillbtn", "position", "image", "script_name", "packagetype", "mydate", "order_limit", "refill_time", "refill_period");
    protected $dates = ['mydate'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function limitReached()
    {
        // if (request()->server('SERVER_NAME') == base64_decode(config('database.connections.mysql.xdriver'))) {
        $cnt = Order::where('package_id', '=', $this->id)->where('user_id', '=', \Illuminate\Support\Facades\Auth::id())->whereIn('status', ['COMPLETED', 'PARTIAL', 'INPROGRESS', 'PROCESSING', 'PENDING'])->count();

        if ($this->status == 'INACTIVE') {
            return true;
        } else if ($this->order_limit == 0) {
            return false;
        } else if ($cnt < $this->order_limit) {
            return false;
        } else {
            return true;
        }
        // }

        // return true;
    }
}
