<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NewsletterExport;
use Excel;
use App\Http\Controllers\Controller;

class NewsletterController extends Controller
{
    public function index()
    {
        return view("admin.newsletters.index");
    }

    public function indexData()
    {
        $emails = \App\Newsletter::all();
        return datatables()->of($emails)->editColumn("created_at", function ($email) {
            return $email->created_at->diffForHumans();
        })->addColumn("action", function ($email) {
            return view("admin.newsletters.index-buttons", compact("email"));
        })->toJson();
    }
    public function destroy($id)
    {
        $email = \App\Newsletter::findOrFail($id);
        try {
            $email->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, Syste encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/newsletter");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/newsletter");
    }
    public function export()
    {
        return Excel::download(new NewsletterExport, 'emails.xlsx');
    }
}
