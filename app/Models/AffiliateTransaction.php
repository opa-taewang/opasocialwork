<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateTransaction extends Model
{
    protected $table = 'affiliate_transactions';
    protected $fillable = [
        'price', 'transferedFund'
    ];

    public function Buser()
    {
        return $this->belongsTo('App\User', 'buyUid');
    }
    public function Ruser()
    {
        return $this->belongsTo('App\User', 'refUid');
    }
    public function packages()
    {
        return $this->belongsTo('App\Package', 'package_id');
    }
}
