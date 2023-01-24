<?php

namespace App\Http\Controllers\Admin;

use App\CouponUser;
use App\Coupon;
use App\CouponHistory;
use App\User;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{
    public function index()
    {
        return view("admin.coupons.index");
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function indexData()
    {

        $coupons = \App\Coupon::all();
        return datatables()->of($coupons)->editColumn("min_funds", function ($coupon) {
            return getOption("currency_symbol") . number_formats($coupon->min_funds, 2, getOption("currency_separator"), "");
        })->editColumn("amount", function ($coupon) {
            return getOption("currency_symbol") . number_formats($coupon->amount, 2, getOption("currency_separator"), "");
        })->editColumn("max_usage", function ($coupon) {
            if ($coupon->max_usage == 1) {
                return $coupon->max_usage . ' time';
            } else {
                return $coupon->max_usage . ' times';
            }
        })->editColumn("account_age", function ($coupon) {
            if ($coupon->account_age == 1) {
                return $coupon->account_age . ' day';
            } else {
                return $coupon->account_age . ' days';
            }
        })->addColumn("users", function ($coupon) {
            return CouponUser::where('coupon_id', $coupon->id)->count();
        })->editColumn("status", function ($coupon) {
            if ($coupon->status == 'active') {
                return 'Active';
            } else {
                return 'Deactive';
            }
        })->editColumn("expiry", function ($coupon) {
            return date('d, F Y', strtotime($coupon->expiry));
        })->editColumn("created_at", function ($coupon) {
            return $coupon->created_at->diffForHumans();
        })->editColumn("updated_at", function ($coupon) {
            return $coupon->updated_at->diffForHumans();
        })->addColumn("action", function ($coupon) {
            return view("admin.coupons.index-buttons", compact("coupon"));
        })->toJson();
    }

    public function create()
    {
        $users = \App\User::where('role', '!=', 'ADMIN')->get()->pluck('email', 'id')->toArray();
        return view("admin.coupons.create", compact("users"));
    }

    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM


    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("code" => "required|max:255|unique:coupons", "min_funds" => "required", "max_usage" => "required", "account_age" => "required", "amount" => "required", "expiry" => "required"));
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->min_funds = $request->min_funds;
        $coupon->max_usage = $request->max_usage;
        $coupon->account_age = $request->account_age;
        $coupon->amount = $request->amount;
        $coupon->expiry = $request->expiry;
        $coupon->status = $request->status;
        $coupon->hours = $request->hours;
        $coupon->funds = $request->funds;
        $coupon->save();
        if (!empty($request->users)) {
            foreach ($request->users as $key => $id) {
                $couponuser = new CouponUser();
                $couponuser->user_id = $id;
                $couponuser->coupon_id = $coupon->id;
                $couponuser->save();
            }
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/coupons/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $coupon = \App\Coupon::findOrFail($id);
        $users = \App\User::where('role', '!=', 'ADMIN')->get()->pluck('email', 'id')->toArray();
        $selected_users = \App\CouponUser::where('coupon_id', $id)->get()->pluck('user_id')->toArray();
        return view("admin.coupons.edit", compact("coupon", "users", "selected_users"));
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("code" => "required|max:255|unique:coupons,code," . $id, "min_funds" => "required", "max_usage" => "required", "account_age" => "required", "amount" => "required", "expiry" => "required"));
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->code = $request->code;
            $coupon->min_funds = $request->min_funds;
            $coupon->max_usage = $request->max_usage;
            $coupon->account_age = $request->account_age;
            $coupon->amount = $request->amount;
            $coupon->expiry = $request->expiry;
            $coupon->status = $request->status;
            $coupon->hours = $request->hours;
            $coupon->funds = $request->funds;
            $coupon->save();
            if (!empty($request->users)) {
                CouponUser::where('coupon_id', $id)->delete();
                foreach ($request->users as $key => $id) {
                    $couponuser = new CouponUser();
                    $couponuser->user_id = $id;
                    $couponuser->coupon_id = $coupon->id;
                    $couponuser->save();
                }
            }
            \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
            return redirect()->back();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("System encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect()->back();
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("System encountered an problem"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
        return redirect()->back();
    }

    public function destroy($id)
    {
        $coupon = \App\Coupon::findOrFail($id);
        try {
            $coupon->delete();
            CouponUser::where('coupon_id', $id)->delete();
            CouponHistory::where('coupon_id', $id)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("System encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/coupons");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/coupons");
    }

    public function destroyhistory($id)
    {
        $coupon = \App\CouponHistory::findOrFail($id);
        try {
            $coupon->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("System encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/coupons");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/coupons/history");
    }

    public function history()
    {
        if (request()->ajax()) {
            $coupon_history = CouponHistory::all();
            return datatables()->of($coupon_history)->editColumn("coupon_id", function ($coupon) {
                return Coupon::find($coupon->coupon_id)->code;
            })->addColumn("amount", function ($coupon) {
                return getOption("currency_symbol") . number_formats(Coupon::find($coupon->coupon_id)->amount, 2, getOption("currency_separator"), "");
            })->editColumn("user_id", function ($coupon) {
                return (User::find($coupon->user_id)) ? User::find($coupon->user_id)->email : 'N/A';
            })->addColumn("name", function ($coupon) {
                return (User::find($coupon->user_id)) ? User::find($coupon->user_id)->name : 'N/A';
            })->editColumn("created_at", function ($coupon) {
                return $coupon->created_at->diffForHumans();
            })->editColumn("updated_at", function ($coupon) {
                return $coupon->updated_at->diffForHumans();
            })->addColumn("action", function ($coupon) {
                return view("admin.coupons.history-buttons", compact("coupon"));
            })->toJson();
        }
        return view("admin.coupons.history");
    }
}
