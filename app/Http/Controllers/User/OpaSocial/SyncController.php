<?php

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

namespace App\Http\Controllers\User\OpaSocial;

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
        return view('changelogs');
    }

    public function syncIndexData()
    {
        $sn = SyncNotification::all();
        return datatables()
            ->of($sn)->editColumn('package_name', function ($sn) {
                return str_limit($sn->package_name, 50);
            })->rawColumns(['package_id', 'package_name', 'reason'])->toJson();
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

    public function __construct()
    {
        // 
    }
}
