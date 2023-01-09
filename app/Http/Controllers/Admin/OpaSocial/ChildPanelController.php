<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\ChildPanelOrder;
use App\User;
use Request;
use App\Http\Controllers\Controller;


class ChildPanelController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function index()
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }
        $error = '';
        $price = '';
        $client = new Client();
        $child_panel = \DB::table('configs')->where('name', 'child_panel')->value('value');
        $buyer = \DB::table('configs')->where('name', 'child_panel_buyer')->value('value');
        $amount = \DB::table('configs')->where('name', 'child_panel_price')->value('value');
        $info = '';
        try {
            $domain = base64_encode($_SERVER['SERVER_NAME']);
            $key = \DB::table('configs')->where('name', 'smm_api_key')->value('value');
            if (empty($key))
                $url = "https://smm-script.com/api/getapikey";
            else
                $url = "https://smm-script.com/api/childpanel";
            $params['headers'] = ['X-XSRF-TOKEN' => csrf_token(), 'X-Authorization' => $key];
            $params['form_params'] = array('domain' => $domain);
            $res = $client->post($url, $params);

            $body = $res->getBody()->getContents();
            if ($body == "Unauthorized") {
                $error = "Unauthorized api key";
            } elseif ($body == 'false') {
                $error = "Contact with Smm-Script Administrator to get an apikey";
            } else {
                $error = "Contact with Smm-Script Administrator to get an apikey";
            }
        } catch (RequestException $e) {
            $error = 'Oops, System Encountered an problem,Contact With SMM SCRIPT';
        }

        return view("admin.childpanel", compact('buyer', 'info', 'error', 'child_panel', 'amount', 'price'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("key" => "required"));
        setOption('smm_api_key', $request->input('key'));

        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/child-panels");
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("child_panel" => "required"));
        setOption('child_panel', $request->input('child_panel'));

        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/child-panels");
    }

    public function updatePrice(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("amount" => "required", "buyer" => "required"));
        setOption('child_panel_price', $request->input('amount'));
        setOption('child_panel_buyer', $request->buyer);

        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/child-panels");
    }

    public function show()
    {
        return view("admin.childpanels.index");
    }

    public function edit($id = '')
    {
        $order = ChildPanelOrder::findOrFail($id);
        if ($order->buyer != 'admin')
            abort(404);
        return view("admin.childpanels.edit", compact('order'));
    }

    public function updateorder(\Illuminate\Http\Request $request, $id = '')
    {
        $this->validate($request, array("status" => "required", "domain" => "required", "admin_user" => "required", "admin_password" => "required"));
        try {
            $order = ChildPanelOrder::findOrFail($id);
            $order->domain = $request->input("domain");
            $order->admin_user = $request->input("admin_user");
            $order->admin_password = $request->input("admin_password");
            $order->status = $request->input("status");
            $order->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, system encountered an problem."));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/child-panels/" . $id . "/edit");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/child-panels/" . $id . '/edit');
    }

    public function getorders()
    {
        $orders = ChildPanelOrder::all();
        return datatables()->of($orders)->editColumn("amount", function ($order) {
            return getOption("currency_symbol") . number_formats($order->amount, 2, getOption("currency_separator"), "");
        })->editColumn("created_at", function ($order) {
            return $order->created_at->diffForHumans();
        })->editColumn("updated_at", function ($order) {
            return $order->updated_at->diffForHumans();
        })->editColumn("admin_password", function ($order) {
            if ($order->buyer == "admin") {
                return $order->admin_password;
            }
            return str_repeat("*", strlen($order->admin_password));
        })->addColumn("name", function ($order) {
            if (User::where('id', $order->user_id)->count()) {
                return User::where('id', $order->user_id)->value('name');
            }
            return '';
        })->addColumn("email", function ($order) {
            if (User::where('id', $order->user_id)->count()) {
                return User::where('id', $order->user_id)->value('email');
            }
            return '';
        })->addColumn("action", function ($order) {
            if ($order->buyer == "admin") {
                return view("admin.childpanels.index-buttons", compact('order'));
            }
            return '';
        })->toJson();
    }
    public function destroy($id)
    {
        $order = ChildPanelOrder::findOrFail($id);
        try {
            $order->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, system encountered an problem."));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/child-panels/orders");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/child-panels/orders");
    }
    public function sync()
    {
        \Artisan::call('update:childpanels');
        \Illuminate\Support\Facades\Session::flash("alert", __("Process has been started, it may take few minutes"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect()->back();
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function __construct()
    {
        if (\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php')) != config('database.connections.mysql.hdriver')) {
            abort('506');
        }
    }
}
