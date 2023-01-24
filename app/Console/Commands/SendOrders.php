<?php 
namespace App\Console\Commands;


class SendOrders extends \Illuminate\Console\Command
{
    protected $signature = "orders:send";
    protected $description = "Send Orders to Reseller Panels Automatically";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
			
		
        $orderCancel = \App\Order::where(array( "status" => "CANCELLING", "api_order_id" => NULL ))->get();
        if( !$orderCancel->isEmpty() ) 
        {
            foreach( $orderCancel as $order ) 
            {
                $order->status = "CANCELLED";
                $order->save();
                $text = 'Order Cancelled on User Request' . "\n";
                $text .= 'Order ID: ' . $order->id. "\n"; 
				
                $user = \App\User::find($order->user_id);
                 $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                $authpoint = ($order->price)*$grouppercent;
                $user->funds = $user->funds + $order->price;
                 $user->points = $user->points - $authpoint;
                $user->save();
                fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
            }
        }
        $orders = \App\Order::where(array( "status" => "PENDING", "api_order_id" => NULL ))->where("api_id", "!=", NULL)->inRandomOrder()->limit(100)->get();
        if( !$orders->isEmpty() ) 
        {
            foreach( $orders as $order ) 
            {
                $api = \App\API::find($order->api_id);
                $apiMapping = \App\ApiMapping::where(array( "api_id" => $api->id, "package_id" => $order->package_id ))->first();
                if( is_null($apiMapping) ) 
                {
                    continue;
                }
                else
                {
                    $dt1 = \Carbon\Carbon::now();
                    $dt2 = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $order->getAttributes()["created_at"])->addMinutes(2);
                    if( $dt1 < $dt2 ) 
                    {
                        continue;
                    }
                    else
                    {
                        $params = array(  );
                        $apiRequestParams = \App\ApiRequestParam::where(array( "api_id" => $api->id, "api_type" => "order" ))->get();
                        if( !$apiRequestParams->isEmpty() ) 
                        {
                            foreach( $apiRequestParams as $row ) 
                            {
                                if( $row->param_type === "custom" ) 
                                {
                                    $params[$row->param_key] = $row->param_value;
                                }
                                elseif( $row->param_value === "package_id" ) 
                                {
                                    $params[$row->param_key] = $apiMapping->api_package_id;
                                }
                                elseif( $row->param_value == "custom_comments" ) 
                                {
                                    $package = \App\Package::find($order->package_id);
                                    if( $package->custom_comments ) 
                                    {
                                        $params[$row->param_key] = $order->{$row->param_value};
                                    }
                                }
                                else
                                {
                                    $params[$row->param_key] = $order->{$row->param_value};
                                }
                            }
                            $client = new \GuzzleHttp\Client();
                            try
                            {
                                $param_key = "form_params";
                                if( $api->order_method === "GET" ) 
                                {
                                    $param_key = "query";
                                }
                                $res = $client->request($api->order_method, $api->order_end_point, array( $param_key => $params, "headers" => array( "Accept" => "application/json" ) ));
                                if( $res->getStatusCode() === 200 ) 
                                {
                                    $resp = $res->getBody()->getContents();
                                    $success_response = array_cast_recursive(json_decode($api->order_success_response));
                                    if( !array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response) ) 
                                    {
                                        $r = json_decode($resp);
                                        \App\Order::find($order->id)->update(array( "api_id" => $api->id, "api_order_id" => $r->{$api->order_id_key}, "status" => "INPROGRESS" ));
                                    }
                                    \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $resp ));
                                }
                            }
                            catch( \GuzzleHttp\Exception\ClientException $e ) 
                            {
                                \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $e->getMessage() ));
                            }
                        }
                    }
                }
            }
        }
    }

}


