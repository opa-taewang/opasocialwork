<?php

namespace App\Http\Controllers;
use Auth;
use Session;
use App\User;
use App\Visit;
use App\Service;
use App\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;

class VisitController extends Controller
{
public function __construct()
    {
        $this->middleware('guest');
        config(["no-captcha.sitekey" => getOption('recaptcha_public_key')]);
        config(["no-captcha.secret" => getOption('recaptcha_private_key')]);
    }
    public function index($name,$uid)
    {
        if (Auth::check()) {
            // $refUid = User::findOrFail($uid);
            // if($refUid!=''){
            // $visit= new Visit;
            // $visit->refVid = Auth::user()->id;
            // $visit->refUid = $uid;
            // $visit->visitorIp = Request::ip();
            // $visit->save();
            return redirect('/dashboard');

            }
        

        if(getOption('front_page') == 'home'){
            Session::put('refUid', $uid);

            return view('auth.register');
        }

        $packages = Package::where(['status' => 'ACTIVE'])->orderBy('service_id')->get();
        return view('index', compact('packages'));
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

}
