<?php

namespace App\Http\Controllers\Moderator;

use App\Models\User;
use App\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index()
    {
        return view("moderator.users.index");
    }

    public function indexData()
    {
        $users = User::all();
        return datatables()->of($users)->editColumn("funds", function ($user) {
            return getOption("currency_symbol") . number_formats($user->funds, 2, getOption("currency_separator"), "");
        })->addColumn("action", function ($user) {
            return view("moderator.users.index-buttons", compact("user"));
        })->toJson();
    }

    public function message(User $user)
    {
        $messages = \App\moderatorMessage::where("user_id", $user->id)->orderBy("created_at", "desc")->get();
        $note = NULL;
        app("App\\Http\\Controllers\\OrderController")->check(3);
        foreach ($messages as $message) {
            $dtval = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $message->created_at, auth()->user()->timezone);
            $note = $note . "<span class=\"text-muted\">" . $dtval . "</span><br>Title: <span class=\"wysiwyg-color-blue\">" . $message->title . "</span><br>Message: <span class=\"wysiwyg-color-red\">" . $message->message . "</span><hr>";
        }
        return view("moderator.users.message", compact("user", "note"));
    }

    public function postmessage(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("user_id" => "required", "type" => "required", "title" => "required", "message" => "required"));
        \App\moderatorMessage::create(array("user_id" => $request->input("user_id"), "moderator_id" => \Auth::user()->id, "type" => $request->input("type"), "title" => $request->input("title"), "message" => $request->input("message")));
        Session::flash("alert", "Message Sent");
        Session::flash("alertClass", "success");
        return redirect("moderator/users/");
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::where(array("config_key" => NULL, "status" => "ACTIVE"))->groupBy("slug")->get();
        return view("moderator.users.create", compact("paymentMethods"));
    }

    public function loginAs($id)
    {
        session(array("imitator" => auth()->user()->id));
        auth()->loginUsingId($id);
        return redirect("/");
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("name" => "required|max:255", "email" => "required|email|max:255|unique:users", "password" => "required|min:6"));
        $payment_methods = "";
        if (!is_null($request->input("payment_methods"))) {
            $payment_methods = implode(",", $request->input("payment_methods"));
        }
        User::create(array("name" => $request->input("name"), "email" => $request->input("email"), "status" => $request->input("status"), "role" => $request->input("role"), "funds" => $request->input("funds"), "skype_id" => $request->input("skype_id"), "enabled_payment_methods" => $payment_methods, "password" => bcrypt($request->input("password"))));
        Session::flash("alert", __("messages.created"));
        Session::flash("alertClass", "success");
        return redirect("/moderator/users/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $user = \App\User::findOrFail($id);
        $packages = \App\Package::where(array("status" => "ACTIVE"))->orderBy("service_id")->get();
        $userPackagePrices = \App\UserPackagePrice::where(array("user_id" => $id))->pluck("price_per_item", "package_id")->toArray();
        $paymentMethods = \App\PaymentMethod::where(array("config_key" => NULL))->groupBy("slug")->get();
        $enabled_payment_methods = array();
        if ($user->enabled_payment_methods != "") {
            $enabled_payment_methods = explode(",", $user->enabled_payment_methods);
        }
        return view("moderator.users.edit", compact("user", "paymentMethods", "enabled_payment_methods", "userPackagePrices", "packages"));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("name" => "required|max:255", "email" => "required|email|max:255"));
        try {
            $user = \App\User::findOrFail($id);
            $payment_methods = "";
            if (!is_null($request->input("payment_methods"))) {
                $payment_methods = implode(",", $request->input("payment_methods"));
            }
            $user->name = $request->input("name");
            $user->status = $request->input("status");
            $user->role = $request->input("role");
            $user->funds = $request->input("funds");
            if ($request->filled("password")) {
                if (strlen($request->input("password")) < 6) {
                    return redirect()->back()->withErrors(array("password" => "Minimum Length Should be 6"));
                }
                $user->password = bcrypt($request->input("password"));
            }
            $user->enabled_payment_methods = $payment_methods;
            $user->skype_id = $request->input("skype_id");
            $user->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            Session::flash("alert", __("messages.email_already_used"));
            Session::flash("alertClass", "danger");
            return redirect("/moderator/users/" . $id . "/edit");
        }
        Session::flash("alert", __("messages.updated"));
        Session::flash("alertClass", "success no-auto-close");
        return redirect("/moderator/users/" . $id . "/edit");
    }

    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);
        try {
            $user->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            Session::flash("alert", __("messages.user_have_orders"));
            Session::flash("alertClass", "danger");
            return redirect("/moderator/users");
        }
        Session::flash("alert", __("messages.deleted"));
        Session::flash("alertClass", "success");
        return redirect("/moderator/users");
    }

    public function getFundsLoadHistory(\Illuminate\Http\Request $request)
    {
        if (!password_verify($request->server("SERVER_NAME"), getOption("app_key", true)) && !password_verify(base64_encode($request->server("SERVER_NAME")), getOption("app_code", true))) {
            Artisan::call("down");
        }
        return view("moderator.transaction-history.index");
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
        $user->funds = $user->funds + $request->input("fund");
        $user->save();
        \App\Transaction::create(array("amount" => $request->input("fund"), "payment_method_id" => $request->input("payment_method_id"), "user_id" => $id, "details" => $request->input("details")));
        Session::flash("alert", __("messages.updated"));
        Session::flash("alertClass", "success");
        return redirect("moderator/users/" . $id . "/edit");
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
        Session::flash("alert", __("messages.updated"));
        Session::flash("alertClass", "success");
        return redirect("moderator/users/" . $id . "/edit");
    }
}
