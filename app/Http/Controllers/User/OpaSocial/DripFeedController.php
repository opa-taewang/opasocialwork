<?php


namespace App\Http\Controllers\User\OpaSocial;

class DripFeedController extends Controller
{
    public function index()
    {
        return view("dripfeed.index");
    }
    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $dripfeeds = \App\DripFeed::with("package.service")->where(["drip_feeds.user_id" => \Auth::user()->id]);
        return datatables()->of($dripfeeds)->addColumn("details_url", function ($dripfeed) {
            return url("dripfeed/" . $dripfeed->id . "/details");
        })->editColumn("link", function ($dripfeed) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $dripfeed->link . "\" target=\"_blank\">" . str_limit($dripfeed->link, 30) . "</a>";
        })->addColumn("total_price", function ($dripfeed) {
            if (strtoupper($dripfeed->status) == "CANCELLED") {
                return getOption("currency_symbol") . number_formats(0, 2, getOption("currency_separator"), "");
            }
            if (strtoupper($dripfeed->status) == "PARTIAL") {
                $surcharge = ceil($dripfeed->runs_triggered * $dripfeed->run_quantity / 1000) * getOption("dripfeed_surcharge", true);
                return getOption("currency_symbol") . number_formats($dripfeed->run_price * $dripfeed->runs_triggered + $surcharge, 2, getOption("currency_separator"), "");
            }
            $surcharge = ceil($dripfeed->runs * $dripfeed->run_quantity / 1000) * getOption("dripfeed_surcharge", true);
            return getOption("currency_symbol") . number_formats($dripfeed->run_price * $dripfeed->runs + $surcharge, 2, getOption("currency_separator"), "");
        })->editColumn("status", function ($dripfeed) {
            return "<span class='status-" . strtolower($dripfeed->status) . "'>" . $dripfeed->status . "</span>";
        })->editColumn("runs", function ($dripfeed) {
            return $dripfeed->runs_triggered . " / " . $dripfeed->runs;
        })->addColumn("total_quantity", function ($dripfeed) {
            return $dripfeed->runs * $dripfeed->run_quantity;
        })->editColumn("created_at", function ($dripfeed) {
            return "<span class='no-word-break'>" . $dripfeed->created_at . "</span>";
        })->rawColumns(["id", "link", "status", "created_at"])->toJson();
    }
    public function details(\App\DripFeed $df)
    {
        return datatables()->of($df->orders)->rawColumns(["desc"])->toJson();
    }
}
