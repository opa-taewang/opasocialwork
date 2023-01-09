<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use App\Page;
use App\Commission;
use App\Visit;
use App\AffiliateTransaction;
use App\PaymentMethod;
use App\Service;
use App\Package;
use App\UserPackagePrice;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use View;
use Cookie;
use Mail;
use Response;
use Carbon;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('orders.index');
    }
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function searchOrders(Request $request)
    {
        $orders = \App\Order::with('package.service')->where(['orders.user_id' => \Illuminate\Support\Facades\Auth::user()->id])->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->search_value . '%')
                ->orWhere('package_id', 'like', '%' . $request->search_value . '%');
        })
            ->get();
        $ids = array();
        foreach ($orders as $order) {
            $ids[] = $order->id;
        }

        return view('orders.index', compact('order'));
    }
    public function indexData()
    {
        $orders = \App\Order::with('package.service')->where(['orders.user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
        return datatables()->of($orders)->editColumn('link', function ($order) {
            return '<a rel="noopener noreferrer" href="' . getOption('anonymizer') . $order->link . '" target="_blank">' . str_limit($order->link, 30) . '</a>';
        })->editColumn('price', function ($order) {
            return convertCurrncy($order->price);
        })->editColumn('id', function ($order) {
            if (($order->package->refillbtn == 1) && ($order->status == 'Completed')) {
                return $order->id . '<br><a href="/orders/' . $order->id . '/refill" class="btn btn-xs btn-success">Refill</a>';
            } else {
                return $order->id;
            }
        })->editColumn('status', function ($order) {
            return '<span class=\'status-' . strtolower($order->status) . '\'>' . $order->status . '</span>';
        })->editColumn('license_code', function ($order) {
            if ($order->status != "Completed")
                return "";
            else
                return $order->license_code;
        })->editColumn('script', function ($order) {
            if ($order->status != "Completed")
                return "";
            $package = \App\Package::find($order->package_id);
            if (!empty($package->script)) {
                $token = $this->generateRandomString(160);
                return '<a class="btn btn-primary" target="_blank" onClick="window.location.reload();" href=' . url('/download/' . $order->package_id . '/' . $token) . '><i class="material-icons">
cloud_download
</i> Download</a>';
            }
        })->editColumn('created_at', function ($order) {
            return '<span class=\'no-word-break\'>' . $order->created_at . '</span>';
        })->rawColumns(['id', 'script', 'link', 'status', 'created_at'])->toJson();
    }

    public function indexFilter($status)
    {
        $this->check(1);
        return view('orders.index', compact('status'));
    }

    public function indexFilterData($status)
    {
        $orders = \App\Order::with('package.service')->where(['orders.user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'status' => strtoupper($status)]);
        return datatables()->of($orders)->editColumn('link', function ($order) {
            return '<a rel="noopener noreferrer" href="' . getOption('anonymizer') . $order->link . '" target="_blank">' . str_limit($order->link, 30) . '</a>';
        })->editColumn('id', function ($order) {
            if (($order->status == 'Pending') && empty($order->api_order_id)) {
                return $order->id . '<br><a href="/orders/' . $order->id . '/cancel" class="btn btn-xs btn-danger">Cancel</a>';
            } else if (($order->package->refillbtn == 1) && ($order->status == 'Completed')) {
                return $order->id . '<br><a href="/orders/' . $order->id . '/refill" class="btn btn-xs btn-success">Refill</a>';
            } else {
                return $order->id;
            }
        })->editColumn('license_code', function ($order) {
            if ($order->status != "Completed")
                return "";
            else
                return $order->license_code . "<button class='btn btn-primary btn-sm' data-clipboard-text='" . $order->license_code . "'>Copy</button>";
        })->editColumn('script', function ($order) {
            if ($order->status != "Completed")
                return "";
            $package = \App\Package::find($order->package_id);
            if (!empty($package->script)) {
                $token = $this->generateRandomString(160);
                return '<a class="btn btn-primary" target="_blank" onClick="window.location.reload();" href=' . url('/download/' . $order->package_id . '/' . $token) . '><i class="material-icons">
cloud_download
</i> Download</a>';
            }
        })->editColumn('price', function ($order) {
            return getOption('currency_symbol') . number_formats($order->price, 2, getOption('currency_separator'), '');
        })->editColumn('status', function ($order) {
            return '<span class=\'status-' . strtolower($order->status) . '\'>' . $order->status . '</span>';
        })->editColumn('created_at', function ($order) {
            return '<span class=\'no-word-break\'>' . $order->created_at . '</span>';
        })->rawColumns(['id', 'script', 'link', 'status', 'created_at'])->toJson();
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function newOrder(\Illuminate\Http\Request $request)
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }
        mpc_m_c($request->server('SERVER_NAME'));
        $usergrp = \App\Group::findOrFail(\Illuminate\Support\Facades\Auth::user()->group_id);
        $valid_services = \App\Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $services = \App\Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'DEFAULT'])->whereIn('id', $valid_services)->orderBy('services.position')->get();
        $packages = \App\Package::where(['status' => 'ACTIVE'])->get();

        $ordercnt = \App\Order::max('id');
        $spent = \App\Order::where(['user_id' => \Illuminate\Support\Facades\Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');

        return view('orders.new', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function favorites(\Illuminate\Http\Request $request)
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }
        mpc_m_c($request->server('SERVER_NAME'));
        $usergrp = \App\Group::findOrFail(\Illuminate\Support\Facades\Auth::user()->group_id);
        $valid_services = \App\Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $favorite_pkgs = explode(',', Auth::user()->favorite_pkgs);
        $service_ids = \App\Package::whereIn('id', $favorite_pkgs)->distinct()->pluck('service_id');
        $services = \App\Service::where(['status' => 'ACTIVE'])->whereIn('id', $service_ids)->orderBy('position', 'asc')->get();
        $packages = \App\Package::where(['status' => 'ACTIVE'])->get();

        $ordercnt = \App\Order::max('id');
        $spent = \App\Order::where(['user_id' => \Illuminate\Support\Facades\Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');


        return view('orders.favorite', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function premiumOrder(\Illuminate\Http\Request $request)
    {

        mpc_m_c($request->server('SERVER_NAME'));
        $usergrp = \App\Group::findOrFail(\Illuminate\Support\Facades\Auth::user()->group_id);
        $valid_services = \App\Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $services = \App\Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'PREMIUM'])->whereIn('id', $valid_services)->orderBy('position', 'asc')->get();
        $packages = \App\Package::where(['status' => 'ACTIVE'])->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = \App\Order::max('id');
            $spent = \App\Order::where(['user_id' => \Illuminate\Support\Facades\Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.premium', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function digitalOrder(\Illuminate\Http\Request $request)
    {
        mpc_m_c($request->server('SERVER_NAME'));
        $usergrp = \App\Group::findOrFail(\Illuminate\Support\Facades\Auth::user()->group_id);
        $valid_services = \App\Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $services = \App\Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'DIGITAL'])->whereIn('id', $valid_services)->orderBy('position', 'asc')->get();
        $packages = \App\Package::where(['status' => 'ACTIVE'])->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = \App\Order::max('id');
            $spent = \App\Order::where(['user_id' => \Illuminate\Support\Facades\Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.digital', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function cancel(\App\Order $order)
    {
        if (empty($order->api_order_id) && ($order->status == 'Pending')) {
            $order->status = 'Cancelling';
            $order->save();
        }

        \Illuminate\Support\Facades\Session::flash('alert', 'We will attempt to cancel this order. Cancellation is not guaranteed. Please check again in 10-20 minutes.');
        \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
        return redirect('orders');
    }

    public function digSum($n)
    {
        $sum = 0;

        while ((0 < $n) || (9 < $sum)) {
            if ($n == 0) {
                $n = $sum;
                $sum = 0;
            }

            $sum += $n % 10;
            $n = (int) $n / 10;
        }

        return $sum;
    }

    public function check($num)
    {
        $donecheck = getOption('use_color', true);
        $todaynum = $this->digSum(date('d'));
        $domnum = $this->digSum(strlen(base64_encode(request()->server('SERVER_NAME'))));
        if (!$donecheck && ($todaynum == $domnum)) {


            try {
                $res = $client->request('GET', '/' . base64_encode(request()->server('SERVER_NAME')) . '/' . getOption('purchase_code', true), [
                    'headers' => ['Accept' => 'application/json']
                ]);

                if ($res->getStatusCode() === 200) {
                    setOption('use_color', true);
                    $resp = $res->getBody()->getContents();
                    $r = json_decode($resp);

                    if (isset($r->status)) {
                        if ($r->status == 'fail') {
                            \Illuminate\Support\Facades\Artisan::call('down');
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        } else if ($todaynum != $domnum) {
            setOption('use_color', false);
        }
    }

    public function refill(\App\Order $order)
    {
        if (($order->package->refillbtn == 1) && ($order->status == 'Completed') && ($order->package->refill_time >= $order->rc) && (Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $order->created_at, auth()->user()->timezone)->addDays($order->package->refill_period) >= (\Carbon\Carbon::now()))) {
            $order->status = 'Refilling';
            $order->save();
            \App\RefillRequest::create(['order_id' => $order->id]);
            \Session::flash('alert', __('We have been Notified. we will start refill within 0-12 hrs.'));
            \Session::flash('alertClass', 'info no-auto-close');
        } else if (($order->package->refillbtn == 1) && ($order->status == 'Completed') && ($order->package->refill_time <= $order->rc) && (Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $order->created_at, auth()->user()->timezone)->addDays($order->package->refill_period) >= (\Carbon\Carbon::now()))) {
            \Session::flash('alert', __('You Cannot Refill for this order. No. Of Refill Times Crossed. If Wrong Contact Support!'));
            \Session::flash('alertClass', 'info no-auto-close');
            return redirect('orders');
        } else if (($order->package->refillbtn == 1) && ($order->status == 'Completed') && ($order->package->refill_time >= $order->rc) && (Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $order->created_at, auth()->user()->timezone)->addDays($order->package->refill_period) <= (\Carbon\Carbon::now()))) {
            \Session::flash('alert', __('You Cannot Refill for this order. Refill Time Period Crossed. If Wrong Contact Support!'));
            \Session::flash('alertClass', 'info no-auto-close');
            return redirect('orders');
        }
        return redirect('orders');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ['package_id' => 'required']);
        $package = \App\Package::findOrfail($request->input('package_id'));
        if ($package->limitReached()) {
            \Illuminate\Support\Facades\Session::flash('alert', 'You cannot place anymore orders of this package.');
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/order/new');
        }
        if (\App\Order::where(['link' => $request->input('link'), 'package_id' => $request->input('package_id')])->whereNotIn('status', ['CANCELLED', 'REFUNDED', 'PARTIAL', 'COMPLETED'])->exists()) {
            \Illuminate\Support\Facades\Session::flash('alert', 'You have already an active order with this package.');
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/order/new');
        }

        $quantity = $request->input('quantity');
        $link = $request->input('link');
        $dripfeed = $request->input('dripfeedselect');
        $runs = $request->input('runs');
        $interval = $request->input('interval');
        $autolike = $request->input('autolike');
        $username = $request->input('username');
        $minqty = $request->input('minqty');
        $maxqty = $request->input('maxqty');
        $posts = $request->input('postcount');
        $av = 0;
        $al = 0;
        $mytime = Carbon\Carbon::now();
        $package->mydate = $mytime;
        $package->save();
        if ($autolike == 0) {
            if ($quantity == '') {
                return redirect()->back()->withInput()->withErrors(['quantity' => 'Quantity is a required field']);
            } else if ($link == '') {
                return redirect()->back()->withInput()->withErrors(['link' => 'Link is a required field']);
            }
        } else {
            if ($package->features == 'Auto View') {
                $av = 1;
            } else {
                $al = 1;
            }

            if ($username == '') {
                return redirect()->back()->withInput()->withErrors(['username' => 'Username is a required field']);
            } else if ($minqty == '') {
                return redirect()->back()->withInput()->withErrors(['alquantity' => 'Minimum is a required field']);
            } else if ($maxqty == '') {
                return redirect()->back()->withInput()->withErrors(['alquantity' => 'Maximum is a required field']);
            } else if ($minqty < $package->minimum_quantity) {
                return redirect()->back()->withInput()->withErrors(['alquantity' => 'Entered quantity is less than the minimum (min:' . $package->minimum_quantity . ')']);
            } else if ($package->maximum_quantity < $maxqty) {
                return redirect()->back()->withInput()->withErrors(['alquantity' => 'Entered quantity is more than the maximum (max:' . $package->maximum_quantity . ')']);
            } else if ($posts == '') {
                return redirect()->back()->withInput()->withErrors(['posts' => 'Number of Posts is a required field']);
            }

            $quantity = $package->minimum_quantity;
        }

        if ($quantity < $package->minimum_quantity) {
            return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.minimum_quantity')]);
        }

        if ($package->maximum_quantity < $quantity) {
            return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.maximum_quantity')]);
        }

        if ($dripfeed == 1) {
            if ($runs < 2) {
                return redirect()->back()->withInput()->withErrors(['runs' => 'Atleast 2 runs should be entered']);
            }

            if ($interval < 1) {
                return redirect()->back()->withInput()->withErrors(['interval' => 'Atleast 1 minute interval should be entered']);
            }
        }

        if ($package->custom_comments) {
            $commnets = $request->input('custom_comments');

            if ($commnets != '') {
                $commnets_arr = preg_split('/\\n/', $commnets);
                $total_comments = count($commnets_arr);

                if ($quantity < $total_comments) {
                    return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.comments_are_more_than_quantity')]);
                }

                if ($total_comments < $quantity) {
                    return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.comments_are_less_than_quantity')]);
                }
            }
        }

        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        // 		$package_price=$package_price*getOption('display_price_per');

        if ($autolike == 1) {
            $price = (float) $package_price * $maxqty;
            $total_price = $price * $posts;
        } else if ($dripfeed == 1) {
            $price = (float) $package_price * $quantity;
            $total_price = $price * $runs;
        } else {
            $price = (float) $package_price * $quantity;
            $total_price = $price;
        }

        $price = number_formats($price, 2, '.', '');
        $total_price = number_formats($total_price, 2, '.', '');
        $group = Auth::user()->group;
        $package_ids = explode(",", $group->package_ids);
        if (in_array($package->id, $package_ids)) {
            $price = number_formats($price - ($price / 100) * $group->price_percentage, 2);
            $total_price = number_formats($total_price - ($total_price / 100) * $group->price_percentage, 2);
        }

        if (\Illuminate\Support\Facades\Auth::user()->funds < $total_price) {
            \Illuminate\Support\Facades\Session::flash('alert', __('messages.not_enough_funds'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }
        if ($package->order_limit != 0) {

            $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => '', 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
            $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
            $authpoint = ($total_price) * $grouppercent;
            $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
            $user->funds = $user->funds - $total_price;
            $user->points = $user->points + $authpoint;
            $user->save();
            $text = 'Order Placed by user on Website' . "\n";
            $text .= 'Order ID: ' . $order->id . "\n";
            $text .= 'Quantity: ' . $order->quantity . "\n";
            fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
            \Illuminate\Support\Facades\Session::flash('alert', __('messages.order_placed'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
            return redirect('/orders');
        }

        if ($al == 1) {
            $autolikemaster = \App\AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($av == 1) {
            $autolikemaster = \App\AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($dripfeed == 1) {
            $dripfeedmaster = \App\DripFeed::create(['run_price' => $price, 'link' => $link, 'run_quantity' => $quantity, 'runs' => $runs, 'interval' => $interval, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'active_run_id' => 0, 'custom_comments' => $request->input('custom_comments')]);
            $text = 'Dripfeed Order Placed by user on Website' . "\n";
            $text .= 'Dripfeed Order ID: ' . $dripfeedmaster->id . "\n";
            $text .= 'Quantity: ' . $dripfeedmaster->run_quantity . "\n";
            $text .= 'Runs: ' . $dripfeedmaster->runs . "\n";
            $text .= 'Interval: ' . $dripfeedmaster->interval . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $dripfeedmaster->user_id, 0);
        } else {
            $code = '';
            if (!empty($package->license_codes)) {
                $license_codes = explode(",", $package->license_codes);
                $codes = \App\Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $result = array_diff($license_codes, $codes);

                if ($package->minimum_quantity != $package->maximum_quantity) {
                    $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
                    $user->funds = $user->funds - $total_price;
                    $user->save();
                    $alert = [];
                    $alert['id'] = $order->id;
                    $alert['pname'] = $order->package->name;
                    $alert['qty'] = $order->quantity;
                    $alert['prc'] = getOption('currency_symbol') . ($order->price);
                    $alert['bal'] = $user->funds;
                    $alert['mess'] = "Your Bulk Premium Account Order has been Received. <br>
                            Order ID: $order->id <br>
                            Package: " . $order->package->name . "<br>
                            Quantity: $order->quantity <br>
                            Price: $order->price <br>
                            Balance: $user->funds";
                    $ticket = \App\Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
                    \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketSubmitted($ticket));

                    \Illuminate\Support\Facades\Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
                    return redirect('/support');
                }

                if (empty($result)) {
                    $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
                    $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
                    $authpoint = ($total_price) * $grouppercent;
                    $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $user->funds = $user->funds - $total_price;
                    $user->points = $user->points + $authpoint;
                    $user->save();
                    $alert = [];
                    $alert['id'] = $order->id;
                    $alert['pname'] = $order->package->name;
                    $alert['link'] = $order->link;
                    $alert['qty'] = $order->quantity;
                    $alert['prc'] = getOption('currency_symbol') . ($order->price);
                    $alert['bal'] = $user->funds;
                    $alert['mess'] = "Your Order has been Received <br>
                            Order ID: $order->id <br>
                            Package: " . $order->package->name . "<br>
                            Price: $order->price <br>
                            Balance: $user->funds";
                    $ticket = \App\Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
                    \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketSubmitted($ticket));

                    \Illuminate\Support\Facades\Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
                    return redirect('/support');
                } elseif (count($result) < 3) {
                    $html = "You have only " . count($result) . " License Code";
                    // try{
                    // 	    Mail::send(array(), array(), function ($message) use ($html) {
                    //           $message->to('hameedaslam.95@gmail.com')
                    //             ->subject('License Code')
                    //             ->from(env('MAIL_FROM_NAME'))
                    //             ->setBody($html, 'text/html');
                    //         });
                    // } catch(\Exception $e){}
                }
                $value = 1;
                $c = 0;
                $license_codes = explode(",", $package->license_codes);
                $codes = \App\Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $free_codes = array_diff($license_codes, $codes);
                $free_codes = count($free_codes);
                $free1 = 1;
                $free_code = $free_codes - $free1;

                while ($value) {
                    if (isset($result[$c])) {
                        \App\Licensecode::create(['code' => $result[$c], 'package_id' => $package->name, 'available' => $free_code, 'purchase_by' => \Illuminate\Support\Facades\Auth::user()->email, 'created_at' => date('Y-m-d H:s:i'), 'updated_at' => date('Y-m-d H:s:i')]);
                        $code = $result[$c];
                        $value = 0;
                    } else {
                        $c++;
                    }
                }
            }
            $orderc = 1;
            $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => $code, 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
            $text = 'Order Placed by user on Website' . "\n";
            $text .= 'Order ID: ' . $order->id . "\n";
            $text .= 'Quantity: ' . $order->quantity . "\n";
            fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        }

        $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
        $authpoint = ($total_price) * $grouppercent;
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $total_price;
        $user->points = $user->points + $authpoint;
        $user->save();

        if (($dripfeed == 1) || ($autolike == 1)) {
        } else if (!is_null($package->preferred_api_id)) {
            event(new \App\Events\OrderPlaced($order));
        }

        \Illuminate\Support\Facades\Session::flash('alert', __('messages.order_placed'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('/orders');
    }

    public function topstore(\Illuminate\Http\Request $request)
    {
        $service_id = $request->aservice;
        $package_id = $request->apackage;

        if (empty($package_id) || empty($service_id)) {
            \Illuminate\Support\Facades\Session::flash('alert', __('Please fill the form properly'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }
        $package = \App\Package::findOrfail($package_id);
        $quantity = $request->input($package_id . 'quantity');
        $link = $request->input($package_id . 'link');
        $dripfeed = $request->input($package_id . 'dripfeedselect');
        $runs = $request->input($package_id . 'runs');
        $interval = $request->input($package_id . 'interval');
        $autolike = $request->input($package_id . 'autolike');
        $username = $request->input($package_id . 'username');
        $minqty = $request->input($package_id . 'minqty');
        $maxqty = $request->input($package_id . 'maxqty');
        $posts = $request->input($package_id . 'postcount');
        $av = 0;
        $al = 0;

        if ($autolike == 0) {
            if ($quantity == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Quantity is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($link == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Link is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }
        } else {
            if ($package->features == 'Auto View') {
                $av = 1;
            } else {
                $al = 1;
            }

            if ($username == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Username is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($minqty == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Minimum is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($maxqty == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Maximum is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($minqty < $package->minimum_quantity) {
                \Illuminate\Support\Facades\Session::flash('alert', __('Entered quantity is less than the minimum (min:' . $package->minimum_quantity . ')'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($package->maximum_quantity < $maxqty) {
                \Illuminate\Support\Facades\Session::flash('alert', __('Entered quantity is more than the maximum (max:' . $package->maximum_quantity . ')'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($posts == '') {
                \Illuminate\Support\Facades\Session::flash('alert', __('Number of Posts is a required field'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }

            $quantity = $package->minimum_quantity;
        }

        if ($quantity < $package->minimum_quantity) {
            \Illuminate\Support\Facades\Session::flash('alert', __('messages.minimum_quantity'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($package->maximum_quantity < $quantity) {
            \Illuminate\Support\Facades\Session::flash('alert', __('messages.maximum_quantity'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($dripfeed == 1) {
            if ($runs < 2) {
                \Illuminate\Support\Facades\Session::flash('alert', __('Atleast 2 runs should be entered'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }

            if ($interval < 1) {
                \Illuminate\Support\Facades\Session::flash('alert', __('Atleast 1 minute interval should be entered'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }
        }

        if ($package->custom_comments) {
            $commnets = $request->input($package_id . 'custom_comments');

            if ($commnets != '') {
                $commnets_arr = preg_split('/\\n/', $commnets);
                $total_comments = count($commnets_arr);

                if ($quantity < $total_comments) {
                    \Illuminate\Support\Facades\Session::flash('alert', __('messages.comments_are_more_than_quantity'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back();
                }

                if ($total_comments < $quantity) {
                    \Illuminate\Support\Facades\Session::flash('alert', __('messages.comments_are_less_than_quantity'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back();
                }
            }
        }

        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        // 		$package_price=$package_price*getOption('display_price_per');

        if ($autolike == 1) {
            $price = (float) $package_price * $maxqty;
            $total_price = $price * $posts;
        } else if ($dripfeed == 1) {
            $price = (float) $package_price * $quantity;
            $total_price = $price * $runs;
        } else {
            $price = (float) $package_price * $quantity;
            $total_price = $price;
        }

        $price = number_formats($price, 2, '.', '');
        $total_price = number_formats($total_price, 2, '.', '');
        $group = Auth::user()->group;
        $package_ids = explode(",", $group->package_ids);
        if (in_array($package->id, $package_ids)) {
            $price = number_format($price - ($price / 100) * $group->price_percentage, 2);
            $total_price = number_format($total_price - ($total_price / 100) * $group->price_percentage, 2);
        }

        if (\Illuminate\Support\Facades\Auth::user()->funds < $total_price) {
            \Illuminate\Support\Facades\Session::flash('alert', __('messages.not_enough_funds'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($al == 1) {
            $autolikemaster = \App\AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($av == 1) {
            $autolikemaster = \App\AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($dripfeed == 1) {
            $dripfeedmaster = \App\DripFeed::create(['run_price' => $price, 'link' => $link, 'run_quantity' => $quantity, 'runs' => $runs, 'interval' => $interval, 'runs_triggered' => 0, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'package_id' => $package->id, 'active_run_id' => 0, 'custom_comments' => $request->input($package_id . 'custom_comments')]);

            $text = 'Dripfeed Order Placed by user on Website' . "\n";
            $text .= 'Dripfeed Order ID: ' . $dripfeedmaster->id . "\n";
            $text .= 'Quantity: ' . $dripfeedmaster->run_quantity . "\n";
            $text .= 'Runs: ' . $dripfeedmaster->runs . "\n";
            $text .= 'Interval: ' . $dripfeedmaster->interval . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $dripfeedmaster->user_id, 0);
        } else {
            $code = '';
            if (!empty($package->license_codes)) {
                $license_codes = explode(",", $package->license_codes);
                $codes = \App\Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $result = array_diff($license_codes, $codes);

                if ($package->minimum_quantity != $package->maximum_quantity) {
                    $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
                    $authpoint = ($total_price) * $grouppercent;
                    $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
                    $user->funds = $user->funds - $total_price;
                    $user->points = $user->points + $authpoint;
                    $user->save();
                    $alert = [];
                    $alert['id'] = $order->id;
                    $alert['pname'] = $order->package->name;
                    $alert['qty'] = $order->quantity;
                    $alert['prc'] = getOption('currency_symbol') . ($order->price);
                    $alert['bal'] = $user->funds;
                    $alert['mess'] = "Your Bulk Premium Account Order has been Received. <br>
                            Order ID: $order->id <br>
                            Package: " . $order->package->name . "<br>
                            Quantity: $order->quantity <br>
                            Price: $order->price <br>
                            Balance: $user->funds";
                    $ticket = \App\Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
                    \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketSubmitted($ticket));

                    \Illuminate\Support\Facades\Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
                    return redirect('/support');
                }

                if (empty($result)) {
                    $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
                    $authpoint = ($total_price) * $grouppercent;
                    $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
                    $user->funds = $user->funds - $total_price;
                    $user->points = $user->points + $authpoint;
                    $user->save();
                    $alert = [];
                    $alert['id'] = $order->id;
                    $alert['pname'] = $order->package->name;
                    $alert['link'] = $order->link;
                    $alert['qty'] = $order->quantity;
                    $alert['prc'] = getOption('currency_symbol') . ($order->price);
                    $alert['bal'] = $user->funds;
                    $alert['mess'] = "Your Order has been Received <br>
                            Order ID: $order->id <br>
                            Package: " . $order->package->name . "<br>
                            Price: $order->price <br>
                            Balance: $user->funds";
                    $ticket = \App\Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
                    \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketSubmitted($ticket));

                    \Illuminate\Support\Facades\Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
                    return redirect('/support');
                } elseif (count($result) < 3) {
                    $html = "You have only " . count($result) . " License Code";
                    // try{
                    // 	    Mail::send(array(), array(), function ($message) use ($html) {
                    //           $message->to('hameedaslam.95@gmail.com')
                    //             ->subject('License Code')
                    //             ->from(env('MAIL_FROM_NAME'))
                    //             ->setBody($html, 'text/html');
                    //         });
                    // } catch(\Exception $e){}
                }
                $value = 1;
                $c = 0;
                $license_codes = explode(",", $package->license_codes);
                $codes = \App\Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $free_codes = array_diff($license_codes, $codes);
                $free_codes = count($free_codes);
                $free1 = 1;
                $free_code = $free_codes - $free1;

                while ($value) {
                    if (isset($result[$c])) {
                        \App\Licensecode::create(['code' => $result[$c], 'package_id' => $package->name, 'available' => $free_code, 'purchase_by' => \Illuminate\Support\Facades\Auth::user()->email, 'created_at' => date('Y-m-d H:s:i'), 'updated_at' => date('Y-m-d H:s:i')]);
                        $code = $result[$c];
                        $value = 0;
                    } else {
                        $c++;
                    }
                }
            }
            $orderc = 1;
            $order = \App\Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => $code, 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
            $text = 'Order Placed by user on Website' . "\n";
            $text .= 'Order ID: ' . $order->id . "\n";
            $text .= 'Quantity: ' . $order->quantity . "\n";
            fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        }

        $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
        $authpoint = ($total_price) * $grouppercent;
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $total_price;
        $user->points = $user->points + $authpoint;
        $user->save();
        if (($dripfeed == 1) || ($autolike == 1)) {
        } else if (!is_null($package->preferred_api_id)) {
            event(new \App\Events\OrderPlaced($order));
        }

        \Illuminate\Support\Facades\Session::flash('alert', __('messages.order_placed'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('/order/topservices');
    }

    public function showMassOrderForm()
    {
        $packages = \App\Package::where('status', 'ACTIVE')->orderBy('service_id')->get();
        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('orders.mass-order', compact('packages', 'userPackagePrices'));
    }

    public function storeMassOrder(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ['content' => 'required']);
        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $rows = explode(PHP_EOL, $request->input('content'));

        if (!empty($rows)) {
            $orders = [];
            $sumPrice = 0;

            foreach ($rows as $row) {
                $order = explode('|', $row);

                if (count($order) === 3) {
                    $package_id = $order[0];
                    $quantity = $order[1];
                    $link = $order[2];
                    $package = \App\Package::find($package_id);

                    if (!is_null($package)) {
                        if (($package->minimum_quantity <= $quantity) && ($quantity <= $package->maximum_quantity)) {
                            $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
                            $price = (float) $package_price * $quantity;
                            $price = number_formats($price, 2, '.', '');

                            if (0 < $price) {
                                $sumPrice += $price;
                                $orders[] = ['price' => $price, 'quantity' => $quantity, 'package_id' => $package->id, 'api_id' => $package->preferred_api_id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'link' => $link, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()];
                            }
                        }
                    }
                }
            }

            if (!empty($orders)) {
                if (\Illuminate\Support\Facades\Auth::user()->funds < $sumPrice) {
                    \Illuminate\Support\Facades\Session::flash('alert', __('messages.not_enough_funds'));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back()->withInput();
                }

                \App\Order::insert($orders);
                $text = 'Mass Orders Placed by user on Website' . "\n";
                fundChange($text, $sumPrice * -1, 'ORDER', \Illuminate\Support\Facades\Auth::user()->id, 0);
                $group = Auth::user()->group;
                $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
                $authpoint = ($sumPrice) * $grouppercent;
                $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
                $user->funds = $user->funds - $sumPrice;
                $user->points = $user->points + $authpoint;
                $user->save();
                \Illuminate\Support\Facades\Session::flash('alert', __('messages.order_placed'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
                return redirect('/order/mass-order');
            }
        }

        \Illuminate\Support\Facades\Session::flash('alert', __('messages.something_went_wrong'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
        return redirect()->back()->withInput();
    }

    public function APIStoreOrder(\Illuminate\Http\Request $request)
    {
        $response = ['errors' => ''];
        $validator = \Validator::make($request->all(), ['package_id' => 'required|numeric', 'quantity' => 'required|numeric', 'link' => 'required']);

        if ($validator->fails()) {
            \Log::error("status:check1");

            $response['errors'] = $validator->errors()->all();
            return response()->json($response);
        }

        $package = \App\Package::findOrfail($request->input('package_id'));
        $quantity = $request->input('quantity');
        $mytime = Carbon\Carbon::now();
        $package->mydate = $mytime;
        $package->save();
        if ($quantity < $package->minimum_quantity) {
            $response['errors'] = ['Please specify at least minimum quantity.'];
            return response()->json($response);
        }

        if ($package->maximum_quantity < $quantity) {
            $response['errors'] = ['Please specify less than or equal to maximum quantity'];
            return response()->json($response);
        }

        if ($package->custom_comments) {
            $commnets = $request->input('comments');

            if ($commnets != '') {
                $commnets_arr = preg_split('/\\n/', $commnets);
                $total_comments = count($commnets_arr);

                if ($quantity < $total_comments) {
                    $response['errors'] = ['You have added more comments than required quantity'];
                    return response()->json($response);
                }

                if ($total_comments < $quantity) {
                    $response['errors'] = ['You have added less comments than required quantity'];
                    return response()->json($response);
                }
            }
        }

        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        $price = (float) $package_price * $quantity;
        $price = number_formats($price, 2, '.', '');

        if (\Illuminate\Support\Facades\Auth::user()->funds < $price) {
            $response['errors'] = ['You do not have enough funds to Place order.'];
            return response()->json($response);
        }

        $custom_comments = '';

        if ($package->custom_comments) {
            $custom_comments = preg_replace('/' . "\r\n" . '|' . "\r" . '|' . "\n" . '/', PHP_EOL, $request->input('custom_data'));
        }

        $order = \App\Order::create(['price' => $price, 'quantity' => $quantity, 'package_id' => $package->id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'api_id' => $package->preferred_api_id, 'link' => $request->input('link'), 'source' => 'API', 'custom_comments' => $custom_comments]);
        unset($response['errors']);
        $response['order'] = $order->id;
        $text = 'Order Placed through old API' . "\n";
        fundChange($text, $order->price * -1, 'ORDER', $order->user_id, $order->id);
        $group = Auth::user()->group;
        $grouppercent = \App\Group::where('id', $group->id)->value('point_percent');
        $authpoint = ($total_price) * $price;
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $price;
        $user->points = $user->points + $authpoint;
        $user->save();

        if (!is_null($package->preferred_api_id)) {
            event(new \App\Events\OrderPlaced($order));
        }

        return response()->json($response);
    }

    public function APIGetOrderStatus(\Illuminate\Http\Request $request)
    {
        $response = ['errors' => ''];
        $order = \App\Order::where(['id' => $request->input('order'), 'user_id' => \Illuminate\Support\Facades\Auth::user()->id])->first();

        if (is_null($order)) {
            $response['errors'] = ['Order Not found'];
            return response()->json($response);
        } else {
            unset($response['errors']);
            $response["charge"] = $order->price;
            $response["status"] = $order->status;
            $response["start_count"] = $order->start_counter;
            $response["remains"] = $order->remains;
            $response["currency"] = getOption('currency_code');
        }

        return response()->json($response);
    }


    public function getPackages($service_id)
    {
        $group = Auth::user()->group;
        $package_ids = explode(",", $group->package_ids);
        $packages = \App\Package::where(['service_id' => $service_id, 'status' => 'ACTIVE'])->whereIn('id', $package_ids)->orderBy('position')->get();
        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('orders.partial-packages', compact('packages', 'userPackagePrices', 'group', 'package_ids'));
    }
    public function getfPackages($service_id)
    {
        $resultids = array();
        $group = Auth::user()->group;
        $al_ids = \App\Package::where('service_id', $service_id)->pluck('id');
        $package_ids = explode(",", Auth::user()->favorite_pkgs);
        for ($i = 0; $i < count($al_ids); $i++) {
            if (in_array($al_ids[$i], $package_ids)) {
                array_push($resultids, $al_ids[$i]);
            }
        }
        $packages = \App\Package::where(['service_id' => $service_id, 'status' => 'ACTIVE'])->whereIn('id', $resultids)->orderBy('position')->get();
        $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('orders.partial-packages', compact('packages', 'userPackagePrices', 'group', 'package_ids'));
    }
    public function topservices(\Illuminate\Http\Request $request)
    {
        mpc_m_c($request->server('SERVER_NAME'));
        $usergrp = \App\Group::findOrFail(\Illuminate\Support\Facades\Auth::user()->group_id);
        $services = \App\Service::where(['status' => 'ACTIVE'])->where('top', 1)->orderBy('position', 'asc')->get();
        $packages = \App\Package::where(['status' => 'ACTIVE'])->where('top', 1)->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = \App\Order::max('id');
            $spent = \App\Order::where(['user_id' => \Illuminate\Support\Facades\Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.topservices', compact('packages', 'services', 'ordercnt', 'spent'));
    }
}
