<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends ApiController
{

    use AuthenticatesUsers;

    /**
     * @var int $decayMinutes
     */
    protected $decayMinutes = 5;

    /**
     * @var int $maxAttempts
     */
    protected $maxAttempts = 5;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {

        $this->clearLoginAttempts($request);

        return $this->authenticated($this->guard()->user());
    }


    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authenticated($user)
    {
        $jwt = $this->guard()->generateToken($user);

        return $this->responseSuccessWithToken($user->toArray(), (string) $jwt);
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\T
     */
    protected function guard()
    {
        return Auth::guard('api');
    }

}
