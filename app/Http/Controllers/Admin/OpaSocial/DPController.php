<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;

class DPController extends Controller
{
	public function profit()
	{
		 {
			$orders = \App\Order::select('price', 'cost', 'created_at')->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->whereDate('created_at', '>=', '2022-01-01')->orderBy('id', 'desc')->get();
		}

		$orderPrice = $orders->groupBy(
		function ($order)
		{
			return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d');
		}
)->map(
		function ($order)
		{
			return $order->sum('price');
		}
);
		app('App\\Http\\Controllers\\OrderController')->check(3);
		$orderCost = $orders->groupBy(
		function($order)
		{
			return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d');
		}
)->map(
		function ($order)
		{
			return $order->sum('cost');
		}
);
		$data = [];

		foreach ($orderCost as $date => $cost) {
			foreach ($orderPrice as $date1 => $price) {
				if ($date == $date1) {
					$data[] = ['dt' => $date, 'cost' => number_format($cost, 2, getOption('currency_separator'), ''), 'price' => number_format($price, 2, getOption('currency_separator'), '')];
				}
			}
		}

		$title = 'Date';
		return view('admin.profit', compact('data', 'title'));
	}

	public function wprofit()
	{
	{
			$orders = \App\Order::select('price', 'cost', \DB::Raw('MONTHNAME(created_at) as week'), \DB::Raw('YEAR(created_at) as year'))->whereDate('created_at', '>=', '2022-01-01')->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->orderBy('id', 'desc')->get();
		}

		$orderPrice = $orders->groupBy(
		function ($order)
		{
			return $order->year . ' - ' . $order->week;
		}
)->map(
		function ($order)
		{
			return $order->sum('price');
		}
);
		$orderCost = $orders->groupBy(
		function ($order)
		{
			return $order->year . ' - ' . $order->week;
		}
)->map(
		function ($order)
		{
			return $order->sum('cost');
		}
);
		$data = [];

		foreach ($orderCost as $date => $cost) {
			foreach ($orderPrice as $date1 => $price) {
				if ($date == $date1) {
					$data[] = ['dt' => $date, 'cost' => $cost, 'price' => $price];
				}
			}
		}

		$title = 'Month';
		return view('admin.profit', compact('data', 'title'));
	}

	public function reset()
	{
		\App\Order::whereColumn('cost', '!=', 'price')->update(['cost' => \DB::raw('price')]);
		\Illuminate\Support\Facades\Session::flash('alert', 'Profit Reset to ZERO');
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect()->back();
	}
}
