<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate',
    ];

    static public function convert($data, $field)
    {

        // $rate= \DB::table('users')->join('currencies', 'currencies.id', '=','users.currency_id')
        // ->where('users.id', \Auth::user()->id)
        // ->select('currencies.*')
        // ->get();
        // $ConvertedPrice = 0;
        // $symbol = "";
        // foreach($rate as $r)
        // {
        //     $ConvertedPrice=$r->rate*$price;
        //     $symbol=$r->symbol;
        // }

        $result = [];

        foreach ($data as $item) {
            $result[] = $item;
        }
        return $data;

        // $data=array('price'=>$ConvertedPrice,
        // 'symbol'=>$symbol);
        // return $data;
    }

    static public function getConversionRate()
    {
        $rate = \DB::table('users')->join('currencies', 'currencies.id', '=', 'users.currency_id')
            ->where('users.id', \Auth::user()->id)
            ->select('currencies.*')
            ->get()
            ->first();

        $data = array(
            'rate' => $rate->rate,
            'symbol' => $rate->symbol
        );
        return $data;
    }
}
