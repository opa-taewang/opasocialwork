<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

class DashboardController extends Controller
{

    public function __construct()
    {
        //
    }

    public function loginBack()
    {
        auth()->loginUsingId(session("imitator"));
        return redirect("/");
    }

    public function index()
    {
        // $spentAmount = 0;
        // $ordersPending = 0;
        // $ordersCancelled = 0;
        // $ordersCompleted = 0;
        // $ordersPartial = 0;
        // $ordersInProgress = 0;
        // $ordersProcessing = 0;
        // $orders = \Illuminate\Support\Facades\Auth::user()->orders;
        // app("App\\Http\\Controllers\\OrderController")->check(7);
        // $ticketIds = \App\Ticket::where(array("user_id" => \Illuminate\Support\Facades\Auth::user()->id))->get()->pluck("id")->toArray();
        // $unreadMessages = \App\TicketMessage::where(array("is_read" => 0))->whereIn("ticket_id", $ticketIds)->whereNotIn("user_id", array(\Illuminate\Support\Facades\Auth::user()->id))->count();
        // $supportTicketOpen = \App\Ticket::where(array("status" => "OPEN", "user_id" => \Illuminate\Support\Facades\Auth::user()->id))->count();
        // foreach ($orders as $order) {
        //     if (strtolower($order->status) == "pending") {
        //         $spentAmount += $order->price;
        //         $ordersPending++;
        //     } elseif (strtolower($order->status) == "cancelled") {
        //         $ordersCancelled++;
        //     } elseif (strtolower($order->status) == "completed") {
        //         $spentAmount += $order->price;
        //         $ordersCompleted++;
        //     } elseif (strtolower($order->status) == "partial") {
        //         $spentAmount += $order->price;
        //         $ordersCompleted++;
        //     } elseif (strtolower($order->status) == "inprogress") {
        //         $ordersInProgress++;
        //     } elseif (strtolower($order->status) == "processing") {
        //         $ordersProcessing++;
        //     }
        // }
        // return view("dashboard", compact("spentAmount", "ordersPending", "ordersCancelled", "ordersCompleted", "ordersProcessing", "unreadMessages", "ordersPartial", "supportTicketOpen", "ordersInProgress"));
        return view('main.user.dashboard');
    }

    public function indexMessages()
    {
        $messages = \App\AdminMessage::where("user_id", \Illuminate\Support\Facades\Auth::user()->id)->orderBy("created_at", "desc")->get();
        $note = NULL;
        foreach ($messages as $message) {
            $dtval = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $message->created_at, auth()->user()->timezone);
            $note = $note . "<span class=\"text-muted\">" . $dtval . "</span><br>Title: <span class=\"wysiwyg-color-blue\">" . $message->title . "</span><br>Message: <span class=\"wysiwyg-color-red\">" . $message->message . "</span><hr>";
        }
        return view("message.index", compact("note"));
    }

    public function getBroadCast($cacheid)
    {
        $msg = \Illuminate\Support\Facades\Auth::user()->adminmessages()->where("status", "SENT")->take(1)->get();
        if ($msg->count() > 0) {
            $data[] = array("id" => 0, "MsgTitle" => $msg->first()->title, "MsgText" => $msg->first()->message, "Icon" => $msg->first()->type);
            $msg->first()->status = "READ";
            $msg->first()->save();
            return datatables()->of($data)->toJson();
        }
        $date1 = date_create("now", new \DateTimeZone(auth()->user()->timezone));
        $bCast = \App\Broadcast::selectRaw("id, MsgTitle, MsgText, Icon")->where("id", ">", $cacheid)->where("MsgStatus", 1)->where("ExpireTime", ">=", $date1)->orderBy("id", "asc")->take(1)->get();
        return datatables()->of($bCast)->toJson();
    }
}
