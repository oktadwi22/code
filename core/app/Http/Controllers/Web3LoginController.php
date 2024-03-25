<?php

namespace App\Http\Controllers;

use App\Facades\Signature;
use App\Web3Login;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Web3LoginController
{
    public function signature(Request $request)
    {
        $request->session()->put('nonce', $nonce = Str::random());

        return Signature::generate($nonce);
    }

    public function link(Request $request)
    {
        $this->checkSignature($request);

        $request->user()->update([
            'username' => strtolower($request->input('address')),
        ]);

        return new Response('', 204);
    }

    public function register(Request $request)
    {
        // $this->checkSignature($request);

        $request->validate([
            'address' => ['required', 'string', 'regex:/0x[a-fA-F0-9]{40}/m'],
            'token' => ['required', 'string'],
        ]);

        if (hash('sha256', $request->address) == $request->token) {
            if (Web3Login::$retrieveUserCallback) {
                $user = call_user_func(Web3Login::$retrieveUserCallback, strtolower($request->input('address')));
            } else {
                $user = $this->getUserModel()->where('username', strtolower($request->input('address')))->first();
            }

            if (! $user) {
                $user = $this->getUserModel()->create([
                    'username' => strtolower($request->input('address')),
                    'kv' => 1,
                    'ev' => 1,
                    'sv' => 1,
                    'profile_complete' => 1,
                    'is_author' => 1
                ]);
            }

            Auth::login($user);

            return new Response($user, 200);
        } else {
            return response()->json([
                "message" => "invalid token!"
            ], 401);
        }
    }

    public function login(Request $request)
    {
        $this->checkSignature($request);

        if (Web3Login::$retrieveUserCallback) {
            $user = call_user_func(Web3Login::$retrieveUserCallback, strtolower($request->input('address')));
        } else {
            $user = $this->getUserModel()->where('username', strtolower($request->input('address')))->first();
        }

        if (! $user) {
            throw ValidationException::withMessages(['address' => 'Address not registered.']);
        }

        Auth::login($user);

        return new Response($user, 200);
    }

    private function checkSignature(Request $request) {
        $request->validate([
            'address' => ['required', 'string', 'regex:/0x[a-fA-F0-9]{40}/m'],
            'signature' => ['required', 'string', 'regex:/^0x([A-Fa-f0-9]{130})$/'],
        ]);
        $nonce = $request->session()->pull('nonce');

        if (!$nonce) {
            throw ValidationException::withMessages(['signature' => 'Nonce not found. Please generate a sign message first.']);
        }

        if (! Signature::verify($nonce, $request->input('signature'), $request->input('address'))) {
            throw ValidationException::withMessages(['signature' => 'Signature verification failed.']);
        }
    }

    protected function getUserModel() : Model
    {
        return app(config('auth.providers.users.model'));
    }
}
