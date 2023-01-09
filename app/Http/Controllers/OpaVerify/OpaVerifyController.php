<?php

namespace App\Http\Controllers\OpaVerify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OpaVerifyController extends Controller
{
    public function dashboard()
    {
        return view('main.user.dashboard');
    }
}
