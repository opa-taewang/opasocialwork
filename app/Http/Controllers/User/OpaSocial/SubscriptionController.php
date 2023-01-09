<?php

namespace App\Http\Controllers\User\OpaSocial;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware("App\\Http\\Middleware\\VerifyModuleSubscriptionEnabled");
    }

    public function index()
    {
        return view("subscriptions.index");
    }

    public function indexData()
    {
        $subscriptions = \App\Subscription::with("package.service")->where(array("subscriptions.user_id" => \Illuminate\Support\Facades\Auth::user()->id));
        return datatables()->of($subscriptions)->editColumn("link", function ($subscription) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $subscription->link . "\" target=\"_blank\">" . str_limit($subscription->link, 50) . "</a>";
        })->editColumn("price", function ($subscription) {
            return getOption("currency_symbol") . number_formats($subscription->price, 2, getOption("currency_separator"), "");
        })->editColumn("posts", function ($subscription) {
            $orders = \App\Order::where(array("subscription_id" => $subscription->id))->count();
            return $orders . "/" . $subscription->posts;
        })->editColumn("status", function ($subscription) {
            return "<span class='status-" . strtolower($subscription->status) . "'>" . $subscription->status . "</span>";
        })->editColumn("created_at", function ($order) {
            return "<span class='no-word-break'>" . $order->created_at . "</span>";
        })->addColumn("action", "subscriptions.index-buttons")->rawColumns(array("link", "action", "status", "created_at"))->toJson();
    }

    public function create(\Illuminate\Http\Request $request)
    {
        mpc_m_c($request->server("SERVER_NAME"));
        $services = \App\Service::where(array("status" => "ACTIVE", "is_subscription_allowed" => 1))->get();
        return view("subscriptions.new", compact("services"));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("package_id" => "required", "quantity" => "required|numeric", "link" => "required", "posts" => "required|numeric"));
        $package = \App\Package::findOrfail($request->input("package_id"));
        $quantity = $request->input("quantity");
        if ($quantity < $package->minimum_quantity) {
            return redirect()->back()->withInput()->withErrors(array("quantity" => __("messages.minimum_quantity")));
        }
        if ($package->maximum_quantity < $quantity) {
            return redirect()->back()->withInput()->withErrors(array("quantity" => __("messages.maximum_quantity")));
        }
        $userPackagePrices = \App\UserPackagePrice::where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id))->pluck("price_per_item", "package_id")->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        $posts = $request->input("posts");
        $price = (float) ($package_price * $quantity * $posts);
        $price = number_formats($price, 2, ".", "");
        if (\Illuminate\Support\Facades\Auth::user()->funds < $price) {
            \Session::flash("alert", __("messages.not_enough_funds"));
            \Session::flash("alertClass", "danger no-auto-close");
            return redirect()->back();
        }
        \App\Subscription::create(array("quantity" => $quantity, "user_id" => \Illuminate\Support\Facades\Auth::user()->id, "package_id" => $package->id, "posts" => $posts, "link" => $request->input("link"), "price" => $price));
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $price;
        $user->save();
        \Session::flash("alert", __("messages.order_placed"));
        \Session::flash("alertClass", "success");
        return redirect("/subscription/new");
    }

    public function show($id)
    {
        $subscription = \App\Subscription::findOrFail($id);
        $orders = \App\Order::where(array("subscription_id" => $id))->get();
        return view("subscriptions.orders.index", compact("subscription", "orders"));
    }
}
