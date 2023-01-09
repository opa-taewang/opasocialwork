<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
class AccountController extends Controller
{
    public function showSettings()
    {
        return view("admin.account-settings");
    }
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ["old" => "required", "password" => "required|min:6", "name" => "required"]);
        if (!\Illuminate\Support\Facades\Hash::check($request->input("old"), \Illuminate\Support\Facades\Auth::user()->password)) {
            return view("settings")->withErrors(["old" => __("messages.confirm_password_did_not_match")]);
        }
        if ($request->input("password") != $request->input("password_confirmation")) {
            return view("settings")->withErrors(["password" => __("messages.confirm_password_did_not_match")]);
        }
        \App\User::where(["id" => \Illuminate\Support\Facades\Auth::user()->id])->update(["password" => bcrypt($request->input("password")), "name" => $request->input("name")]);
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/account/settings");
    }
}
