<?php


namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;
class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware("App\\Http\\Middleware\\VerifyModuleSubscriptionEnabled");
    }
    public function index()
    {
        return view("admin.subscriptions.index");
    }
    public function indexData()
    {
        $subscriptions = \App\Subscription::with("package.service");
        return datatables()->of($subscriptions)->editColumn("link", function ($subscription) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $subscription->link . "\" target=\"_blank\">" . str_limit($subscription->link, 30) . "</a>";
        })->editColumn("price", function ($subscription) {
            return getOption("currency_symbol") . number_formats($subscription->price, 2, getOption("currency_separator"), "");
        })->editColumn("posts", function ($subscription) {
            $orders = \App\Order::where(["subscription_id" => $subscription->id])->count();
            return $orders . "/" . $subscription->posts;
        })->editColumn("status", function ($subscription) {
            return "<span class='status-" . strtolower($subscription->status) . "'>" . $subscription->status . "</span>";
        })->editColumn("created_at", function ($order) {
            return "<span class='no-word-break'>" . $order->created_at . "</span>";
        })->addColumn("action", "admin.subscriptions.index-buttons")->rawColumns(["link", "action", "status", "created_at"])->toJson();
    }
    public function indexFilter($status)
    {
        return view("admin.subscriptions.index", compact("status"));
    }
    public function indexFilterData($status)
    {
        $subscriptions = \App\Subscription::with("package.service")->where(["status" => strtoupper($status)]);
        return datatables()->of($subscriptions)->editColumn("link", function ($subscription) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $subscription->link . "\" target=\"_blank\">" . str_limit($subscription->link, 30) . "</a>";
        })->editColumn("price", function ($subscription) {
            return getOption("currency_symbol") . number_formats($subscription->price, 2, getOption("currency_separator"), "");
        })->editColumn("posts", function ($subscription) {
            $orders = \App\Order::where(["subscription_id" => $subscription->id])->count();
            return $orders . "/" . $subscription->posts;
        })->editColumn("status", function ($subscription) {
            return "<span class='status-" . strtolower($subscription->status) . "'>" . $subscription->status . "</span>";
        })->editColumn("created_at", function ($order) {
            return "<span class='no-word-break'>" . $order->created_at . "</span>";
        })->addColumn("action", "admin.subscriptions.index-buttons")->rawColumns(["link", "action", "status", "created_at"])->toJson();
    }
    public function edit($id)
    {
        $subscription = \App\Subscription::findOrFail($id);
        return view("admin.subscriptions.edit", compact("subscription"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["link" => "required"]);
        \App\Subscription::where(["id" => $id])->update(["link" => $request->input("link")]);
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/subscriptions/" . $id . "/edit");
    }
    public function cancel(\Illuminate\Http\Request $request, $id)
    {
        $subscription = \App\Subscription::findOrFail($id);
        $subscription->status = "CANCELLED";
        $subscription->save();
        $user = \App\User::find($subscription->user_id);
        $user->funds = $user->funds + $subscription->price;
        $user->save();
        \Session::flash("alert", __("messages.subscription_cancelled"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/subscriptions");
    }
    public function stop(\Illuminate\Http\Request $request, $id)
    {
        $subscription = \App\Subscription::findOrFail($id);
        $subscription->status = "STOPPED";
        $subscription->save();
        $total = \App\Order::where(["subscription_id" => $subscription->id])->sum("price");
        $userRefund = $subscription->price - $total;
        $user = \App\User::find($subscription->user_id);
        $user->funds = $user->funds + $userRefund;
        $user->save();
        \Session::flash("alert", __("messages.subscription_stopped"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/subscriptions");
    }
    public function orders($id)
    {
        $subscription = \App\Subscription::findOrFail($id);
        $orders = \App\Order::where(["subscription_id" => $id])->get();
        return view("admin.subscriptions.orders.index", compact("subscription", "orders"));
    }
    public function storeOrder(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["link" => "required", "start_counter" => "required", "remains" => "required"]);
        $subscription = \App\Subscription::findOrFail($id);
        $package = \App\Package::findOrFail($subscription->package_id);
        $userPackagePrices = \App\UserPackagePrice::where(["user_id" => $subscription->user_id])->pluck("price_per_item", "package_id")->toArray();
        $package_price = isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item;
        $price = (int) $package_price * $subscription->quantity;
        $price = number_formats($price, 2, ".", "");
        \App\Order::create(["price" => $price, "quantity" => $subscription->quantity, "package_id" => $package->id, "user_id" => $subscription->user_id, "link" => $request->input("link"), "start_counter" => $request->input("start_counter"), "remains" => $request->input("remains"), "status" => "COMPLETED", "subscription_id" => $id]);
        $subscription->status = "ACTIVE";
        $totalOrders = \App\Order::where(["subscription_id" => $subscription->id])->count();
        if ($subscription->posts <= $totalOrders) {
            $subscription->status = "COMPLETED";
        }
        $subscription->save();
        \Session::flash("alert", __("messages.order_placed"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/subscriptions/" . $id . "/orders");
    }
}
