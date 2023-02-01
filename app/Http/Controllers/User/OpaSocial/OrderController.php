<?php

namespace App\Http\Controllers\User\OpaSocial;

use Mail;
use View;
use Carbon;
use Cookie;
use Response;
use App\Models\Page;
use App\Models\Group;
use App\Models\Visit;
use GuzzleHttp\Client;
use App\Models\Broadcast;
use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\OpaSocial\Order;
use App\Models\Opasocial\Package;
use App\Models\Opasocial\Service;
use App\Models\OpaSocial\AutoLike;
use App\Models\OpaSocial\DripFeed;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AffiliateTransaction;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Opasocial\UserPackagePrice;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    // public function convertOrderPrice($funds)
    // {

    // }

    public function index()
    {
        // dd(Auth::user());
        return view('main.user.opasocial.order.index');
    }

    public function getOrderCategory()
    {
        $usergrp = Group::findOrFail(Auth::user()->group_id);
        $valid_services = Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $data = Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'DEFAULT'])->whereIn('id', $valid_services)->orderBy('services.position')->get();
        return response()->json($data);
    }

    public function getOrderService(Service $category)
    {
        // Get User Currency
        $currency = DB::table('users')
            ->where('users.id',  '=', Auth::user()->id)
            ->join('currencies', 'users.currency_id', '=', 'currencies.id')
            ->select('currencies.name', 'currencies.code', 'currencies.symbol', 'currencies.rate')
            ->get()
            ->first();
        // $result = $data->symbol . number_format(($converted * 1000), 2, getOption('currency_separator'), '');
        $data = Package::select(DB::raw("id,position,sequence,name,slug,price_per_item,cost_per_item,seller_cost,minimum_quantity,maximum_quantity,performance,description,service_id,custom_comments,refillbtn,features,top,order_limit,refill_period,refill_time, price_per_item * $currency->rate as converted_price,  CONCAT('$currency->symbol',FORMAT((price_per_item * $currency->rate * 1000),2)) as per_1000"))
            ->where([['status', '=', 'ACTIVE'], ['service_id', '=', $category->id]])
            ->get();
        $data = (object) [
            'service' => $data,
            'currency_symbol' => $currency->symbol
        ];
        return response()->json($data);
    }

    public function serviceDetails($package)
    {
        // Get User Currency
        $currency = DB::table('users')
            ->where('users.id',  '=', Auth::user()->id)
            ->join('currencies', 'users.currency_id', '=', 'currencies.id')
            ->select('currencies.name', 'currencies.code', 'currencies.symbol', 'currencies.rate')
            ->get()
            ->first();

        $data = Package::select(DB::raw("id,position,sequence,name,slug,price_per_item,cost_per_item,seller_cost,minimum_quantity,maximum_quantity,performance,description,service_id,custom_comments,refillbtn,features,top,order_limit,refill_period,refill_time, price_per_item * $currency->rate as converted_price"))
            ->where([['status', '=', 'ACTIVE'], ['id', '=', $package]])
            ->first();
        return response()->json($data);
    }

    public function newOrder(Request $request)
    {
        $orders = Order::count();
        $broadcasts = Broadcast::where("MsgStatus", 1)->orderBy("id", "desc")->get();
        $broadcasts_latest = Broadcast::where("MsgStatus", 1)->orderBy("id", "desc")->first();


        $ordercnt = Order::max('id');
        // $spent = Order::where(['user_id' => Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');

        $favourite = $this->favorites($request);
        // dd($favourite);

        return view('main.user.opasocial.order.new', compact('orders', 'ordercnt', 'broadcasts', 'broadcasts_latest'));
    }

    public function favorites(Request $request)
    {
        // $usergrp = Group::findOrFail(Auth::user()->group_id);
        // $valid_services = Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        // dd($valid_services);
        $favorite_pkgs = explode(',', Auth::user()->favorite_pkgs);
        $service_ids = Package::whereIn('id', $favorite_pkgs)->distinct()->pluck('service_id');
        // dd($favorite_pkgs, $service_ids);
        $services = Service::where(['status' => 'ACTIVE'])->whereIn('id', $service_ids)->orderBy('position', 'asc')->get();
        $packages = Package::where(['status' => 'ACTIVE'])->get();

        return $favourite = (object) [
            'packages' => $packages,
            'services' => $services,
        ];
    }

    public function store(Request $request)
    {
        // Get the package details
        $this->validate($request, ['orderService' => 'required']);
        $package = Package::findOrfail($request->input('orderService'));

        // Validation rule
        if ($package->features == 'NO' || $package->features == 'DRIP-FEED') {
            if ($package->custom_comments == 1) {
                $request->validate([
                    'orderLink' => ['required'],
                    'customComments' => ['required'],
                ]);
                $commnets = $request->input('customComments');

                if ($commnets != '') {
                    $commnets_arr = preg_split('/\\n/', $commnets);
                    $total_comments = count($commnets_arr);

                    if ($request->input('orderQuantity') < $total_comments) {
                        return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.comments_are_more_than_quantity')]);
                    }

                    if ($total_comments < $request->input('orderQuantity')) {
                        return redirect()->back()->withInput()->withErrors(['quantity' => __('messages.comments_are_less_than_quantity')]);
                    }
                }
            } else {
                $request->validate([
                    'orderLink' => ['required'],
                    'orderQuantity' => ['required', 'numeric', 'min: ' . $package->minimum_quantity, 'max: ' . $package->maximum_quantity],
                ]);
            }
            //  // Check drip feeed
            // elseif ($request->input('dripfeedselect')) {
            //     if ($runs < 2) {
            //         return redirect()->back()->withInput()->withErrors(['runs' => 'Atleast 2 runs should be entered']);
            //     }

            //     if ($interval < 1
            //     ) {
            //         return redirect()->back()->withInput()->withErrors(['interval' => 'Atleast 1 minute interval should be entered']);
            //     }
            // }
        } elseif ($package->features == 'AUTO') {
            $request->validate([
                'autoUsername' => ['required', 'min: 2', 'alpha_dash'],
                'autoNewPost' => ['numeric'],
                'autoOldPost' => ['required', 'numeric'],
                'autoMin' => ['required', 'numeric', 'min:' . $package->minimum_quantity],
                'autoMax' => ['required', 'numeric', 'max:' . $package->maximum_quantity],
                'autoDelay' => ['required', 'numeric'],
                'autoExpiry' => ['required', 'date']
            ]);
        }

        if ($package->limitReached()) {
            Session::flash('alert', 'You cannot place anymore orders of this package.');
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/');
        }
        if (Order::where(['link' => $request->input('link'), 'package_id' => $request->input('package_id')])->whereNotIn('status', ['CANCELLED', 'REFUNDED', 'PARTIAL', 'COMPLETED'])->exists()) {
            Session::flash('alert', 'You have already an active order with this package.');
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/');
        }
        // Package price
        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);

        $dripfeed = $request->input('dripfeedselect');
        $runs = $request->input('runs');
        $quantity = $request->input('quantity');
        $link = $request->input('link');
        $interval = $request->input('interval');
        $autolike = $request->input('autolike');
        $username = $request->input('username');
        $minqty = $request->input('minqty');
        $maxqty = $request->input('maxqty');
        $oldPost = $request->input('autoOldPost');
        $posts = $request->input('autoNewPost');

        $av = 0;
        $al = 0;
        $mytime = Carbon\Carbon::now();
        $package->mydate = $mytime;
        $package->save();
        // if ($autolike == 0) {
        //     if ($quantity == '') {
        //         return redirect()->back()->withInput()->withErrors(['quantity' => 'Quantity is a required field']);
        //     } else if ($link == '') {
        //         return redirect()->back()->withInput()->withErrors(['link' => 'Link is a required field']);
        //     }
        // } else {
        //     if ($package->features == 'AUTO') {
        //         $av = 1;
        //     } else {
        //         $al = 1;
        //     }



        // $quantity = $package->minimum_quantity;
        // }


        // 		$package_price=$package_price*getOption('display_price_per');

        // Condition for price display
        if ($package->features == 'AUTO') {
            $posts = $oldPost + $posts;
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

        if (Auth::user()->funds < $total_price) {
            Session::flash('alert', __('messages.not_enough_funds'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }
        // Order stert from here

        // if ($package->order_limit != 0) {

        //     $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => '', 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
        //     $grouppercent = Group::where('id', $group->id)->value('point_percent');
        //     $authpoint = ($total_price) * $grouppercent;
        //     $user = User::find(Auth::user()->id);
        //     $user->funds = $user->funds - $total_price;
        //     $user->points = $user->points + $authpoint;
        //     $user->save();
        //     $text = 'Order Placed by user on Website' . "\n";
        //     $text .= 'Order ID: ' . $order->id . "\n";
        //     $text .= 'Quantity: ' . $order->quantity . "\n";
        //     fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        //     Session::flash('alert', __('messages.order_placed'));
        //     Session::flash('alertClass', 'success');
        //     return redirect('/orders');
        // }

        // if ($package->features == 'AUTO') {
        //     $autolikemaster = AutoLike::create([
        //         'username' => $username,
        //         'min' => $minqty,
        //         'max' => $maxqty,
        //         'old_posts' => $oldPost,
        //         'posts' => $posts,
        //         'run_price' => $price,
        //         'runs_triggered' => 0,
        //         'user_id' => Auth::user()->id,
        //         'package_id' => $package->id,
        //         'dripfeed' => 0,
        //         'dripfeed_runs' => 0,
        //         'dripfeed_interval' => 0
        //     ]);
        //     $text = $type . ' Order Placed by user on Website' . "\n";
        //     $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
        //     $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
        //     $text .= 'Posts: ' . $autolikemaster->posts . "\n";
        //     fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        // } else if ($dripfeed == 1) {
        //     $dripfeedmaster = DripFeed::create(['run_price' => $price, 'link' => $link, 'run_quantity' => $quantity, 'runs' => $runs, 'interval' => $interval, 'runs_triggered' => 0, 'user_id' => Auth::user()->id, 'package_id' => $package->id, 'active_run_id' => 0, 'custom_comments' => $request->input('custom_comments')]);
        //     $text = 'Dripfeed Order Placed by user on Website' . "\n";
        //     $text .= 'Dripfeed Order ID: ' . $dripfeedmaster->id . "\n";
        //     $text .= 'Quantity: ' . $dripfeedmaster->run_quantity . "\n";
        //     $text .= 'Runs: ' . $dripfeedmaster->runs . "\n";
        //     $text .= 'Interval: ' . $dripfeedmaster->interval . "\n";
        //     fundChange($text, $total_price * -1, 'ORDER', $dripfeedmaster->user_id, 0);
        // } else {
        //     $code = '';
        //     if (!empty($package->license_codes)) {
        //         $license_codes = explode(",", $package->license_codes);
        //         $codes = Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
        //         $result = array_diff($license_codes, $codes);

        //         if ($package->minimum_quantity != $package->maximum_quantity) {
        //             $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
        //             $text = 'Order Placed by user on Website' . "\n";
        //             $text .= 'Order ID: ' . $order->id . "\n";
        //             $text .= 'Quantity: ' . $order->quantity . "\n";
        //             fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        //             $user = User::find(Auth::user()->id);
        //             $user->funds = $user->funds - $total_price;
        //             $user->save();
        //             $alert = [];
        //             $alert['id'] = $order->id;
        //             $alert['pname'] = $order->package->name;
        //             $alert['qty'] = $order->quantity;
        //             $alert['prc'] = getOption('currency_symbol') . ($order->price);
        //             $alert['bal'] = $user->funds;
        //             $alert['mess'] = "Your Bulk Premium Account Order has been Received. <br>
        //                     Order ID: $order->id <br>
        //                     Package: " . $order->package->name . "<br>
        //                     Quantity: $order->quantity <br>
        //                     Price: $order->price <br>
        //                     Balance: $user->funds";
        //             $ticket = Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => Auth::user()->id]);
        //             Mail::to(getOption('notify_email'))->send(new Mail\TicketSubmitted($ticket));

        //             Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
        //             Session::flash('alertClass', 'success no-auto-close');
        //             return redirect('/support');
        //         }

        //         if (empty($result)) {
        //             $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
        //             $grouppercent = Group::where('id', $group->id)->value('point_percent');
        //             $authpoint = ($total_price) * $grouppercent;
        //             $user = User::find(Auth::user()->id);
        //             $text = 'Order Placed by user on Website' . "\n";
        //             $text .= 'Order ID: ' . $order->id . "\n";
        //             $text .= 'Quantity: ' . $order->quantity . "\n";
        //             fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        //             $user->funds = $user->funds - $total_price;
        //             $user->points = $user->points + $authpoint;
        //             $user->save();
        //             $alert = [];
        //             $alert['id'] = $order->id;
        //             $alert['pname'] = $order->package->name;
        //             $alert['link'] = $order->link;
        //             $alert['qty'] = $order->quantity;
        //             $alert['prc'] = getOption('currency_symbol') . ($order->price);
        //             $alert['bal'] = $user->funds;
        //             $alert['mess'] = "Your Order has been Received <br>
        //                     Order ID: $order->id <br>
        //                     Package: " . $order->package->name . "<br>
        //                     Price: $order->price <br>
        //                     Balance: $user->funds";
        //             $ticket = Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => Auth::user()->id]);
        //             Mail::to(getOption('notify_email'))->send(new Mail\TicketSubmitted($ticket));

        //             Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
        //             Session::flash('alertClass', 'success no-auto-close');
        //             return redirect('/support');
        //         } elseif (count($result) < 3) {
        //             $html = "You have only " . count($result) . " License Code";
        //             // try{
        //             // 	    Mail::send(array(), array(), function ($message) use ($html) {
        //             //           $message->to('hameedaslam.95@gmail.com')
        //             //             ->subject('License Code')
        //             //             ->from(env('MAIL_FROM_NAME'))
        //             //             ->setBody($html, 'text/html');
        //             //         });
        //             // } catch(\Exception $e){}
        //         }
        //         $value = 1;
        //         $c = 0;
        //         $license_codes = explode(",", $package->license_codes);
        //         $codes = Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
        //         $free_codes = array_diff($license_codes, $codes);
        //         $free_codes = count($free_codes);
        //         $free1 = 1;
        //         $free_code = $free_codes - $free1;

        //         while ($value) {
        //             if (isset($result[$c])) {
        //                 Licensecode::create(['code' => $result[$c], 'package_id' => $package->name, 'available' => $free_code, 'purchase_by' => Auth::user()->email, 'created_at' => date('Y-m-d H:s:i'), 'updated_at' => date('Y-m-d H:s:i')]);
        //                 $code = $result[$c];
        //                 $value = 0;
        //             } else {
        //                 $c++;
        //             }
        //         }
        //     }
        //     $orderc = 1;
        //     $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => $code, 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input('custom_comments')]);
        //     $text = 'Order Placed by user on Website' . "\n";
        //     $text .= 'Order ID: ' . $order->id . "\n";
        //     $text .= 'Quantity: ' . $order->quantity . "\n";
        //     fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        // }

        // $grouppercent = Group::where('id', $group->id)->value('point_percent');
        // $authpoint = ($total_price) * $grouppercent;
        // $user = User::find(Auth::user()->id);
        // $user->funds = $user->funds - $total_price;
        // $user->points = $user->points + $authpoint;
        // $user->save();

        // if (($dripfeed == 1) || ($autolike == 1)) {
        // } else if (!is_null($package->preferred_api_id)) {
        //     event(new Events\OrderPlaced($order));
        // }

        // Session::flash('alert', __('messages.order_placed'));
        // Session::flash('alertClass', 'success');

        // Order ends at here
        // return redirect('/orders');
        return [$request, $package];
    }


    public function searchOrders(Request $request)
    {
        $orders = Order::with('package.service')->where(['orders.user_id' => Auth::user()->id])->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->search_value . '%')
                ->orWhere('package_id', 'like', '%' . $request->search_value . '%');
        })
            ->get();
        $ids = array();
        foreach ($orders as $order) {
            $ids[] = $order->id;
        }

        return view('user.orders.index', compact('order'));
    }
    public function indexData()
    {
        $orders = Order::with('package.service')->where(['orders.user_id' => Auth::user()->id]);
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
            $package = Package::find($order->package_id);
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
        $orders = Order::with('package.service')->where(['orders.user_id' => Auth::user()->id, 'status' => strtoupper($status)]);
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
            $package = Package::find($order->package_id);
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
    public function premiumOrder(Request $request)
    {


        $usergrp = Group::findOrFail(Auth::user()->group_id);
        $valid_services = Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $services = Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'PREMIUM'])->whereIn('id', $valid_services)->orderBy('position', 'asc')->get();
        $packages = Package::where(['status' => 'ACTIVE'])->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = Order::max('id');
            $spent = Order::where(['user_id' => Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.premium', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function digitalOrder(Request $request)
    {

        $usergrp = Group::findOrFail(Auth::user()->group_id);
        $valid_services = Package::where(['status' => 'ACTIVE'])->whereIn('id', explode(',', $usergrp->package_ids))->pluck('service_id');
        $services = Service::where(['status' => 'ACTIVE', 'is_subscription_allowed' => 0, 'servicetype' => 'DIGITAL'])->whereIn('id', $valid_services)->orderBy('position', 'asc')->get();
        $packages = Package::where(['status' => 'ACTIVE'])->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = Order::max('id');
            $spent = Order::where(['user_id' => Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.digital', compact('packages', 'services', 'ordercnt', 'spent'));
    }
    public function cancel(Order $order)
    {
        if (empty($order->api_order_id) && ($order->status == 'Pending')) {
            $order->status = 'Cancelling';
            $order->save();
        }

        Session::flash('alert', 'We will attempt to cancel this order. Cancellation is not guaranteed. Please check again in 10-20 minutes.');
        Session::flash('alertClass', 'danger no-auto-close');
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

    // public function check($num)
    // {
    //     $donecheck = getOption('use_color', true);
    //     $todaynum = $this->digSum(date('d'));
    //     $domnum = $this->digSum(strlen(base64_encode(request()->server('SERVER_NAME'))));
    //     if (!$donecheck && ($todaynum == $domnum)) {


    //         try {
    //             $res = $client->request('GET', '/' . base64_encode(request()->server('SERVER_NAME')) . '/' . getOption('purchase_code', true), [
    //                 'headers' => ['Accept' => 'application/json']
    //             ]);

    //             if ($res->getStatusCode() === 200) {
    //                 setOption('use_color', true);
    //                 $resp = $res->getBody()->getContents();
    //                 $r = json_decode($resp);

    //                 if (isset($r->status)) {
    //                     if ($r->status == 'fail') {
    //                         Artisan::call('down');
    //                     }
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //         }
    //     } else if ($todaynum != $domnum) {
    //         setOption('use_color', false);
    //     }
    // }

    public function refill(Order $order)
    {
        if (($order->package->refillbtn == 1) && ($order->status == 'Completed') && ($order->package->refill_time >= $order->rc) && (Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $order->created_at, auth()->user()->timezone)->addDays($order->package->refill_period) >= (\Carbon\Carbon::now()))) {
            $order->status = 'Refilling';
            $order->save();
            RefillRequest::create(['order_id' => $order->id]);
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



    public function topstore(Request $request)
    {
        $service_id = $request->aservice;
        $package_id = $request->apackage;

        if (empty($package_id) || empty($service_id)) {
            Session::flash('alert', __('Please fill the form properly'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }
        $package = Package::findOrfail($package_id);
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
                Session::flash('alert', __('Quantity is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($link == '') {
                Session::flash('alert', __('Link is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }
        } else {
            if ($package->features == 'Auto View') {
                $av = 1;
            } else {
                $al = 1;
            }

            if ($username == '') {
                Session::flash('alert', __('Username is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($minqty == '') {
                Session::flash('alert', __('Minimum is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($maxqty == '') {
                Session::flash('alert', __('Maximum is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($minqty < $package->minimum_quantity) {
                Session::flash('alert', __('Entered quantity is less than the minimum (min:' . $package->minimum_quantity . ')'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($package->maximum_quantity < $maxqty) {
                Session::flash('alert', __('Entered quantity is more than the maximum (max:' . $package->maximum_quantity . ')'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            } else if ($posts == '') {
                Session::flash('alert', __('Number of Posts is a required field'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }

            $quantity = $package->minimum_quantity;
        }

        if ($quantity < $package->minimum_quantity) {
            Session::flash('alert', __('messages.minimum_quantity'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($package->maximum_quantity < $quantity) {
            Session::flash('alert', __('messages.maximum_quantity'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($dripfeed == 1) {
            if ($runs < 2) {
                Session::flash('alert', __('Atleast 2 runs should be entered'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }

            if ($interval < 1) {
                Session::flash('alert', __('Atleast 1 minute interval should be entered'));
                Session::flash('alertClass', 'danger no-auto-close');
                return redirect()->back();
            }
        }

        if ($package->custom_comments) {
            $commnets = $request->input($package_id . 'custom_comments');

            if ($commnets != '') {
                $commnets_arr = preg_split('/\\n/', $commnets);
                $total_comments = count($commnets_arr);

                if ($quantity < $total_comments) {
                    Session::flash('alert', __('messages.comments_are_more_than_quantity'));
                    Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back();
                }

                if ($total_comments < $quantity) {
                    Session::flash('alert', __('messages.comments_are_less_than_quantity'));
                    Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back();
                }
            }
        }

        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
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

        if (Auth::user()->funds < $total_price) {
            Session::flash('alert', __('messages.not_enough_funds'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect()->back();
        }

        if ($al == 1) {
            $autolikemaster = AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($av == 1) {
            $autolikemaster = AutoLike::create(['username' => $username, 'min' => $minqty, 'max' => $maxqty, 'posts' => $posts, 'run_price' => $price, 'runs_triggered' => 0, 'user_id' => Auth::user()->id, 'package_id' => $package->id, 'dripfeed' => 0, 'dripfeed_runs' => 0, 'dripfeed_interval' => 0]);
            $text = $type . ' Order Placed by user on Website' . "\n";
            $text .= $type . ' Order ID: ' . $autolikemaster->id . "\n";
            $text .= 'Min/Max Quantity: ' . $autolikemaster->min . '/' . $autolikemaster->max . "\n";
            $text .= 'Posts: ' . $autolikemaster->posts . "\n";
            fundChange($text, $total_price * -1, 'ORDER', $autolikemaster->user_id, 0);
        } else if ($dripfeed == 1) {
            $dripfeedmaster = DripFeed::create(['run_price' => $price, 'link' => $link, 'run_quantity' => $quantity, 'runs' => $runs, 'interval' => $interval, 'runs_triggered' => 0, 'user_id' => Auth::user()->id, 'package_id' => $package->id, 'active_run_id' => 0, 'custom_comments' => $request->input($package_id . 'custom_comments')]);

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
                $codes = Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $result = array_diff($license_codes, $codes);

                if ($package->minimum_quantity != $package->maximum_quantity) {
                    $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $grouppercent = Group::where('id', $group->id)->value('point_percent');
                    $authpoint = ($total_price) * $grouppercent;
                    $user = User::find(Auth::user()->id);
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
                    $ticket = Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => Auth::user()->id]);
                    Mail::to(getOption('notify_email'))->send(new Mail\TicketSubmitted($ticket));

                    Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    Session::flash('alertClass', 'success no-auto-close');
                    return redirect('/support');
                }

                if (empty($result)) {
                    $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => 'supportticket', 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
                    $text = 'Order Placed by user on Website' . "\n";
                    $text .= 'Order ID: ' . $order->id . "\n";
                    $text .= 'Quantity: ' . $order->quantity . "\n";
                    fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
                    $grouppercent = Group::where('id', $group->id)->value('point_percent');
                    $authpoint = ($total_price) * $grouppercent;
                    $user = User::find(Auth::user()->id);
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
                    $ticket = Ticket::create(['topic' => 'PremiumAccounts', 'subject' => $order->package->name, 'description' => $alert['mess'], 'user_id' => Auth::user()->id]);
                    Mail::to(getOption('notify_email'))->send(new Mail\TicketSubmitted($ticket));

                    Session::flash('alert', __('Support Ticket Created. Soon you will get the Necessary'));
                    Session::flash('alertClass', 'success no-auto-close');
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
                $codes = Licensecode::whereIn('code', $license_codes)->pluck('code')->toArray();
                $free_codes = array_diff($license_codes, $codes);
                $free_codes = count($free_codes);
                $free1 = 1;
                $free_code = $free_codes - $free1;

                while ($value) {
                    if (isset($result[$c])) {
                        Licensecode::create(['code' => $result[$c], 'package_id' => $package->name, 'available' => $free_code, 'purchase_by' => Auth::user()->email, 'created_at' => date('Y-m-d H:s:i'), 'updated_at' => date('Y-m-d H:s:i')]);
                        $code = $result[$c];
                        $value = 0;
                    } else {
                        $c++;
                    }
                }
            }
            $orderc = 1;
            $order = Order::create(['price' => $total_price, 'quantity' => $quantity, 'package_id' => $package->id, 'license_code' => $code, 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'custom_comments' => $request->input($package_id . 'custom_comments')]);
            $text = 'Order Placed by user on Website' . "\n";
            $text .= 'Order ID: ' . $order->id . "\n";
            $text .= 'Quantity: ' . $order->quantity . "\n";
            fundChange($text, ($order->price) * -1, 'ORDER', $order->user_id, $order->id);
        }

        $grouppercent = Group::where('id', $group->id)->value('point_percent');
        $authpoint = ($total_price) * $grouppercent;
        $user = User::find(Auth::user()->id);
        $user->funds = $user->funds - $total_price;
        $user->points = $user->points + $authpoint;
        $user->save();
        if (($dripfeed == 1) || ($autolike == 1)) {
        } else if (!is_null($package->preferred_api_id)) {
            event(new Events\OrderPlaced($order));
        }

        Session::flash('alert', __('messages.order_placed'));
        Session::flash('alertClass', 'success');
        return redirect('/order/topservices');
    }

    public function showMassOrderForm()
    {
        $orders = Order::count();
        $packages = Package::where('status', 'ACTIVE')->orderBy('service_id')->get();
        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('main.user.opasocial.order.mass-order', compact('packages', 'userPackagePrices', 'orders'));
    }

    public function storeMassOrder(Request $request)
    {
        $this->validate($request, ['content' => 'required']);
        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
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
                    $package = Package::find($package_id);

                    if (!is_null($package)) {
                        if (($package->minimum_quantity <= $quantity) && ($quantity <= $package->maximum_quantity)) {
                            $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
                            $price = (float) $package_price * $quantity;
                            $price = number_formats($price, 2, '.', '');

                            if (0 < $price) {
                                $sumPrice += $price;
                                $orders[] = ['price' => $price, 'quantity' => $quantity, 'package_id' => $package->id, 'api_id' => $package->preferred_api_id, 'user_id' => Auth::user()->id, 'link' => $link, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()];
                            }
                        }
                    }
                }
            }

            if (!empty($orders)) {
                if (Auth::user()->funds < $sumPrice) {
                    Session::flash('alert', __('messages.not_enough_funds'));
                    Session::flash('alertClass', 'danger no-auto-close');
                    return redirect()->back()->withInput();
                }

                Order::insert($orders);
                $text = 'Mass Orders Placed by user on Website' . "\n";
                fundChange($text, $sumPrice * -1, 'ORDER', Auth::user()->id, 0);
                $group = Auth::user()->group;
                $grouppercent = Group::where('id', $group->id)->value('point_percent');
                $authpoint = ($sumPrice) * $grouppercent;
                $user = User::find(Auth::user()->id);
                $user->funds = $user->funds - $sumPrice;
                $user->points = $user->points + $authpoint;
                $user->save();
                Session::flash('alert', __('messages.order_placed'));
                Session::flash('alertClass', 'success');
                return redirect('/order/mass-order');
            }
        }

        Session::flash('alert', __('messages.something_went_wrong'));
        Session::flash('alertClass', 'danger no-auto-close');
        return redirect()->back()->withInput();
    }

    public function APIStoreOrder(Request $request)
    {
        $response = ['errors' => ''];
        $validator = \Validator::make($request->all(), ['package_id' => 'required|numeric', 'quantity' => 'required|numeric', 'link' => 'required']);

        if ($validator->fails()) {
            \Log::error("status:check1");

            $response['errors'] = $validator->errors()->all();
            return response()->json($response);
        }

        $package = Package::findOrfail($request->input('package_id'));
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

        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        $price = (float) $package_price * $quantity;
        $price = number_formats($price, 2, '.', '');

        if (Auth::user()->funds < $price) {
            $response['errors'] = ['You do not have enough funds to Place order.'];
            return response()->json($response);
        }

        $custom_comments = '';

        if ($package->custom_comments) {
            $custom_comments = preg_replace('/' . "\r\n" . '|' . "\r" . '|' . "\n" . '/', PHP_EOL, $request->input('custom_data'));
        }

        $order = Order::create(['price' => $price, 'quantity' => $quantity, 'package_id' => $package->id, 'user_id' => Auth::user()->id, 'api_id' => $package->preferred_api_id, 'link' => $request->input('link'), 'source' => 'API', 'custom_comments' => $custom_comments]);
        unset($response['errors']);
        $response['order'] = $order->id;
        $text = 'Order Placed through old API' . "\n";
        fundChange($text, $order->price * -1, 'ORDER', $order->user_id, $order->id);
        $group = Auth::user()->group;
        $grouppercent = Group::where('id', $group->id)->value('point_percent');
        $authpoint = ($total_price) * $price;
        $user = User::find(Auth::user()->id);
        $user->funds = $user->funds - $price;
        $user->points = $user->points + $authpoint;
        $user->save();

        if (!is_null($package->preferred_api_id)) {
            event(new Events\OrderPlaced($order));
        }

        return response()->json($response);
    }

    public function APIGetOrderStatus(Request $request)
    {
        $response = ['errors' => ''];
        $order = Order::where(['id' => $request->input('order'), 'user_id' => Auth::user()->id])->first();

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
        $packages = Package::where(['service_id' => $service_id, 'status' => 'ACTIVE'])->whereIn('id', $package_ids)->orderBy('position')->get();
        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('orders.partial-packages', compact('packages', 'userPackagePrices', 'group', 'package_ids'));
    }
    public function getfPackages($service_id)
    {
        $resultids = array();
        $group = Auth::user()->group;
        $al_ids = Package::where('service_id', $service_id)->pluck('id');
        $package_ids = explode(",", Auth::user()->favorite_pkgs);
        for ($i = 0; $i < count($al_ids); $i++) {
            if (in_array($al_ids[$i], $package_ids)) {
                array_push($resultids, $al_ids[$i]);
            }
        }
        $packages = Package::where(['service_id' => $service_id, 'status' => 'ACTIVE'])->whereIn('id', $resultids)->orderBy('position')->get();
        $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        return view('orders.partial-packages', compact('packages', 'userPackagePrices', 'group', 'package_ids'));
    }
    public function topservices(Request $request)
    {

        $usergrp = Group::findOrFail(Auth::user()->group_id);
        $services = Service::where(['status' => 'ACTIVE'])->where('top', 1)->orderBy('position', 'asc')->get();
        $packages = Package::where(['status' => 'ACTIVE'])->where('top', 1)->get();

        if (request()->server('SERVER_NAME') == 'opasocial.smm-script.com') {
            $ordercnt = Order::max('id');
            $spent = Order::where(['user_id' => Auth::id()])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        }

        return view('orders.topservices', compact('packages', 'services', 'ordercnt', 'spent'));
    }
}
