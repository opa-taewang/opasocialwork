<?php
/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */
namespace App\Http\Controllers\Moderator\OpaSocial;

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

class OrderController extends Controller
{
	private $order_statuses = [];

	public function __construct()
	{
		$this->order_statuses = config('constants.ORDER_STATUSES');
	}

	public function index()
	{
		return view('moderator.orders.index');
	}

	public function indexData()
	{
		$orders = \App\Order::with('user', 'package.service');
		return datatables()->of($orders)->addColumn('action', 'moderator.orders.index-buttons')->addColumn('bulk', function($order) {
			$disabled = '';

			if (in_array(strtoupper($order->status), ['COMPLETED', 'PARTIAL', 'REFUNDED', 'CANCELLED', 'REFILLING', 'CANCELLING'])) {
				$disabled = 'disabled';
			}

			return '<input type=\'checkbox\' ' . $disabled . ' class=\'input-sm row-checkbox\' name=\'order_id[' . $order->id . ']\' value=\'' . $order->id . '\'>';
		})->editColumn('price', function($order) {
			return getOption('currency_symbol') . number_formats($order->price, 2, getOption('currency_separator'), '');
		})->editColumn('start_counter', function($order) {
			return '<input type=\'text\' style=\'width: 60px;\' readonly class=\'form-control input-sm row-edit\' value=\'' . $order->start_counter . '\' name=\'start_counter[' . $order->id . ']\'>';
		})->editColumn('remains', function($order) {
			return '<input type=\'text\' style=\'width: 60px;\' readonly class=\'form-control input-sm row-edit\' value=\'' . $order->remains . '\' name=\'remains[' . $order->id . ']\'>';
		})->editColumn('status', function($order) {
			$html = '<select class=\'form-control row-edit\' readonly name=\'status[' . $order->id . ']\'>';

			foreach ($this->order_statuses as $status) {
				if ($status == strtoupper($order->status)) {
					$html .= '<option selected value=\'' . $status . '\'>' . $status . '</option>';
				}
				else {
					$html .= '<option value=\'' . $status . '\'>' . $status . '</option>';
				}
			}

			return $html;
		})->editColumn('link', function($order) {
			return '<a rel="noopener noreferrer" href="' . getOption('anonymizer') . $order->link . '" target="_blank">' . str_limit($order->link, 30) . '</a>';
		})->editColumn('created_at', function($order) {
			return '<span class=\'no-word-break\'>' . $order->created_at . '</span>';
		})->rawColumns(['action', 'bulk', 'start_counter', 'remains', 'status', 'link', 'created_at'])->toJson();
	}

	public function indexFilter($status)
	{
		return view('moderator.orders.index', compact('status'));
	}

	public function indexFilterData($status)
	{
		$orders = \App\Order::with('user', 'package.service')->where(['status' => strtoupper($status)]);
		return datatables()->of($orders)->addColumn('action', 'moderator.orders.index-buttons')->addColumn('bulk', function($order) {
			$disabled = '';

			if (in_array(strtoupper($order->status), ['COMPLETED', 'PARTIAL', 'REFUNDED', 'CANCELLED', 'REFILLING', 'CANCELLING'])) {
				$disabled = 'disabled';
			}

			return '<input type=\'checkbox\' ' . $disabled . ' class=\'input-sm row-checkbox\' name=\'order_id[' . $order->id . ']\' value=\'' . $order->id . '\'>';
		})->editColumn('price', function($order) {
			return getOption('currency_symbol') . number_formats($order->price, 2, getOption('currency_separator'), '');
		})->editColumn('start_counter', function($order) {
			return '<input type=\'text\' style=\'width: 60px;\' readonly class=\'form-control input-sm row-edit\' value=\'' . $order->start_counter . '\' name=\'start_counter[' . $order->id . ']\'>';
		})->editColumn('remains', function($order) {
			return '<input type=\'text\' style=\'width: 60px;\' readonly class=\'form-control input-sm row-edit\' value=\'' . $order->remains . '\' name=\'remains[' . $order->id . ']\'>';
		})->editColumn('status', function($order) {
			$html = '<select class=\'form-control row-edit\' readonly name=\'status[' . $order->id . ']\'>';

			foreach ($this->order_statuses as $status) {
				if ($status == strtoupper($order->status)) {
					$html .= '<option selected value=\'' . $status . '\'>' . $status . '</option>';
				}
				else {
					$html .= '<option value=\'' . $status . '\'>' . $status . '</option>';
				}
			}

			return $html;
		})->editColumn('link', function($order) {
			return '<a rel="noopener noreferrer" href="' . getOption('anonymizer') . $order->link . '" target="_blank">' . str_limit($order->link, 30) . '</a>';
		})->editColumn('created_at', function($order) {
			return '<span class=\'no-word-break\'>' . $order->created_at . '</span>';
		})->rawColumns(['action', 'bulk', 'start_counter', 'remains', 'status', 'link', 'created_at'])->toJson();
	}

	public function create()
	{
		return redirect('/moderator/orders');
	}

	public function store(\Illuminate\Http\Request $request)
	{
		return redirect('/moderator/orders');
	}

	public function show($id)
	{
		$order = \App\Order::findOrFail($id);
		$apis = \App\API::all();
		return view('moderator.orders.show', compact('order', 'apis'));
	}

