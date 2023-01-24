<?php


namespace App\Console\Commands;

class CheckPanelOrder extends \Illuminate\Console\Command
{
    protected $signature = "update:childpanels";
    protected $description = "Check order status of child panels";
    private $order_statuses = [];
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $admin_id = \App\User::where("role", "ADMIN")->first()->value("id");
        $orders = \App\ChildPanelOrder::where("buyer", "smm-script.com")->where("status", "!=", "Completed")->get();
        if (!$orders->isEmpty()) {
            foreach ($orders as $order) {
                $error = "";
                $client = new \GuzzleHttp\Client();
                $info = "";
                try {
                    $domain = base64_encode($_SERVER["SERVER_NAME"]);
                    $key = \DB::table("configs")->where("name", "smm_api_key")->value("value");
                    if (!empty($key)) {
                        $url = "https://smm-script.com/api/childpanel/order/status";
                        $params["headers"] = ["X-XSRF-TOKEN" => csrf_token(), "X-Authorization" => $key];
                        $params["form_params"] = ["domain" => $domain, "order_id" => $order->id];
                        $res = $client->post($url, $params);
                        $body = $res->getBody()->getContents();
                        if ($body == "true") {
                            $error = "Unauthorized api key";
                        } else {
                            if ($body == "false") {
                                $error = "Contact with Smm-Script Administrator to get an apikey";
                            } else {
                                $order->status = $body;
                                $order->save();
                            }
                        }
                    }
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                }
            }
        }
    }
}

?>