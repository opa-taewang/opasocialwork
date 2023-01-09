<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = \App\PaymentMethod::where(["config_key" => NULL])->groupBy("slug")->get();
        return view("admin.payment-methods.index", compact("paymentMethods"));
    }
    public function edit($id)
    {
        $paymentMethod = \App\PaymentMethod::findOrFail($id);
        $configOptions = \App\PaymentMethod::where(["slug" => $paymentMethod->slug])->whereNotNull("config_key")->get();
        return view("admin.payment-methods.edit", compact("paymentMethod", "configOptions"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $paymentMethod = \App\PaymentMethod::findOrFail($id);
        $paymentMethod->status = $request->input("status");
        $paymentMethod->is_disabled_default = is_null($request->input("is_disabled_default")) ? 0 : 1;
        $paymentMethod->save();
        $config_key = $request->input("config_key");
        $config_value = $request->input("config_value");
        $rows = [];
        for ($i = 0; $i < count($config_key); $i++) {
            $rows[] = ["name" => $paymentMethod->name, "slug" => $paymentMethod->slug, "config_key" => $config_key[$i], "config_value" => $config_value[$i]];
        }
        \App\PaymentMethod::where(["slug" => $paymentMethod->slug])->whereNotNull("config_key")->delete();
        \Illuminate\Support\Facades\DB::table("payment_methods")->insert($rows);
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/payment-methods/" . $id . "/edit");
    }
}
