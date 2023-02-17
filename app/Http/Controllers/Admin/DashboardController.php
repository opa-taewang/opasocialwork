<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function index(\Illuminate\Http\Request $request)
    {


        $totalSell = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->sum("price");
        $totalOrdersCompleted = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->count();
        $totalOrdersPending = \App\Order::where(array("status" => "PENDING"))->count();
        $totalOrdersCancelled = \App\Order::where(array("status" => "CANCELLED"))->count();
        $totalOrdersInProgress = \App\Order::where(array("status" => "INPROGRESS"))->count();
        $totalOrdersProcessing = \App\Order::where(array("status" => "PROCESSING"))->count();
        $totalrefillpending = \App\RefillRequest::where(array("status" => "PENDING"))->count();
        $totalseopending = \App\SeoOrder::where(array("status" => "Pending"))->count();
        $totalOrders = \App\Order::count();
        $totalUsers = \App\User::where("id", "<>", \Illuminate\Support\Facades\Auth::user()->id)->count();
        $date = \Carbon\Carbon::today()->subDays(30);
        $ttotalSell = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->where('created_at', '>=', $date)->sum("price");
        $totalUserfunds = \App\User::where("id", "<>", \Illuminate\Support\Facades\Auth::user()->id)->where('last_login', '>=', $date)->sum("funds");
        $supportTicketOpen = \App\Models\Ticket::where(array("status" => "OPEN"))->count();
        $unreadMessages = \App\TicketMessage::where(array("is_read" => 0))->whereNotIn("user_id", array(\Illuminate\Support\Facades\Auth::user()->id))->count();
        $tktcnt = \App\Models\Ticket::where(['is_read' => 0])->count();
        $msgcnt = \App\TicketMessage::where(['is_read' => 0])->whereNotIn('user_id', [auth()->user()->id])->count();
        $msgcnt += $tktcnt;
        $orderprice = \App\Order::sum('price');
        $ordercost = \App\Order::sum('cost');
        $totalprofit = $orderprice - $ordercost;
        $torderprice = \App\Order::where('created_at', '>=', $date)->sum('price');
        $tordercost = \App\Order::where('created_at', '>=', $date)->sum('cost');
        $ttotalprofit = $torderprice - $tordercost;
        return view("admin.dashboard", compact("totalSell", "totalOrdersCompleted", "ttotalSell", "totalseopending", "msgcnt", "totalrefillpending", "totalOrdersPending", "totalOrdersCancelled", "totalUsers", "supportTicketOpen", "totalOrdersProcessing", "unreadMessages", "totalOrdersInProgress", "totalOrders", "totalUsers", "totalUserfunds", "totalprofit", "ttotalprofit"));
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function indexdash(\Illuminate\Http\Request $request)
    {

        $totalSell = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->sum("price");
        $totalOrdersCompleted = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->count();
        $totalOrdersPending = \App\Order::where(array("status" => "PENDING"))->count();
        $totalOrdersCancelled = \App\Order::where(array("status" => "CANCELLED"))->count();
        $totalOrdersInProgress = \App\Order::where(array("status" => "INPROGRESS"))->count();
        $totalOrdersProcessing = \App\Order::where(array("status" => "PROCESSING"))->count();
        $totalrefillpending = \App\RefillRequest::where(array("status" => "PENDING"))->count();
        $totalseopending = \App\SeoOrder::where(array("status" => "Pending"))->count();
        $totalOrders = \App\Order::count();
        $totalUsers = \App\User::where("id", "<>", \Illuminate\Support\Facades\Auth::user()->id)->count();
        $date = \Carbon\Carbon::today()->subDays(30);
        $totalUserfunds = \App\User::where("id", "<>", \Illuminate\Support\Facades\Auth::user()->id)->where('last_login', '>=', $date)->sum("funds");
        $ttotalSell = \App\Order::whereIn("status", array("COMPLETED", "PARTIAL"))->where('created_at', '>=', $date)->sum("price");
        $supportTicketOpen = \App\Models\Ticket::where(array("status" => "OPEN"))->count();
        $unreadMessages = \App\TicketMessage::where(array("is_read" => 0))->whereNotIn("user_id", array(\Illuminate\Support\Facades\Auth::user()->id))->count();
        $tktcnt = \App\Models\Ticket::where(['is_read' => 0])->count();
        $msgcnt = \App\TicketMessage::where(['is_read' => 0])->whereNotIn('user_id', [auth()->user()->id])->count();
        $msgcnt += $tktcnt;
        $orderprice = \App\Order::sum('price');
        $ordercost = \App\Order::sum('cost');
        $totalprofit = $orderprice - $ordercost;
        $torderprice = \App\Order::where('created_at', '>=', $date)->sum('price');
        $tordercost = \App\Order::where('created_at', '>=', $date)->sum('cost');
        $ttotalprofit = $torderprice - $tordercost;
        return view("admin.dash", compact("totalSell", "totalOrdersCompleted", "totalseopending", "msgcnt", "totalrefillpending", "totalOrdersPending", "totalOrdersCancelled", "totalUsers", "supportTicketOpen", "totalOrdersProcessing", "unreadMessages", "totalOrdersInProgress", "totalOrders", "totalUsers", "totalUserfunds", "totalprofit", "ttotalprofit"));
    }
    public function saveNote(\Illuminate\Http\Request $request)
    {
        setOption("admin_note", $request->input("admin_note"));
        return redirect("/admin");
    }

    public function refreshSystem(\Illuminate\Http\Request $request)
    {
        $url = url("/admin");
        // \Illuminate\Support\Facades\Artisan::call("config:cache");
        return redirect($url);
    }
}
