<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;


class AccountController extends Controller
{
    public function showSettings()
    {
        return view("moderator.account-settings");
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
        return redirect("/moderator/account/settings");
    }
}
