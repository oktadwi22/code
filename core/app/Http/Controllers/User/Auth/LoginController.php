<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    use AuthenticatesUsers;


    protected $username;


    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function showLoginForm()
    {
        $pageTitle = "Login";
        return view($this->activeTemplate . 'user.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $cartItems = session('cart');
        $request->session()->regenerateToken();
        $request->session()->put('cart', $cartItems);

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);


        return $this->sendFailedLoginResponse($request);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

    }

    public function logout()
    {
        $this->guard()->logout();

        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('home')->withNotify($notify);
    }


    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude    =  $exist->longitude;
            $userLogin->latitude     =  $exist->latitude;
            $userLogin->city         =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      =  $exist->country;
        }else{
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',',$info['long']);
            $userLogin->latitude     = @implode(',',$info['lat']);
            $userLogin->city         = @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        $sessionIds = collect(session('cart'))->pluck('session_id')->toArray();
        Cart::whereIn('session_id', $sessionIds)->update(['user_id' => auth()->id()]);
        session()->forget('cart');

        return to_route('user.home');
    }


}
