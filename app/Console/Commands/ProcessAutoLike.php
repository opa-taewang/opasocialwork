<?php


namespace App\Console\Commands;

class ProcessAutoLike extends \Illuminate\Console\Command
{
    protected $signature = "auto:like";
    protected $postlink = "https://www.instagram.com/p/";
    protected $description = "Command description";
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        \Log::error("auto:like");
        $als = \App\AutoLike::whereIn("status", ["SUBMITTED", "INPROGRESS"])->inRandomOrder()->get();
        foreach ($als as $al) {
            $iR = $this->getInsta($al->username);
            if (strtoupper($al->status) == "SUBMITTED") {
                if ($iR[0]["id"] != 0) {
                    \App\AutoLikeOrder::create(["master_id" => $al->id, "slave_id" => 0, "posttime" => $iR[0]["time"]]);
                    $al->last_post = $iR[0]["shortcode"];
                    $al->status = "INPROGRESS";
                    $al->save();
                }
            } else {
                if ($al->runs_triggered < $al->posts && strtoupper($al->status) == "INPROGRESS") {
                    $lastposttime = \App\AutoLikeOrder::where("master_id", $al->id)->max("posttime");
                    for ($i = count($iR) - 1; 0 <= $i && strtoupper($al->status) == "INPROGRESS"; $i--) {
                        if ($lastposttime < $iR[(int) $i]["time"] && ($al->package->features == "Auto Like" || $al->package->features == "Auto View" && $iR[(int) $i]["type"] == "GraphVideo")) {
                            if ($al->dripfeed == 1) {
                                $this->placedforder($al, $iR[(int) $i]["shortcode"], $iR[(int) $i]["time"]);
                            } else {
                                $this->placeorder($al, $iR[(int) $i]["shortcode"], $iR[(int) $i]["time"]);
                            }
                            $al->runs_triggered = $al->runs_triggered + 1;
                            if ($al->runs_triggered == $al->posts) {
                                $al->status = "COMPLETED";
                            }
                            $al->save();
                        }
                    }
                } else {
                    if ($al->runs_triggered == $al->posts) {
                        $al->status = "COMPLETED";
                        $al->save();
                    }
                }
            }
        }
    }
    public function placeorder(\App\AutoLike $al, $sc, $pt)
    {
        $order = \App\Order::create(["price" => $al->run_price, "quantity" => rand($al->min, $al->max), "package_id" => $al->package->id, "api_id" => $al->package->preferred_api_id, "user_id" => $al->user_id, "link" => $this->postlink . $sc]);
        \App\AutoLikeOrder::create(["master_id" => $al->id, "slave_id" => $order->id, "posttime" => $pt]);
        if (!is_null($al->package->preferred_api_id)) {
            event(new \App\Events\OrderPlaced($order));
        }
    }
    public function placedforder(\App\AutoLike $al, $sc, $pt)
    {
        $df = \App\DripFeed::create(["run_price" => number_formats($al->run_price / $al->dripfeed_runs, 2, ".", ""), "link" => $this->postlink . $sc, "run_quantity" => rand($al->min, $al->max), "runs" => $al->dripfeed_runs, "interval" => $al->dripfeed_interval, "runs_triggered" => 0, "user_id" => $al->user_id, "package_id" => $al->package->id, "active_run_id" => 0]);
        \App\AutoLikeOrder::create(["master_id" => $al->id, "slave_id" => $df->id, "posttime" => $pt]);
    }
    public function getInsta($username)
    {
        $url = "https://www.instagram.com/" . $username;
        $client = new \GuzzleHttp\Client();
        try {
            $res = $client->request("GET", $url);
        } catch (\Exception $e) {
            $res = false;
        }
        $data[] = ["id" => 0, "type" => 0, "shortcode" => 0, "time" => 0];
        if ($res !== false && $res->getStatusCode() === 200) {
            $resp = $res->getBody()->getContents();
            $arr = explode("window._sharedData = ", $resp);
            $arr = explode(";</script>", $arr[1]);
            $obj = json_decode($arr[0], true);
            $posts = $obj["entry_data"]["ProfilePage"][0]["graphql"]["user"]["edge_owner_to_timeline_media"]["edges"];
            if (!empty($posts)) {
                $data = NULL;
            }
            foreach ($posts as $post) {
                $post = $post["node"];
                $data[] = ["id" => $post["id"], "type" => $post["__typename"], "shortcode" => $post["shortcode"], "time" => $post["taken_at_timestamp"]];
            }
        }
        return $data;
    }
}

?>