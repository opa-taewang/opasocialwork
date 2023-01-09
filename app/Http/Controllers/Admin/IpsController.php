<?php

namespace App\Http\Controllers\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\Controller;

class IpsController extends Controller
{
    public function index()
    {

        return view("admin.ips.index");
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

    public function indexData()
    {
        $ips = \App\Ip::all();
        return datatables()->of($ips)->editColumn("blocked", function ($ip) {
            return ($ip->blocked == 1) ? 'Blocked' : 'Un-blocked';
        })->editColumn("created_at", function ($ip) {
            return $ip->created_at->diffForHumans();
        })->editColumn("user_id", function ($ip) {
            if (!empty($ip->user_id)) {
                $user = \App\User::find($ip->user_id);
                if (!empty($user))
                    return $user->email;
                return "";
            }
            return '';
        })->addColumn("action", function ($ip) {
            return view("admin.ips.index-buttons", compact("ip"));
        })->toJson();
    }

    public function create()
    {
        return view("admin.ips.create");
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("address" => "required|unique:ips", "status" => "required", "reason" => "required"));
        \App\Ip::create(array("address" => $request->input("address"), "blocked" => $request->input("status"), "reason" => $request->reason));
        if ((strtoupper($request->input("blocked")) == '1'))
            try {
                $client = new Client();
                $domain = base64_encode(\Request::server("SERVER_NAME"));
                $url = "http://smm-script.com/api/v2/blackips";
                $params['headers'] = ['X-XSRF-TOKEN' => csrf_token()];
                $params['form_params'] = array('address' => $request->input("address"), 'reason' => $request->reason, 'domain' => $domain);
                $client->post($url, $params);
            } catch (\Exception $e) {
            }
        else
            \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/ips/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $ip = \App\Ip::findOrFail($id);
        return view("admin.ips.edit", compact("ip"));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("address" => "required", "status" => "required"));
        if (\App\Ip::where('address', $request->input("address"))->where('id', '!=', $id)->count()) {
            \Illuminate\Support\Facades\Session::flash("alert", __("The address has already been taken."));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect('admin/ips/' . $id . '/edit');
        }
        $ip = \App\Ip::findOrFail($id);
        $ip->address = $request->input("address");
        $ip->blocked = $request->input("status");
        $ip->save();
        if (($request->input("status")) == '1')
            try {
                $client = new Client();
                $domain = base64_encode(\Request::server("SERVER_NAME"));
                $url = "http://smm-script.com/api/v2/blackips";
                $params['headers'] = ['X-XSRF-TOKEN' => csrf_token()];
                $params['form_params'] = array('address' => $request->input("address"), 'reason' => $request->reason, 'domain' => $domain);
                $client->post($url, $params);
            } catch (\Exception $e) {
            }
        \Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('admin/ips/' . $id . '/edit');
    }

    public function destroy($id)
    {
        $ip = \App\Ip::findOrFail($id);
        try {
            $ip->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, Syste encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/ips");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/ips");
    }
}
