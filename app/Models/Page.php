<?php


namespace App\Models;

class Page extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ["slug", "content", "meta_tags"];
}
