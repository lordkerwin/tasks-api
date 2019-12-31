<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
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
            'assignee_id' => $user->id,
            'user_id' => $user->id,
        ]);
        $response = $this->json('GET', 'api/tasks');
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_get_a_single_task()
    {
        $this->withoutExceptionHandling();
        // create a user
        $user = factory(User::class)->create();
        // authenticate as user
        Passport::actingAs($user);
        // create some tasks and assign to that user
        $task = factory(Task::class)->create([
            'assignee_id' => $user->id,
            'user_id' => $user->id,
        ]);
        $response = $this->json('GET', 'api/tasks/1');


        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'body',
                    'due_date',
                    'assignee_id',
                    'user_id'
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title
                ],
                'message' => "Task Found",
            ]);

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
        $response = $this->postJson('/api/tasks/store', $task);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => $task['title'],
            'assignee_id' => $user->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_update_a_task()
    {
        $this->withoutExceptionHandling();
        // create and authenticate as user
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        // create a task and assign it to a user
        $task = factory(Task::class)->create([
            'assignee_id' => $user->id,
            'user_id' => $user->id,
        ]);

        $payload = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph
        ];

        $response = $this->putJson('/api/tasks/' . $task->id . '/update', $payload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => $payload['title'],
            'body' => $payload['body']
        ]);
    }

    /** @test */
    public function user_can_delete_a_task()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $task = factory(Task::class)->create([
            'assignee_id' => $user->id,
            'user_id' => $user->id,
        ]);

        $response = $this->delete('/api/tasks/' . $task->id . '/delete');
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => null,
                'message' => "Task Deleted",
            ]);
    }

    /** @test */
    public function user_can_restore_a_task()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        // create a task that's already been soft-deleted
        $task = factory(Task::class)->create([
            'assignee_id' => $user->id,
            'user_id' => $user->id,
            'deleted_at' => Carbon::now()
        ]);


        $response = $this->patch('/api/tasks/' . $task->id . '/restore');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $task->title
                ],
                'message' => "Task Restored",
            ]);
    }


}
