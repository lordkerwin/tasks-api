<?php

namespace App\Http\Controllers;

use App\Task;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TasksController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)->get();
        if ($tasks) {
            return $this->sendResponse($tasks, 'Tasks Found');
        } else {
            return $this->sendError('', 'No Tasks Found', 404);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('-- Begin Creating Task --');
        Log::info($request);

        try {
            $request->validate([
                'title' => 'required',
            ]);
        } catch (Exception $ex) {
            Log::info('-- Task Validation Failed --');
            return $this->sendError($ex->getMessage(), 'Validation Failed', 422);
        }

        DB::beginTransaction();

        try {
            $task = Task::create([
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'due_date' => $request->input('due_date.date'),
                'user_id' => auth()->user()->id,
                'assignee_id' => auth()->user()->id,
            ]);
            $task->save();

            DB::commit();
            Log::info('-- End Creating Task --');
            return $this->sendResponse($task, 'Task Created', 201);

        } catch (Exception $ex) {
            // Log the error
            Log::info($ex->getMessage());

            DB::rollBack();
            // return error
            return $this->sendError('Task not created', '', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $task_id
     * @return JsonResponse
     */
    public function show($task_id)
    {
        $user = auth()->user();
        $task = Task::where('user_id', $user->id)->find($task_id);
        if ($task) {
            return $this->sendResponse($task, 'Task Found');
        } else {
            return $this->sendError('', 'Task not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        $user = auth()->user();

        if ($user->cannot('update', $task)) {
            return $this->sendError('This task does not belong to you', '', 401);
        }

        try {
            $request->validate([
                'title' => 'required',
            ]);
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Validation Failed', 422);
        }

        DB::beginTransaction();
        try {
            $input = $request->only(['title', 'body', 'due_date', 'assignee_id']);
            $task->update($input);
            $task->save();

            DB::commit();
            return $this->sendResponse($task, 'Task Updated', 200);
        } catch (Exception $ex) {

            // Log the error
            Log::info($ex->getMessage());

            DB::rollBack();
            // return error
            return $this->sendError('Task not updated', '', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        $user = auth()->user();

        Log::info('>> User ' . $user->name . ' (ID #' . $user->id . ') has requested to destroy task #' . $id);

        if (!$task) {
            Log::info('>> Task #' . $id . ' could not be found');
            return $this->sendError('Task could not be found', '', 404);
        }

        if ($user->cannot('delete', $task)) {
            Log::info('>> User #' . $user->id . ' does not have permission to destroy task ' . $id);
            return $this->sendError('This task does not belong to you', '', 401);
        }


        DB::beginTransaction();
        try {
            Log::info('>> Attempting to delete task #' . $task->id);
            $task->delete();
            DB::commit();
            Log::info('>> Task #' . $task->id . ' Deleted');
            return $this->sendResponse(null, 'Task Deleted', 200);
        } catch (Exception $ex) {
            // Log the error
            Log::info('>> There has been an error whilst trying to delete task #' . $task->id);
            Log::debug($ex->getMessage());
            DB::rollBack();
            // return error
            return $this->sendError('Task could not be deleted', '', 400);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function restore($id)
    {

        $task = Task::withTrashed()->find($id);

        if (!$task) {
            return $this->sendError('Task could not be found', '', 404);
        }
        DB::beginTransaction();
        try {
            $task->restore();
            DB::commit();
            return $this->sendResponse($task, 'Task Restored', 200);
        } catch (Exception $ex) {
            // Log the error
            Log::info($ex->getMessage());

            DB::rollBack();
            // return error
            return $this->sendError('Task could not be restored', '', 400);
        }
    }
}
