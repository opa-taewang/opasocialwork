<?php

namespace App\Http\Controllers\User\OpaSocial;

use Auth;
use Carbon;


class Api1Controller extends Controller
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
            $validator = \Validator::make($request->all(), array("service" => "required|numeric", "quantity" => "required|numeric", "link" => "required"));
            if ($validator->fails()) {
                $response["errors"] = $validator->errors()->all();
                return response()->json($response);
            }
            $params["service"] = $request->input("service");
            $params["quantity"] = $request->input("quantity");
            $params["link"] = $request->input("link");
            $params["comments"] = $request->input("comments") ?? "";
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
        } elseif (strtolower($request->input("action")) == "services") {
            $response = array();
            $group = Auth::user()->group;
            $package_ids = explode(",", $group->package_ids);
            $service_ids = \App\Package::whereIn('id', $package_ids)->distinct()->pluck('service_id');
            $services = \App\Service::where(['services.status' => 'ACTIVE', 'packages.status' => 'ACTIVE', 'services.servicetype' => 'DEFAULT'])->join('packages', 'services.id', '=', 'packages.service_id')->whereIn('services.id', $service_ids)->select('services.*')->distinct()->orderBy('services.position')->get();
            $packages = \App\Package::where(array("packages.status" => "ACTIVE", "packages.packagetype" => "DEFAULT"))->whereIn('id', $package_ids)->orderBy('position_id')->get();
            if (!$packages->isEmpty()) {
                foreach ($packages as $package) {
                    $userPackagePrices = \App\UserPackagePrice::where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id))->pluck("price_per_item", "package_id")->toArray();
                    $package_price = (isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item);
                    $group = Auth::user()->group;
                    $package_ids = explode(",", $group->package_ids);
                    $packages = \App\Package::where(array("packages.status" => "ACTIVE", "packages.packagetype" => "DEFAULT"))->orderBy("service_id")->get();
                    $price = isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item;
                    //$price=$price * getOption('display_price_per');
                    if (in_array($package->id, $package_ids)) {
                        $price1 = number_formats($price - ($price / 100) * $group->price_percentage, 2);
                        $price = $price1 * 1000;
                    }

                    $type = ($package->custom_comments == 1 ? "comments" : "default");
                    $response[] = array("service" => $package->id, "service_id" => $package->service->id, "name" => $package->name, "rate" => $price, "min" => $package->minimum_quantity, "max" => $package->maximum_quantity, "category" => $package->service->name, "type" => $type, "desc" => $package->description);
                }
            }
            return response()->json($response);
        } else {
            \Log::error("786");

            return response()->json(array("errors" => array("Incorrect request")));
        }
    }

    public function add($params)
    {
        $response = array("errors" => "");
        $package = \App\Package::findOrfail($params["service"]);
        $quantity = $params["quantity"];
        if ($package->status == 'INACTIVE') {
            $response["errors"] = array("Selected Package is not Active");
            return $response;
        }
        if ($package->limitReached()) {
            $response["errors"] = array("Package Limit per user Reached");
            return $response;
        }
        if ($quantity < $package->minimum_quantity) {
            $response["errors"] = array("Please specify at least minimum quantity.");
            return $response;
        }
        if ($package->maximum_quantity < $quantity) {
            $response["errors"] = array("Please specify less than or equal to maximum quantity");
            return $response;
        }
        if ($package->custom_comments) {
            $comments = $params["comments"];
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
            $custom_data = preg_replace("/\r\n|\r|\n/", PHP_EOL, $params["comments"]);
        }
        $order = \App\Order::create(array("price" => $price, "quantity" => $quantity, "package_id" => $package->id, "user_id" => \Illuminate\Support\Facades\Auth::user()->id, "api_id" => $package->preferred_api_id, "link" => $params["link"], "source" => "API", "custom_comments" => $custom_data));
        unset($response["errors"]);
        $response["order"] = $order->id;
        $user = \App\User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $user->funds = $user->funds - $price;
        $user->save();
        $mytime = Carbon\Carbon::now();
        $package->mydate = $mytime;
        $package->save();
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
            if (($order->status) == 'Inprogress') {
                $orderstatus = 'In progress';
            } else if (($order->status) == 'Cancelled') {
                $orderstatus = 'Canceled';
            } else $orderstatus = $order->status;
            unset($response["errors"]);
            $response["charge"] = $order->price;
            $response["status"] = $orderstatus;
            $response["start_count"] = $order->start_counter;
            $response["remains"] = $order->remains;
            $response["currency"] = getOption('currency_code');
        }
        \Log::error($response);
        return $response;
    }
}
