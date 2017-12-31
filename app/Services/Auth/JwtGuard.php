<?php

namespace App\Services\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Auth\UserProvider;
use \Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Guard;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtGuard implements Guard
{

    use GuardHelpers;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $name;


    /**
     * JwtGuard constructor.
     * @param Application $app
     * @param string $name
     * @param UserProvider $provider
     */
    public function __construct(Application $app, $name, UserProvider $provider)
    {
        $this->app = $app;
        $this->name = $name;
        $this->provider = $provider;
    }


    public function guest()
    {
        return !$this->user;
    }

    /**
     * Return the authenticated user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $token = $this->getToken();

        return $this->provider->retrieveById($token);
    }

    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials);
    }


    protected function getToken()
    {
        return Str::replaceFirst('Bearer ', '', $this->app->request->headers->get('Authorization'));
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return ! is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->user = $user;
    }

    public function login($user)
    {
        $this->setUser($user);

    }

    public function generateToken($user)
    {
        $signer = new Sha256();

        $token = (new Builder())
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
            ->set('uid', $user->id) // Configures a new claim, called "uid"
            ->sign($signer, 'secret') // creates a signature using "secret" as key
            ->getToken(); // Retrieves the generated token

        return $token;
    }

}