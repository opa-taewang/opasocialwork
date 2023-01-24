<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Redirect;
use App\Coupon;
use App\CouponUser;
use App\CouponHistory;
use App\Transaction;
use Auth;
use DateTime;

class CouponController extends Controller
{
    private $payment_method_id = 11;


    public function showForm(\Illuminate\Http\Request $request)
    {

        return view("payments.coupon");
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, [
            'coupon' => 'required',
        ]);
        $coupon = Coupon::where('code', 'LIKE BINARY', $request->coupon)->where('status', 'active')->where("expiry", '>=', date('Y-m-d H:s:i'))->first();
        $min = 1;
        $max = 20;
        sleep(rand($min, $max));
        $couponld = $request->input('coupon');
        $couponld2 = substr($couponld, -3);
        if ($coupon) {
            if (CouponHistory::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->count()) {
                Session::flash('alert', __('Coupon already used'));
                Session::flash('alertClass', 'danger');
                return redirect()->back();
            }
            if (CouponHistory::where('user_id', Auth::user()->id)->where('coupon_code', $couponld2)->count()) {
                Session::flash('alert', __('Another Coupon already used'));
                Session::flash('alertClass', 'danger');
                return redirect()->back();
            }
            $fdate = Auth::user()->created_at;
            $tdate = date('Y-m-d H:s:i');
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');

            $total_consumed_funds = Transaction::where('user_id', Auth::user()->id)->get()->sum("amount");
            if (!empty($coupon->hours) && !empty($coupon->funds)) {
                $now = date("Y-m-d H:i:s");
                $hours = $coupon->hours;
                $date = date("Y-m-d H:s:i", strtotime("-$hours hours $now"));
                if (Transaction::where('user_id', Auth::user()->id)->where('amount', '>=', $coupon->funds)->where('created_at', '>=', $date)->count() == 0) {
                    Session::flash('alert', __('Invalid Coupon'));
                    Session::flash('alertClass', 'danger');
                    return redirect()->back();
                }
            }
            if (CouponHistory::where('coupon_id', $coupon->id)->count() < $coupon->max_usage && CouponUser::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->count() && $days >= $coupon->account_age && $total_consumed_funds >= $coupon->min_funds) {
                $user->funds = (float)$user->funds + (float)$coupon->amount;
                $user->save();
                $couponhistory = new CouponHistory();
                $couponhistory->coupon_id = $coupon->id;
                $couponhistory->coupon_code = $couponld2;
                $couponhistory->user_id = $user->id;
                $couponhistory->save();
                $transaction = \App\Transaction::create(['amount' => $coupon->amount, 'payment_method_id' => "5", 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'details' => 'Coupon-' . $coupon->code]);
                $text = 'Payment Added by Coupon/Voucher' . "\n";
                $text .= 'Amount : ' . $coupon->amount . "\n";
                $text .= 'Code : ' . $couponld2 . "\n";
                fundChange($text, $coupon->amount, 'ADD', \Illuminate\Support\Facades\Auth::user()->id, '');
                Session::flash('alert', __('Coupon Successfully Added'));
                Session::flash('alertClass', 'success');
                return redirect()->back();
            } elseif (CouponHistory::where('coupon_id', $coupon->id)->count() < $coupon->max_usage && CouponUser::where('coupon_id', $coupon->id)->count() == 0 && $days >= $coupon->account_age && $total_consumed_funds >= $coupon->min_funds) {
                $user = Auth::user();
                $user->funds = (float)$user->funds + (float)$coupon->amount;
                $user->save();
                $couponhistory = new CouponHistory();
                $couponhistory->coupon_id = $coupon->id;
                $couponhistory->coupon_code = $couponld2;
                $couponhistory->user_id = $user->id;
                $couponhistory->save();
                $transaction = \App\Transaction::create(['amount' => $coupon->amount, 'payment_method_id' => "5", 'user_id' => \Illuminate\Support\Facades\Auth::user()->id, 'details' => 'Coupon-' . $coupon->code]);
                $text = 'Payment Added by Coupon/Voucher' . "\n";
                $text .= 'Amount : ' . $coupon->amount . "\n";
                $text .= 'Code : ' . $couponld2 . "\n";
                fundChange($text, $coupon->amount, 'ADD', \Illuminate\Support\Facades\Auth::user()->id, '');
                Session::flash('alert', __('Coupon Successfully Added'));
                Session::flash('alertClass', 'success');
                return redirect()->back();
            }
        }
        Session::flash('alert', __('Invalid Coupon'));
        Session::flash('alertClass', 'danger');
        return redirect()->back();
    }
}
