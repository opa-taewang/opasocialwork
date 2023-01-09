<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class FlutterWaveController extends Controller
{
    private $payment_method_id = 212;
    public function showForm(\Illuminate\Http\Request $request)
    {
        $paymentMethod = \App\PaymentMethod::where(["id" => $this->payment_method_id, "status" => "ACTIVE"])->first();
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
        $publicKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "PUBLIC_KEY"])->value("config_value");
        $secretKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "SECRET_KEY"])->value("config_value");
        $encryptionKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "ENCRYPTION_KEY"])->value("config_value");
        $env = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "ENV"])->value("config_value");
        return view("payments.flutterwave")->with("key", $publicKey);
    }
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ["amount" => "required"]);
        $minimum_deposit_amount = getOption("minimum_deposit_amount");
        $validator = \Validator::make($request->all(), ["amount" => "required|numeric|min:" . $minimum_deposit_amount]);
        if ($validator->fails()) {
            return redirect("payment/add-funds/flutterwave")->withErrors($validator)->withInput();
        }
        $publicKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "PUBLIC_KEY"])->value("config_value");
        $secretKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "SECRET_KEY"])->value("config_value");
        $encryptionKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "ENCRYPTION_KEY"])->value("config_value");
        \KingFlamez\Rave\Facades\Rave::setApi($publicKey, $secretKey, $encryptionKey);
        $reference = \KingFlamez\Rave\Facades\Rave::generateReference();
        $data = ["payment_options" => "account,card,banktransfer,mpesa,mobilemoneyrwanda,mobilemoneyzambia,qr,mobilemoneyuganda,ussd,credit,barter,mobilemoneyghana,payattitude,mobilemoneyfranco,paga,1voucher,mobilemoneytanzania", "amount" => $request->amount, "email" => \Auth::user()->email, "tx_ref" => $reference, "currency" => "NGN", "redirect_url" => url("/rave/callback"), "customer" => ["email" => \Auth::user()->email, "phone_number" => "", "name" => \Auth::user()->name], "customizations" => ["title" => "Add Funds", "description" => "Deposit Funds", "logo" => asset(getOption("logo"))]];
        $payment = \KingFlamez\Rave\Facades\Rave::initializePayment($data);
        if (!isset($payment->status)) {
            \Session::flash("alert", __("System encountered an problem, contact with admin.1"));
            \Session::flash("alertClass", "danger");
            return redirect("/payment/add-funds/flutterwave");
        }
        if ($payment->status !== "success") {
            \Session::flash("alert", __("System encountered an problem, contact with admin.2"));
            \Session::flash("alertClass", "danger");
            return redirect("/payment/add-funds/flutterwave");
        }
        return redirect($payment->data->link);
    }
    public function callback()
    {
        $status = request()->status;
        if ($status == "successful" || $status == "completed") {
            \Log::error("check22");
            $publicKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "PUBLIC_KEY"])->value("config_value");
            $secretKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "SECRET_KEY"])->value("config_value");
            $encryptionKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "ENCRYPTION_KEY"])->value("config_value");
            \KingFlamez\Rave\Facades\Rave::setApi($publicKey, $secretKey, $encryptionKey);
            $transactionID = \KingFlamez\Rave\Facades\Rave::getTransactionIDFromCallback();
            \Log::error($transactionID);
            $trans = \KingFlamez\Rave\Facades\Rave::verifyTransaction($transactionID);
            \Log::error($trans->status);
            \Log::error($trans->data->status);
            $amount = $trans->data->amount;
            if (strtoupper(getOption("currency_code")) == "USD") {
                $amount = $trans->data->amount / "570";
            }
            if (isset($trans->status) && $trans->status == "success" && isset($trans->data->status) && $trans->data->status == "successful" && \App\Transaction::where("details", $transactionID)->count() == 0) {
                \App\PaymentLog::create(["details" => "Add funds", "currency_code" => $trans->data->currency, "total_amount" => $trans->data->amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id]);
                \App\Transaction::create(["amount" => $amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id, "details" => $transactionID]);
                $user = \App\User::find(\Auth::user()->id);
                $user->funds = $user->funds + $amount;
                $user->save();
                \Session::flash("alert", __("messages.payment_success"));
                \Session::flash("alertClass", "success");
                return redirect("/payment/add-funds/flutterwave");
            }
        } else {
            if ($status == "cancelled") {
                \Session::flash("alert", __("Payment Cacneled!"));
                \Session::flash("alertClass", "danger");
                return redirect("/payment/add-funds/flutterwave");
            }
        }
        \Session::flash("alert", __("Payment Failed"));
        \Session::flash("alertClass", "danger");
        return redirect("/payment/add-funds/flutterwave");
    }
    public function getURL($url, $data = [])
    {
        $urlArr = explode("?", $url);
        $params = array_merge($_GET, $data);
        $new_query_string = http_build_query($params) . "&" . $urlArr[1];
        $newUrl = $urlArr[0] . "?" . $new_query_string;
        return $newUrl;
    }
    public function success()
    {
        require "rave/library/Transactions.php";
        $secretKey = \App\PaymentMethod::where(["slug" => "flutterwave", "status" => "ACTIVE", "config_key" => "SECRET_KEY"])->value("config_value");
        if (request()->get("transaction_id")) {
            $tx_ref = request()->get("tx_ref");
            $transaction_id = request()->get("transaction_id");
            $payment = new Transactions();
            $curl = curl_init();
            curl_setopt_array($curl, [CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $secretKey]]);
            $response = curl_exec($curl);
            curl_close($curl);
            $trans = json_decode($response);
            if (isset($trans->data->status) && $trans->data->status == "successful") {
                \App\PaymentLog::create(["details" => "Add funds", "currency_code" => $trans->data->currency, "total_amount" => $trans->data->amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id]);
                \App\Transaction::create(["amount" => $trans->data->amount, "payment_method_id" => $this->payment_method_id, "user_id" => \Auth::user()->id]);
                $user = \App\User::find(\Auth::user()->id);
                $user->funds = $user->funds + $trans->data->amount;
                $user->save();
                \Session::flash("alert", __("messages.payment_success"));
                \Session::flash("alertClass", "success");
                return redirect("/payment/add-funds/flutterwave");
            }
        }
        \Session::flash("alert", __("Payment Failed"));
        \Session::flash("alertClass", "danger");
        return redirect("/payment/add-funds/flutterwave");
    }
    public function cancel()
    {
        \Session::flash("alert", __("Payment Failed"));
        \Session::flash("alertClass", "danger");
        return redirect("/payment/add-funds/flutterwave");
    }
    public function showServices()
    {
        $services = \App\Service::where(["services.status" => "ACTIVE", "packages.status" => "ACTIVE"])->join("packages", "services.id", "=", "packages.service_id")->select("services.*")->distinct()->orderBy("services.position")->get();
        $packages = \App\Package::where(["status" => "ACTIVE"])->orderBy("position")->get();
        if (\Auth::check()) {
            $userPackagePrices = \App\UserPackagePrice::where(["user_id" => \Auth::user()->id])->pluck("price_per_item", "package_id")->toArray();
            foreach ($packages as $package) {
                if (isset($userPackagePrices[$package->id])) {
                    $package->price_per_item = $userPackagePrices[$package->id];
                }
            }
            $userPackagePrices = NULL;
            return view("services", compact("services", "packages", "userPackagePrices"));
        } else {
            return view("services", compact("services", "packages"));
        }
    }
    public function searchServices(\Illuminate\Http\Request $request)
    {
        $services = \App\Service::where(["status" => "ACTIVE"])->where(function ($query) {
            $query->where("name", "like", "%" . $request->search_value . "%")->orWhere("slug", "like", "%" . $request->search_value . "%");
        })->get();
        $ids = [];
        foreach ($services as $service) {
            $ids[] = $service->id;
        }
        $packages = \App\Package::where(["status" => "ACTIVE"])->whereIn("service_id", $ids)->orderBy("service_id")->get();
        if (\Auth::check()) {
            $userPackagePrices = \App\UserPackagePrice::where(["user_id" => \Auth::user()->id])->pluck("price_per_item", "package_id")->toArray();
        }
        return view("services", compact("services", "packages", "userPackagePrices"));
    }
    public function APIDocV2()
    {
        return view("api-v2");
    }
    public function ApiDocV1()
    {
        return view("api-v1");
    }
}
