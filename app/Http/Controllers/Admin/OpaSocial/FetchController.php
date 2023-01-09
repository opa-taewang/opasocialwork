<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;


class FetchController extends Controller
{
    private $services = array();

    public function __construct()
    {
        $this->order_statuses = config("constants.ORDER_STATUSES");
    }

    public function index()
    {
        $apis = \App\API::where("package_end_point", "!=", NULL)->get();
        return view("admin.apifetch.index", compact("apis"));
    }

    public function showData(\Illuminate\Http\Request $request)
    {
        $api_id = $request->input("api_id");
        $api_name = \App\API::where(array("id" => $api_id))->first()->name;
        $api_name = strtoupper($api_name);
        setOption("profit_percentage", $request->input("profit_percentage"));
        setOption("api_id", $request->input("api_id"));
        $this->showDataDup($api_id);
        return view("admin.apifetch.map", compact("api_id", "api_name"));
    }

    private function showDataDup($api_id)
    {
        $api = \App\API::find($api_id);
        $profit = getOption("profit_percentage", true) / 100 + 1;
        $params = array();
        $apiRequestParams = \App\ApiRequestParam::where(array("api_id" => $api->id, "api_type" => "package"))->get();
        if (!$apiRequestParams->isEmpty()) {
            foreach ($apiRequestParams as $row) {
                $params[$row->param_key] = $row->param_value;
            }
            $client = new \GuzzleHttp\Client();
            try {
                $param_key = "form_params";
                if ($api->package_method === "GET") {
                    $param_key = "Something is wrong";
                }
                $res = $client->request($api->package_method, $api->package_end_point, array($param_key => $params, "headers" => array("Accept" => "application/json")));
                if ($res->getStatusCode() === 200) {
                    $resp = $res->getBody()->getContents();
                    $r = array_cast_recursive(json_decode($resp));
                    $insertRows = array();
                    for ($i = 0; $i < count($r); $i++) {
                        $amaps = \App\ApiMapping::where(array("api_package_id" => $r[$i][(string) $api->package_id_key], "api_id" => $api->id))->get();
                        $flag = 0;
                        $type = strtoupper(trim($r[$i][(string) $api->type_key]));
                        if ($type == "DEFAULT") {
                            $type = 0;
                        } elseif ($type == "SUBSCRIPTIONS") {
                        } else {
                            $type = 1;
                        }
                        $newrate = (((array_key_exists((string) $api->rate_key, $r[$i]) ? $r[$i][(string) $api->rate_key] : 0)) * $api->rate * $profit) / 1000;
                        if (!$amaps->isEmpty()) {
                            foreach ($amaps as $amap) {
                                $pckg = \App\Package::with("service")->where(array("id" => $amap->package_id, "status" => "ACTIVE"))->first();
                                if (!is_null($pckg)) {
                                    $flag = 1;
                                    $insertRows[] = array("service_id" => $pckg->service->id, "service_name" => $pckg->service->name, "api_service_name" => (array_key_exists((string) $api->service_key, $r[$i]) ? $r[$i][(string) $api->service_key] : ""), "api_package_id" => (array_key_exists((string) $api->package_id_key, $r[$i]) ? $r[$i][(string) $api->package_id_key] : ""), "package_id" => $pckg->id, "package_name" => $pckg->name, "package_description" => $pckg->description, "api_package_description" => (array_key_exists((string) $api->desc_key, $r[$i]) ? $r[$i][(string) $api->desc_key] : ""), "api_package_name" => (array_key_exists((string) $api->package_name, $r[$i]) ? $r[$i][(string) $api->package_name] : ""), "api_price_per_item" => (((array_key_exists((string) $api->rate_key, $r[$i]) ? $r[$i][(string) $api->rate_key] : 0)) * $api->rate) / 1000, "price_per_item" => ($pckg->price_per_item < $newrate ? $newrate : $pckg->price_per_item), "api_minimum_quantity" => (array_key_exists((string) $api->min_key, $r[$i]) ? $r[$i][(string) $api->min_key] : ""), "minimum_quantity" => $pckg->minimum_quantity, "api_maximum_quantity" => min(2147483647, (array_key_exists((string) $api->max_key, $r[$i]) ? $r[$i][(string) $api->max_key] : "")), "maximum_quantity" => $pckg->maximum_quantity, "type" => $type);
                                }
                            }
                        }
                        if ($flag == 0) {
                            $insertRows[] = array("service_id" => 0, "service_name" => (array_key_exists((string) $api->service_key, $r[$i]) ? $r[$i][(string) $api->service_key] : ""), "api_service_name" => (array_key_exists((string) $api->service_key, $r[$i]) ? $r[$i][(string) $api->service_key] : ""), "api_package_id" => (array_key_exists((string) $api->package_id_key, $r[$i]) ? $r[$i][(string) $api->package_id_key] : ""), "package_id" => 0, "package_name" => (array_key_exists((string) $api->package_name, $r[$i]) ? $r[$i][(string) $api->package_name] : ""), "package_description" => (array_key_exists((string) $api->desc_key, $r[$i]) ? $r[$i][(string) $api->desc_key] : (array_key_exists((string) $api->package_name, $r[$i]) ? $r[$i][(string) $api->package_name] : "")), "api_package_description" => (array_key_exists((string) $api->desc_key, $r[$i]) ? $r[$i][(string) $api->desc_key] : ""), "api_package_name" => (array_key_exists((string) $api->package_name, $r[$i]) ? $r[$i][(string) $api->package_name] : ""), "api_price_per_item" => (((array_key_exists((string) $api->rate_key, $r[$i]) ? $r[$i][(string) $api->rate_key] : 0)) * $api->rate) / 1000, "price_per_item" => $newrate, "api_minimum_quantity" => (array_key_exists((string) $api->min_key, $r[$i]) ? $r[$i][(string) $api->min_key] : ""), "minimum_quantity" => (array_key_exists((string) $api->min_key, $r[$i]) ? $r[$i][(string) $api->min_key] : ""), "api_maximum_quantity" => min(2147483647, (array_key_exists((string) $api->max_key, $r[$i]) ? $r[$i][(string) $api->max_key] : "")), "maximum_quantity" => min(2147483647, (array_key_exists((string) $api->max_key, $r[$i]) ? $r[$i][(string) $api->max_key] : "")), "type" => $type);
                        }
                    }
                    \DB::table("api_fetch_temps")->delete();
                    \DB::statement("ALTER TABLE api_fetch_temps AUTO_INCREMENT = 1");
                    if (!empty($insertRows)) {
                        \DB::table("api_fetch_temps")->insert($insertRows);
                    }
                }
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }
    }

    public function getMap()
    {
        $apifetchs = \DB::Select("SELECT api_service_name,@rowid:=@rowid+1 AS id FROM (SELECT DISTINCT(api_service_name) FROM api_fetch_temps) AS b, (SELECT @rowid:=0) AS init");
        $services = \App\Service::where("status", "ACTIVE")->get();
        return datatables()->of($apifetchs)->addColumn("check", function ($apifetch) {
            return "<input type='checkbox' class='input-sm row-checkbox' name='apifetch_id[" . (string) $apifetch->id . "]' value='" . (string) $apifetch->id . "'>";
        })->addColumn("sno", function ($apifetch) {
            return "<label class='row-id' name='sno[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->id . "'>" . (string) $apifetch->id . "</label>";
        })->addColumn("service", function ($apifetch) use ($services) {
            $html = "<select class='form-control row-edit srvcclass' readonly disabled name='service[" . (string) $apifetch->id . "]'>";
            $html .= "<option value=0 selected>Create New</option>";
            foreach ($services as $service) {
                if (trim($apifetch->api_service_name) == $service->name) {
                    $html .= "<option selected value=" . (string) $service->id . " odata='" . (string) $service->name . "'>" . (string) $service->id . "-" . (string) $service->name . "</option>";
                } else {
                    $html .= "<option value=" . (string) $service->id . " odata='" . (string) $service->name . "'>" . (string) $service->id . "-" . (string) $service->name . "</option>";
                }
            }
            return $html;
        })->editColumn("api_service_name", function ($apifetch) use ($services) {
            $html = "<input type='text' style='width:100%;' class='form-control input-sm sel-edit' readonly value='" . (string) $apifetch->api_service_name . "' name='service_name[" . (string) $apifetch->id . "]'>";
            return (string) $html . "<br><label name='api_service_name[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->api_service_name . "'>" . (string) $apifetch->api_service_name . "</label>";
        })->rawColumns(array("check", "sno", "service", "api_service_name"))->toJson();
    }

    public function saveMap(\Illuminate\Http\Request $request)
    {
        $maxid = \App\Service::max("id");
        $newid = $maxid + 1;
        $api_id = $request->input("api_id");
        $apifetch_ids = $request->input("apifetch_id");
        $maxsort = 10;
        $newsort = $newid * $maxsort;
        \Log::error("$newsort");
        $services = $request->input("service");
        $service_names = $request->input("service_name");
        $api_service_names = $request->input("api_service_name");
        $api_name = \App\API::where(array("id" => $api_id))->first()->name;
        $api_name = strtoupper($api_name);
        if (!is_null($apifetch_ids)) {
            foreach ($apifetch_ids as $id) {
                if ($services[$id] == 0) {
                    $svc = \App\Service::create(array("name" => trim($service_names[$id]), "slug" => str_slug(trim($service_names[$id])), "description" => trim($service_names[$id]), "is_subscription_allowed" => 0, "status" => "ACTIVE", "position" => $newsort));
                    $services[$id] = $svc->id;
                }
                $afts = \App\ApiFetchTemp::where("service_id", 0)->where("api_service_name", trim($api_service_names[$id]))->get();
                foreach ($afts as $aft) {
                    $aft->service_id = $services[$id];
                    $aft->save();
                }
            }
            $apitemps = \DB::Select("Select DISTINCT service_id from api_fetch_temps");
            foreach ($apitemps as $apitemp) {
                $flag = 0;
                foreach ($services as $svid) {
                    if ($apitemp->service_id == $svid) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) {
                    \DB::Statement("DELETE FROM api_fetch_temps WHERE service_id =" . $apitemp->service_id);
                }
            }
        }
        $fetch_count = \App\ApiFetchTemp::where("service_id", "!=", 0)->max("id");
        return view("admin.apifetch.list", compact("api_id", "api_name", "fetch_count"));
    }

    public function getData()
    {
        $apifetchs = \App\ApiFetchTemp::where("service_id", "!=", 0)->get();
        $services = \App\Service::where("status", "ACTIVE")->get();
        return datatables()->of($apifetchs)->addColumn("check", function ($apifetch) {
            return "<input type='checkbox' class='input-sm row-checkbox' name='apifetch_id[" . (string) $apifetch->id . "]' value='" . (string) $apifetch->id . "'><br><input type='hidden' name='type[" . (string) $apifetch->id . "]' value='" . (string) $apifetch->type . "'><input type='hidden' name='package_idh[" . (string) $apifetch->id . "]' value='" . (string) $apifetch->package_id . "'><input type='hidden' name='api_package_idh[" . (string) $apifetch->id . "]' value='" . (string) $apifetch->api_package_id . "'>";
        })->addColumn("service", function ($apifetch) use ($services) {
            $html = "<select class='form-control row-edit srvcclass' readonly disabled name='service[" . (string) $apifetch->id . "]'>";
            foreach ($services as $service) {
                if ($apifetch->service_id == $service->id) {
                    $html .= "<option selected value=" . (string) $service->id . ">" . (string) $service->id . "-" . (string) $service->name . "</option>";
                } else {
                    $html .= "<option value=" . (string) $service->id . ">" . (string) $service->id . "-" . (string) $service->name . "</option>";
                }
            }
            $html .= "</select><br>[<label name='api_service_name[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->api_service_name . "'>" . (string) $apifetch->api_service_name . "</label>]";
            return $html;
        })->editColumn("package_id", function ($apifetch) {
            return "<label name='package_id[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->package_id . "'>" . (string) $apifetch->package_id . "</label><br>[<label name='api_package_id[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->api_package_id . "'>" . (string) $apifetch->api_package_id . "</label>]";
        })->editColumn("package_name", function ($apifetch) {
            $html = "<input type='text' style='width:100%;' readonly class='form-control input-sm row-edit' value='" . (string) $apifetch->package_name . "' name='package_name[" . (string) $apifetch->id . "]'>";
            return (string) $html . "<br>[" . (string) $apifetch->api_package_name . "]";
        })->editColumn("package_description", function ($apifetch) {
            $html = "<textarea style='height:150px;' readonly class='form-control input-sm row-edit' name='package_description[" . (string) $apifetch->id . "]'>" . (string) $apifetch->package_description . "</textarea>";
            return (string) $html . "<br>[" . (string) $apifetch->api_package_description . "]";
        })->editColumn("price_per_item", function ($apifetch) {
            $html = "<input type='text' style='width:100%;' readonly class='form-control input-sm row-edit' value='" . (string) $apifetch->price_per_item . "' name='price_per_item[" . (string) $apifetch->id . "]'>";
            return (string) $html . "<br>[<label name='api_price_per_item[" . (string) $apifetch->id . "]' ldata='" . (string) $apifetch->api_price_per_item . "'>" . (string) $apifetch->api_price_per_item . "</label>]";
        })->editColumn("minimum_quantity", function ($apifetch) {
            $html = "<input type='text' style='width:100%;' readonly class='form-control input-sm row-edit' value='" . (string) $apifetch->minimum_quantity . "' name='minimum_quantity[" . (string) $apifetch->id . "]'>";
            return (string) $html . "<br>[" . (string) $apifetch->api_minimum_quantity . "]";
        })->editColumn("maximum_quantity", function ($apifetch) {
            $html = "<input type='text' style='width:100%;' readonly class='form-control input-sm row-edit' value='" . (string) $apifetch->maximum_quantity . "' name='maximum_quantity[" . (string) $apifetch->id . "]'>";
            return (string) $html . "<br>[" . (string) $apifetch->api_maximum_quantity . "]";
        })->removeColumn("service_id")->rawColumns(array("check", "service", "package_id", "package_name", "package_description", "maximum_quantity", "minimum_quantity", "price_per_item"))->toJson();
    }

    public function redirect(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Session::flash("alert", "Packages Saved");
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        $apis = \App\API::where("package_end_point", "!=", NULL)->get();
        return view("admin.apifetch.index", compact("apis"));
    }

    public function saveData(\Illuminate\Http\Request $request)
    {
        foreach ($request->data as $obj) {
            $flag = 0;
            $api_id = getOption("api_id", true);
            if ($obj["package_id"] != 0) {
                $package = \App\Package::findOrFail($obj["package_id"]);
                if ($package->price_per_item == $obj["price_per_item"]) {
                    $flag = 1;
                    $package->service_id = $obj["service"];
                    $package->name = $obj["package_name"];
                    $package->slug = str_slug($obj["package_name"]);
                    $package->price_per_item = $obj["price_per_item"];
                    $package->minimum_quantity = $obj["minimum_quantity"];
                    $package->maximum_quantity = $obj["maximum_quantity"];
                    $package->description = $obj["package_description"];
                    $package->preferred_api_id = $api_id;
                    $package->status = "ACTIVE";
                    $package->custom_comments = $obj["type"];
                    $package->save();
                } else {
                    $package->status = "INACTIVE";
                    $package->save();
                }
            }
            if ($flag == 0) {
                $maxid = \App\Package::max("id");
                $newid = $maxid + 1;
                $maxsort = 10;
                $newsort = $newid * $maxsort;
                \Log::error("$newsort");
                $package = \App\Package::create(array("id" => $newid, "position" => $newsort, "service_id" => $obj["service"], "name" => $obj["package_name"], "slug" => str_slug($obj["package_name"]), "price_per_item" => $obj["price_per_item"], "minimum_quantity" => $obj["minimum_quantity"], "maximum_quantity" => $obj["maximum_quantity"], "description" => $obj["package_description"], "status" => "ACTIVE", "preferred_api_id" => $api_id, "custom_comments" => $obj["type"]));
                $groups = \App\Group::all();

                foreach ($groups as $group) {
                    if ($group->package_ids == '') {
                        $group->package_ids = $package->id;
                    } else {
                        $group->package_ids .= ',' . $package->id;
                    }

                    $group->save();
                }
            }

            $aft = \App\ApiFetchTemp::find($obj["cnt"]);
            $aft->service_id = $obj["service"];
            $aft->package_id = $package->id;
            $aft->package_name = $package->name;
            $aft->package_description = $package->description;
            $aft->price_per_item = $package->price_per_item;
            $aft->minimum_quantity = $package->minimum_quantity;
            $aft->maximum_quantity = $package->maximum_quantity;
            $aft->save();
            $insert = array();
            \App\ApiMapping::where(array("api_id" => $api_id, "package_id" => $package->id))->delete();
            $insert[] = array("package_id" => $package->id, "api_package_id" => $obj["api_package_id"], "api_id" => $api_id);
            \DB::table("api_mappings")->insert($insert);
            \DB::update("UPDATE packages INNER JOIN services ON services.id = packages.service_id SET packages.position_id = services.position");
        }
    }
}
