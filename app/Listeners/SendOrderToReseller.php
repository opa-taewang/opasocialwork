<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Group;
use GuzzleHttp\Client;
use App\Events\OrderPlaced;
use App\Models\OpaSocial\API;
use App\Models\OpaSocial\Order;
use App\Models\OpaSocial\ApiMapping;
use App\Models\OpaSocial\ApiResponseLog;
use App\Models\OpaSocial\ApiRequestParam;
use GuzzleHttp\Exception\ClientException;


class SendOrderToReseller
{
    public function __construct()
    {
    }

    public function handle(OrderPlaced $event)
    {
        $order = $event->order;
        $api = API::find($order->api_id);
        $apiMapping = ApiMapping::where(array("api_id" => $api->id, "package_id" => $order->package_id))->first();
        if (is_null($apiMapping)) {
            return NULL;
        }
        $params = array();
        $apiRequestParams = ApiRequestParam::where(array("api_id" => $api->id, "api_type" => "order"))->get();
        if (!$apiRequestParams->isEmpty()) {
            foreach ($apiRequestParams as $row) {
                if ($row->param_type == "custom") {
                    $params[$row->param_key] = $row->param_value;
                } elseif ($row->param_value == "package_id") {
                    $params[$row->param_key] = $apiMapping->api_package_id;
                } else {
                    $params[$row->param_key] = $order->{$row->param_value};
                }
            }
            $client = new Client();
            try {
                $param_key = "form_params";
                if ($api->order_method === "GET") {
                    $param_key = "query";
                }
                $res = $client->request($api->order_method, $api->order_end_point, array($param_key => $params, "headers" => array("Accept" => "application/json")));
                if ($res->getStatusCode() === 200) {
                    $resp = $res->getBody()->getContents();
                    $success_response = array_cast_recursive(json_decode($api->order_success_response));
                    if (!array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response)) {
                        $r = json_decode($resp);
                        Order::find($order->id)->update(array("api_id" => $api->id, "api_order_id" => $r->{$api->order_id_key}, "status" => "INPROGRESS"));
                        ApiResponseLog::create(array("order_id" => $order->id, "api_id" => $api->id, "response" => $resp));
                    } else {
                        Order::find($order->id)->update(array("api_id" => $api->id, "status" => "CANCELLED"));
                        ApiResponseLog::create(array("order_id" => $order->id, "api_id" => $api->id, "response" => $resp));
                        $user = User::find($order->user_id);
                        $grouppercent = Group::where('id', $user->group_id)->value('point_percent');
                        $authpoint = ($order->price) * $grouppercent;
                        $text = 'Order Not Accepted by Server' . "\n";
                        $text .= 'Order ID: ' . $order->id . "\n";

                        $user->funds = $user->funds + $order->price;
                        $user->points = $user->points - $authpoint;
                        $user->save();
                        fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
                    }
                }
            } catch (ClientException $e) {
                ApiResponseLog::create(array("order_id" => $order->id, "api_id" => $api->id, "response" => $e->getMessage()));
            }
        }
    }
}

// {"error":"Not enough funds on balance"}
