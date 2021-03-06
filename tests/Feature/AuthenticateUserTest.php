<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_authenticate_with_valid_credentials()
    {
        $existingUser = factory(User::class)->create(['password' => Hash::make('123456')]);

        $this->json('POST', 'v1/login', [
            'email' => $existingUser->email,
            'password' => '123456'
        ])->assertStatus(200)
          ->assertJsonFragment(['token']);
    }

    /** @test */
    public function a_user_cannot_authenticate_with_invalid_credentials()
    {
        $existingUser = factory(User::class)->create(['password' => Hash::make('123456')]);

        $this->json('POST', 'v1/login', [
            'email' => $existingUser->email,
            'password' => '654321'
        ])->assertStatus(422)
            ->assertJsonMissing(['token']);
    }

    /** @test */
    public function a_user_with_invalid_credentials_should_be_blocked_after_5_attempts()
    {
        $existingUser = factory(User::class)->create(['password' => Hash::make('123456')]);

        for ($i=0; $i<=5; $i++) {
            $response = $this->json('POST', 'v1/login', [
                'email' => $existingUser->email,
                'password' => '654321'
            ]);
        }

        $response->assertStatus(423);
    }

//    /** @test */
//    public function a_user_cannot_register_with_invalid_data()
//    {
//        $this->json('POST', 'v1/register', [
//            'name' => '',
//            'email' => '',
//            'password' => '',
//        ])->assertStatus(422);
//    }
//
//    /** @test */
//    public function a_user_cannot_register_with_a_duplicate_email()
//    {
//        $existingUser = factory(User::class)->create();
//
//        $this->json('POST', 'v1/register', [
//            'name' => 'John Doe',
//            'email' => $existingUser->email,
//            'password' => '123456'
//        ])->assertStatus(422)
//          ->assertJsonFragment([
//              'errors' => ['email' => ['The email has already been taken.']]
//          ]);
//
//    }
//
//    /** @test */
//    public function a_user_cannot_register_more_than_3_users_from_same_ip_in_a_short_time()
//    {
//        for ($i=0; $i<=3; $i++) {
//            $response = $this->json('POST', 'v1/register', [
//                'name' => 'John Doe',
//                'email' => 'john@doe.com',
//                'password' => '123456'
//            ]);
//        }
//
//        $response->assertStatus(429);
//
//    }
}
