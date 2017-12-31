<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\Auth\JwtGuard;
use App\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class RegisterController
 *
 * @package App\Http\Controllers\Auth
 * @author Marcio Nido <marcionido@gmail.com>
 * @version 2017-12-30
 */
class RegisterController extends ApiController
{

    protected $rateLimiter;

    /**
     * Create a new controller instance.
     *
     * @param RateLimiter $rateLimiter
     */
    public function __construct(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {

        $this->rateLimitCheck($request);

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return $this->responseUnprocessableEntity($validator->errors());
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($user);
    }

    /**
     * Rate Limiter for registration by IP address
     *
     * @param Request $request
     */
    protected function rateLimitCheck(Request $request)
    {
        $key = 'registration_' . $request->getClientIp();

        $this->rateLimiter->hit($key);
        if ($this->rateLimiter->tooManyAttempts($key, 3, 1)) {
            throw new TooManyRequestsHttpException(1, 'Too many registration requests');
        }

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
        ]);
    }

    /**
     * Return the user created response with the authentication token
     *
     * @param Authenticatable $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(Authenticatable $user)
    {
        $jwt = $this->guard()->generateToken($user);

        return $this->responseCreatedWithToken($user->toArray(), (string) $jwt);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return JwtGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }

}
