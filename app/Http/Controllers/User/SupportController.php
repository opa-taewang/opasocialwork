<?php


namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Mail\TicketSubmitted;
use App\Models\TicketMessage;
use App\Mail\TicketNewMessage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class SupportController extends Controller
{
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tickets = Ticket::where(['user_id' => Auth::user()->id])->orderBy('updated_at', 'desc')->get();
        // dd($tickets);
        return view('main.user.support.ticket.all', compact('tickets'));
    }

    public function create()
    {
        return view('main.user.support.ticket.new');
    }

    public function store(Request $request)
    {
        $content = $this->ticketTest($request);
        $ticket = Ticket::updateOrCreate(['subject' => $request->input('subject'), 'contents' => $content, 'description' => $request->input('description'), 'user_id' => Auth::user()->id]);
        // Mail::to(getOption('notify_email'))->send(new TicketSubmitted($ticket));
        if (!$ticket) {
            return false;
        }
        Session::flash('success', (object) [
            'message' => 'Ticket Submitted',
            // 'class' => 'success'
        ]);
        // toastr('Ticket Submitted', 'success');
        return true;
    }

    public function show($id)
    {

        $ticket = Ticket::where(['id' => $id, 'user_id' => Auth::user()->id])->firstOrFail();
        $ticketMessages = $ticket->messages;
        // dd($ticketMessages);

        $lastId = count($ticketMessages) > 0 ? $ticketMessages[count($ticketMessages) - 1]->id : '';

        if (!$ticketMessages->isEmpty()) {
            foreach ($ticketMessages as $message) {
                if ($message->user_id != Auth::user()->id) {
                    $message->update(['is_read' => 1]);
                }
            }
        }

        return view('main.user.support.message.show', compact('ticket', 'ticketMessages', 'lastId'));
    }

    public function message(Request $request, $id)
    {
        if ($request->input('send') == 'reopen') {
            Ticket::where(['id' => $id])->update(['status' => 'OPEN']);
            Session::flash('alert', 'Ticket Reopened');
            Session::flash('alertClass', 'success');
            return redirect()->route('user.ticket.show', $id);
        }
        $this->validate($request, ['content' => 'required']);
        $ticketMessage = TicketMessage::create(['content' => $request->input('content'), 'ticket_id' => $id, 'user_id' => Auth::user()->id]);
        dd(Mail::to(getOption('notify_email'))->send(new TicketNewMessage($ticketMessage)));
        Ticket::where(['id' => $id])->update(['updated_at' => Carbon::now(), 'status' => 'OPEN']);
        return redirect()->route('user.ticket.show', $id);
    }

    public function tickCount()
    { {
            $ticketIds = Ticket::where(['user_id' => Auth::user()->id])->get()->pluck('id')->toArray();
            $unreadMessages = TicketMessage::where(['is_read' => 0])->whereIn('ticket_id', $ticketIds)->whereNotIn('user_id', [Auth::user()->id])->count();
        }

        return $unreadMessages;
    }

    private function ticketTest(Request $request)
    {
        $content = [];
        if ($request->input('subject') == 'Order') {
            $request->validate([
                'subject' => ['required'],
                'orderId' => ['required'],
                'request' => ['required'],
            ]);
            $content = [
                'Order ID' => $request->input('orderId'),
                'Request' => $request->input('request'),
            ];
        } elseif ($request->input('subject') == 'Payment') {
            $request->validate([
                'subject' => ['required'],
                'paymentMethod' => ['required'],
                'transactionId' => ['required'],
                'paymentEmail' => ['required'],
                'paymentAmount' => ['required'],
                'description' => ['required'],
            ]);

            $content = [
                'Payment Method' => $request->input('paymentMethod'),
                'Transaction ID' => $request->input('transactionId'),
                'Payment Email' => $request->input('paymentEmail'),
                'Payment Amount' => $request->input('paymentAmount'),
            ];
        } elseif ($request->input('subject') == 'Child Panel') {
            $request->validate([
                'subject' => ['required'],
                'description' => ['required'],
            ]);
        } elseif ($request->input('subject') == 'API') {
            $request->validate([
                'subject' => ['required'],
                'websiteUrl' => ['required'],
                'contact' => ['required'],
                'monthlySell' => ['required'],
                'serviceId' => ['required'],
                'description' => ['required']
            ]);

            $content = [
                'Website Url' => $request->input('websiteUrl'),
                'Email/Phone' => $request->input('contact'),
                'Monthly Sell' => $request->input('monthlySell'),
                'Service ID' => $request->input('serviceId'),
            ];
        } elseif ($request->input('subject') == 'Bug') {
            $request->validate([
                'subject' => ['required'],
                'bug' => ['required'],
                'description' => ['required'],
            ]);

            $content = [
                'bug' => $request->input('bug')
            ];
        } elseif ($request->input('subject') == 'Request') {
            $request->validate([
                'subject' => ['required'],
                'request' => ['required'],
                'description' => ['required'],
            ]);
            $content = [
                'Request' => $request->input('request'),
            ];
        } elseif ($request->input('subject') == 'Point') {
            $request->validate([
                'subject' => ['required'],
                'request' => ['required'],
                'description' => ['required'],
            ]);
            $content = [
                'Request' => $request->input('request'),
            ];
        } elseif ($request->input('subject') == 'Number') {
            // $request->validate([
            //     'description' => ['required'],
            //     'autoUsername' => ['required'],
            //     'autoUsername' => ['required'],
            // ]);
            abort('404');
        } elseif ($request->input('subject') == 'Other') {
            $request->validate([
                'subject' => ['required'],
                'description' => ['required'],
            ]);
        }
        return json_encode($content);
    }
}
