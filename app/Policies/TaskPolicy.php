<?php

namespace App\Policies;

use App\Task;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tasks.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        return $user->id == $task->user_id;
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        return $user->id == $task->user_id;
    }

    /**
     * Determine whether the user can restore the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function restore(User $user, Task $task)
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can permanently delete the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function forceDelete(User $user, Task $task)
    {
        //
    }
}
