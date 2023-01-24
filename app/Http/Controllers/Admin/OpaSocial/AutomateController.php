<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;


class AutomateController extends Controller
{
    private $order_statuses = array();

    public function __construct()
    {
        $this->order_statuses = config("constants.ORDER_STATUSES");
    }


    public function listApi(Request $request)
    {
        $apis = \App\API::all();
        return view("admin.automate.api-list", compact("apis"));
    }

    public function addApi()
    {
        return view("admin.automate.api-add");
    }
    public function addApimxz()
    {
        return view("admin.automate.api-addmxz");
    }
    public function addApiperfect()
    {
        return view("admin.automate.api-addperfect");
    }
    public function storeApi(\Illuminate\Http\Request $request)
    {
        $this->validate($request, array("name" => "required", "order_end_point" => "required|url", "order_method" => "required", "order_key" => "required", "order_key_type" => "required", "order_key_value" => "required", "order_success_response" => "required|json", "status_end_point" => "required|url", "status_key" => "required", "status_key_type" => "required", "status_key_value" => "required", "order_id_key" => "required", "start_counter_key" => "required", "status_key_equal" => "required", "remains_key" => "required", "process_all_order" => "required", "status_success_response" => "required|json"));
        $api = \App\API::create(array("name" => $request->input("name"), "rate" => $request->input("rate"), "order_end_point" => $request->input("order_end_point"), "order_method" => $request->input("order_method"), "order_success_response" => str_replace("\\t", "", $request->input("order_success_response")), "status_end_point" => $request->input("status_end_point"), "status_method" => $request->input("status_method"), "package_end_point" => $request->input("package_end_point"), "package_method" => $request->input("package_method"), "order_id_key" => $request->input("order_id_key"), "start_counter_key" => $request->input("start_counter_key"), "status_key" => $request->input("status_key_equal"), "remains_key" => $request->input("remains_key"), "package_id_key" => $request->input("package_id_key"), "package_name" => $request->input("package_name"), "rate_key" => $request->input("rate_key"), "min_key" => $request->input("min_key"), "max_key" => $request->input("max_key"), "service_key" => $request->input("service_key"), "desc_key" => $request->input("desc_key"), "type_key" => $request->input("type_key"), "process_all_order" => $request->input("process_all_order"), "status_success_response" => str_replace("\\t", "", $request->input("status_success_response"))));
        $order_keys = $request->input("order_key");
        $order_key_values = $request->input("order_key_value");
        $order_key_types = $request->input("order_key_type");
        for ($i = 0; $i < count($order_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($order_keys[$i]), "param_value" => trim($order_key_values[$i]), "param_type" => trim($order_key_types[$i]), "api_type" => "order", "api_id" => $api->id));
        }
        $status_keys = $request->input("status_key");
        $status_key_values = $request->input("status_key_value");
        $status_key_types = $request->input("status_key_type");
        for ($i = 0; $i < count($status_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($status_keys[$i]), "param_value" => trim($status_key_values[$i]), "param_type" => trim($status_key_types[$i]), "api_type" => "status", "api_id" => $api->id));
        }
        $package_keys = $request->input("package_key");
        $package_key_values = $request->input("package_key_value");
        $package_key_types = $request->input("package_key_type");
        for ($i = 0; $i < count($package_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($package_keys[$i]), "param_value" => trim($package_key_values[$i]), "param_type" => trim($package_key_types[$i]), "api_type" => "package", "api_id" => $api->id));
        }
        \Session::flash("alert", __("messages.created"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/automate/api-list");
    }

    public function editApi($id)
    {
        $api = \App\API::findOrFail($id);
        $content = "";
        $apiMapping = \App\ApiMapping::where(array("api_id" => $id))->pluck("api_package_id", "package_id")->toArray();
        $apiRequestParams = \App\ApiRequestParam::where(array("api_id" => $id))->get();
        $packages = \App\Package::where(array("status" => "ACTIVE"))->orderBy("service_id")->get();
        return view("admin.automate.api-edit", compact("api", "content", "apiRequestParams", "packages", "apiMapping"));
    }

    public function updateApi($id, \Illuminate\Http\Request $request)
    {
        $this->validate($request, array("name" => "required", "order_end_point" => "required|url", "order_key" => "required", "order_method" => "required", "order_key_type" => "required", "order_key_value" => "required", "order_success_response" => "required|json", "status_end_point" => "required|url", "status_key" => "required", "status_key_type" => "required", "status_key_value" => "required", "order_id_key" => "required", "start_counter_key" => "required", "status_key_equal" => "required", "remains_key" => "required", "process_all_order" => "required", "status_success_response" => "required|json"));
        \App\API::findOrFail($id)->update(array("name" => $request->input("name"), "rate" => $request->input("rate"), "order_end_point" => $request->input("order_end_point"), "order_method" => $request->input("order_method"), "order_success_response" => str_replace("\\t", "", $request->input("order_success_response")), "status_end_point" => $request->input("status_end_point"), "status_method" => $request->input("status_method"), "package_end_point" => $request->input("package_end_point"), "package_method" => $request->input("package_method"), "order_id_key" => $request->input("order_id_key"), "start_counter_key" => $request->input("start_counter_key"), "status_key" => $request->input("status_key_equal"), "remains_key" => $request->input("remains_key"), "package_id_key" => $request->input("package_id_key"), "package_name" => $request->input("package_name"), "rate_key" => $request->input("rate_key"), "min_key" => $request->input("min_key"), "max_key" => $request->input("max_key"), "service_key" => $request->input("service_key"), "type_key" => $request->input("type_key"), "desc_key" => $request->input("desc_key"), "process_all_order" => $request->input("process_all_order"), "status_success_response" => str_replace("\\t", "", $request->input("status_success_response"))));
        \App\ApiRequestParam::where(array("api_id" => $id))->delete();
        $order_keys = $request->input("order_key");
        $order_key_values = $request->input("order_key_value");
        $order_key_types = $request->input("order_key_type");
        for ($i = 0; $i < count($order_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($order_keys[$i]), "param_value" => trim($order_key_values[$i]), "param_type" => trim($order_key_types[$i]), "api_type" => "order", "api_id" => $id));
        }
        $status_keys = $request->input("status_key");
        $status_key_values = $request->input("status_key_value");
        $status_key_types = $request->input("status_key_type");
        for ($i = 0; $i < count($status_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($status_keys[$i]), "param_value" => trim($status_key_values[$i]), "param_type" => trim($status_key_types[$i]), "api_type" => "status", "api_id" => $id));
        }
        $package_keys = $request->input("package_key");
        $package_key_values = $request->input("package_key_value");
        $package_key_types = $request->input("package_key_type");
        for ($i = 0; $i < count($package_keys); $i++) {
            \App\ApiRequestParam::create(array("param_key" => trim($package_keys[$i]), "param_value" => trim($package_key_values[$i]), "param_type" => trim($package_key_types[$i]), "api_type" => "package", "api_id" => $id));
        }
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/automate/api/" . $id . "/edit");
    }

    public function deleteApi($id)
    {
        $api = \App\API::findOrFail($id);
        try {
            if (!\App\Package::where(array("preferred_api_id" => $api->id))->exists() && !\App\Order::where(array("api_id" => $api->id))->exists() && !\App\ApiResponseLog::where(array("api_id" => $api->id))->exists()) {
                \App\ApiMapping::where(array("api_id" => $api->id))->delete();
                \App\ApiRequestParam::where(array("api_id" => $api->id))->delete();
                $api->delete();
            } else {
                \Session::flash("alert", __("messages.api_have_logs"));
                \Session::flash("alertClass", "danger");
                return redirect("/admin/automate/api-list");
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            \Session::flash("alert", __("messages.api_have_logs"));
            \Session::flash("alertClass", "danger");
            return redirect("/admin/automate/api-list");
        }
        \Session::flash("alert", __("messages.deleted"));
        \Session::flash("alertClass", "success");
        return redirect("/admin/automate/api-list");
    }

    public function storeMapping($id, \Illuminate\Http\Request $request)
    {
        $packages = $request->input("package_id");
        $apiPackages = $request->input("api_package_id");
        $insert = array();
        for ($i = 0; $i < count($packages); $i++) {
            if ($apiPackages[$i] != "" && $apiPackages[$i] != "0") {
                $insert[] = array("package_id" => trim($packages[$i]), "api_package_id" => trim($apiPackages[$i]), "api_id" => $id, "created_at" => \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now());
            }
        }
        if (!empty($insert)) {
            \App\ApiMapping::where(array("api_id" => $id))->delete();
            \DB::table("api_mappings")->insert($insert);
        }
        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect("admin/automate/api/" . $id . "/edit");
    }

    public function sendOrdersIndex()
    {
        return view("admin.automate.send-orders-index");
    }

    public function sendOrdersIndexData()
    {
        $orders = \App\Order::with("user", "package.service")->where(array("status" => "PENDING", "api_order_id" => NULL));
        return datatables()->of($orders)->addColumn('bulk', function ($order) {
            $disabled = '';



            return '<input type=\'checkbox\' ' . $disabled . ' class=\'input-sm row-checkbox\' name=\'order_id[' . $order->id . ']\' value=\'' . $order->id . '\'>';
        })->addColumn("api", "admin.automate.send-orders-api-select")->addColumn("action", "admin.automate.send-orders-action-buttons")->editColumn("link", function ($order) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $order->link . "\" target=\"_blank\">" . str_limit($order->link, 30) . "</a>";
        })->rawColumns(array("action", "api", "link"))->toJson();
    }

    public function sendOrderToApi(\Illuminate\Http\Request $request)
    {
        $api = \App\API::find($request->input("api_id"));
        if (is_null($api)) {
            return response()->json(array("success" => false, "message" => "Selected API is not configured yet!", "css_class" => "alert-warning"));
        }
        $apiMapping = \App\ApiMapping::where(array("api_id" => $api->id, "package_id" => $request->input("package_id")))->first();
        if (is_null($apiMapping)) {
            return response()->json(array("success" => false, "message" => "package_id is not mapped with API Package ID.", "css_class" => "alert-warning"));
        }
        $order = \App\Order::find($request->input("id"));
        $params = array();
        $apiRequestParams = \App\ApiRequestParam::where(array("api_id" => $api->id, "api_type" => "order"))->get();
        if (!$apiRequestParams->isEmpty()) {
            foreach ($apiRequestParams as $row) {
                if ($row->param_type == "custom") {
                    $params[$row->param_key] = $row->param_value;
                } elseif ($row->param_value == "package_id") {
                    $params[$row->param_key] = $apiMapping->api_package_id;
                } elseif ($row->param_value == "custom_comments") {
                    $package = \App\Package::find($order->package_id);
                    if ($package->custom_comments) {
                        $params[$row->param_key] = $order->{$row->param_value};
                    }
                } else {
                    $params[$row->param_key] = $order->{$row->param_value};
                }
            }
            $client = new \GuzzleHttp\Client();
            try {
                $param_key = "form_params";
                if ($api->order_method === "GET") {
                    $param_key = "query";
                }
                $res = $client->request($api->order_method, $api->order_end_point, array($param_key => $params, "headers" => array("Accept" => "application/json")));
                \Log::error($params);

                if ($res->getStatusCode() === 200) {
                    $resp = $res->getBody()->getContents();
                    $success_response = array_cast_recursive(json_decode($api->order_success_response));
                    if (!array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response)) {
                        \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $resp));
                        $r = json_decode($resp);
                        \App\Order::find($request->input("id"))->update(array("api_id" => $api->id, "api_order_id" => $r->{$api->order_id_key}, "status" => "INPROGRESS"));
                        return response()->json(array("success" => true, "message" => "Success! Order placed successfully!", "css_class" => "alert-success"));
                    } else {
                        \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $resp));
                        return response()->json(array("success" => false, "message" => $resp, "css_class" => "alert-danger"));
                    }
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $e->getMessage()));
                return response()->json(array("success" => false, "message" => $e->getMessage(), "css_class" => "alert-danger"));
            }
        }
        return response()->json(array("success" => false, "message" => "Error! Something Went Wrong!", "css_class" => "alert-danger"));
    }
    public function bulkUpdatePending(\Illuminate\Http\Request $request)
    {
        $orderIds = $request->input('order_id');


        foreach ($orderIds as $id) {
            $order = \App\Order::find($id);
            $api = \App\API::find($order->api_id);
            if (is_null($api)) {
                \Illuminate\Support\Facades\Session::flash('alert', __('Please select API for ALL Packages'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect('/admin/automate/send-orders');
            }
            $apiMapping = \App\ApiMapping::where(array("api_id" => $api->id, "package_id" => $order->package_id))->first();
            if (is_null($apiMapping)) {
                \Illuminate\Support\Facades\Session::flash('alert', __('package id' . $order->package_id . ' is not mapped with API Package ID.'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect('/admin/automate/send-orders');
            }

            $params = array();
            $apiRequestParams = \App\ApiRequestParam::where(array("api_id" => $api->id, "api_type" => "order"))->get();
            if (!$apiRequestParams->isEmpty()) {
                foreach ($apiRequestParams as $row) {
                    if ($row->param_type == "custom") {
                        $params[$row->param_key] = $row->param_value;
                    } elseif ($row->param_value == "package_id") {
                        $params[$row->param_key] = $apiMapping->api_package_id;
                    } elseif ($row->param_value == "custom_comments") {
                        $package = \App\Package::find($order->package_id);
                        if ($package->custom_comments) {
                            $params[$row->param_key] = $order->{$row->param_value};
                        }
                    } else {
                        $params[$row->param_key] = $order->{$row->param_value};
                    }
                }
                $client = new \GuzzleHttp\Client();
                try {
                    $param_key = "form_params";
                    if ($api->order_method === "GET") {
                        $param_key = "query";
                    }
                    $res = $client->request($api->order_method, $api->order_end_point, array($param_key => $params, "headers" => array("Accept" => "application/json")));
                    \Log::error($params);

                    if ($res->getStatusCode() === 200) {
                        $resp = $res->getBody()->getContents();
                        $success_response = array_cast_recursive(json_decode($api->order_success_response));
                        if (!array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response)) {
                            \App\ApiResponseLog::create(array("order_id" => $id, "api_id" => $api->id, "response" => $resp));
                            $r = json_decode($resp);
                            \App\Order::find($id)->update(array("api_id" => $api->id, "api_order_id" => $r->{$api->order_id_key}, "status" => "INPROGRESS"));
                        } else {
                            \App\ApiResponseLog::create(array("order_id" => $id, "api_id" => $api->id, "response" => $resp));
                            \Illuminate\Support\Facades\Session::flash('alert', __($resp));
                            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                            return redirect('/admin/automate/send-orders');
                        }
                    }
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    \App\ApiResponseLog::create(array("order_id" => $id, "api_id" => $api->id, "response" => $e->getMessage()));
                    \Illuminate\Support\Facades\Session::flash('alert', __($e->getMessage()));
                    \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                    return redirect('/admin/automate/send-orders');
                }
            }
        }

        \Session::flash("alert", __("messages.updated"));
        \Session::flash("alertClass", "success");
        return redirect('/admin/automate/send-orders');
    }

    public function getResponseLogsIndex()
    {
        return view("admin.automate.response-logs");
    }

    public function getResponseLogsIndexData()
    {
        $logs = \App\ApiResponseLog::with("api");
        return datatables()->of($logs)->editColumn("response", function ($log) {
            return "<code>" . $log->response . "</code>";
        })->rawColumns(array("response"))->toJson();
    }

    public function getOrderStatusIndex()
    {
        return view("admin.automate.get-order-status-index");
    }

    public function getOrderStatusIndexData()
    {
        $orders = \App\Order::with("user", "package.service", "api")->whereNotIn("status", array("COMPLETED", "CANCELLED", "PARTIAL", "REFUNDED", "REFILLING"))->where("api_order_id", "!=", NULL);
        return datatables()->of($orders)->addColumn("action", "admin.automate.get-order-status-action-buttons")->editColumn("link", function ($order) {
            return "<a rel=\"noopener noreferrer\" href=\"" . getOption("anonymizer") . $order->link . "\" target=\"_blank\">" . str_limit($order->link, 30) . "</a>";
        })->rawColumns(array("action", "link"))->toJson();
    }

    public function getOrderStatusFromAPI(\Illuminate\Http\Request $request)
    {
        $order = \App\Order::findOrFail($request->input("id"));
        $api = \App\API::find($order->api_id);
        $params = array();
        $apiRequestParams = \App\ApiRequestParam::where(array("api_id" => $api->id, "api_type" => "status"))->get();
        if (!$apiRequestParams->isEmpty()) {
            foreach ($apiRequestParams as $row) {
                if ($row->param_type === "custom") {
                    $params[$row->param_key] = $row->param_value;
                } else {
                    $params[$row->param_key] = $order->{$row->param_value};
                }
            }
            $params[$api->order_id_key] = $order->api_order_id;
            $client = new \GuzzleHttp\Client();
            try {
                $param_key = "form_params";
                if ($api->status_method === "GET") {
                    $param_key = "query";
                }
                $res = $client->request($api->status_method, $api->status_end_point, array($param_key => $params, "headers" => array("Accept" => "application/json")));
                if ($res->getStatusCode() === 200) {
                    $resp = $res->getBody()->getContents();
                    $success_response = array_cast_recursive(json_decode($api->status_success_response));
                    if (!array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response)) {
                        \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $resp));
                        $r = array_cast_recursive(json_decode($resp));
                        if (array_key_exists($api->status_key, $r)) {
                            if (strtoupper(trim($r[$api->status_key])) == "COMPLETED" || strtoupper(trim($r[$api->status_key])) == "COMPLETE") {
                                \App\Order::find($request->input("id"))->update(array("status" => "COMPLETED", "start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key]));
                                return response()->json(array("success" => true, "message" => "Order completed.", "css_class" => "alert-success"));
                            } elseif (strtoupper(trim($r[$api->status_key])) == "PENDING" || strtoupper(trim($r[$api->status_key])) == "INPROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN_PROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN-PROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN PROGRESS" || strtoupper(trim($r[$api->status_key])) == "PROGRESS") {
                                \App\Order::find($request->input("id"))->update(array("start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key]));
                            } elseif (strtoupper(trim($r[$api->status_key])) == "CANCEL" || strtoupper(trim($r[$api->status_key])) == "CANCELLED" || strtoupper(trim($r[$api->status_key])) == "CANCELED") {
                                if ($api->process_all_order) {
                                    \App\Order::find($request->input("id"))->update(array("start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key], "status" => "CANCELLED"));
                                    $user = \App\User::find($order->user_id);
                                    $user->funds = $user->funds + $order->price;
                                    $user->save();
                                    return response()->json(array("success" => true, "message" => "Order Cancelled.", "css_class" => "alert-info"));
                                }
                                return response()->json(array("success" => false, "message" => "Order Cancelled, Please Mark Order Cancel Manually", "css_class" => "alert-info"));
                            } elseif (strtoupper(trim($r[$api->status_key])) == "REFUND" || strtoupper(trim($r[$api->status_key])) == "REFUNDED") {
                                if ($api->process_all_order) {
                                    \App\Order::find($request->input("id"))->update(array("start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key], "status" => "REFUNDED"));
                                    $user = \App\User::find($order->user_id);
                                    $user->funds = $user->funds + $order->price;
                                    $user->save();
                                    return response()->json(array("success" => true, "message" => "Order Refunded.", "css_class" => "alert-info"));
                                }
                                return response()->json(array("success" => false, "message" => "Order Refunded, Please Mark Order Refund Manually", "css_class" => "alert-info"));
                            } elseif (in_array(strtoupper(trim($r[$api->status_key])), array("PARTIAL", "PARTIALLY", "PARTIALLY COMPLETED", "PARTIAL COMPLETE"))) {
                                if (isset($r[$api->remains_key]) && $r[$api->remains_key] > 0) {
                                    $remains = $r[$api->remains_key];
                                    $quantity = $order->quantity;
                                    $orderPrice = $order->price;
                                    $user = \App\User::find($order->user_id);
                                    $price_per_item = \App\Package::find($order->package_id)->price_per_item;
                                    $userPackagePrice = \App\UserPackagePrice::where(array("user_id" => $order->user_id, "package_id" => $order->package_id))->first();
                                    if (!is_null($userPackagePrice)) {
                                        $price_per_item = $userPackagePrice->price_per_item;
                                    }
                                    if ($remains < $quantity) {
                                        $refundAmount = (float) $price_per_item * $remains;
                                        $refundAmount = number_formats($refundAmount, 2, ".", "");
                                        if ($refundAmount > 0) {
                                            $orderPrice = $orderPrice - $refundAmount;
                                            \App\Order::find($order->id)->update(array("start_counter" => $r[$api->start_counter_key], "status" => "PARTIAL", "remains" => $r[$api->remains_key], "price" => $orderPrice));
                                            $user->funds = $user->funds + $refundAmount;
                                            $user->save();
                                        } else {
                                            \App\Order::find($order->id)->update(array("status" => "PARTIAL", "remains" => $r[$api->remains_key]));
                                        }
                                    } else {
                                        \App\Order::find($order->id)->update(array("start_counter" => $r[$api->start_counter_key], "status" => "CANCELLED", "remains" => $r[$api->remains_key], "price" => $orderPrice));
                                        $user->funds = $user->funds + $orderPrice;
                                        $user->save();
                                    }
                                }
                                return response()->json(array("success" => true, "message" => "Order Partial Complete.", "css_class" => "alert-success"));
                            } elseif (!in_array(strtoupper(trim($r[$api->status_key])), array("REFUNDED", "REFUND")) && in_array(strtoupper(trim($r[$api->status_key])), $this->order_statuses)) {
                                \App\Order::find($request->input("id"))->update(array("start_counter" => $r[$api->start_counter_key], "status" => strtoupper(trim($r[$api->status_key])), "remains" => $r[$api->remains_key]));
                            }
                        }
                        return response()->json(array("success" => false, "message" => "Order Status: " . $r[$api->status_key], "css_class" => "alert-info"));
                    } else {
                        \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $resp));
                        return response()->json(array("success" => false, "message" => "Failed! Please see response logs.", "css_class" => "alert-danger"));
                    }
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                \App\ApiResponseLog::create(array("order_id" => $request->input("id"), "api_id" => $api->id, "response" => $e->getMessage()));
                return response()->json(array("success" => false, "message" => "Failed! Please see response logs.", "css_class" => "alert-danger"));
            }
        }
        return response()->json(array("success" => false, "message" => "Failed! Please see response logs.", "css_class" => "alert-danger"));
    }

    public function changeReseller(\Illuminate\Http\Request $request)
    {
        $order = \App\Order::findOrFail($request->input("id"));
        $order->api_id = NULL;
        $order->api_order_id = NULL;
        $order->status = "PENDING";
        $order->save();
        return response()->json(array("success" => true, "message" => "Order is ready to send to another reseller, in " . __("menus.automate") . " -> " . __("menus.send_orders"), "css_class" => "alert-success"));
    }
}
