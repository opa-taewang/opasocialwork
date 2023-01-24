<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $pages = \App\Page::all();
        return view("admin.pages.index", compact("pages"));
    }
    public function edit($slug)
    {
        $page = \App\Page::where(["slug" => $slug])->firstOrFail();
        return view("admin.pages.edit", compact("page"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["content" => "required", "meta_tags" => "required"]);
        $page = \App\Page::findOrFail($id);
        $page->content = $request->input("content");
        $page->meta_tags = $request->input("meta_tags");
        $page->save();
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/page-edit/" . $page->slug);
    }
}
