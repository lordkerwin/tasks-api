<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_register()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => $this->faker->name,
            'email' =>$this->faker->safeEmail,
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
        $this->withoutExceptionHandling();
        // install passport
        \Artisan::call('passport:install');
        // create user
        $user = factory(User::class)->create();
        // try to login
        $response = $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

//        dd($response);

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

    /** @test */
    public function registering_a_user_requires_a_name()
    {
        // try registering a user without a name
        $response = $this->json('POST', '/api/auth/register', [
            'name' => null,
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'c_password' => 'password',
        ]);
        // expect a 422 as the user is missing a name!
        $response
            ->assertStatus(422);
    }

    /** @test */
    public function registering_a_user_requires_an_email()
    {
        // try registering a user without a name
        $response = $this->json('POST', '/api/auth/register', [
            'name' => $this->faker->name,
            'email' => null,
            'password' => 'password',
            'c_password' => 'password',
        ]);
        // expect a 422 as the user is missing an email!
        $response
            ->assertStatus(422);
    }

    /** @test */
    public function registering_a_user_requires_a_password()
    {
        // try registering a user without a name
        $response = $this->json('POST', '/api/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => null,
            'c_password' => null,
        ]);
        // expect a 422 as the user is missing an email!
        $response
            ->assertStatus(422);
    }

    /** @test */
    public function registering_a_user_requires_both_password_fields_to_match()
    {
        // try registering a user without a name
        $response = $this->json('POST', '/api/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'c_password' => null,
        ]);
        // expect a 422 as the user is missing an email!
        $response
            ->assertStatus(422);
    }
}
