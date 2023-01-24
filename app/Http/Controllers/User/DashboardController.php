<?php

namespace App\Http\Controllers\User;

use App\Broadcast;
use App\Models\Ticket;
use App\Models\AdminMessage;
use App\Models\TicketMessage;
use App\Models\OpaSocial\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

class DashboardController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function loginBack()
    {
        auth()->loginUsingId(session("imitator"));
        return redirect("/");
    }

    public function index()
    {
        $orders = Order::where([
            ['user_id', '=', $this->user->id]
        ])->count();
        $ordersPending = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'pending']
        ])->count();
        $ordersProcessing = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'processing']
        ])->count();
        $ordersInProgress = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'inprogress']
        ])->count();
        $ordersCancelled = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'cancelled']
        ])->count();
        $ordersPartial = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'partial']
        ])->count();
        $ordersCompleted = Order::where([
            ['user_id', '=', $this->user->id], ['status', '=', 'completed']
        ])->count();
        $spentAmount = Order::where([['user_id', '=', $this->user->id]])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');
        $noteFromAdmin = $row = DB::table("configs")->where("name", 'admin_note')->first();
        // app("App\\Http\\Controllers\\User\\OpaSocial\\OrderController")->check(7);
        $ticketIds = Ticket::where('user_id', '=', $this->user->id)->get()->pluck("id")->toArray();
        $unreadMessages = TicketMessage::where(array("is_read" => 0))->whereIn("ticket_id", $ticketIds)->whereNotIn("user_id", array($this->user->id))->count();
        $supportTicketOpen = Ticket::where(array("status" => "OPEN", "user_id" => $this->user->id))->count();

        $userData = (object) array(
            'orders' => $orders,
            'spentAmount' => $spentAmount,
            'ordersPending' => $ordersPending,
            'ordersCancelled' => $ordersCancelled,
            'ordersCompleted' => $ordersCompleted,
            'ordersPartial' => $ordersPartial,
            'ordersInProgress' => $ordersInProgress,
            'ordersProcessing' => $ordersProcessing,
            'unreadMessages' => $unreadMessages,
            'supportTicketOpen' =>        $supportTicketOpen,
        );
        return view('main.user.dashboard', compact('userData', 'noteFromAdmin'));
    }

    public function indexMessages()
    {
        $messages = AdminMessage::where("user_id", $this->user->id)->orderBy("created_at", "desc")->get();
        $note = NULL;
        foreach ($messages as $message) {
            $dtval = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $message->created_at, auth()->user()->timezone);
            $note = $note . "<span class=\"text-muted\">" . $dtval . "</span><br>Title: <span class=\"wysiwyg-color-blue\">" . $message->title . "</span><br>Message: <span class=\"wysiwyg-color-red\">" . $message->message . "</span><hr>";
        }
        return view("message.index", compact("note"));
    }

    public function getBroadCast($cacheid)
    {
        $msg = $this->user->adminmessages()->where("status", "SENT")->take(1)->get();
        if ($msg->count() > 0) {
            $data[] = array("id" => 0, "MsgTitle" => $msg->first()->title, "MsgText" => $msg->first()->message, "Icon" => $msg->first()->type);
            $msg->first()->status = "READ";
            $msg->first()->save();
            return datatables()->of($data)->toJson();
        }
        $date1 = date_create("now", new \DateTimeZone(auth()->user()->timezone));
        $bCast = Broadcast::selectRaw("id, MsgTitle, MsgText, Icon")->where("id", ">", $cacheid)->where("MsgStatus", 1)->where("ExpireTime", ">=", $date1)->orderBy("id", "asc")->take(1)->get();
        return datatables()->of($bCast)->toJson();
    }
}
