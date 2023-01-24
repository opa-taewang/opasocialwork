<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function index()
    {

        return view("admin.groups.index");
    }
    public function indexData()
    {

        $groups = \App\Group::all();
        return datatables()->of($groups)->editColumn("price_percentage", function ($group) {
            return $group->price_percentage . ' %';
        })->editColumn("funds_limit", function ($group) {
            return "$" . $group->funds_limit;
        })->editColumn("created_at", function ($group) {
            return $group->created_at->diffforhumans();
        })->addColumn("action", function ($group) {
            return view("admin.groups.index-buttons", compact("group"));
        })->toJson();
    }

    public function create()
    {
        $users = \App\User::all();
        $packages = \App\Package::all();
        return view("admin.groups.create", compact("users", "packages"));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("name" => "required|max:255", "pricepercentage" => "required", "packages" => "required", "funds_limit" => "required", "point_percent" => "required"));
        $package_ids = implode(",", $request->packages);
        $default = 0;
        if ($request->isdefault) {
            $default_group = \App\Group::where("isdefault", 1)->first();
            if ($default_group) {
                $default_group->isdefault = 0;
                $default_group->save();
            }
            $default = 1;
        }
        \App\Group::create(array("name" => $request->input("name"), "price_percentage" => $request->input("pricepercentage"), "funds_limit" => $request->input("funds_limit"), "point_percent" => $request->input("point_percent"), "package_ids" => $package_ids, "isdefault" => $default));
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/groups/create");
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $group = \App\Group::findOrFail($id);
        $packages = \App\Package::all();
        return view("admin.groups.edit", compact("group",  "packages"));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, array("name" => "required|max:255", "pricepercentage" => "required", "packages" => "required", "funds_limit" => "required", "point_percent" => "required"));
        $group = \App\Group::findOrFail($id);
        $default = 0;
        if ($request->isdefault) {
            $default_group = \App\Group::where("isdefault", 1)->where('id', '!=', $group->id)->first();
            if ($default_group) {
                $default_group->isdefault = 0;
                $default_group->save();
            }
            $default = 1;
        }
        $package_ids = implode(",", $request->packages);
        try {
            $group->name = $request->name;
            $group->price_percentage = $request->pricepercentage;
            $group->point_percent = $request->point_percent;
            $group->package_ids = $package_ids;
            $group->isdefault = $default;
            $group->funds_limit = $request->funds_limit;
            $group->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, system encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/groups/" . $id . "/edit");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/groups/" . $id . "/edit");
    }

    public function destroy($id)
    {
        $group = \App\Group::findOrFail($id);
        try {
            $group->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, System encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/groups");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/groups");
    }
}
