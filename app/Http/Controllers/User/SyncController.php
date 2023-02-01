<?php

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\SyncNotification;
use App\Http\Controllers\Controller;
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
        // if (\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php')) != config('database.connections.mysql.hdriver')) {
        //     abort('506');
        // }
    }
}
