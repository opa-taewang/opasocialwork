<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
class BlogController extends Controller
{
    public function index()
    {
        return view("admin.blog.index");
    }
    public function indexData()
    {
        $posts = \App\Blog::all();
        return datatables()->of($posts)->editColumn("description", function ($post) {
            return str_limit($post->description, 100);
        })->editColumn("short_description", function ($post) {
            return str_limit($post->short_description, 100);
        })->editColumn("created_at", function ($post) {
            return $post->created_at->diffForHumans();
        })->addColumn("action", function ($post) {
            return view("admin.blog.index-buttons", compact("post"));
        })->toJson();
    }
    public function create()
    {
        return view("admin.blog.create");
    }
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ["title" => "required", "image" => "required", "description" => "required", "short_description" => "required", "status" => "required"]);
        $slug = str_slug($request->title, "-");
        $image = "";
        if ($request->hasFile("image")) {
            $imgfile = $request->file("image");
            $imgpath = "blog/images";
            \File::makeDirectory($imgpath, $mode = 511, true, true);
            $name = time() . "_" . $imgfile->getClientOriginalName();
            $filename1 = $name;
            $success = $imgfile->move(dirname(base_path()) . "/blog/images", $filename1);
            $image = $name;
        }
        \App\Blog::create(["title" => $request->input("title"), "description" => $request->input("description"), "short_description" => $request->input("short_description"), "image" => $image, "status" => $request->status, "slug" => $slug]);
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.created"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/blog/create");
    }
    public function show($id)
    {
        $post = \App\Blog::findOrFail($id);
        return view("admin.blog.show", compact("post"));
    }
    public function edit($id)
    {
        $post = \App\Blog::findOrFail($id);
        return view("admin.blog.edit", compact("post"));
    }
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->validate($request, ["title" => "required", "description" => "required", "status" => "required"]);
        $slug = str_slug($request->title, "-");
        $post = \App\Blog::findOrFail($id);
        $image = "";
        if ($request->hasFile("image")) {
            $imgfile = $request->file("image");
            $imgpath = "blog/images";
            \File::makeDirectory($imgpath, $mode = 511, true, true);
            $name = time() . "_" . $imgfile->getClientOriginalName();
            $filename1 = $name;
            $success = $imgfile->move(dirname(base_path()) . "/blog/images", $filename1);
            $image = $name;
        } else {
            $image = $post->image;
        }
        \App\Blog::where("id", $id)->update(["title" => $request->input("title"), "description" => $request->input("description"), "short_description" => $request->input("short_description"), "image" => $image, "status" => $request->status, "slug" => $slug]);
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success no-auto-close");
        return redirect("/admin/blog/" . $id . "/edit");
    }
    public function destroy($id)
    {
        $notification = \App\Blog::findOrFail($id);
        try {
            $notification->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Session::flash("alert", __("Opps, Syste encountered an problem"));
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return redirect("/admin/blog");
        }
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.deleted"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/blog");
    }
}
