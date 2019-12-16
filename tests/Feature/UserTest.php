<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** test */
    public function user_can_register()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
