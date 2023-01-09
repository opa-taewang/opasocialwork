<?php

namespace App\Http\Controllers\Admin\OpaSocial;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use File;
class PackageController extends Controller
{
	public function index()
	{
		return view('admin.packages.index');
	}

	public function indexData()
	{
		{
			$packages = \App\Package::join('services', 'packages.service_id', '=', 'services.id')->select('packages.*', 'services.name as sname', 'services.position as sposition')->where('packages.id', '!=', 0)->orderBy('services.position')->orderBy('packages.position')->getQuery()->get();
		}

		return datatables()->of($packages)->editColumn('id', function($package) {
			return $package->id . '<br><a href="/admin/package/' . $package->id . '/up" class="btn btn-xs btn-success"><i class="fas fa-caret-square-up"></i></a>&nbsp;<a href="/admin/package/' . $package->id . '/down" class="btn btn-xs btn-success"><i class="fas fa-caret-square-down"></i></a>';
		})->addColumn('action', 'admin.packages.index-buttons')->editColumn('description', '{{ str_limit($description,50) }}')->editColumn('price_per_item', '{{ getOption(\'currency_symbol\') . number_formats(($price_per_item * getOption(\'display_price_per\')),2, getOption(\'currency_separator\'), \'\') }}')->rawColumns(['id', 'action'])->toJson();
	}

	public function create()
	{
		$apis = \App\API::all();
		$services = \App\Service::where(['status' => 'ACTIVE'])->orderBy('position')->get();
		return view('admin.packages.create', compact('services', 'apis'));
	}

	public function store(\Illuminate\Http\Request $request)
	{
		$this->validate($request, ['service_id' => 'required', 'name' => 'required', 'price_per_item' => 'required|numeric', 'minimum_quantity' => 'required|numeric', 'maximum_quantity' => 'required|numeric', 'description' => 'required', 'features' => 'required']);
		$price_per_item = $request->input('price_per_item');
		$minimum_quantity = $request->input('minimum_quantity');
		$min_regular_price = (double) $price_per_item * $minimum_quantity;
		$min_regular_price = number_formats($min_regular_price, 2, '.', '');


        $servicety = $request->input('service_id');
	     $servicesz = \App\Service::where(['id' => $servicety])->value('servicetype');
		$preferred_api_id = (!!$request->input('preferred_api_id') ? $request->input('preferred_api_id') : NULL);
	    $package =	\App\Package::create(['service_id' => $request->input('service_id'),'refill_time' => $request->input('refill_time'),'refill_period' => $request->input('refill_period'), 'name' => $request->input('name'), 'slug' => str_slug($request->input('name')), 'price_per_item' => $request->input('price_per_item'), 'minimum_quantity' => $request->input('minimum_quantity'), 'maximum_quantity' => $request->input('maximum_quantity'), 'refillbtn' => $request->input('refillbtn'), 'features' => $request->input('features'),'license_codes' => $request->input('license_codes'), 'position' => $request->input('position'), 'status' => $request->input('status'), 'preferred_api_id' => $preferred_api_id, 'custom_comments' => $request->input('custom_comments'), 'description' => $request->input('description'), 'packagetype' => $servicesz]);
		if(($preferred_api_id) != NULL ){
        $apiPackages = $request->input("seller_package_id");
        $pid = $package->id;
        $insert = array(  );

        $insert[] = array( "package_id" => $pid, "api_package_id" => $apiPackages, "api_id" => $preferred_api_id, "created_at" => \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now() );

        if( !empty($insert) )
        {
            \DB::table("api_mappings")->insert($insert);
        }
        }
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.created'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/packages/create');
	}
