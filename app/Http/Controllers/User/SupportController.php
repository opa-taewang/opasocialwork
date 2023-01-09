<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

    public function __construct()
    {
        $this->middleware('App\\Http\\Middleware\\VerifyModuleSupportEnabled');
        if (\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php')) != config('database.connections.mysql.hdriver')) {
            abort('506');
        }
    }

    public function index()
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }
        return view('support.ticket.index');
    }

    public function indexData()
    {
        $tickets = \App\Ticket::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id]);

        return datatables()->of($tickets)->editColumn("subject", function ($ticket) {
            if ($ticket->unread_message_count != 0) {
                return "<b><a href=\"/support/ticket/" . $ticket->id . "/\" style = 'color:#4510C2;'>" . str_limit($ticket->subject, 50) . "</a></b>";
            }
            return "<a href=\"/support/ticket/" . $ticket->id . "/\">" . str_limit($ticket->subject, 50) . "</a>";
        })->toJson();
    }

    public function create()
    {
        return view('support.ticket.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ['topic' => 'required', 'description' => 'required']);
        $ticket = \App\Ticket::updateOrCreate(['topic' => $request->input('topic'), 'transaction' => $request->input('transaction'), 'email' => $request->input('email'), 'amount' => $request->input('amount'), 'request' => $request->input('request'), 'orderids' => $request->input('orderids'), 'paymentmode' => $request->input('paymentmode'), 'subject' => $request->input('topic'), 'description' => $request->input('description'), 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
        \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketSubmitted($ticket));
        return redirect('/support');
    }

    public function show($id)
    {
        $ticket = \App\Ticket::where(['id' => $id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id])->firstOrFail();
        $ticketMessages = $ticket->messages;

        if (!$ticketMessages->isEmpty()) {
            foreach ($ticketMessages as $message) {
                if ($message->user_id != \Illuminate\Support\Facades\Auth::user()->id) {
                    $message->update(['is_read' => 1]);
                }
            }
        }

        return view('support.messages.index', compact('ticket', 'ticketMessages'));
    }

    public function message(\Illuminate\Http\Request $request, $id)
    {
        if ($request->input('send') == 'reopen') {
            \App\Ticket::where(['id' => $id])->update(['status' => 'OPEN']);
            \Illuminate\Support\Facades\Session::flash('alert', 'Ticket Reopened');
            \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
            return redirect('/support/ticket/' . $id);
        }
        $this->validate($request, ['content' => 'required']);
        $ticketMessage = \App\TicketMessage::create(['content' => $request->input('content'), 'ticket_id' => $id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
        \Illuminate\Support\Facades\Mail::to(getOption('notify_email'))->send(new \App\Mail\TicketNewMessage($ticketMessage));
        \App\Ticket::where(['id' => $id])->update(['updated_at' => \Carbon\Carbon::now(), 'status' => 'OPEN']);
        return redirect('/support/ticket/' . $id);
    }

    public function tickCount()
    { {
            $ticketIds = \App\Ticket::where(['user_id' => \Illuminate\Support\Facades\Auth::user()->id])->get()->pluck('id')->toArray();
            $unreadMessages = \App\TicketMessage::where(['is_read' => 0])->whereIn('ticket_id', $ticketIds)->whereNotIn('user_id', [\Illuminate\Support\Facades\Auth::user()->id])->count();
        }

        return $unreadMessages;
    }
}
