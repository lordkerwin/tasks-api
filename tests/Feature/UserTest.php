<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'Joe Bloggs',
            'email' => 'joe.bloggs@gmail.com',
            'password' => 'password',
            'c_password' => 'password',
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_register_more_than_once()
    {
        // create a user using faker first
        $user = factory(User::class)->create();
        // try to register that user again!
        $response = $this->json('POST', '/api/auth/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'c_password' => 'password',
        ]);
        // expect a 422 as the user will already exist!
        $response
            ->assertStatus(422);
    }

    /** @test */
    public function user_can_login()
    {
        // install passport
        \Artisan::call('passport:install');
        // create user
        $user = factory(User::class)->create();
        // try to login
        $response = $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => [
                        'name',
                        'email'
                    ]
                ],
            ]);

    }

    /** @test */
    public function user_can_get_user_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/auth/getUser');
        $response->assertJsonStructure([
            'success',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_user_object()
    {
        $response = $this->json('GET', 'api/auth/getUser');
        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/auth/logout');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => "Success! You have logged out!",
        ]);
    }
}
