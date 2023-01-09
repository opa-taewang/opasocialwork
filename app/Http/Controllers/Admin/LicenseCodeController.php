<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LicenseCodeController extends Controller
{
    public function index()
    {
        return view("admin.codes.index");
    }
    public function indexData()
    {
        $codes = \App\Licensecode::orderBy("created_at", "DESC")->get();
        return datatables()->of($codes)->editColumn("available", function ($code) {
            return $code->available;
        })->editColumn("created_at", function ($code) {
            return $code->created_at->diffForHumans();
        })->addColumn("action", function ($code) {
            return view("admin.codes.index-buttons", compact("code"));
        })->toJson();
    }
    public function create()
    {
        return view("admin.codes.create");
    }
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ["code" => "required|max:255|unique:licensecodes"]);
        \App\Licensecode::create(["code" => $request->input("code"), "available" => 1]);
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/codes/create");
    }
    public function edit($id)
    {
        $code = \App\Licensecode::findOrFail($id);
        return view("admin.codes.edit", compact("code"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["code" => "required|max:255"]);
        try {
            $code = \App\Licensecode::findOrFail($id);
            $code->code = $request->input("code");
            $code->updated_at = date("Y-m-d H:s:i");
            $code->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("License Code Already Exist"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/codes/" . $id . "/edit");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/codes/" . $id . "/edit");
    }
    public function destroy($id)
    {
        $code = \App\Licensecode::findOrFail($id);
        try {
            $code->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("License code in use"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/codes");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/users");
    }
}
