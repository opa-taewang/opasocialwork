<?php


namespace App\Http\Controllers\User\OpaSocial;

use App\Http\Controllers\Controller;

class ChildPanelController extends Controller
{
    public function __construct()
    {
        if (\DB::table("configs")->where("name", "child_panel")->value("value") != "on") {
            abort(401);
        }
    }
    // @Function index is protected ioncube.dynamickey encoding key.
    public function index()
    {
    }
    public function indexData()
    {
        $user_id = \Auth::user()->id;
        $orders = \App\ChildPanelOrder::where("user_id", $user_id)->get();
        return datatables()->of($orders)->editColumn("amount", function ($order) {
            return getOption("currency_symbol") . number_formats($order->amount, 2, getOption("currency_separator"), "");
        })->editColumn("status", function ($order) {
            $today = date("Y-m-d H:s:i");
            $expire = $order->expiry_at;
            $today_time = strtotime($today);
            $expire_time = strtotime($expire);
            if ($expire_time < $today_time) {
                return "Expired";
            }
            return $order->status;
        })->editColumn("start_at", function ($order) {
            return date("d, F Y H:s:i", strtotime($order->start_at));
        })->editColumn("expiry_at", function ($order) {
            return date("d, F Y H:s:i", strtotime($order->expiry_at));
        })->addColumn("details", function ($order) {
            return $order->id;
        })->toJson();
    }
    public function indexFilterData($filter = "")
    {
        $user_id = \Auth::user()->id;
        $orders = \App\ChildPanelOrder::where("user_id", $user_id)->where("status", $filter)->get();
        return datatables()->of($orders)->editColumn("amount", function ($order) {
            return getOption("currency_symbol") . number_formats($order->amount, 2, getOption("currency_separator"), "");
        })->editColumn("created_at", function ($order) {
            return $order->created_at->diffForHumans();
        })->editColumn("updated_at", function ($order) {
            return $order->updated_at->diffForHumans();
        })->addColumn("details", function ($order) {
            return $order->id;
        })->toJson();
    }
    public function create()
    {
        $amount = \DB::table("configs")->where("name", "child_panel_price")->value("value");
        $amount = getOption("currency_symbol") . number_formats($amount, 2, getOption("currency_separator"), "");
        return view("childpanel.new", compact("amount"));
    }
    // @Function store is protected ioncube.dynamickey encoding key.
    public function store()
    {
    }
    public function show($id)
    {
    }
    public function edit($id)
    {
        $key = ApiKey::findOrFail($id);
        if (!\DB::table("api_keys")->count()) {
            $users = \DB::table("users")->where(["users.role" => "USER", "users.status" => "ACTIVE"])->select("users.email")->get();
        } else {
            $users = \DB::table("users")->join("api_keys", "api_keys.name", "!=", "users.email")->where(["users.role" => "USER", "users.status" => "ACTIVE"])->select("users.email")->get();
        }
        return view("admin.childpanels.edit", compact("users", "key"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["status" => "required"]);
        try {
            $key = ApiKey::findOrFail($id);
            $key->active = $request->input("status");
            $key->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, system encountered an problem."));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/child-panels/" . $id . "/edit");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/child-panels/" . $id . "/edit");
    }
    public function destroy($id)
    {
        $key = ApiKey::findOrFail($id);
        try {
            $key->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, system encountered an problem."));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/child-panels");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/child-panels");
    }
    public function accessEvents()
    {
        return view("admin.childpanels.access");
    }
    public function sync()
    {
        \Artisan::call("schedule:run");
        \Illuminate\Support\Facades\Session::flash("alert", __("Process has been started, it may take few minutes"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect()->back();
    }
    public function renew(\Illuminate\Http\Request $request, $id)
    {
        $childpanel = \App\ChildPanelOrder::findOrFail($id);
        $childpanel->renew = (int) $request->renew ? 1 : 0;
        $childpanel->save();
        return json_encode("success");
    }
}
