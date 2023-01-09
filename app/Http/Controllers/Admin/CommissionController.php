<?php

namespace App\Http\Controllers\Admin;

use App\Commission;
use App\Service;
use App\Order;
use App\Package;
use App\User;
use App\AffiliateTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CommissionController extends Controller
{

    public function index()
    {
        $commission = Commission::all();
        return view('admin.commission', compact('commission'));
    }

  public function removetable($id,Service $services){
        if($id!=''){
            if($id==1){
                $api_fetch_temps = DB::delete('delete from api_fetch_temps');
                Session::flash('alert', __('messages.deleted'));
                Session::flash('alertClass', 'success');
                return redirect('/admin/apifetch');
            }
            }
        Session::flash('alert', __('messages.deleted'));
        Session::flash('alertClass', 'success');
        return redirect('/admin/services');

    }

    public function update(Request $request, $id)
    {

        $commission = Commission::findOrFail($id);
        $commission->min_payout = $request->input('min_payout');
        $commission->commission_val = $request->input('commission_val');
        $commission->save();

        Session::flash('alert', __('messages.updated'));
        Session::flash('alertClass', 'success');
        return redirect('/admin/commission');
    }

    public function affiliate_transaction(){
        $transactions=AffiliateTransaction::all();
        return view('admin.affiliatetransactions',compact('transactions'));
    }

}
