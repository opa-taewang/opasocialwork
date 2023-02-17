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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexmakemoney()
    {
        return view('main.user.make-money');
    }

    public function faqs()
    {
        return view('main.user.faqs');
    }

    public function api()
    {
        return view('main.user.api');
    }
}
