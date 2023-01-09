<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;
use App\SyncNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SyncController extends Controller
{
    public function mail()
    {
        $exitCode = Artisan::call('seller:sync');
        Session::flash('alert', 'Seller Sites Checked.');
        Session::flash('alertClass', 'success');
        return redirect()->back();
    }

    public function syncIndex()
    {
        return view('admin.automate.sync');
    }

    public function syncIndexData()
    {
        $sn = SyncNotification::all();
        return datatables()
            ->of($sn)
            ->toJson();
    }
}
