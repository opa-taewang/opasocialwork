<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
class BroadcastController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        return view("admin.broadcast.index");
    }
    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $broadcast = \App\Broadcast::all();
        return datatables()->of($broadcast)->addColumn("action", "admin.broadcast.index-buttons")->editColumn("MsgStatus", function ($broadcast) {
            $stat = "ACTIVE";
            if ($broadcast->MsgStatus == 0) {
                $stat = "INACTIVE";
            }
            return $stat;
        })->editColumn("Icon", function ($broadcast) {
            return $broadcast->type();
        })->toJson();
    }
    public function create()
    {
        return view("admin.broadcast.create");
    }
    public function addfunc(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ["mtitle" => "required", "mtext" => "required", "mtype" => "required", "mstatus" => "required", "mstime" => "required", "metime" => "required"]);
        \App\Broadcast::create(["MsgTitle" => $request->input("mtitle"), "MsgText" => $request->input("mtext"), "MsgStatus" => $request->input("mstatus"), "Icon" => $request->input("mtype"), "StartTime" => $request->input("mstime"), "ExpireTime" => $request->input("metime")]);
        \Illuminate\Support\Facades\Session::flash("alert", "Broadcast Created");
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("admin/broadcast");
    }
    public function edit($id)
    {
        $broadcast = \App\Broadcast::findOrFail($id);
        return view("admin.broadcast.edit", compact("broadcast"));
    }
    public function destroy($id)
    {
        $broadcast = \App\Broadcast::findOrFail($id);
        try {
            $broadcast->delete();
        } catch (QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", "Delete Failed");
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/broadcast");
        }
        \Illuminate\Support\Facades\Session::flash("alert", "Broadcast Deleted");
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/broadcast");
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["mtitle" => "required", "mtext" => "required", "mtype" => "required", "mstatus" => "required", "mstime" => "required", "metime" => "required"]);
        \App\Broadcast::where(["id" => $id])->update(["MsgTitle" => $request->input("mtitle"), "MsgText" => $request->input("mtext"), "MsgStatus" => $request->input("mstatus"), "Icon" => $request->input("mtype"), "StartTime" => $request->input("mstime"), "ExpireTime" => $request->input("metime")]);
        \Illuminate\Support\Facades\Session::flash("alert", "Broadcast Updated");
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/broadcast/" . $id);
    }
}
