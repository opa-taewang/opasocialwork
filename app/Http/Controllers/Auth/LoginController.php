<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    public function authenticated()
    {
        if (Auth::check() && Auth::user()->role === 'ADMIN') {
            $this->redirectTo = route('admin.dashboard');
        } elseif (Auth::check() && Auth::user()->role === 'MODERATOR') {
            $this->redirectTo = route('moderator.dashboard');
        } else {
            $this->redirectTo = RouteServiceProvider::HOME;
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function login(Request $request)
    // {
    //     $input = $request->all();

    //     $this->validate($request, [
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);

    //     $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    //     if (auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password']))) {
    //         $this->authenticated();
    //     } else {
    //         return redirect()->route('login')
    //             ->with('error', 'Username or Password is incorrect.');
    //     }
    // }
}
