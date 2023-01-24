<?php 
namespace App\Console\Commands;
use App\API;
use App\Order;
use App\Package;
use App\User;
use App\Visit;
use App\Commission;
use App\AffiliateTransaction;
use App\UserPackagePrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CheckOrderStatus extends \Illuminate\Console\Command
{
    protected $signature = "status:check";
    protected $description = "Check order status sent to APIs";
    private $order_statuses = array(  );

    public function __construct()
    {
        parent::__construct();
        $this->order_statuses = config("constants.ORDER_STATUSES");
    }

    public function handle()
    {
        \Log::error("status:check");
        $orders = \App\Order::whereNotIn("status", array( "PENDING", "CANCELLED", "COMPLETED", "PARTIAL", "REFUNDED", "REFILLING" ))->where("api_order_id", "!=", NULL)->inRandomOrder()->limit(15)->get();
        if( !$orders->isEmpty() ) 
        {
            foreach( $orders as $order ) 
            {
                $api = \App\API::find($order->api_id);
                $params = array(  );
                $apiRequestParams = \App\ApiRequestParam::where(array( "api_id" => $api->id, "api_type" => "status" ))->get();
                if( !$apiRequestParams->isEmpty() ) 
                {
                    foreach( $apiRequestParams as $row ) 
                    {
                        if( $row->param_type === "custom" ) 
                        {
                            $params[$row->param_key] = $row->param_value;
                        }
                        else
                        {
                            $params[$row->param_key] = $order->{$row->param_value};
                        }
                    }
                    $params[$api->order_id_key] = $order->api_order_id;
                    $client = new \GuzzleHttp\Client();
                    try
                    {
                        $param_key = "form_params";
                        if( $api->status_method === "GET" ) 
                        {
                            $param_key = "query";
                        }
                        $res = $client->request($api->status_method, $api->status_end_point, array( $param_key => $params, "headers" => array( "Accept" => "application/json" ) ));
                        if( $res->getStatusCode() === 200 ) 
                        {
                            $resp = $res->getBody()->getContents();
                            $success_response = array_cast_recursive(json_decode($api->status_success_response));
                            if( !array_diff_key_recursive(array_cast_recursive(json_decode($resp)), $success_response) ) 
                            {
                                $r = array_cast_recursive(json_decode($resp));
                                if( array_key_exists($api->status_key, $r) ) 
                                {
                                    if( strtoupper(trim($r[$api->status_key])) == "COMPLETED" || strtoupper(trim($r[$api->status_key])) == "COMPLETE" ) 
                                    {
                                        \App\Order::find($order->id)->update(array( "status" => "COMPLETED", "start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key] ));
                                        $user = \App\User::find($order->user_id);
			$visit = Visit::where('refVid','=',$order->user_id)->limit(1)->get();
					$orderPrice = $order->price;

			        $commission = Commission::all();
if(count($visit)>0 && $orderPrice>=$commission[0]->min_payout){
        $calAmt= ($orderPrice-($orderPrice - ($orderPrice *($commission[0]->commission_val/100))));
        $refUid= $visit[0]->refUid;
        $refuser = User::findOrFail($refUid);
        $refuser->treffund = $refuser->treffund + $calAmt;
        $refuser->save();
        $affiliateTransaction = new AffiliateTransaction;
        $affiliateTransaction->package_id = $order->package_id;
        $affiliateTransaction->refUid = $refUid;
        $affiliateTransaction->buyUid = $order->user_id;
        $affiliateTransaction->price = $orderPrice;
        $affiliateTransaction->transferedFund = $calAmt;
        $affiliateTransaction->save();
                                    }
                                    }
                                    elseif( strtoupper(trim($r[$api->status_key])) == "PENDING" || strtoupper(trim($r[$api->status_key])) == "INPROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN_PROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN-PROGRESS" || strtoupper(trim($r[$api->status_key])) == "IN PROGRESS" || strtoupper(trim($r[$api->status_key])) == "PPROCESSING" || strtoupper(trim($r[$api->status_key])) == "PROGRESS" ) 
                                    {
                                         \Log::error("status:check2");
                                        \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key] ));
                                    }
                                    elseif( in_array(strtoupper(trim($r[$api->status_key])), array( "PARTIAL", "PARTIALLY", "PARTIALLY COMPLETED", "PARTIAL COMPLETE" )) ) 
                                    {
                                        if( isset($r[$api->remains_key]) && $r[$api->remains_key] > 0 ) 
                                        {
                                            $remains = $r[$api->remains_key];
                                            $quantity = $order->quantity;
                                            $orderPrice = $order->price;
                                            $user = \App\User::find($order->user_id);
                                            $price_per_item = \App\Package::find($order->package_id)->price_per_item;
                                            $userPackagePrice = \App\UserPackagePrice::where(array( "user_id" => $order->user_id, "package_id" => $order->package_id ))->first();
                                            if( !is_null($userPackagePrice) ) 
                                            {
                                                $price_per_item = $userPackagePrice->price_per_item;
                                            }
                                            if( $remains < $quantity ) 
                                            {
                                                $refundAmount = (double) $price_per_item * $remains;
                                                $refundAmount = number_formats($refundAmount, 2, ".", "");
                                                if( $refundAmount > 0 ) 
                                                {
                                                    $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                                                    $authpoint = ($refundAmount)*$grouppercent;
                                                    $orderPrice = $orderPrice - $refundAmount;
                                                    \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "status" => "PARTIAL", "remains" => $r[$api->remains_key], "price" => $orderPrice ));
                                        $text = 'Order Partial by Server' . "\n";
                                                    $text .= 'Order ID: ' . $order->id. "\n"; 
													fundChange($text, $refundAmount, 'REFUND', $order->user_id, $order->id);
                                                   $user->funds = $user->funds + $refundAmount;
                                                    $user->points = $user->points - $authpoint;
                                                    $user->save();
                                                }
                                                else
                                                {
                                                    \App\Order::find($order->id)->update(array( "status" => "PARTIAL", "remains" => $r[$api->remains_key] ));
                                        $text = 'Order Partial by Server' . "\n";
                                                    $text .= 'Order ID: ' . $order->id. "\n"; 
                                                    
													fundChange($text, 0, 'NO REFUND', $order->user_id, $order->id);
                                                }
                                            }
                                            elseif( $quantity <= $remains ) 
                                            {
                                                $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                                                $authpoint = ($order->price)*$grouppercent;
                                                \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "status" => "CANCELLED", "remains" => $r[$api->remains_key] ));
                                        $text = 'Order Partial by Server with full remains' . "\n";
                                                $text .= 'Order ID: ' . $order->id. "\n"; 
												fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
                                                $user->funds = $user->funds + $order->price;
                                               $user->points = $user->points - $authpoint;
                                                $user->save();
                                            }
                                        }
                                    }
                                    elseif( in_array(strtoupper(trim($r[$api->status_key])), array( "CANCEL", "CANCELLED", "CANCELED" )) ) 
                                    {
                                         \Log::error("status:check3");
                                        if( $api->process_all_order ) 
                                        {
                                        $user = \App\User::find($order->user_id);
                                           $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                                            $authpoint = ($order->price)*$grouppercent;
                                            \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key], "status" => "CANCELLED" ));
                                         $text = 'Order Cancelled by Server' . "\n";
                                            $text .= 'Order ID: ' . $order->id. "\n"; 
											   
                                            $user->funds = $user->funds + $order->price;
                                            $user->points = $user->points - $authpoint;
                                            $user->save();
                                            fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
                                        }
                                    }
                                    elseif( in_array(strtoupper(trim($r[$api->status_key])), array( "REFUND", "REFUNDED" )) ) 
                                    {
                                        if( $api->process_all_order ) 
                                        {
                                             \Log::error("status:check4");
                                        $user = \App\User::find($order->user_id);
                                            $grouppercent=\App\Group::where('id',$user->group_id)->value('point_percent');
                                            $authpoint = ($order->price)*$grouppercent;
                                            \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "remains" => $r[$api->remains_key], "status" => "REFUNDED" ));
                                        $text = 'Order Refunded by Server' . "\n";
                                             $text .= 'Order ID: ' . $order->id. "\n";
											fundChange($text, $order->price, 'REFUND', $order->user_id, $order->id);
                                            $user->funds = $user->funds + $order->price;
                                            $user->points = $user->points - $authpoint;
                                            $user->save();
                                        }
                                    }
                                    elseif( in_array(strtoupper(trim($r[$api->status_key])), $this->order_statuses) ) 
                                    {
                                        \App\Order::find($order->id)->update(array( "start_counter" => $r[$api->start_counter_key], "status" => strtoupper(trim($r[$api->status_key])), "remains" => $r[$api->remains_key] ));
                                    }
                                }
                            }
                            \App\ApiResponseLog::create(array( "order_id" => $order->id, "api_id" => $api->id, "response" => $resp ));
                        }
                    }
                    
                   catch (\GuzzleHttp\Exception\ClientException $e) {
						\App\ApiResponseLog::create(['order_id' => $order->id, 'api_id' => $api->id, 'response' => $e->getMessage()]);
                }
            }
        }
    }

}


}