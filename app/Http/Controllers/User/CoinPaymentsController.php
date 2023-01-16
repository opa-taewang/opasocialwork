<?php

namespace App\Http\Controllers\User;

use App\Models\Coupon\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

session_start();

class CoinPaymentsController extends Controller
{
    private $url = "https://www.coinpayments.net/index.php?";
    private $merchantId = "";
    private $secretKey = "";
    private $payment_method_id = 3;

    public function __construct()
    {
        $this->merchantId = PaymentMethod::where(array("config_key" => "merchant_id"))->first()->config_value;
        $this->secretKey = PaymentMethod::where(array("config_key" => "secret_key"))->first()->config_value;
    }

    public function showForm()
    {
        $paymentMethod = \App\PaymentMethod::where(array("id" => $this->payment_method_id, "status" => "ACTIVE"))->first();
        if (is_null($paymentMethod)) {
            abort(403);
        }
        if (empty(\Auth::user()->enabled_payment_methods)) {
            abort(403);
        }
        $enabled_payment_methods = explode(",", \Auth::user()->enabled_payment_methods);
        if (!in_array($this->payment_method_id, $enabled_payment_methods)) {
            abort(403);
        }
        return view("payments.bitcoin");
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $minimum_deposit_amount = getOption("minimum_deposit_amount");
        $validator = \Validator::make($request->all(), array("amount" => "required|numeric|min:" . $minimum_deposit_amount));
        if ($validator->fails()) {
            return redirect("payment/add-funds/bitcoin")->withErrors($validator)->withInput();
        }
        $params = array("merchant" => $this->merchantId, "cmd" => "_pay_simple", "reset" => 1, "currency" => getOption("currency_code"), "amountf" => $request->input("amount"), "item_name" => "Add Funds", "email" => \Auth::user()->email, "ipn_url" => url("/payment/add-funds/bitcoin/bit-ipn"), "success_url" => url("/payment/add-funds/bitcoin/success"), "cancel_url" => url("/payment/add-funds/bitcoin/cancel"), "first_name" => \Auth::user()->name, "last_name" => \Auth::user()->name, "want_shipping" => 0);
        $paymentLogSecret = bcrypt(\Auth::user()->email . "PayPal" . time() . rand(1, 90000));
        \App\PaymentLog::create(array("currency_code" => strtoupper(getOption("currency_code")), "details" => $paymentLogSecret, "total_amount" => $request->input("amount"), "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id));
        $params["custom"] = $paymentLogSecret;
        $this->url .= http_build_query($params);
        return redirect()->away($this->url);
    }

    public function checkoutstore(\Illuminate\Http\Request $request)
    {
        $amount = $request->amount;
        $params = array("merchant" => $this->merchantId, "cmd" => "_pay_simple", "reset" => 1, "currency" => getOption("currency_code"), "amountf" => $amount, "item_name" => "Add Funds", "email" => \Auth::user()->email, "ipn_url" => url("/payment/checkout/bitcoin/bit-ipn"), "success_url" => url("/payment/checkout/bitcoin/success"), "cancel_url" => url("/payment/checkout/bitcoin/cancel"), "first_name" => \Auth::user()->name, "last_name" => \Auth::user()->name, "want_shipping" => 0);
        $paymentLogSecret = bcrypt(\Auth::user()->email . "PayPal" . time() . rand(1, 90000));
        \App\PaymentLog::create(array("currency_code" => strtoupper(getOption("currency_code")), "details" => $paymentLogSecret, "total_amount" => $amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id));
        \App\Transaction::create(array("amount" => $amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id, "txn_id" => $paymentLogSecret, 'status' => 0, "details" => "UnPaid"));

        $_SESSION['transaction_id'] = $paymentLogSecret;

        $params["custom"] = $paymentLogSecret;
        $this->url .= http_build_query($params);
        header('Location: ' . $this->url);
        return redirect()->away($this->url);
    }

    public function success(\Illuminate\Http\Request $request)
    {
        \Session::flash("alert", __("messages.payment_success"));
        \Session::flash("alertClass", "success");
        return redirect("/payment/add-funds/bitcoin");
    }

    public function checkoutsuccess(\Illuminate\Http\Request $request)
    {
        \Session::flash("alert", __("messages.payment_success"));
        \Session::flash("alertClass", "success");
        return redirect("/checkout/verify");
    }

    public function cancel(\Illuminate\Http\Request $request)
    {
        \Session::flash("alert", __("messages.payment_failed"));
        \Session::flash("alertClass", "danger no-auto-close");
        if (empty($_SESSION['transaction_id'])) {
            return redirect("/payment/add-funds/bitcoin");
        }
        $_SESSION["transaction_id"] = '';
        return redirect("/dashboard");
    }

    public function checkoutcancel(\Illuminate\Http\Request $request)
    {
        try {
            $transaction_id = $_SESSION['transaction_id'];
            \App\Transaction::where("txn_id", $transaction_id)->delete();
        } catch (\Exception $e) {
        }
        \Session::flash("alert", __("messages.payment_failed"));
        \Session::flash("alertClass", "danger no-auto-close");
        $_SESSION["transaction_id"] = '';
        return redirect("/dashboard");
    }

    public function ipn(\Illuminate\Http\Request $request)
    {
        if ($request->input("status_text") == "Complete") {
            if (!$request->filled("custom")) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("custom data is missing from request");
                exit();
            }
            $custom = $request->input("custom");
            $paymentLog = \App\PaymentLog::where(array("details" => $custom))->first();
            // if( !is_null($paymentLog) )
            // {
            $txn_id = $request->input("txn_id");
            $item_name = $request->input("item_name");
            $amount1 = $request->input("amount1");
            $amount2 = $request->input("amount2");
            $fee = $request->input("fee");
            $tax = $request->input("tax");
            $currency1 = $request->input("currency1");
            $currency2 = $request->input("currency2");
            if (strtolower($currency1) != strtolower(getOption("currency_code"))) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Original currency mismatch. Currency:" . $currency1);
                exit();
            }
            if ($amount1 < $paymentLog->total_amount) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Amount is less than order total. Amount:" . $amount1);
                exit();
            }
            \App\PaymentLog::where(array("details" => $custom))->update(array("details" => json_encode($request->all())));
            $amountAfterTax = $amount1 - $tax;
            $transaction = \App\Transaction::create(array("amount" => $amountAfterTax, "payment_method_id" => $this->payment_method_id, "user_id" => $paymentLog->user_id));
            $user = \App\User::find($paymentLog->user_id);
            $text = 'Payment Added by CoinPayments' . "\n";
            $text .= 'Amount : ' . $amountAfterTax . "\n";
            $text .= 'Funds Load ID : ' . $transaction->id . "\n";
            fundChange($text, $amountAfterTax * 1, 'ADD', $paymentLog->user_id, '');
            $total_amount = \App\Order::where(['user_id' => $paymentLog->user_id])->whereNotIn('status', ['CANCELLED', 'REFUNDED'])->sum('price');

            $group = \App\Group::where('funds_limit', '>', $total_amount)->orderBy('funds_limit', 'ASC')->first();
            if ($group) {
                $user->group_id = $group->id;
            }
            $user->funds = $user->funds + $amountAfterTax;
            $user->save();
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Payment Loaded successfully for user_id:" . $paymentLog->user_id . " amount:" . $amountAfterTax);
            exit();
            // }
            // activity("coinpayments")->withProperties(array( "ip" => $request->ip() ))->log("PaymentLog Object not found, might be payment already loaded.");
            // exit();
        }
        if (!$request->filled("ipn_mode") || !$request->filled("merchant")) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Missing POST data from callback.");
            exit();
        }
        if ($request->input("ipn_mode") == "httpauth") {
            if ($request->server("PHP_AUTH_USER") != $this->merchantId || $request->server("PHP_AUTH_PW") != $this->secretKey) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HTTP Request");
                exit();
            }
        } elseif ($request->input("ipn_mode") == "hmac") {
            $hmac = hash_hmac("sha512", "Add funds", $this->secretKey);
            if ($hmac != $request->server("HTTP_HMAC")) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HMAC Request");
                exit();
            }
        } else {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HMAC Request");
            exit();
        }
        $status = intval($request->input("status"));
        $statusText = $request->input("status_text");
        if ($request->input("merchant") != $this->merchantId) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Mismatching merchant ID. MerchantID:" . $request->input("merchant"));
            exit();
        }
        if ($status < 0) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip(), "status" => $status, "StatusText" => $statusText))->log("Payment Failed");
            exit();
        } elseif ($status == 0) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Payment is in Pending, Waiting for buyer funds");
            exit();
        }

        activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unkown error, no condition matched.");
        exit();
    }

    public function checkoutipn(\Illuminate\Http\Request $request)
    {
        if (!$request->filled("ipn_mode") || !$request->filled("merchant")) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Missing POST data from callback.");
            exit();
        }
        if ($request->input("ipn_mode") == "httpauth") {
            if ($request->server("PHP_AUTH_USER") != $this->merchantId || $request->server("PHP_AUTH_PW") != $this->secretKey) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HTTP Request");
                exit();
            }
        } elseif ($request->input("ipn_mode") == "hmac") {
            $hmac = hash_hmac("sha512", "Add funds", $this->secretKey);
            if ($hmac != $request->server("HTTP_HMAC")) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HMAC Request");
                exit();
            }
        } else {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unauthorized HMAC Request");
            exit();
        }
        $status = intval($request->input("status"));
        $statusText = $request->input("status_text");
        if ($request->input("merchant") != $this->merchantId) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Mismatching merchant ID. MerchantID:" . $request->input("merchant"));
            exit();
        }
        if ($status < 0) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip(), "status" => $status, "StatusText" => $statusText))->log("Payment Failed");
            exit();
        } elseif ($status == 0) {
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Payment is in Pending, Waiting for buyer funds");
            exit();
        } elseif ($status >= 100 || $status == 2) {
            if (!$request->filled("custom")) {
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("custom data is missing from request");
                exit();
            }
            $custom = $request->input("custom");
            $paymentLog = \App\PaymentLog::where(array("details" => $custom))->first();
            if (!is_null($paymentLog)) {
                $txn_id = $request->input("txn_id");
                $item_name = $request->input("item_name");
                $amount1 = $request->input("amount1");
                $amount2 = $request->input("amount2");
                $fee = $request->input("fee");
                $tax = $request->input("tax");
                $currency1 = $request->input("currency1");
                $currency2 = $request->input("currency2");
                if (strtolower($currency1) != strtolower(getOption("currency_code"))) {
                    activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Original currency mismatch. Currency:" . $currency1);
                    exit();
                }
                if ($amount1 < $paymentLog->total_amount) {
                    activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Amount is less than order total. Amount:" . $amount1);
                    exit();
                }
                \App\PaymentLog::where(array("details" => $custom))->update(array("details" => json_encode($request->all())));
                $amountAfterTax = $amount1 - $tax;
                if (empty($_SESSION['transaction_id'])) {
                    \App\Transaction::create(array("amount" => $amountAfterTax, "payment_method_id" => $this->payment_method_id, "user_id" => $paymentLog->user_id, "txn_id" => $request->input("txn_id")));
                    $_SESSION['transaction_id'] = $request->input("txn_id");
                }
                activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Payment Loaded successfully for user_id:" . $paymentLog->user_id . " amount:" . $amountAfterTax);
                exit();
            }
            activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("PaymentLog Object not found, might be payment already loaded.");
            exit();
        }
        activity("coinpayments")->withProperties(array("ip" => $request->ip()))->log("Unkown error, no condition matched.");
        exit();
    }
}
