<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.currency.index');
    }
    public function IndexData()
    {
        $data = Currency::all();
        return datatables()
            ->of($data)
            ->addColumn('action', 'admin.currency.index-buttons')
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.currency.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'code' => 'required|max:191',
            'symbol' => 'required',
            'rate' => 'required',

        ]);
        $symbol = $request->input('symbol');

        $res = Currency::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'symbol' => $symbol,
            'rate' => $request->input('rate'),
        ]);
        $id = $res->id;
        $currency = Currency::findOrFail($id);
        $currency->symbol = $symbol;
        $currency->save();
        // echo $res->id;exit;

        Session::flash('alert', __('messages.created'));
        Session::flash('alertClass', 'success');
        return redirect('/admin/currency');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id == 1) {
            Session::flash('alert', __('You Can not Edit Default Currency'));
            Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/admin/currency');
        }
        $currency = Currency::findOrFail($id);
        //echo '<pre>',print_r($currency);exit;
        return view('admin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'code' => 'required|max:191',
            'symbol' => 'required',
            'rate' => 'required',

        ]);
        $currency = Currency::findOrFail($id);
        //echo '<pre>',print_r($currency);exit;
        $currency->name = $request->input('name');
        $currency->code = $request->input('code');
        $currency->symbol = $request->input('symbol');
        $currency->rate = $request->input('rate');
        $currency->save();

        Session::flash('alert', __('messages.updated'));
        Session::flash('alertClass', 'success no-auto-close');
        return redirect('/admin/currency');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id == 1) {
            Session::flash('alert', __('You can not delete Default Currency'));
            Session::flash('alertClass', 'danger no-auto-close');
        } else {
            $currency = Currency::findOrFail($id);
            $users = User::all();
            foreach ($users as $user) {
                if ($user->currency_id == $currency->id) {
                    $user->currency_id = 1;
                    $user->save();
                }
            }
            // echo '<pre>',print_r($currency);exit;
            $currency->delete();
            Session::flash('alert', __('messages.deleted'));
            Session::flash('alertClass', 'success');
        }

        return redirect('/admin/currency');
    }
    public function updaterates()
    {
        $req_url = 'https://api.exchangerate-api.com/v4/latest/USD';
        $response_json = file_get_contents($req_url);
        if (false !== $response_json) {
            try {
                $response_object = json_decode($response_json);
                $rates = (array)$response_object->rates;
                foreach (Currency::all() as $currency) {
                    $currency->rate = $rates[strtoupper($currency->code)];
                    $currency->save();
                    setOption("usdtoinr", $rates[strtoupper('INR')]);
                }
                Session::flash('alert', __('Currency rates are updated successfully'));
                Session::flash('alertClass', 'success');
                return redirect('/admin/currency');
            } catch (\Exception $e) {
                Session::flash('alert', __($e->getMessage()));
                Session::flash('alertClass', 'danger');
                return redirect('/admin/currency');
            }
        }
    }
}
