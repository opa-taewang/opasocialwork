<?php 
namespace App\Console\Commands;


class ProcessDripFeed extends \Illuminate\Console\Command
{
    protected $signature = "drip:feed";
    protected $description = "Command description";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \Log::error("drip:feed");
        $dfs = \App\DripFeed::whereIn("status", array( "SUBMITTED", "INPROGRESS" ))->inRandomOrder()->get();
        $neworder = false;
        foreach( $dfs as $df ) 
        {
            if( $df->runs_triggered == 0 ) 
            {
                $this->placeorder($df);
            }
            elseif( $df->runs_triggered < $df->runs ) 
            {
                if( strtoupper($df->activerun->status) == "COMPLETED" ) 
                {
                    $dt1 = \Carbon\Carbon::now();
                    $dt2 = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $df->activerun->getAttributes()["created_at"])->addMinutes($df->interval);
                    if( $dt2 <= $dt1 ) 
                    {
                        $this->placeorder($df);
                    }
                }
                elseif( in_array(strtoupper($df->activerun->status), array( "PARTIAL", "CANCELLED", "REFUNDED" )) ) 
                {
                    $surcharge = ceil(($df->runs * $df->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
                    $surcharge1 = ceil(($df->runs_triggered * $df->run_quantity) / 1000) * getOption("dripfeed_surcharge", true);
                    $remains = $df->runs - $df->runs_triggered;
                    $remains = ($remains <= 0 ? 0 : $remains);
                    $refundAmount = $remains * $df->run_price + $surcharge - $surcharge1;
                    $df->user->funds = $df->user->funds + $refundAmount;
                    $df->user->save();
                    $df->active_run_id = 0;
                    $df->status = "PARTIAL";
                    $df->save();
                }
                elseif( in_array(strtoupper($df->activerun->status), array( "PROCESSING", "INPROGRESS", "PENDING" )) ) 
                {
                    
                }
            }
            elseif( $df->runs_triggered == $df->runs ) 
            {
                if( strtoupper($df->activerun->status) == "COMPLETED" ) 
                {
                    $df->status = "COMPLETED";
                    $df->save();
                }
                elseif( in_array(strtoupper($df->activerun->status), array( "PARTIAL", "CANCELLED", "REFUNDED" )) ) 
                {
                    $df->status = "PARTIAL";
                    $df->save();
                }
                elseif( in_array(strtoupper($df->activerun->status), array( "PROCESSING", "INPROGRESS", "PENDING" )) ) 
                {
                }
            }
        }
    }

    public function placeorder(\App\DripFeed $df)
    {
        $order = \App\Order::create(array( "price" => $df->run_price, "quantity" => $df->run_quantity, "package_id" => $df->package->id, "api_id" => $df->package->preferred_api_id, "user_id" => $df->user_id, "link" => $df->link, "custom_comments" => $df->custom_comments ));
        \App\DripFeedOrder::create(array( "master_id" => $df->id, "slave_id" => $order->id ));
        $df->status = "INPROGRESS";
        $df->runs_triggered = $df->runs_triggered + 1;
        $df->active_run_id = $order->id;
        $df->save();
        if( !is_null($df->package->preferred_api_id) ) 
        {
            event(new \App\Events\OrderPlaced($order));
        }
    }

}


