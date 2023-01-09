<?php

namespace App\Http\Controllers\Admin;

use File;

use App\Http\Controllers\Controller;

class AdsController extends Controller
{
    public function index()
    {
        return view("admin.ads.index");
    }

    public function indexData()
    {
        $ads = \App\Ad::all();
        return datatables()->of($ads)->addColumn("type", function ($ad) {
            if (!empty($ad->code))
                return "Code";
            if (!empty($ad->image))
                return "Image";
            return "";
        })->addColumn("action", function ($ad) {
            return view("admin.ads.index-buttons", compact("ad"));
        })->toJson();
    }

    public function create()
    {
        return view("admin.ads.create");
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("address" => "required|unique:ips", "status" => "required"));
        \App\Ip::create(array("address" => $request->input("address"), "blocked" => $request->input("status")));

        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/ips/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $ad = \App\Ad::findOrFail($id);
        return view("admin.ads.edit", compact("ad"));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        if ($request->type == "Code")
            $this->validate($request, array("code" => "required", "link" => "required", "status" => "required"));
        if ($request->type == "Image")
            $this->validate($request, array("status" => "required", "link" => "required"));
        $image = '';
        if ($request->hasFile('image')) {
            $imgfile = $request->file('image');
            $imgpath = 'ads/images';
            File::makeDirectory($imgpath, $mode = 0777, true, true);
            $name = time() . "_" . $imgfile->getClientOriginalName();
            $filename1 = $name;
            $success = $imgfile->move(dirname(base_path()) . '/ads/images', $filename1);
            $image = $name;
        }
        $ad = \App\Ad::findOrFail($id);
        if (empty($image) && $request->type == "Image")
            $image = $ad->image;
        if ($request->type == "Code")
            $ad->code = $request->code;
        if ($request->type == "Image")
            $ad->image = $image;
        $ad->status = $request->status;
        $ad->link = $request->link;
        $ad->save();
        \Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('admin/ads/' . $id . '/edit');
    }

    public function destroy($id)
    {
        $ad = \App\Ad::findOrFail($id);
        try {
            $ad->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, Syste encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/ads");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/ads");
    }
}
