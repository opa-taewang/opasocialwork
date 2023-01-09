<?php


namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
   // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

	public function __construct()
	{
		$this->middleware('VerifyModuleSupportEnabled');
		if(\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php'))!=config('database.connections.mysql.hdriver')){abort('506');}
	}

	public function index(\Illuminate\Http\Request $request)
	{
		mpc_m_c($request->server('SERVER_NAME'));
		return view('moderator.support.ticket.index');
	}

	public function indexData()
	{

		$tickets = \App\Ticket::with('user');
		return datatables()->of($tickets)->addColumn('action', 'moderator.support.ticket.index-buttons')->setRowClass(		function ($ticket)
		{
			return $ticket->is_read == 0 ? 'unreadRow' : '';
		})->toJson();
	}

	public function create()
	{
	}

	public function store(\Illuminate\Http\Request $request)
	{
	}

	public function show($id)
	{
		$ticket = \App\Ticket::findOrFail($id);
		$ticket->update(['is_read' => 1]);
		$ticketMessages = $ticket->messages;

		if (!$ticketMessages->isEmpty()) {
			foreach ($ticketMessages as $message) {
				if ($message->user_id != \Illuminate\Support\Facades\Auth::user()->id) {
					$message->update(['is_read' => 1]);
				}
			}
		}

		return view('moderator.support.messages.index', compact('ticket', 'ticketMessages'));
	}

	public function edit($id)
	{
		$ticket = \App\Ticket::findOrFail($id);
		return view('moderator.support.ticket.edit', compact('ticket'));
	}

	public function update(\Illuminate\Http\Request $request, $id)
	{
		$this->validate($request, ['subject' => 'required', 'description' => 'required']);
		\App\Ticket::where(['id' => $id])->update(['subject' => $request->input('subject'), 'description' => $request->input('description'), 'status' => $request->input('status')]);
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/moderator/support/tickets/' . $id . '/edit');
	}

	public function destroy($id)
	{
		$ticket = \App\Ticket::findOrFail($id);
		$ticket->delete();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/moderator/support/tickets');
	}

	public function message(\Illuminate\Http\Request $request, $id)
	{
		if ($request->input('send') == 'reopen') {
			\App\Ticket::where(['id' => $id])->update(['status' => 'OPEN']);
			\Illuminate\Support\Facades\Session::flash('alert', 'Ticket Reopened');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
			return redirect('/moderator/support/tickets/' . $id);
		}

		if ($request->input('send') != 'close') {
			if ($request->input('content') == '') {
				return redirect()->back()->withInput()->withErrors(['content' => 'New Message is mandatory']);
			}

			\App\TicketMessage::create(['content' => $request->input('content'), 'ticket_id' => $id, 'user_id' => \Illuminate\Support\Facades\Auth::user()->id]);
		}

		if ($request->input('send') != 'send') {
			\App\Ticket::where(['id' => $id])->update(['status' => 'CLOSED']);
			\Illuminate\Support\Facades\Session::flash('alert', 'Ticket Closed');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		}

		return redirect('/moderator/support/tickets/' . $id);
	}

	public function tickCount()
	{
		 {
			$tktcnt = \App\Ticket::where(['is_read' => 0])->count();
			$msgcnt = \App\TicketMessage::where(['is_read' => 0])->whereNotIn('user_id', [auth()->user()->id])->count();
			$msgcnt += $tktcnt;
		}

		return $msgcnt;
	}

	public function indexFilter($topic)
	{
		return view('moderator.support.ticket.index', compact('topic'));
	}

	public function indexFilterData($topic)
	{
		$tickets = \App\Ticket::with('user')->where(['topic' => $topic]);
		return datatables()->of($tickets)->addColumn('action', 'moderator.support.ticket.index-buttons')->toJson();
	}

	public function indexNFilter($topic)
	{
		return view('moderator.support.ticket.index', compact('topic'));
	}

	public function indexNFilterData($topic)
	{
		$tickets = \App\Ticket::with('user')->whereNotIn('status', ['CLOSED'])->get();
		$tBucket = \App\Ticket::where('status', 'NULLED')->get();
		$admins = \App\User::where('role', 'ADMIN')->get();

		if ($topic == 'new') {
			foreach ($tickets as $ticket) {
				$flag = true;

				foreach ($ticket->messages as $message) {
					foreach ($admins as $admin) {
						if ($message->user_id == $admin->id) {
							$flag = false;
							break;
						}
					}
				}

				if ($flag) {
					$tBucket->push($ticket);
				}
			}
		}
		else if ($topic == 'message') {
			foreach ($tickets as $ticket) {
				$flag1 = false;

				foreach ($ticket->messages as $message) {
					$flag = true;

					foreach ($admins as $admin) {
						if ($message->user_id == $admin->id) {
							$flag = false;
							break;
						}
					}

					if ($flag) {
						if ($message->is_read == '0') {
							$flag1 = true;
							break;
						}
					}
				}

				if ($flag1) {
					$tBucket->push($ticket);
				}
			}
		}
		else if ($topic == 'open') {
			$tBucket = \App\Ticket::with('user')->whereNotIn('status', ['CLOSED'])->get();
		}
		else if ($topic == 'all') {
			$tBucket = \App\Ticket::with('user');
		}
		else if ($topic == 'admin') {
			$tBucket = \App\Ticket::with('user')->whereNotIn('status', ['CLOSED'])->where(['assign_admin' => 1])->get();
		}

		return datatables()->of($tBucket)->addColumn('action', 'moderator.support.ticket.index-buttons')->toJson();
	}
}
