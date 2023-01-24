<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function index()
    {

        return view("admin.users.index");
    }

    public function indexData()
    {
        $users = \App\User::all();
        return datatables()->of($users)->editColumn("funds", function ($user) {
            return getOption("currency_symbol") . number_formats($user->funds, 2, getOption("currency_separator"), "");
        })->addColumn("group", function ($user) {
            $name = '';
            if (!empty($user->group_id)) {
                $group = \App\Group::find($user->group_id);
                if ($group)
                    $name = $group->name;
            }
            return $name;
        })->addColumn("action", function ($user) {
            return view("admin.users.index-buttons", compact("user"));
        })->toJson();
    }
    public function newFundIndex($id)
    {
        $id = $id;
        return view("admin.funds.index", compact('id'));
    }
    public function indexFundData($id)
    {
        $users = \App\User::all();
        $fundsdata = \App\FundChange::with("user")->where(array("user_id" => $id));
        return datatables()->of($fundsdata)->editColumn("amount", function ($pack) {
            return getOption("currency_symbol") . number_formats($pack->amount, 2, getOption("currency_separator"), "");
        })->editColumn("pricebefore", function ($pack) {
            return getOption("currency_symbol") . number_formats($pack->pricebefore, 2, getOption("currency_separator"), "");
        })->editColumn("priceafter", function ($pack) {
            return getOption("currency_symbol") . number_formats($pack->priceafter, 2, getOption("currency_separator"), "");
        })->editColumn(
            'name',
            function ($pack) {
                $apipack = $pack->user_id;
                $apis = \App\User::where('id', $apipack)->pluck('name')->toArray();
                return $apis;
            }
        )->editColumn(
            'details',
            function ($pack) {
                $details = nl2br(e($pack->details));
                return $details;
            }
        )->toJson();
    }
    public function referralindex()
    {
        return view("admin.users.referralindex");
    }

    public function referralindexData()
    {
        $userreferrals = \App\Visit::distinct('refUid')->groupBy('refUid')->get();
        return datatables()->of($userreferrals)->addColumn("name", function ($userreferral) {
            $user = \App\User::find($userreferral->refUid);
            return $user->name;
        })->addColumn("rname", function ($userreferral) {
            $user = \App\User::find($userreferral->refVid);
            return $user->name;
        })->addColumn("count", function ($userreferral) {
            $count = \App\Visit::where('refUid', '=', $userreferral->refUid)->count();
            return $count;
        })->toJson();
    }
    public function message(\App\User $user)
    {
        $messages = \App\AdminMessage::where("user_id", $user->id)->orderBy("created_at", "desc")->get();
        $note = NULL;
        app("App\\Http\\Controllers\\OrderController")->check(3);
        foreach ($messages as $message) {
            $dtval = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $message->created_at, auth()->user()->timezone);
            $note = $note . "<span class=\"text-muted\">" . $dtval . "</span><br>Title: <span class=\"wysiwyg-color-blue\">" . $message->title . "</span><br>Message: <span class=\"wysiwyg-color-red\">" . $message->message . "</span><hr>";
        }
        return view("admin.users.message", compact("user", "note"));
    }
    public function redeemAccept($id)
    {
        $points = \App\RedeemPoints::findOrFail($id);
        $user = \App\User::find($points->user_id);

        $usrfunds = $user->funds;
        $usrpoints = $points->amount;
        $totalpoints = $usrfunds + $usrpoints;
        $user->funds = $totalpoints;
        $user->save();
        $points->status = 'Completed';
        $points->save();
        $text = 'Payment Added for InstaRaja Points' . "\n";
        $text .= 'Amount : ' . $usrpoints . "\n";
        $text .= 'Points : ' . $usrpoints * 100 . "\n";
        fundChange($text, $usrpoints, 'ADD', $points->user_id, '');
        \App\Transaction::create(array("amount" => $usrpoints, "payment_method_id" => '713', "user_id" => $user->id, "details" => 'InstaRaja Points Redeem'));

        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
        return redirect("/admin/points-history");
    }
    public function redeemReject($id)
    {
        $points = \App\RedeemPoints::findOrFail($id);
        $points->status = 'Cancelled';
        $points->save();
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
        return redirect("/admin/points-history");
    }
    public function postmessage(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("user_id" => "required", "type" => "required", "title" => "required", "message" => "required"));
        \App\AdminMessage::create(array("user_id" => $request->input("user_id"), "admin_id" => \Auth::user()->id, "type" => $request->input("type"), "title" => $request->input("title"), "message" => $request->input("message")));
        \Illuminate\Support\Facades\Session::flash("alert", "Message Sent");
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/users/");
    }

    public function create()
    {
        $groups = \App\Group::all();
        $paymentMethods = \App\PaymentMethod::where(array("config_key" => NULL, "status" => "ACTIVE"))->groupBy("slug")->get();
        return view("admin.users.create", compact("paymentMethods", "groups"));
    }

    public function loginAs($id)
    {
        session(array("imitator" => auth()->user()->id));
        auth()->loginUsingId($id);
        return redirect("");
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("name" => "required|max:255", "email" => "required|email|max:255|unique:users", "username" => "required|max:25|unique:users", "password" => "required|min:6", "group" => "required"));
        $payment_methods = "";
        if (!is_null($request->input("payment_methods"))) {
            $payment_methods = implode(",", $request->input("payment_methods"));
        }
        \App\User::create(array("name" => $request->input("name"), "username" => $request->input("username"), "verified" => $request->input("verified"), "email" => $request->input("email"), "status" => $request->input("status"), "role" => $request->input("role"), "funds" => $request->input("funds"), "skype_id" => $request->input("skype_id"), "enabled_payment_methods" => $payment_methods, "password" => bcrypt($request->input("password")), "group_id" => $request->group));
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/users/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $user = \App\User::findOrFail($id);
        $groups = \App\Group::all();
        $packages = \App\Package::where(array("status" => "ACTIVE"))->orderBy("service_id")->get();
        $userPackagePrices = \App\UserPackagePrice::where(array("user_id" => $id))->pluck("price_per_item", "package_id")->toArray();
        $paymentMethods = \App\PaymentMethod::where(array("config_key" => NULL))->groupBy("slug")->get();
        $enabled_payment_methods = array();
        if ($user->enabled_payment_methods != "") {
            $enabled_payment_methods = explode(",", $user->enabled_payment_methods);
        }
        return view("admin.users.edit", compact("user", "paymentMethods", "enabled_payment_methods", "userPackagePrices", "packages", "groups"));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("name" => "required|max:255", "email" => "required|email|max:255", "group" => "required"));
        try {
            $user = \App\User::findOrFail($id);
            $payment_methods = "";
            if (!is_null($request->input("payment_methods"))) {
                $payment_methods = implode(",", $request->input("payment_methods"));
            }
            $user->name = $request->input("name");
            $user->status = $request->input("status");
            $user->role = $request->input("role");
            if ($user->funds != $request->input("funds")) {
                $text = 'User Funds Changed by Admin' . "\n";
                $text .= 'Changed Funds : ' . $request->input("funds") . "\n";
                fundChange($text, $request->input("funds"), 'CHANGEADMIN', $id, 0);
            }
            $user->funds = $request->input("funds");
            $user->points = $request->input("points");
            $user->reffund = $request->input("reffund");
            if ($request->filled("password")) {
                if (strlen($request->input("password")) < 6) {
                    return redirect()->back()->withErrors(array("password" => "Minimum Length Should be 6"));
                }
                $user->password = bcrypt($request->input("password"));
            }
            $user->enabled_payment_methods = $payment_methods;
            $user->skype_id = $request->input("skype_id");
            $user->verified = $request->input("verified");
            $user->group_id = $request->group;
            if ($user->funds != $request->input("funds")) {
                $text = 'User Funds Changed by Admin' . "\n";
                $text .= 'Changed Funds : ' . $request->input("funds") . "\n";
                fundChange($text, $request->input("funds"), 'CHANGEADMIN', $id, 0);
            }
            $user->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("messages.email_already_used"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/users/" . $id . "/edit");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/users/" . $id . "/edit");
    }

    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);
        try {
            $user->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("messages.user_have_orders"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/users");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/users");
    }

    public function getFundsLoadHistory(\Illuminate\Http\Request $request)
    {
        $paymentMethods = \App\PaymentMethod::where(array("config_key" => NULL))->groupBy("slug")->get();
        return view("admin.transaction-history.index", compact("paymentMethods"));
    }

    public function getFundsLoadHistoryData()
    {
        $transactions = \App\Transaction::with("paymentMethod", "user");
        return datatables()->of($transactions)->editColumn("amount", function ($transaction) {
            return getOption("currency_symbol") . number_formats($transaction->amount, 2, getOption("currency_separator"), "");
        })->toJson();
    }

    public function addFunds(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("payment_method_id" => "required", "fund" => "required", "details" => "required"));
        $user = \App\User::findOrFail($id);
        $total_amount = \App\Order::where(['user_id' => $id])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');

        $group = \App\Group::where('funds_limit', '>', $total_amount)->orderBy('funds_limit', 'ASC')->first();
        if ($group) {
            $user->group_id = $group->id;
        }
        $user->funds = $user->funds + $request->input("fund");
        $user->save();

        $transaction = \App\Transaction::create(array("amount" => $request->input("fund"), "payment_method_id" => $request->input("payment_method_id"), "user_id" => $id, "details" => $request->input("details")));
        $text = 'Payment Added by Admin' . "\n";
        $text .= 'Amount : ' . $request->input("fund") . "\n";
        $text .= 'Fund Loads ID : ' . $transaction->id . "\n";
        fundChange($text, $request->input("fund") * 1, 'ADDADMIN', $id, '');
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/users/" . $id . "/edit");
    }
    public function addFundsAdmin(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("payment_method_id" => "required", "fund" => "required", "details" => "required"));
        $user = \App\User::where(['email' => $request->email])->first();
        if (!$user) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Email doesn't exist!"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("admin/funds-load-history");
        }
        $total_amount = \App\Order::where(['user_id' => $user->id])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');

        $group = \App\Group::where('funds_limit', '>', $total_amount)->orderBy('funds_limit', 'ASC')->first();
        if ($group) {
            $user->group_id = $group->id;
        }
        $user->funds = $user->funds + $request->input("fund");
        $user->save();

        $transaction = \App\Transaction::create(array("amount" => $request->input("fund"), "payment_method_id" => $request->input("payment_method_id"), "user_id" => $user->id, "details" => $request->input("details")));
        $text = 'Payment Added by Admin' . "\n";
        $text .= 'Amount : ' . $request->input("fund") . "\n";
        $text .= 'Fund Loads ID : ' . $transaction->id . "\n";
        fundChange($text, $request->input("fund") * 1, 'ADDADMIN', $user->id, '');
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/funds-load-history");
    }
    public function getRedeemHistory(\Illuminate\Http\Request $request)
    {

        return view("admin.points.index");
    }

    public function getRedeemHistoryData()
    {
        $points = \App\RedeemPoints::with("user");
        return datatables()->of($points)->editColumn("amount", function ($point) {
            return getOption("currency_symbol") . number_formats($point->amount, 2, getOption("currency_separator"), "");
        })->addColumn("action", function ($point) {
            return view("admin.points.index-buttons", compact("point"));
        })->toJson();
    }

    public function packageSpecialPrices($id, \Illuminate\Http\Request $request)
    {
        $packageIds = $request->input("package_id");
        $pricePerItems = $request->input("price_per_item");
        $minimumQuanties = $request->input("minimum_quantity");
        if (empty($packageIds)) {
            return redirect()->back();
        }
        $insertRows = array();
        foreach ($packageIds as $packageId) {
            $min_regular_price = (float) $pricePerItems[$packageId] * $minimumQuanties[$packageId];
            $min_regular_price = number_formats($min_regular_price, 2, ".", "");
            if ($min_regular_price > 0) {
                $insertRows[] = array("user_id" => $id, "package_id" => $packageId, "price_per_item" => $pricePerItems[$packageId]);
            }
        }
        \App\UserPackagePrice::where(array("user_id" => $id))->delete();
        if (!empty($insertRows)) {
            \DB::table("user_package_prices")->insert($insertRows);
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/users/" . $id . "/edit");
    }
}
