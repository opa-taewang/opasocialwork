<?php

namespace App\Http\Controllers\Moderator\OpaSocial;

use App\Http\Controllers\Controller;

class AutoLikeController extends Controller
{
    public function index()
    {
        return view("moderator.autolike.index");
    }

    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $autolikes = \App\AutoLike::with("user", "package.service");
        return datatables()->of($autolikes)->addColumn("details_url", function ($autolike) {
            return url("moderator/autolike/" . $autolike->id . "/details");
        })->editColumn("package.service.name", function ($autolike) {
            return "<a href=\"/moderator/services/" . $autolike->package->service->id . "/edit\">" . $autolike->package->service->name . "</a>";
        })->editColumn("package.name", function ($autolike) {
            return "<a href=\"/moderator/packages/" . $autolike->package_id . "/edit\">" . $autolike->package->name . "</a>";
        })->editColumn("user.name", function ($autolike) {
            return "<a href=\"/moderator/users/" . $autolike->user_id . "/edit\">" . $autolike->user->name . "</a>";
        })->editColumn("username", function ($autolike) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . "https://www.instagram.com/" . $autolike->username . "\" target=\"_blank\">" . str_limit($autolike->username, 30) . "</a>";
        })->addColumn("total_price", function ($autolike) {
            if (strtoupper($autolike->status) == "CANCELLED") {
                return getOption("currency_symbol") . number_formats(0, 2, getOption("currency_separator"), "");
            } elseif (strtoupper($autolike->status) == "PARTIAL") {
                return getOption("currency_symbol") . number_formats($autolike->run_price * $autolike->runs_triggered, 2, getOption("currency_separator"), "");
            } else {
                return getOption("currency_symbol") . number_formats($autolike->run_price * $autolike->posts, 2, getOption("currency_separator"), "");
            }
        })->editColumn("status", function ($autolike) {
            return "<span class='status-" . strtolower($autolike->status) . "'>" . $autolike->status . "</span>";
        })->editColumn("posts", function ($autolike) {
            return (string) $autolike->runs_triggered . " / " . (string) $autolike->posts;
        })->addColumn("total_quantity", function ($autolike) {
            if ($autolike->dripfeed == 1) {
                return $autolike->posts * $autolike->max * $autolike->dripfeed_runs;
            } else {
                return $autolike->posts * $autolike->max;
            }
        })->addColumn("type", function ($autolike) {
            if ($autolike->dripfeed == 1) {
                return "Runs:" . $autolike->dripfeed_runs . "<br>Interval:" . $autolike->dripfeed_interval;
            } else {
                return "No";
            }
        })->addColumn("action", function ($autolike) {
            return "<a type=\"button\" href=\"/moderator/autolike/edit/" . $autolike->id . "\"class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-pencil\"></span></a>";
        })->addColumn("quantity", function ($autolike) {
            return (string) $autolike->min . " to " . (string) $autolike->max;
        })->editColumn("created_at", function ($autolike) {
            return "<span class='no-word-break'>" . $autolike->created_at . "</span>";
        })->rawColumns(array("id", "link", "status", "created_at", "package.service.name", "package.name", "user.name", "action", "username", "type"))->toJson();
    }

    public function edit(\App\AutoLike $autolike)
    {
        if ($autolike->status == "CANCELLED") {
            $price = 0;
        } elseif ($autolike->status == "PARTIAL") {
            $price = $autolike->run_price * $autolike->runs_triggered;
        } else {
            $price = $autolike->run_price * $autolike->posts;
        }
        return view("moderator.autolike.edit", compact("autolike", "price"));
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $id = $request->input("id");
        $autolike = \App\AutoLike::findOrFail($id);
        if (in_array(strtoupper($autolike->status), array("PARTIAL", "CANCELLED", "COMPLETED"))) {
            \Illuminate\Support\Facades\Session::flash("alert", "Drip Feed Order is already " . $autolike->status);
            \Illuminate\Support\Facades\Session::flash("alertClass", "danger");
            return back();
        }
        $status = $request->input("status");
        $orderPrice = $autolike->run_price * $autolike->posts;
        if (strtoupper($status) == "CANCELLED") {
            $autolike->user->funds = $autolike->user->funds + $orderPrice;
            $autolike->user->save();
        } elseif (strtoupper($status) == "PARTIAL") {
            $remains = $autolike->posts - $autolike->runs_triggered;
            $remains = ($remains <= 0 ? 0 : $remains);
            $refundAmount = $remains * $autolike->run_price;
            $autolike->user->funds = $autolike->user->funds + $refundAmount;
            $autolike->user->save();
        }
        $autolike->status = $status;
        $autolike->save();
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return back();
    }

    public function details(\App\AutoLike $al)
    {
        $orders = ($al->dripfeed == 1 ? $al->dripfeeds : $al->orders);
        return datatables()->of($orders)->editColumn("link", function ($order) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $order->link . "\" target=\"_blank\">" . $order->link . "</a>";
        })->editColumn("start_counter", function ($order) use ($al) {
            return ($al->dripfeed == 1 ? "Drip Feed" : $order->start_counter);
        })->editColumn("remains", function ($order) use ($al) {
            return ($al->dripfeed == 1 ? "Drip Feed" : $order->remains);
        })->editColumn("api_order_id", function ($order) use ($al) {
            return ($al->dripfeed == 1 ? "Drip Feed" : $order->api_order_id);
        })->rawColumns(array("desc", "link"))->toJson();
    }
}
