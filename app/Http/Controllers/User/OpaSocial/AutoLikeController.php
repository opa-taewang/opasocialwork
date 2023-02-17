<?php


namespace App\Http\Controllers\User\OpaSocial;

use App\Http\Controllers\Controller;

class AutoLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view("autolike.index");
    }
    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $autolikes = \App\AutoLike::with("user", "package.service")->where(["user_id" => \Auth::user()->id]);
        return datatables()->of($autolikes)->addColumn("details_url", function ($autolike) {
            return url("autolike/" . $autolike->id . "/details");
        })->editColumn("package.service.name", function ($autolike) {
            return $autolike->package->service->name;
        })->editColumn("package.name", function ($autolike) {
            return $autolike->package->name;
        })->editColumn("username", function ($autolike) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . "https://www.instagram.com/" . $autolike->username . "\" target=\"_blank\">" . str_limit($autolike->username, 30) . "</a>";
        })->addColumn("total_price", function ($autolike) {
            if (strtoupper($autolike->status) == "CANCELLED") {
                return getOption("currency_symbol") . number_formats(0, 2, getOption("currency_separator"), "");
            }
            if (strtoupper($autolike->status) == "PARTIAL") {
                return getOption("currency_symbol") . number_formats($autolike->run_price * $autolike->runs_triggered, 2, getOption("currency_separator"), "");
            }
            return getOption("currency_symbol") . number_formats($autolike->run_price * $autolike->posts, 2, getOption("currency_separator"), "");
        })->editColumn("status", function ($autolike) {
            return "<span class='status-" . strtolower($autolike->status) . "'>" . $autolike->status . "</span>";
        })->editColumn("posts", function ($autolike) {
            return $autolike->runs_triggered . " / " . $autolike->posts;
        })->addColumn("total_quantity", function ($autolike) {
            if ($autolike->dripfeed == 1) {
                return $autolike->posts * $autolike->max * $autolike->dripfeed_runs;
            }
            return $autolike->posts * $autolike->max;
        })->addColumn("type", function ($autolike) {
            if ($autolike->dripfeed == 1) {
                return "Runs:" . $autolike->dripfeed_runs . "\\nInterval:" . $autolike->dripfeed_interval;
            }
            return "No";
        })->addColumn("quantity", function ($autolike) {
            return $autolike->min . " to " . $autolike->max;
        })->addColumn("action", function ($autolike) {
            return "<a type=\"button\" href=\"/admin/autolike/edit/" . $autolike->id . "\"class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-pencil\"></span></a>";
        })->editColumn("created_at", function ($autolike) {
            return "<span class='no-word-break'>" . $autolike->created_at . "</span>";
        })->rawColumns(["id", "link", "status", "created_at", "action", "username"])->toJson();
    }
    public function details(\App\AutoLike $al)
    {
        return datatables()->of($al->orders)->editColumn("link", function ($order) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $order->link . "\" target=\"_blank\">" . $order->link . "</a>";
        })->rawColumns(["desc", "link"])->toJson();
    }
}