	public function edit($id)
	{
		$order = \App\Order::findOrFail($id);
		$apis = \App\API::all();
		return view('moderator.orders.edit', compact('order', 'apis'));
	}

	public function update(\Illuminate\Http\Request $request, $id)
	{
		$order = \App\Order::findOrFail($id);
		$user = \App\User::find($order->user_id);
		$visit = Visit::where('refVid','=',$order->user_id)->limit(1)->get();
        $commission = Commission::all();
		$orderPrice = $order->price;
		if (($request->input('status') == 'CANCELLED') || ($request->input('status') == 'REFUNDED')) {
			$user->funds = $user->funds + $orderPrice;
			$user->save();
		}
		else if ($request->input('status') == 'PARTIAL') {
			$remains = (1 < $request->input('remains') ? $request->input('remains') : 1);
			$quantity = $order->quantity;
			$price_per_item = \App\Package::find($order->package_id)->price_per_item;
			$userPackagePrice = \App\UserPackagePrice::where(['user_id' => $order->user_id, 'package_id' => $order->package_id])->first();

			if (!is_null($userPackagePrice)) {
				$price_per_item = $userPackagePrice->price_per_item;
			}

			if ($remains < $quantity) {
				$refundAmount = (double) $price_per_item * $remains;
				$refundAmount = number_formats($refundAmount, 2, '.', '');

				if (0 < $refundAmount) {
					$orderPrice = $orderPrice - $refundAmount;
					$user->funds = $user->funds + $refundAmount;
					$user->save();
				}
			}
		}

//Referral calculation code
        if(count($visit)>0 && $orderPrice>=$commission[0]->min_payout){
        $calAmt= ($orderPrice-($orderPrice - ($orderPrice *($commission[0]->commission_val/100))));
        $refUid= $visit[0]->refUid;
        $refuser = User::findOrFail($refUid);
        $refuser->funds = $refuser->funds + $calAmt;
        $refuser->save();
        $affiliateTransaction = new AffiliateTransaction;
        $affiliateTransaction->package_id = $order->package_id;
        $affiliateTransaction->refUid = $refUid;
        $affiliateTransaction->buyUid = $order->user_id;
        $affiliateTransaction->price = $orderPrice;
        $affiliateTransaction->transferedFund = $calAmt;
        $affiliateTransaction->save();
        }

		$api_id = (!!$request->input('api_id') ? $request->input('api_id') : NULL);
		$status = (!is_null($request->input('status')) ? $request->input('status') : $order->status);
		$order->status = $status;
		$order->start_counter = $request->input('start_counter');
		$order->remains = $request->input('remains');
		$order->api_id = $api_id;
		$order->price = $orderPrice;
		$order->link = $request->input('link');
		$order->custom_comments = $request->input('custom_comments');
		$order->save();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/moderator/orders/' . $id . '/edit');
	}

	public function destroy($id)
	{
		$order = \App\Order::findOrFail($id);

		if (in_array(strtoupper($order->status), ['COMPLETED', 'PARTIAL'])) {
			\Illuminate\Support\Facades\Session::flash('alert', __('messages.order_completed_cannot_delete'));
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/moderator/orders');
		}
		else if ($order->status === 'Pending') {
			\Illuminate\Support\Facades\Session::flash('alert', __('messages.order_processing_cannot_delete'));
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/moderator/orders');
		}

		$order->delete();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/moderator/orders');
	}

	public function completeOrder($id)
	{
		$order = \App\Order::find($id);
		$success = false;

		if (!is_null($order)) {
			$order->status = 'COMPLETED';
			$order->save();
			$success = true;
		}

		return response()->json(['success' => $success, 'status' => $order->status]);
	}

	public function bulkUpdate(\Illuminate\Http\Request $request)
	{
		$orderIds = $request->input('order_id');
		$startCounters = $request->input('start_counter');
		$remains = $request->input('remains');
		$statuses = $request->input('status');

		foreach ($orderIds as $id) {
			$order = \App\Order::find($id);
			$user = \App\User::find($order->user_id);
			$orderPrice = $order->price;
			if ((strtoupper($statuses[$id]) == 'CANCELLED') || (strtoupper($statuses[$id]) == 'REFUNDED')) {
				$user->funds = $user->funds + $orderPrice;
				$user->save();
			}
			else if (strtoupper($statuses[$id]) == 'PARTIAL') {
				if ($remains[$id] < 1) {
					continue;
				}

				$price_per_item = \App\Package::find($order->package_id)->price_per_item;
				$userPackagePrice = \App\UserPackagePrice::where(['user_id' => $order->user_id, 'package_id' => $order->package_id])->first();

				if (!is_null($userPackagePrice)) {
					$price_per_item = $userPackagePrice->price_per_item;
				}

				if ($remains[$id] < $order->quantity) {
					$refundAmount = (double) $price_per_item * $remains[$id];
					$refundAmount = number_formats($refundAmount, 2, '.', '');

					if (0 < $refundAmount) {
						$orderPrice = $orderPrice - $refundAmount;
						$user->funds = $user->funds + $refundAmount;
						$user->save();
					}
				}
			}

			$order->start_counter = $startCounters[$id];
			$order->remains = $remains[$id];
			$order->status = $statuses[$id];
			$order->price = $orderPrice;
			$order->save();
		}

		\Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/moderator/orders');
	}
}
