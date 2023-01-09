<?php

namespace App\Http\Controllers\User;

/**
 * ympnl
 * Domain:
 * CCWORLD
 *
 */

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Session;
use App\Page;
use App\Commission;
use App\Visit;
use App\AffiliateTransaction;
use App\PaymentMethod;
use App\Service;
use App\Package;
use App\SeoPackage;
use App\SeoService;
use App\SeoCategory;
use App\UserPackagePrice;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use View;
use Cookie;
use Mail;
use Response;
use Carbon\Carbon;
use App\VerifyUser;
use App\Mail\VerifyMail;

class HomeController extends Controller
{
    public function indexfaq()
    {
        return view('faqs');
    }
    public function indexmakemoney()
    {
        return view('makemoney');
    }
    public function indexhiw()
    {
        return view('howitwork');
    }
    // @ioncube.dynamickey fn("world") -> "ITSMYWORLD world" RANDOM
    public function index()
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }

        if (\Auth::check()) {
            return redirect('/order/new');
        }

        if (getOption('front_page') == 'login') {
            return redirect('/login');
        }

        $packages = \App\Package::where(['status' => 'ACTIVE'])->orderBy('service_id')->get();
        return view(__FUNCTION__, compact('packages'));
    }

    public function showServices()
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }


        if (\Auth::check()) {
            $group = Auth::user()->group;
            $package_ids = explode(",", $group->package_ids);
            $service_ids = \App\Package::whereIn('id', $package_ids)->distinct()->pluck('service_id');
            $services = \App\Service::where(['services.status' => 'ACTIVE', 'packages.status' => 'ACTIVE', 'services.servicetype' => 'DEFAULT'])->join('packages', 'services.id', '=', 'packages.service_id')->whereIn('services.id', $service_ids)->select('services.*')->distinct()->orderBy('services.position')->get();
            $packages = \App\Package::where(['status' => 'ACTIVE', "packages.packagetype" => "DEFAULT"])->whereIn('id', $package_ids)->orderBy('position')->get();

            if (\Auth::check()) {
                $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();

                foreach ($packages as $package) {
                    if (isset($userPackagePrices[$package->id])) {
                        $package->price_per_item = number_format(($userPackagePrices[$package->id] / 100) * $group->price_percentage, 2);
                    }
                }

                $userPackagePrices = NULL;
            }
            $favorite_pkgs = explode(",", Auth::user()->favorite_pkgs);
            return view('services', compact('services', 'packages', 'group', 'package_ids', 'userPackagePrices', 'favorite_pkgs'));
        } else {
            $services = \App\Service::where(['services.status' => 'ACTIVE', 'packages.status' => 'ACTIVE', 'services.servicetype' => 'DEFAULT'])->join('packages', 'services.id', '=', 'packages.service_id')->select('services.*')->distinct()->orderBy('services.position')->get();
            $packages = \App\Package::where(['status' => 'ACTIVE', "packages.packagetype" => "DEFAULT"])->orderBy('position')->get();

            if (\Auth::check()) {
                $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();

                foreach ($packages as $package) {
                    if (isset($userPackagePrices[$package->id])) {
                        $package->price_per_item = $userPackagePrices[$package->id];
                    }
                }

                $userPackagePrices = NULL;
            }
            return view('services1', compact('services', 'packages'));
        }
    }
    public function blog()
    {
        $posts = \App\Blog::where('status', 'Active')->orderBy('created_at', 'desc')->paginate(5);
        $latestposts = \App\Blog::where('status', 'Active')->orderBy('created_at', 'desc')->take(4)->get();
        return view('blog', compact('posts', 'latestposts'));
    }
    public function showpost($slug = '')
    {
        $postviews = \App\Blog::where('slug', $slug)->value('views');
        $blview = 1;
        $postviews = $postviews + $blview;
        \DB::table('blog')->where('slug', $slug)->update(['views' => $postviews]);
        if (empty($slug))
            abort(404);
        $post = \App\Blog::where('slug', $slug)->first();
        if (empty($post))
            abort(404);
        $latestposts = \App\Blog::where('status', 'Active')->orderBy('created_at', 'desc')->take(4)->get();

        return view('showpost', compact('post', 'latestposts'));
    }
    public function packagetracker()
    { {
            $packages = \App\Package::where(['status' => 'ACTIVE', "packages.packagetype" => "DEFAULT"])->orderBy('position_id')->get();

            if (\Auth::check()) {
                $userPackagePrices = \App\UserPackagePrice::where(['user_id' => \Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();

                foreach ($packages as $package) {
                    if (isset($userPackagePrices[$package->id])) {
                        $package->price_per_item = $userPackagePrices[$package->id];
                    }
                }

                $userPackagePrices = NULL;
            }
        }

        return view('packagetrack', compact('services', 'packages', 'userPackagePrices'));
    }
    public function newsletter(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:newsletters'
        ]);
        \App\Newsletter::create(['email' => $request->email]);
        \Session::flash("alert", __("Subscribed Successfully"));
        \Session::flash("alertClass", "success");
        return redirect()->back();
    }
    public function searchServices(Request $request)
    {
        if (\Auth::check()) {
            $group = Auth::user()->group;
            $package_ids = explode(",", $group->package_ids);
            $service_ids = \App\Package::whereIn('id', $package_ids)->distinct()->pluck('service_id');
            $services = Service::where(['status' => 'ACTIVE'])
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search_value . '%')
                        ->orWhere('slug', 'like', '%' . $request->search_value . '%');
                })
                ->get();
            $ids = array();
            foreach ($services as $service) {
                $ids[] = $service->id;
            }
            $packages = \App\Package::where(['status' => 'ACTIVE', "packages.packagetype" => "DEFAULT"])->whereIn('id', $package_ids)->orderBy('position')->get();
            if (Auth::check()) {
                $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
            }
            $favorite_pkgs = explode(",", Auth::user()->favorite_pkgs);

            return view('services', compact('services', 'packages', 'group', 'package_ids', 'userPackagePrices', 'favorite_pkgs'));
        } else {
            $services = Service::where(['status' => 'ACTIVE'])
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search_value . '%')
                        ->orWhere('slug', 'like', '%' . $request->search_value . '%');
                })
                ->get();
            $ids = array();
            foreach ($services as $service) {
                $ids[] = $service->id;
            }
            $packages = Package::where(['status' => 'ACTIVE'])->whereIn('service_id', $ids)->orderBy('service_id')->get();
            if (Auth::check()) {
                $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
            }

            return view('services1', compact('services', 'packages', 'userPackagePrices'));
        }
    }
    public function showAffiliates()
    {
        $commission = Commission::all();
        $userinfo = Auth::user();
        $name = $userinfo->name;
        $val = substr($name, 0, 3);
        $sname = $val[0] . $val[1] . $val[2];
        $id = $userinfo->id;
        $link = env('APP_URL') . 'ref/' . $sname . '/' . $id;
        $visits = Visit::where('refUid', '=', $userinfo->id)->count();
        $earning = AffiliateTransaction::where('refUid', '=', $userinfo->id)->sum('transferedFund');
        return view('affiliates', compact('commission', 'userinfo', 'visits', 'earning', 'link'));
    }
    public function searchServicetracker(Request $request)
    {
        $packages = Package::where(['status' => 'ACTIVE'])->where(function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search_value . '%')
                ->orWhere('slug', 'like', '%' . $request->search_value . '%')->orWhere('id', 'like', '%' . $request->search_value . '%');
        })
            ->get();
        if (Auth::check()) {
            $userPackagePrices = UserPackagePrice::where(['user_id' => Auth::user()->id])->pluck('price_per_item', 'package_id')->toArray();
        }

        return view('packagetrack', compact('services', 'packages', 'userPackagePrices'));
    }
    public function generateKey()
    {
        $api_token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < 32; $i++) {
            $api_token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        \App\User::where(array("id" => \Illuminate\Support\Facades\Auth::user()->id))->update(array("api_token" => $api_token));
        return redirect("/api");
    }
    public function currencyConverter($currency)
    {
        if ($usd) {
            $usd = 1;
            $inr = 70;
            $pkr = 12;
            return $usd;
        }
    }
    public function showPage($slug)
    {
        $page = \App\Page::where(['slug' => $slug])->firstOrFail();
        $metaTags = $page->meta_tags;
        if (\Auth::check() && (\Auth::user()->role === 'ADMIN')) {
            return view('admin.static', compact('page', 'metaTags'));
        }

        return view('static', compact('page', 'metaTags'));
    }

    public function APIDocV2()
    {
        return view('api-v2');
    }

    public function ApiDocV1()
    {
        return view('api-v1');
    }

    public function showManualPaymentForm(\Illuminate\Http\Request $request)
    {
        $paymentMethod = \App\PaymentMethod::where(['id' => 5, 'status' => 'ACTIVE'])->first();

        if (is_null($paymentMethod)) {
            abort(403);
        }

        $details = \App\PaymentMethod::where(['config_key' => 'bank_details', 'status' => 'ACTIVE'])->first()->config_value;
        return view('payments.bank', compact('details'));
    }

    public function changeLanguage(\Illuminate\Http\Request $request)
    {
        $locale = $request->input('locale');
        \App::setLocale($locale);
        \Session::put('locale', $locale);
        return redirect('/');
    }
    public function DownloadScript($id, $token, Request $request)
    {
        if (!Auth::check())
            exit('Requested file does not exist on our server!');
        $package = \App\Package::find($id);
        $order = \App\Order::where('user_id', \Auth::user()->id)->where('package_id', $id)->count();
        if ($order) {
            $downloads = \App\Downloadrecords::create(array("ip" => $request->input("ip"), "downloads" => $request->input("downloads"),  "script_name" => $package->script_name, "user_id" => \Illuminate\Support\Facades\Auth::user()->id));
            $downloads->ip = $request->ip();
            $downloads->downloads = (int)$downloads->downloads + 1;
            $downloads->user_id = \Auth::user()->id;
            $downloads->save();
            $downloads->script_name = $package->script_name;
            $file_path = $package->script;
            if (file_exists($file_path)) {
                return Response::download($file_path, ($package->script_name) ? $package->script_name : '', ['Content-Length:' . filesize($file_path)]);
            }
        }
        exit('Requested file does not exist on our server!');
    }
    public function addtofavorite(Request $request)
    {
        $sid = $request->sid;
        $pid = $request->pid;
        $user = Auth::user();
        if (!empty($user->favorite_pkgs))
            $favorite_pkgs = explode(",", $user->favorite_pkgs);
        else
            $favorite_pkgs = array();
        if (in_array($pid, $favorite_pkgs)) {
            if (($key = array_search($pid, $favorite_pkgs)) !== false) {
                unset($favorite_pkgs[$key]);
            }
            $user->favorite_pkgs = implode(",", $favorite_pkgs);
            $user->save();
            return "false";
        }
        array_push($favorite_pkgs, $pid);
        $user->favorite_pkgs = implode(",", $favorite_pkgs);
        $user->save();
        return "true";
    }
    public function changeCurrency(Request $request)
    {
        if ((request()->server('SERVER_NAME')) != base64_decode(config('database.connections.mysql.xdriver'))) {
            abort('506');
        }
        $id = Auth::user()->id;
        $user = User::findorFail($id);
        echo $request->input('locale');
        $user->currency_id = $request->input('locale');
        $user->save();
        return redirect('/order/new');
    }
    public function getservices(Request $request, $id)
    {
        $category = SeoCategory::findOrFail($id);
        $service_id = $request->service;
        echo '<option value="">Choose Service</option>';
        $services = SeoService::where('category_id', $id)->where('status', 1)->orderBy('rank', 'asc')->get();
        foreach ($services as $service) {
            if ($service->status && count($service->packages)) {
                if ($service_id == $service->id) {
                    echo '<option value="' . $service->id . '" selected>' . $service->name . '</option>';
                } else {
                    echo '<option value="' . $service->id . '">' . $service->name . '</option>';
                }
            }
        }
    }

    public function getpackages(Request $request, $id)
    {
        $service = SeoService::findOrFail($id);
        $package_id = $request->package;
        echo '<option value="">Choose Package</option>';
        $packages = SeoPackage::where('service_id', $id)->where('status', 1)->orderBy('rank', 'asc')->get();
        foreach ($packages as $package) {
            if ($package->status) {
                if ($package_id == $package->id) {
                    echo '<option value="' . $package->id . '" selected>' . $package->name . '</option>';
                } else {
                    echo '<option value="' . $package->id . '">' . $package->name . '</option>';
                }
            }
        }
    }
    public function getVerificationform()
    {
        return view('auth.verify');
    }
    public function getEmailform()
    {
        return view('auth.changeemail');
    }
    public function getotpform()
    {
        return view('auth.enterotp');
    }
    public function enterotpform(Request $request)
    {
        if (\App\VerifyUser::where('user_id', Auth::user()->id)->where('otp', $request->input('otp'))->exists()) {
            User::where(['id' => Auth::user()->id])->update([
                'Verified' => '1'
            ]);
            \Illuminate\Support\Facades\Session::flash('alert', __('Email Successfully Verified!'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
            return redirect('/dashboard');
        } else {
            \Session::flash("alert", __("Wrong OTP!"));
            \Session::flash("alertClass", "danger");
            return redirect('/enter-otp');
        }
    }
    public function sendVerficationMail(Request $request)
    {
        $user = User::where('email', $request->email)->get()->first();
        if (isset($user->email) && $user->verified != 1) {
            if (!empty($user->verifyUser->token)) {
                \Session::flash("alert", __("Please Check Your Email for OTP! If You cannot Find Please Check Spam Folders!"));
                \Session::flash("alertClass", "danger");
                return redirect('/enter-otp');
            }
            $pass = rand(100000, 999999);
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => sha1(time()),
                'otp' => $pass
            ]);
            $token = $verifyUser->token;
            \Mail::to($user->email)->send(new VerifyMail($user, $token, $pass));
            \Session::flash("alert", __("We have sent you an activation code, please check your email. If You cannot Find Please Check Spam Folders!"));
            \Session::flash("alertClass", "danger");
            return redirect('/enter-otp');
        } else {
            return back('/dashboard')->with('warning', 'Sorry email cannot be identified.');
        }
    }
    public function changeEmailform(Request $request)
    {
        if (\App\User::where('email', $request->input('email'))->exists()) {
            \Session::flash("alert", __("Email Already Exists!"));
            \Session::flash("alertClass", "danger no-auto-close");
            return redirect('/dashboard');
        }
        try {
            User::where(['id' => Auth::user()->id])->update([
                'email' => $request->input('email')
            ]);
        } catch (\Exception $e) {
        }
        \Illuminate\Support\Facades\Session::flash('alert', __('Email Successfully changed!'));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('/dashboard');
    }
    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser)) {
            $user = $verifyUser->user;
            if (isset($user)) {
                if (!$user->verified) {
                    $verifyUser->user->verified = 1;
                    $verifyUser->user->save();
                    $status = "Your e-mail is verified.";
                } else {
                    $status = "Your e-mail is already verified.";
                }
            } else {
                \Illuminate\Support\Facades\Session::flash('alert', __('Token Not Found!'));
                \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
                return redirect('/dashboard');
            }
        } else {
            \Illuminate\Support\Facades\Session::flash('alert', __('Sorry your email cannot be identified.'));
            \Illuminate\Support\Facades\Session::flash('alertClass', 'danger no-auto-close');
            return redirect('/dashboard');
        }
        \Illuminate\Support\Facades\Session::flash('alert', __($status));
        \Illuminate\Support\Facades\Session::flash('alertClass', 'success');
        return redirect('/dashboard')->with('status', $status);
    }
}
