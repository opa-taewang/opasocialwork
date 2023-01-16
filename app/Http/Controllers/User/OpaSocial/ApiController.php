<?php

namespace App\Http\Controllers\User\OpaSocial;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $validator = \Validator::make($request->all(), array("action" => "required"));
        if ($validator->fails()) {
            $response["errors"] = $validator->errors()->all();
            return response()->json($response);
        }
        $params = array();
        if (strtolower($request->input("action")) == "add") {
            $validator = \Validator::make($request->all(), array("package" => "required|numeric", "quantity" => "required|numeric", "link" => "required"));
            if ($validator->fails()) {
                $response["errors"] = $validator->errors()->all();
                return response()->json($response);
            }
            $params["package"] = $request->input("package");
            $params["quantity"] = $request->input("quantity");
            $params["link"] = $request->input("link");
            $params["custom_data"] = $request->input("custom_data") ?? "";
            $response = $this->add($params);
            return response()->json($response);
        } elseif (strtolower($request->input("action")) == "status") {
            $validator = \Validator::make($request->all(), array("order" => "required"));
            if ($validator->fails()) {
                $response["errors"] = $validator->errors()->all();
                return response()->json($response);
            }
            $params["order"] = $request->input("order");
            $response = $this->status($params);
            return response()->json($response);
        } elseif (strtolower($request->input("action")) == "balance") {
            $response["balance"] = \Illuminate\Support\Facades\Auth::user()->funds;
            $response["currency"] = getOption("currency_code", true);
            return response()->json($response);
        } elseif (strtolower($request->input("action")) == "packages") {
            $response = array();
            $packages = \App\Package::where(array("packages.status" => "ACTIVE"))->orderBy("service_id")->get();
            if (!$packages->isEmpty()) {
                foreach ($packages as $package) {
                    $type = ($package->custom_comments == 1 ? "custom_data" : "default");
                    $response[] = array("id" => $package->id, "service_id" => $package->service->id, "name" => $package->name, "rate" => number_formats($package->price_per_item * 1000, 2, ".", ""), "min" => $package->minimum_quantity, "max" => $package->maximum_quantity, "service" => $package->service->name, "type" => $type, "desc" => $package->description);
                }
            }
            return response()->json($response);
        } else {
            return response()->json(array("errors" => array("Incorrect request")));
        }
    }

    public function add($params)
    {
        $response = array("errors" => "");
        $package = \App\Package::findOrfail($params["package"]);
        $quantity = $params["quantity"];
        if ($quantity < $package->minimum_quantity) {
            $response["errors"] = array("Please specify at least minimum quantity.");
            return $response;
        }
        if ($package->maximum_quantity < $quantity) {
            $response["errors"] = array("Please specify less than or equal to maximum quantity");
            return $response;
        }
        if ($package->custom_comments) {
            $comments = $params["custom_data"];
            if ($comments != "") {
                $comments_arr = preg_split("/\\r\\n|\\r|\\n/", $comments);
                $total_comments = count($comments_arr);
                if ($quantity < $total_comments) {
                    $response["errors"] = array("You have added more comments than required quantity");
                    return $response;
                }
                if ($total_comments < $quantity) {
                    $response["errors"] = array("You have added less comments than required quantity");
                    return $response;
                }
            }
        }
        $userPackagePrices = \App\UserPackagePrice::where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id))->pluck("price_per_item", "package_id")->toArray();
        $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
        $price = (float) $package_price * $quantity;
        $price = number_formats($price, 2, ".", "");
        if (\Illuminate\Support\Facades\Auth::user()->funds < $price) {
            $response["errors"] = array("You do not have enough funds to Place order.");
            return $response;
        }
        $custom_data = "";
        if ($package->custom_comments) {
            $custom_data = preg_replace("/\r\n|\r|\n/", PHP_EOL, $params["custom_data"]);
        }
        $order = \App\Order::create(array("price" => $price, "quantity" => $quantity, "package_id" => $package->id, "user_id" => \Illuminate\Support\Facades\Auth::user()->id, "api_id" => $package->preferred_api_id, "link" => $params["link"], "source" => "API", "custom_comments" => $custom_data));
        unset($response["errors"]);
        $response["order"] = $order->id;
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $price;
        $user->save();
        if (!is_null($package->preferred_api_id)) {
            event(new \App\Events\OrderPlaced($order));
        }
        return $response;
    }

    public function status($params)
    {
        $response = array("errors" => "");
        $order = \App\Order::where(array("id" => $params["order"], "user_id" => \Illuminate\Support\Facades\Auth::user()->id))->first();
        if (is_null($order)) {
            $response["errors"] = array("Order Not found");
            return $response;
        } else {
            unset($response["errors"]);
            $response["status"] = $order->status;
            $response["start_counter"] = $order->start_counter;
            $response["remains"] = $order->remains;
        }
        return $response;
    }
}
