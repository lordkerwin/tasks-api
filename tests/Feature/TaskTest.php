<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Task;


class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_get_all_their_tasks()
    {
        $this->withoutExceptionHandling();
        // create a user
        $user = factory(User::class)->create();
        // authenticate as user
        Passport::actingAs($user);
        // create some tasks and assign to that user
        factory(Task::class, 5)->create([
            'user_id' => $user->id
        ]);
        $response = $this->json('GET', 'api/tasks');
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_create_a_task()
    {
        $this->withoutExceptionHandling();
        // create and authenticate as user
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // create a task and assign it to a user
        $task = factory(Task::class)->raw();
        $response = $this->json('POST', '/api/tasks/store', $task);
        dd($response);
        $response->assertStatus(200);

    }
}
