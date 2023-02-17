<?php

namespace App\Http\Controllers\User;

use Session;
use App\Page;
use App\Models\User;
use App\Models\Visit;
use App\PaymentMethod;
use GuzzleHttp\Client;
use App\Models\Package;
use App\Models\Service;
use App\UserPackagePrice;
use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\OpaSocial\Order;
use App\Http\Controllers\Controller;
use App\Models\AffiliateTransaction;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    // public function showServices(Request $request)
    // {

    //     if ($request->q) {
    //         $packages = Package::where(['status' => 'ACTIVE'])->whereHas('service', function ($query) use ($request) {
    //             $query->where('name', 'like', '%' . $request->q . '%');
    //         })
    //             ->with(['service' => function ($query) use ($request) {
    //                 $query->where('name', 'like', '%' . $request->q . '%');
    //             }])->get();
    //         if (count($packages) == 0) {
    //             $packages = Package::where(['status' => 'ACTIVE'])->Where('name', 'LIKE', '%' . $request->q . '%')->orderBy('service_id')->get();
    //         }
    //     } else {
    //         // $services = Service::where(['status' => 'ACTIVE'])->get();
    //         $packages = Package::where(['status' => 'ACTIVE'])->orderBy('service_id')->get();
    //     }
    //     if (count($packages) == 0) {
    //         $packages = '';
    //     }
    //     if (Auth::check()) {
    //         $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
    //     }

    //     return view('services', compact('services', 'packages', 'userPackagePrices'));
    // }



    public function showAffiliates()
    {
        $commission = Commission::all();
        $userinfo = Auth::user();
        $name = $userinfo->name;
        $val = substr($name, 0, 3);
        $sname = $val[0] . $val[1] . $val[2];
        $id = $userinfo->id;
        $link = (request()->server('SERVER_NAME')) . '/ref/' . $sname . '/' . $id;
        $refids = Visit::where('refUid', '=', $userinfo->id)->select('refVid')->get();
        $order = new Order;
        $orderPrice = $order->orWhereIn('user_id', $refids);
        $visits = count($refids);
        $registration = User::where('user_from', $id)->count();
        $unpaidAmt = ($orderPrice - ($orderPrice - ($orderPrice * ($commission[0]->commission_val / 100))));

        $earning = AffiliateTransaction::where('refUid', '=', $userinfo->id)->sum('transferedFund');
        return view('main.user.affiliate', compact('commission', 'userinfo', 'visits', 'registration', 'earning', 'link', 'unpaidAmt'));
    }
}
