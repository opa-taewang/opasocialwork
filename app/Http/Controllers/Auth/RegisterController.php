<?php

namespace App\Http\Controllers\Auth;

use App\Ip;
use App\Visit;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'min:6', 'max:25', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */

    protected function create(array $data)
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        $paymentMethods = PaymentMethod::where(['config_key' => null, 'status' => 'ACTIVE', 'is_disabled_default' => 0])->groupBy('slug')->get()->pluck('id')->toArray();
        $payment_methods = '';
        $groups = Group::where(['isdefault' => 1])->get()->pluck('id')->toArray();
        $groups = implode($groups);
        if (!empty($paymentMethods)) {
            $payment_methods = implode(',', $paymentMethods);
        }
        $options = Config::where('name', 'users_per_ip')->value('value');
        $ip = $_SERVER['REMOTE_ADDR'];
        if (User::where('ip', $ip)->count() >= (int)$options) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'skype_id' => $data['skype_id'],
                'group_id' => $groups,
                'enabled_payment_methods' => $payment_methods,
                'password' => bcrypt($data['password']),
                'status' => 'DEACTIVATED',
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);
            $ip = Ip::create([
                'address' => $_SERVER['REMOTE_ADDR'],
                'user_id' => $user->id,
                'blocked' => 0,
                'reason' => 'Number of Accounts Per IP Crossed'
            ]);
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'skype_id' => $data['skype_id'],
                'group_id' => $groups,
                'enabled_payment_methods' => $payment_methods,
                'password' => Hash::make($data['password']),
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);
            $ip = Ip::create([
                'address' => $_SERVER['REMOTE_ADDR'],
                'user_id' => $user->id,
                'blocked' => 0,
                'reason' => 'New Registration'
            ]);
        }
        $refVid = $user->id;
        $refUid = Session::get('refUid');
        if ($refUid != null) {
            $refuser = User::findOrFail($refUid);
            if ($refuser != '') {
                $visit = new Visit;
                $visit->refVid = $refVid;
                $visit->refUid = $refUid;
                $visit->visitorIp = $_SERVER['REMOTE_ADDR'];
                $visit->save();
            }
        }
        return $user;
    }
}
