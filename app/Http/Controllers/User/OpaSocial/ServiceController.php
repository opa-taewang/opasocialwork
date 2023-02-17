<?php

namespace App\Http\Controllers\User\OpaSocial;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OpaSocial\Package;
use App\Models\OpaSocial\Service;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OpaSocial\UserPackagePrice;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showServices()
    {
        $group = Auth::user()->group;
        $package_ids = explode(",", $group->package_ids);
        $service_ids = Package::whereIn('id', $package_ids)->distinct()->pluck('service_id');
        $services = Service::where(['services.status' => 'ACTIVE', 'packages.status' => 'ACTIVE', 'services.servicetype' => 'DEFAULT'])->join('packages', 'services.id', '=', 'packages.service_id')->whereIn('services.id', $service_ids)->select('services.*')->distinct()->orderBy('services.position')->get();
        $packages = Package::where(['status' => 'ACTIVE', "packages.packagetype" => "DEFAULT"])->whereIn('id', $package_ids)->orderBy('position')->get();

        if (Auth::check()) {
            $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();

            foreach ($packages as $package) {
                if (isset($userPackagePrices[$package->id])) {
                    $package->price_per_item = number_format(($userPackagePrices[$package->id] / 100) * $group->price_percentage, 2);
                }
            }

            $userPackagePrices = NULL;
        }
        $favorite_pkgs = explode(",", Auth::user()->favorite_pkgs);
        return view('main.user.opasocial.services.index', compact('services', 'packages', 'group', 'package_ids', 'userPackagePrices', 'favorite_pkgs'));
    }

    public function addtofavoritetest($pid)
    {
        // $sid = $request->sid;
        // $pid = $request->pid;
        $user = User::find(Auth::user()->id);
        $favorite_pkgs = (!empty($user->favorite_pkgs)) ? explode(",", $user->favorite_pkgs) : array();
        // $user = json_encode($favorite_pkgs);


        if (in_array($pid, $favorite_pkgs)) {
            // if (( != false) {
            $key = array_search($pid, $favorite_pkgs);
            unset($favorite_pkgs[$key]);
            $user->favorite_pkgs = implode(",", $favorite_pkgs);
            $user->save();
            return 'remove';

            // return $user->save() ? 'remove' : '';
            // }
        } else {
            // dd($favorite_pkgs);
            array_push($favorite_pkgs, $pid);
            $user->favorite_pkgs = implode(",", $favorite_pkgs);
            // dd($user->favorite_pkgs);
            dd($user->save());
            return 'add';
            // return $user->save() ? 'add' : '';
        }
        return $favorite_pkgs;
    }

    public function addtofavorite(Request $request)
    {
        // $sid = $request->sid;
        $pid = $request->pid;
        $user = User::find(Auth::user()->id);
        $favorite_pkgs = (!empty($user->favorite_pkgs)) ? explode(",", $user->favorite_pkgs) : array();
        // return $favorite_pkgs;

        if (in_array($pid, $favorite_pkgs)) {
            // if (( != false) {
            $key = array_search($pid, $favorite_pkgs);
            unset($favorite_pkgs[$key]);
            $user->favorite_pkgs = implode(",", $favorite_pkgs);
            $user->save();
            return 'false';
            // return $user->save() ? 'remove' : '';
            // }
        }

        array_push($favorite_pkgs, $pid);
        $user->favorite_pkgs = implode(",", $favorite_pkgs);
        $user->save();
        return 'true';
        // return $user->save() ? 'add' : '';

        // return $favorite_pkgs;
    }
}
