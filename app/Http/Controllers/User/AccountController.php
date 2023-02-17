<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showSettings()
    {
        $tzlist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        return view("settings", compact("tzlist"));
    }

    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("old" => "required", "password" => "required|min:6", "name" => "required"));
        if (!\Illuminate\Support\Facades\Hash::check($request->input("old"), \Illuminate\Support\Facades\Auth::user()->password)) {
            return view("settings")->withErrors(array("old" => __("messages.confirm_password_did_not_match")));
        } elseif ($request->input("password") != $request->input("password_confirmation")) {
            return view("settings")->withErrors(array("password" => __("messages.confirm_password_did_not_match")));
        }
        \App\User::where(array("id" => \Illuminate\Support\Facades\Auth::user()->id))->update(array("password" => bcrypt($request->input("password")), "name" => $request->input("name")));
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/account/settings");
    }

    public function updateConfig(\Illuminate\Http\Request $request)
    {
        \App\User::where(array("id" => \Illuminate\Support\Facades\Auth::user()->id))->update(array("timezone" => $request->input("timezone")));
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/account/settings");
    }
    public function redeemPoints(\Illuminate\Http\Request $request)
    {
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $points = $user->points;
        \App\RedeemPoints::updateOrCreate(['amount' => ($points) / 100, 'user_id' => $user->id, 'status' => 'Pending']);
        $user->points = '0';
        $user->save();
        \Illuminate\Support\Facades\Session::flash('alert', 'Your InstaRaja Points has been Redeemed and will be added to your funds within 0-24 hrs.');
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success no-auto-close');
        return redirect("/account/settings");
    }
    public function getRedeemHistory()
    {
        return view("points.index");
    }

    public function getRedeemHistoryData()
    {
        $points = \App\RedeemPoints::where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id));
        return datatables()->of($points)->editColumn("amount", function ($point) {
            return getOption("currency_symbol") . number_formats($point->amount, 2, getOption("currency_separator"), "");
        })->toJson();
    }

    public function updateKey(\Illuminate\Http\Request $request)
    {
        $var = "aHR0cHM6Ly9idXkuaW5kdXNyYWJiaXRzY3JpcHQuY29t";
        $username = getOption("username");
        $purchase_code = getOption("purchase_code");
        $domain = base64_encode($request->server("SERVER_NAME"));
        $url = base64_decode($var) . "/validate/" . (string) $username . "/" . (string) $purchase_code . "/" . (string) $domain;
        $client = new \GuzzleHttp\Client();
        try {
            $res = $client->request("GET", $url, array("headers" => array("Accept" => "application/json")));
            if ($res->getStatusCode() != 200) {
                \Session::flash("error", "Error with licenses server, Please contact support");
                return redirect("/admin/system/transfer")->withInput();
            }
            $body = json_decode($res->getBody()->getContents());
            if (!$body->success) {
                \Session::flash("error", $body->error);
                return redirect("/dashboard");
            }
            setOption("app_key", $body->appkey);
            setOption("app_code", $body->appcode);
            return redirect("/dashboard");
        } catch (\Exception $e) {
            \Session::flash("alert", "Error On license Server");
            \Session::flash("alertClass", "success");
            return redirect("/dashboard");
        }
    }

    public function generateKey()
    {
        $api_token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < 32; $i++) {
            $api_token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        \App\User::where(array("id" => \Illuminate\Support\Facades\Auth::user()->id))->update(array("api_token" => $api_token));
        return redirect("/account/settings");
    }

    public function getFundsLoadHistory()
    {
        return view("transaction-history.index");
    }

    public function getFundsLoadHistoryData()
    {
        $transactions = \App\Transaction::with("paymentMethod")->where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id));
        return datatables()->of($transactions)->editColumn("amount", function ($transaction) {
            return getOption("currency_symbol") . number_formats($transaction->amount, 2, getOption("currency_separator"), "");
        })->toJson();
    }
}
