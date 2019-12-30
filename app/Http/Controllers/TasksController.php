<?php

namespace App\Http\Controllers;

use App\Task;
use Faker\Provider\Base;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'title' => 'required',
            ]);
        } catch (\Exception $ex) {
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
            return $this->sendResponse($task, 'Task Created', 201);
        } catch (\Exception $ex) {

            // Log the error
            \Log::info($ex->getMessage());

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
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Task $task
     * @return JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        try {
            $request->validate([
                'title' => 'required',
            ]);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 'Validation Failed', 422);
        }


        DB::beginTransaction();
        try {

            $task->update($request->input());
            $task->save();

            DB::commit();
            return $this->sendResponse($task, 'Task Updated', 200);
        } catch (\Exception $ex) {

            // Log the error
            \Log::info($ex->getMessage());

            DB::rollBack();
            // return error
            return $this->sendError('Task not updated', '', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
