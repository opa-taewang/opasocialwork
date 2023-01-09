<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
	public function index()
	{
		return view('admin.services.index');
	}
 // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

public function __construct()
    {
    if(\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php'))!=config('database.connections.mysql.hdriver')){abort('506');}
    }
	public function indexData()
	{
		 {
			$services = \App\Service::where('id', '>', 0);
		}

		return datatables()->of($services)->addColumn('details_url',
		function($service)
		{
			return url('admin/services/' . $service->id . '/details');
		}
)->addColumn('action', 'admin.services.index-buttons')->editColumn('is_subscription_allowed',
		function ($service)
		{
			return $service->is_subscription_allowed == 1 ? 'Yes' : 'No';
		}
)->addColumn('ids',
		function ($service)
		{
			return '<input type=\'checkbox\' class=\'input-sm row-checkboxsvc\' name=\'service_id[' . $service->id . ']\' value=\'' . $service->id . '\' style=\'height:13px\'>&nbsp;' . $service->id;
		}
)->rawColumns(['ids', 'action'])->toJson();
	}

	public function create()
	{
		return view('admin.services.create');
	}

	public function store(\Illuminate\Http\Request $request)
	{
		mpc_m_c($request->server('SERVER_NAME'));
		$this->validate($request, ['name' => 'required', 'description' => 'required']);
		$service = \App\Service::create(['name' => $request->input('name'), 'slug' => str_slug($request->input('name')), 'description' => $request->input('description'), 'status' => $request->input('status')]);
		$maxpos = \App\Service::max('position');
		$service->position = $maxpos + 10;
		$service->save();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.created'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/services/create');
	}

	public function show($id)
	{
	}

	public function edit($id)
	{
		$service = \App\Service::findOrFail($id);
		return view('admin.services.edit', compact('service'));
	}

	public function update(\Illuminate\Http\Request $request, $id)
	{
		$this->validate($request, ['name' => 'required', 'description' => 'required']);
		$service = \App\Service::findOrFail($id);
		$service->name = $request->input('name');
		$service->slug = str_slug($request->input('name'));
		$service->description = $request->input('description');
		$service->position = $request->input('position');
		$service->status = $request->input('status');
		$service->save();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('admin/services/' . $id . '/edit');
	}

	public function destroy($id)
	{
		$service = \App\Service::findOrFail($id);

		try {
			$service->delete();
		}
		catch (\Illuminate\Database\QueryException $ex) {
			\Illuminate\Support\Facades\Session::flash('alert', __('messages.service_have_packages'));
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger');
			return redirect('/admin/services');
		}

		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/services');
	}

	public function details(\App\Service $service)
	{

		$data = $service->packages;
		return datatables()->of($data)->editColumn('price_per_item', 		function ($pack)
		{
			return getOption('currency_symbol') . number_formats($pack->price_per_item * getOption('display_price_per'), 2, getOption('currency_separator'), '');
		}
)->editColumn('minimum_quantity',
		function ($pack)
		{
			return $pack->minimum_quantity . ' - ' . $pack->maximum_quantity;
		}
)->editColumn('id',
		function ($package)
		{
			return '<input type=\'checkbox\' class=\'input-sm row-checkboxpkg row-checkboxpkg-' . $package->service_id . '\' name=\'package_id[' . $package->id . ']\' value=\'' . $package->id . '\' style=\'height:13px\'>&nbsp;' . '&nbsp;' . $package->id;
		}
)->editColumn('apiname',
		function ($pack)
		{
	          $apipack = $pack->preferred_api_id;
	           $apis = \App\API::where('id',$apipack)->pluck('name')->toArray();
	           return $apis;

		}
)->addColumn('action',
		function ($package)
		{
			$id = $package->id;
			return view('admin.services.package-buttons', compact('id'));
		}
)->rawColumns(['id', 'action'])->toJson();
	}

	public function ups(\App\Service $service)
	{
		$twopckg = \App\Service::where('position', '<=', $service->position)->orderBy('position', 'desc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/services/');
	}

	public function downs(\App\Service $service)
	{
		$twopckg = \App\Service::where('position', '>=', $service->position)->orderBy('position', 'asc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/services/');
	}

	public function up(\App\Package $package)
	{
		$twopckg = \App\Package::where('position', '<=', $package->position)->where('service_id', '=', $package->service_id)->orderBy('position', 'desc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/services/');
	}

	public function down(\App\Package $package)
	{
		$twopckg = \App\Package::where('position', '>=', $package->position)->where('service_id', '=', $package->service_id)->orderBy('position', 'asc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/services/');
	}

	public function activeSP($service, $package)
	{
		\App\Service::whereIn('id', explode(',', $service))->update(['status' => 'ACTIVE']);
		\App\Package::whereIn('id', explode(',', $package))->update(['status' => 'ACTIVE']);
	}

	public function inactiveSP($service, $package)
	{
		\App\Service::whereIn('id', explode(',', $service))->update(['status' => 'INACTIVE']);
		\App\Package::whereIn('id', explode(',', $package))->update(['status' => 'INACTIVE', 'preferred_api_id' => NULL]);
	}

	public function deleteSP($service, $package)
	{
		 {
			\App\Package::whereIn('service_id', explode(',', $service))->update(['service_id' => '0']);
			\App\Order::whereIn('package_id', explode(',', $package))->update(['package_id' => '0']);
		}

		try {
			\App\Service::whereIn('id', explode(',', $service))->delete();
			\App\Package::whereIn('id', explode(',', $package))->delete();
		}
		catch (\Exception $e) {
			\Illuminate\Support\Facades\Session::flash('alert', 'Delete Failed');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/admin/services');
		}

		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/services');
	}

	public function getTopServices() {
	    return view('admin.services.topIndex');
	}
	public function TopindexData()
	{
		$services = \App\Service::where('top', 1)->get();
		$packages = \App\Package::join('services', 'packages.service_id', '=', 'services.id')->select('packages.*', 'services.name as sname', 'services.position as sposition')->where('packages.id', '!=', 0)->where('packages.top',1)->orderBy('services.position')->orderBy('packages.position')->getQuery()->get();
		return datatables()->of($packages)->editColumn('id', function($package) {
			return $package->id . '<br><a href="/admin/package/' . $package->id . '/up" class="btn btn-xs btn-success"><i class="fas fa-caret-square-up"></i></a>&nbsp;<a href="/admin/package/' . $package->id . '/down" class="btn btn-xs btn-success"><i class="fas fa-caret-square-down"></i></a>';
		})->addColumn('action', 'admin.services.index-tops-buttons')->editColumn('description', '{{ str_limit($description,50) }}')->editColumn('price_per_item', '{{ getOption(\'currency_symbol\') . number_formats(($price_per_item * getOption(\'display_price_per\')),2, getOption(\'currency_separator\'), \'\') }}')->rawColumns(['id', 'action'])->toJson();
	}
	public function createTopServices() {
	    $services=\App\Service::all();
	    $packages=\App\Package::where('top',0)->get();
	    return view('admin.services.topcreate',compact('services','packages'));
	}
	public function storeTopServices(\Illuminate\Http\Request $request)
	{
	    mpc_m_c($request->server('SERVER_NAME'));
		$this->validate($request, ['service' => 'required', 'package' => 'required']);
		$sid=$request->service;
		$pid=$request->package;
		$package=\App\Package::findOrFail($pid);
		if($package->service_id != $sid)
		{
		    \Illuminate\Support\Facades\Session::flash('alert', 'Mismatch, The selected package is not related to service.');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/admin/top-services/create');
		}
		if(\App\Service::where('top',1)->count() > 4) {
		    \Illuminate\Support\Facades\Session::flash('alert', 'Sorry you can create maximum 4 top services.');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/admin/top-services/create');
		}
		if(\App\Package::where('top',1)->where('service_id',$sid)->count() > 2) {
		    \Illuminate\Support\Facades\Session::flash('alert', 'Sorry you can create maximum 3 packages of a top service.');
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
			return redirect('/admin/top-services/create');
		}
		$service=\App\Service::findOrFail($sid);
		$service->top=1;
		$service->save();
		$package=\App\Package::findOrFail($pid);
		$package->top=1;
		$package->save();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.created'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/top-services/create');
	}
	public function editTopServices($id) {
	    $package=\App\Package::findOrFail($id);
	    $service=\App\Service::findOrFail($package->id);
	    $services=\App\Service::all();
	    $packages=\App\Package::where('top',0)->get();
	    return view('admin.services.topedit',compact('services','packages','service','package'));
	}
	public function deleteTopServices($id)
	{
	    $package=\App\Package::findOrFail($id);
	    try {
	        $package->top=0;
			$package->save();
			if(\App\Package::where('service_id',$package->service_id)->where('top',1)->count()==0)
			{
			    $service=\App\Service::findOrFail($package->service_id);
			    $service->top=0;
			    $service->save();
			}
		}
		catch (\Illuminate\Database\QueryException $ex) {
			\Illuminate\Support\Facades\Session::flash('alert', __('Opps, System encountered an problem'));
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger');
			return redirect('/admin/top-services');
		}

		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/top-services');
	}
}
