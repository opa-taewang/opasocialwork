<?php 
namespace App\Listeners;


class SendOrderToReseller
{
    public function __construct()
    {
    }

    public function handle(\App\Events\OrderPlaced $event)
    {
        $order = $event->order;
        $api = \App\API::find($order->api_id);
        $apiMapping = \App\ApiMapping::where(array( "api_id" => $api->id, "package_id" => $order->package_id ))->first();
        if( is_null($apiMapping) ) 
        {
            return NULL;
        }
        $params = array(  );
        $apiRequestParams = \App\ApiRequestParam::where(array( "api_id" => $api->id, "api_type" => "order" ))->get();
        if( !$apiRequestParams->isEmpty() ) 
        {
            foreach( $apiRequestParams as $row ) 
            {
                if( $row->param_type == "custom" ) 
                {
                    $params[$row->param_key] = $row->param_value;
                }
                elseif( $row->param_value == "package_id" ) 
                {
                    $params[$row->param_key] = $apiMapping->api_package_id;
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
                        \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $resp ));
                    }
                    else
                    {
                        \App\Order::find($order->id)->update(array( "api_id" => $api->id,"status" => "CANCELLED" ));
                        \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $resp ));
                        $user = \App\User::find($order->user_id);
                                           $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                                            $authpoint = ($order->price)*$grouppercent;
                                         $text = 'Order Not Accepted by Server' . "\n";
                                            $text .= 'Order ID: ' . $order->id. "\n"; 
											   
                                            $user->funds = $user->funds + $order->price;
                                            $user->points = $user->points - $authpoint;
                                            $user->save();
                                            fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
                        
                    }
                }
            }
            catch( \GuzzleHttp\Exception\ClientException $e ) 
            {
                \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $e->getMessage() ));
            }
        }
    }

}


