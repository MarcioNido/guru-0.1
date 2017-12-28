<?php

use Illuminate\Auth\GuardHelpers;

class JwtGuard implements \Illuminate\Contracts\Auth\Guard
{

    use GuardHelpers;


    public function guest()
    {
        // TODO: Implement guest() method.
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }
    }

    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        // TODO: Implement setUser() method.
    }
}