public function ajaxPost(\Illuminate\Http\Request $request)
	{
	    if(!$request->ajax)
		$this->validate($request, ['service_id' => 'required', 'name' => 'required', 'price_per_item' => 'required|numeric', 'minimum_quantity' => 'required|numeric', 'maximum_quantity' => 'required|numeric', 'description' => 'required','features' => 'required']);

		$price_per_item = $request->input('price_per_item');
		$minimum_quantity = $request->input('minimum_quantity');
		$min_regular_price = (double) $price_per_item * $minimum_quantity;
		$min_regular_price = number_formats($min_regular_price, 2, '.', '');


         $servicety = $request->input('service_id');
	     $servicesz = \App\Service::where(['id' => $servicety])->value('servicetype');
		$preferred_api_id = (!!$request->input('preferred_api_id') ? $request->input('preferred_api_id') : NULL);
		$path='';
		$script_name='';
		if($request->hasFile('file')) {
		    $file=$request->file('file');
		    $script_name=$file->getClientOriginalName();
		    $path=$file->store('storage/uploads');
		}
		\App\Package::create(['service_id' => $request->input('service_id'), 'name' => $request->input('name'), 'slug' => str_slug($request->input('name')), 'price_per_item' => $request->input('price_per_item'), 'minimum_quantity' => $request->input('minimum_quantity'), 'maximum_quantity' => $request->input('maximum_quantity'),'refillbtn' => $request->input('refillbtn'),'script_name' => $script_name,'script' => $path, 'features' => $request->input('features'), 'license_codes' => $request->input('license_codes'), 'position' => $request->input('position'), 'status' => $request->input('status'), 'preferred_api_id' => $preferred_api_id, 'custom_comments' => $request->input('custom_comments'), 'description' => $request->input('description'), 'packagetype' => $servicesz ]);
		if(($preferred_api_id) != NULL ){
        $apiPackages = $request->input("seller_package_id");

        $insert = array(  );

        $insert[] = array( "package_id" => $id, "api_package_id" => $apiPackages, "api_id" => $preferred_api_id, "created_at" => \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now() );

        if( !empty($insert) )
        {
            \App\ApiMapping::where(array( "package_id" => $id ))->delete();
            \DB::table("api_mappings")->insert($insert);
        }
        }
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.created'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		die('success');
	}


	public function show($id)
	{
		return redirect('/admin/packages');
	}

	public function edit($id)
	{
		$package = \App\Package::findOrFail($id);
		$apis = \App\API::all();
		$services = \App\Service::where(['status' => 'ACTIVE'])->orderBy('position')->get();

    	$api_package_id =\App\ApiMapping::where(['package_id' => $id])->pluck('api_package_id')->toArray();
		$api_package_id =implode($api_package_id);

		return view('admin.packages.edit', compact('services', 'package', 'apis', 'api_package_id'));

	}

	public function update(\Illuminate\Http\Request $request, $id)
	{
	    if(!$request->ajax)
		    $this->validate($request, ['service_id' => 'required', 'name' => 'required', 'price_per_item' => 'required|numeric', 'minimum_quantity' => 'required|numeric', 'maximum_quantity' => 'required|numeric', 'description' => 'required', 'features' => 'required']);

		$price_per_item = $request->input('price_per_item');
		$minimum_quantity = $request->input('minimum_quantity');
		$min_regular_price = (double) $price_per_item * $minimum_quantity;
		$min_regular_price = number_formats($min_regular_price, 2, '.', '');


		$package = \App\Package::findOrFail($id);
		$preferred_api_id = (!!$request->input('preferred_api_id') ? $request->input('preferred_api_id') : NULL);
		if(($preferred_api_id) != NULL ){
        $apiPackages = $request->input("seller_package_id");

        $insert = array(  );

        $insert[] = array( "package_id" => $id, "api_package_id" => $apiPackages, "api_id" => $preferred_api_id, "created_at" => \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now() );

        if( !empty($insert) )
        {
            \App\ApiMapping::where(array( "package_id" => $id ))->delete();
            \DB::table("api_mappings")->insert($insert);
        }
        }
		$package->service_id = $request->service_id;
		$package->name = $request->name;
		$package->slug = str_slug($request->name);
		$package->price_per_item = $request->price_per_item;
		$package->cost_per_item = $request->cost_per_item;
		$package->seller_cost = $request->seller_cost;
		$package->minimum_quantity = $request->minimum_quantity;
		$package->maximum_quantity = $request->maximum_quantity;
		$package->refillbtn = $request->refillbtn;
		$package->refill_time = $request->input('refill_time');
		$package->refill_period = $request->input('refill_period');
		$package->position = $request->position;
		$package->features = $request->features;
		$package->license_codes = $request->license_codes;
		$package->status = $request->status;
		$package->description = $request->description;
		$package->preferred_api_id = $preferred_api_id;
		$package->custom_comments = $request->custom_comments;
		if($request->hasFile('file')) {
		    $oldscript=$package->script;
		    $file=$request->file('file');
		    $script_name=$file->getClientOriginalName();
		    $path=$file->store('storage/uploads');
		    $package->script=$path;
		    $package->script_name=$script_name;
		    if (File::exists($oldscript)) {
                File::delete($oldscript);
            }
		}
		$package->save();
		\Illuminate\Support\Facades\Session::flash('alert', __('messages.updated'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('admin/packages/' . $id . '/edit');
	}


	public function destroy($id)
	{
		$package = \App\Package::findOrFail($id);

		try {
			$package->delete();
		}
		catch (\Illuminate\Database\QueryException $ex) {
			\Illuminate\Support\Facades\Session::flash('alert', __('messages.package_have_orders'));
			\Illuminate\Support\Facades\Session::flash('alertClass', 'danger');
			return redirect('/admin/packages');
		}

		\Illuminate\Support\Facades\Session::flash('alert', __('messages.deleted'));
		\Illuminate\Support\Facades\Session::flash('alertClass', 'success');
		return redirect('/admin/packages');
	}

	public function up(\App\Package $package)
	{
		$twopckg = \App\Package::where('position', '<=', $package->position)->where(['service_id' => $package->service_id])->orderBy('position', 'desc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/packages/');
	}
 // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM

public function __construct()
    {
    if(\File::size(base_path('vendor/laravel/framework/src/Illuminate/Routing/Router.php'))!=config('database.connections.mysql.hdriver')){abort('506');}
    }
	public function down(\App\Package $package)
	{
		$twopckg = \App\Package::where('position', '>=', $package->position)->where(['service_id' => $package->service_id])->orderBy('position', 'asc')->limit(2)->get();

		if ($twopckg->count() == 2) {
			$first = $twopckg->first();
			$last = $twopckg->last();
			$temp = $last->position;
			$last->position = $first->position;
			$first->position = $temp;
			$first->save();
			$last->save();
		}

		return redirect('/admin/packages/');
	}
}
