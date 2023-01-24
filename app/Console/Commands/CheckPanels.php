<?php 
namespace App\Console\Commands;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\ChildPanelOrder;
use App\User;

class CheckPanels extends \Illuminate\Console\Command
{
    protected $signature = "renew:childpanels";
    protected $description = "Renew child panels";
    private $order_statuses = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users=User::join('child_panel_orders','child_panel_orders.user_id','=','users.id')->select('users.*')->get();
        foreach($users as $key => $user) {
            $orders = ChildPanelOrder::where('user_id',$user->id)->where('renew',1)->where('expiry_at','<=', \Carbon\Carbon::now())->orderBy('created_at', 'desc')->get();
            
            foreach($orders as $order) {
                if($user->funds >= $order->amount) {
                    $balance=$user->funds-$order->amount;
                    $user->funds=$balance;
                    $user->save();
                    $order->start_at=$order->expiry_at;
                    $order->expiry_at=date('Y-m-d H:s:i', strtotime("+30 days"));
                    $order->save();
                }
            }
        }
    }

}


