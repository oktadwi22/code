<?php

namespace App\Http\Controllers\Reviewer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Reviewer;
use App\Models\ReviewerPasswordReset;
use Illuminate\Http\Request; 

class ForgotPasswordController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('reviewer.guest');
    }


    public function showLinkRequestForm()
    {
        $pageTitle = 'Account Recovery';
        return view('reviewer.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $reviewer = Reviewer::where('email', $request->email)->first();
        if (!$reviewer) {
            return back()->withErrors(['Email Not Available']);
        }

        $code                              = verificationCode(6);
        $reviewerPasswordReset             = new ReviewerPasswordReset();
        $reviewerPasswordReset->email      = $reviewer->email;
        $reviewerPasswordReset->token      = $code;
        $reviewerPasswordReset->created_at = date("Y-m-d h:i:s");
        $reviewerPasswordReset->save();

        $reviewerIpInfo  = getIpInfo();
        $reviewerBrowser = osBrowser();
        notify($reviewer, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => $reviewerBrowser['os_platform'],
            'browser'          => $reviewerBrowser['browser'],
            'ip'               => $reviewerIpInfo['ip'],
            'time'             => $reviewerIpInfo['time']
        ], ['email'], false);

        $email = $reviewer->email;
        session()->put('pass_res_mail', $email);

        return to_route('reviewer.password.code.verify');
    }

    public function codeVerify()
    {
        $pageTitle = 'Verify Code';
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('reviewer.password.reset')->withNotify($notify);
        }
        return view('reviewer.auth.passwords.code_verify', compact('pageTitle', 'email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);
        $notify[] = ['success', 'You can change your password.'];
        $code     = str_replace(' ', '', $request->code);
        return to_route('reviewer.password.reset.form', $code)->withNotify($notify);
    }
}
