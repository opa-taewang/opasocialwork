<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use App\Page;
use App\Order;
use App\Commission;
use App\Visit;
use App\AffiliateTransaction;
use App\PaymentMethod;
use App\Service;
use App\Package;
use App\UserPackagePrice;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ReferralController extends Controller
{




    public function showServices(Request $request)
    {

        if ($request->q) {
            $packages = Package::where(['status' => 'ACTIVE'])->whereHas('service', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%');
            })
                ->with(['service' => function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->q . '%');
                }])->get();
            if (count($packages) == 0) {
                $packages = Package::where(['status' => 'ACTIVE'])->Where('name', 'LIKE', '%' . $request->q . '%')->orderBy('service_id')->get();
            }
        } else {
            // $services = Service::where(['status' => 'ACTIVE'])->get();
            $packages = Package::where(['status' => 'ACTIVE'])->orderBy('service_id')->get();
        }
        if (count($packages) == 0) {
            $packages = '';
        }
        if (Auth::check()) {
            $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        }

        return view('services', compact('services', 'packages', 'userPackagePrices'));
    }



    public function showAffiliates()
    {
        if (\Auth::check()) {
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
            $unpaidAmt = ($orderPrice - ($orderPrice - ($orderPrice * ($commission[0]->commission_val / 100))));

            $earning = AffiliateTransaction::where('refUid', '=', $userinfo->id)->sum('transferedFund');
            return view('affiliates', compact('commission', 'userinfo', 'visits', 'earning', 'link', 'unpaidAmt'));
        } else {

            return redirect('/login');
        }
    }
}
