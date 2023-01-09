<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commission';

    protected $fillable = [
        'commission_val', 'min_payout'
    ];
}
