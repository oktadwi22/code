<?php

namespace App\Http\Controllers\Reviewer\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Reviewer;
use App\Models\ReviewerPasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('reviewer.guest');
    }

    public function showResetForm(Request $request, $token)
    {
        $pageTitle = "Account Recovery";
        $resetToken = ReviewerPasswordReset::where('token', $token)->where('status', Status::ENABLE)->first();

        if (!$resetToken) {
            $notify[] = ['error', 'Verification code mismatch'];
            return to_route('reviewer.password.reset')->withNotify($notify);
        }
        $email = $resetToken->email;
        return view('reviewer.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);

        $reset = ReviewerPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        $reviewer = Reviewer::where('email', $reset->email)->first();
        if ($reset->status == Status::DISABLE) {
            $notify[] = ['error', 'Invalid code'];
            return to_route('reviewer.login')->withNotify($notify);
        }

        $reviewer->password = Hash::make($request->password);
        $reviewer->save();
        $reset->status = Status::DISABLE;
        $reset->save();

        $ipInfo = getIpInfo();
        $browser = osBrowser();
        notify($reviewer, 'PASS_RESET_DONE', [
            'operating_system' => $browser['os_platform'],
            'browser' => $browser['browser'],
            'ip' => $ipInfo['ip'],
            'time' => $ipInfo['time']
        ], ['email'], false);

        $notify[] = ['success', 'Password changed'];
        return to_route('reviewer.login')->withNotify($notify);
    }
}
