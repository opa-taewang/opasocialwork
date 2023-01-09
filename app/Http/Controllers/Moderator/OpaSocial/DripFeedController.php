<?php

namespace App\Http\Controllers\Moderator\OpaSocial;

use App\Http\Controllers\Controller;

class DripFeedController extends Controller
{
    public function index()
    {
        return view("moderator.dripfeed.index");
    }

    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $dripfeeds = \App\DripFeed::with("user", "package.service");
        return datatables()->of($dripfeeds)->addColumn("details_url", function ($dripfeed) {
            return url("moderator/dripfeed/" . $dripfeed->id . "/details");
        })->editColumn("package.service.name", function ($dripfeed) {
            return "<a href=\"/moderator/services/" . $dripfeed->package->service->id . "/edit\">" . $dripfeed->package->service->name . "</a>";
        })->editColumn("package.name", function ($dripfeed) {
            return "<a href=\"/moderator/packages/" . $dripfeed->package_id . "/edit\">" . $dripfeed->package->name . "</a>";
        })->editColumn("user.name", function ($dripfeed) {
            return "<a href=\"/moderator/users/" . $dripfeed->user_id . "/edit\">" . $dripfeed->user->name . "</a>";
        })->editColumn("link", function ($dripfeed) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $dripfeed->link . "\" target=\"_blank\">" . str_limit($dripfeed->link, 30) . "</a>";
        })->addColumn("total_price", function ($dripfeed) {
            if (strtoupper($dripfeed->status) == "CANCELLED") {
                return getOption("currency_symbol") . number_formats(0, 2, getOption("currency_separator"), "");
            } elseif (strtoupper($dripfeed->status) == "PARTIAL") {
                $surcharge = ceil(($dripfeed->runs_triggered * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
                return getOption("currency_symbol") . number_formats($dripfeed->run_price * $dripfeed->runs_triggered + $surcharge, 2, getOption("currency_separator"), "");
            } else {
                $surcharge = ceil(($dripfeed->runs * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
                return getOption("currency_symbol") . number_formats($dripfeed->run_price * $dripfeed->runs + $surcharge, 2, getOption("currency_separator"), "");
            }
        })->editColumn("status", function ($dripfeed) {
            return "<span class='status-" . strtolower($dripfeed->status) . "'>" . $dripfeed->status . "</span>";
        })->editColumn("runs", function ($dripfeed) {
            return (string) $dripfeed->runs_triggered . " / " . (string) $dripfeed->runs;
        })->addColumn("total_quantity", function ($dripfeed) {
            return $dripfeed->runs * $dripfeed->run_quantity;
        })->addColumn("action", function ($dripfeed) {
            return "<a type=\"button\" href=\"/moderator/dripfeed/edit/" . $dripfeed->id . "\"class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-pencil\"></span></a>";
        })->editColumn("created_at", function ($dripfeed) {
            return "<span class='no-word-break'>" . $dripfeed->created_at . "</span>";
        })->rawColumns(array("id", "link", "status", "created_at", "package.service.name", "package.name", "user.name", "action"))->toJson();
    }

    public function edit(\App\DripFeed $dripfeed)
    {
        if ($dripfeed->status == "CANCELLED") {
            $price = 0;
        } elseif ($dripfeed->status == "PARTIAL") {
            $surcharge = ceil(($dripfeed->runs_triggered * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
            $price = $dripfeed->run_price * $dripfeed->runs_triggered + $surcharge;
        } else {
            $surcharge = ceil(($dripfeed->runs * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
            $price = $dripfeed->run_price * $dripfeed->runs + $surcharge;
        }
        return view("moderator.dripfeed.edit", compact("dripfeed", "price"));
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $id = $request->input("id");
        $dripfeed = \App\DripFeed::findOrFail($id);
        if (in_array(strtoupper($dripfeed->status), array("PARTIAL", "CANCELLED", "COMPLETED"))) {
            \Illuminate\Support\Facades\Session::flash("alert", "Drip Feed Order is already " . $dripfeed->status);
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return back();
        }
        $status = $request->input("status");
        $surcharge = ceil(($dripfeed->runs * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
        $orderPrice = $dripfeed->run_price * $dripfeed->runs + $surcharge;
        if (strtoupper($status) == "CANCELLED") {
            $dripfeed->user->funds = $dripfeed->user->funds + $orderPrice;
            $dripfeed->user->save();
        } elseif (strtoupper($status) == "PARTIAL") {
            $surcharge1 = ceil(($dripfeed->runs_triggered * $dripfeed->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
            $remains = $dripfeed->runs - $dripfeed->runs_triggered;
            $remains = ($remains <= 0 ? 0 : $remains);
            $refundAmount = $remains * $dripfeed->run_price + $surcharge - $surcharge1;
            $dripfeed->user->funds = $dripfeed->user->funds + $refundAmount;
            $dripfeed->user->save();
        }
        $dripfeed->status = $status;
        $dripfeed->save();
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return back();
    }

    public function details(\App\DripFeed $df)
    {
        return datatables()->of($df->orders)->rawColumns(array("desc"))->toJson();
    }
}
