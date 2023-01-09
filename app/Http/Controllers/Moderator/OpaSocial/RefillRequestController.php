<?php

namespace App\Http\Controllers\Moderator\OpaSocial;

use App\Http\Controllers\Controller;

class RefillRequestController extends Controller
{
    public function index()
    {
        return view("moderator.refills.index");
    }

    public function indexData()
    {
        app("App\\Http\\Controllers\\OrderController")->check(3);
        $refills = \App\RefillRequest::join("orders", "refill_requests.order_id", "=", "orders.id")->join("apis", "orders.api_id", "=", "apis.id")->select("refill_requests.id as rid", "refill_requests.status", "apis.name", "orders.api_order_id", "orders.id", "refill_requests.created_at")->getQuery()->get();
        return datatables()->of($refills)->addColumn("details_url", function ($refill) {
            return url("moderator/refills/" . $refill->id . "/details");
        })->addColumn("action", function ($refill) {
            return view("moderator.refills.index-buttons", compact("refill"));
        })->editColumn("status", function ($refill) {
            return "<span class='status-" . str_replace(" ", "", strtolower($refill->status)) . "'>" . $refill->status . "</span>";
        })->addColumn("api", function ($refill) {
            return $refill->name;
        })->addColumn("api_order_id", function ($refill) {
            return $refill->api_order_id;
        })->rawColumns(array("action", "status"))->toJson();
    }

    public function indexFilter($status)
    {
        return view("moderator.refills.index", compact("status"));
    }

    public function indexFilterData($status)
    {
        $status = ($status == "inprogress" ? "in progress" : $status);
        $refills = \App\RefillRequest::join("orders", "refill_requests.order_id", "=", "orders.id")->join("apis", "orders.api_id", "=", "apis.id")->select("refill_requests.id as rid", "refill_requests.status", "apis.name", "orders.api_order_id", "orders.id", "refill_requests.created_at")->where(array("refill_requests.status" => strtoupper($status)))->getQuery()->get();
        return datatables()->of($refills)->addColumn("details_url", function ($refill) {
            return url("moderator/refills/" . $refill->id . "/details");
        })->addColumn("action", function ($refill) {
            return view("moderator.refills.index-buttons", compact("refill"));
        })->editColumn("status", function ($refill) {
            return "<span class='status-" . str_replace(" ", "", strtolower($refill->status)) . "'>" . $refill->status . "</span>";
        })->addColumn("api", function ($refill) {
            return $refill->name;
        })->addColumn("api_order_id", function ($refill) {
            return $refill->api_order_id;
        })->rawColumns(array("action", "status"))->toJson();
    }

    public function details(\App\Order $order)
    {
        $data[] = array("name" => "User", "desc" => "<a href=\"/moderator/users/" . $order->user->id . "/edit\">" . $order->user->name . "</a>");
        $data[] = array("name" => "Service", "desc" => "<a href=\"/moderator/services/" . $order->package->service->id . "/edit\">" . $order->package->service->name . "</a>");
        $data[] = array("name" => "Package", "desc" => "<a href=\"/moderator/packages/" . $order->package->id . "/edit\">" . $order->package->name . "</a>");
        $data[] = array("name" => "Link", "desc" => "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $order->link . "\" target=\"_blank\">" . $order->link . "</a>");
        $data[] = array("name" => "Amount", "desc" => $order->price);
        $data[] = array("name" => "Quantity", "desc" => $order->quantity);
        $data[] = array("name" => "Start Counter", "desc" => $order->start_counter);
        $data[] = array("name" => "Order Date", "desc" => $order->created_at);
        return datatables()->of($data)->rawColumns(array("desc"))->toJson();
    }

    public function changeStatus(\App\RefillRequest $refill, $status)
    {
        $str = strtolower($refill->status);
        switch ($str) {
            case "pending":
                switch ($status) {
                    case "start":
                        $refill->status = "IN PROGRESS";
                        break;
                    case "cancel":
                        $refill->status = "CANCELLED";
                        $refill->order->status = "COMPLETED";
                        break;
                    case "complete":
                        $refill->status = "COMPLETED";
                        $refill->order->status = "COMPLETED";
                        break;
                    default:
                        break;
                }
            case "in progress":
                switch ($status) {
                    case "cancel":
                        $refill->status = "CANCELLED";
                        $refill->order->status = "COMPLETED";
                        break;
                    case "complete":
                        $refill->status = "COMPLETED";
                        $refill->order->status = "COMPLETED";
                        break;
                    default:
                        break;
                }
        }
        $refill->order->save();
        $refill->save();
        return back();
    }
}
