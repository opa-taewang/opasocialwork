<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;

class RefillRequestController extends Controller
{
    public function index()
    {
        return view("admin.refills.index");
    }

    public function indexData()
    {
        $refills = \App\RefillRequest::join("orders", "refill_requests.order_id", "=", "orders.id")->join("apis", "orders.api_id", "=", "apis.id")->select("refill_requests.id as rid", "refill_requests.status", "apis.name", "orders.api_order_id", "orders.id", "refill_requests.created_at")->getQuery()->get();
        return datatables()->of($refills)->addColumn("details_url", function ($refill) {
            return url("admin/refills/" . $refill->id . "/details");
        })->addColumn("action", function ($refill) {
            return view("admin.refills.index-buttons", compact("refill"));
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
        return view("admin.refills.index", compact("status"));
    }

    public function indexFilterData($status)
    {
        $status = ($status == "inprogress" ? "in progress" : $status);
        $refills = \App\RefillRequest::join("orders", "refill_requests.order_id", "=", "orders.id")->join("apis", "orders.api_id", "=", "apis.id")->select("refill_requests.id as rid", "refill_requests.status", "apis.name", "orders.api_order_id", "orders.id", "refill_requests.created_at")->where(array("refill_requests.status" => strtoupper($status)))->getQuery()->get();
        return datatables()->of($refills)->addColumn("details_url", function ($refill) {
            return url("admin/refills/" . $refill->id . "/details");
        })->addColumn("action", function ($refill) {
            return view("admin.refills.index-buttons", compact("refill"));
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
        $data[] = array("name" => "User", "desc" => "<a href=\"/admin/users/" . $order->user->id . "/edit\">" . $order->user->name . "</a>");
        $data[] = array("name" => "Service", "desc" => "<a href=\"/admin/services/" . $order->package->service->id . "/edit\">" . $order->package->service->name . "</a>");
        $data[] = array("name" => "Package", "desc" => "<a href=\"/admin/packages/" . $order->package->id . "/edit\">" . $order->package->name . "</a>");
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
                        $refill1 =   $refill->order->api_id;
                        $api = \App\API::find($refill1);
                        $api2 = $api->name;
                        $url = $api2 . '/orders/' . $refill->order->api_order_id . '/refill';
                        $refill->order->save();
                        $refill->save();
                        return redirect($url);
                        break;
                    case "cancel":
                        $refill->status = "CANCELLED";
                        $refill->order->status = "COMPLETED";
                        $refill->order->rc = "1";
                        break;
                    case "complete":
                        $refill->status = "COMPLETED";
                        $refill->order->status = "COMPLETED";
                        $rc = $refill->order->rc;
                        break;
                    default:
                        break;
                }
            case "in progress":
                switch ($status) {
                    case "cancel":
                        $refill->status = "CANCELLED";
                        $refill->order->status = "COMPLETED";
                        $refill->order->rc = "1";
                        break;
                    case "complete":
                        $refill->status = "COMPLETED";
                        $refill->order->status = "COMPLETED";
                        $rc = $refill->order->rc;
                        $refill->order->rc = $rc + "1";
                        break;
                    default:
                        break;
                }
        }
        $refill->order->save();
        $refill->save();
        return back();
    }
    public function completeStatus()
    {
        $refillsprogess = \App\RefillRequest::where(['status' => 'IN PROGRESS'])->get();
        foreach ($refillsprogess as $refillsproges) {
            $refillsproges->status =  'COMPLETED';
            $refillsproges->save();
            $order = \App\Order::where('id', '=', $refillsproges->order_id)->update(array('status' => 'COMPLETED'));
        }
        \Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('/admin/refills/list');
    }
}
