<?php

namespace App\Http\Controllers;

use App\Task;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TasksController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $tasks = Task::all()->where('user_id', $user->id);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        $validatedData = $request->validate([
//            'title' => 'required|max:2'
//        ]);
//
//        if (!$validatedData) {
//            dd('not valid');
//        }
//
//        $input = $request->all();
//        dd($input['body']);
//        $user = auth()->user();
//        DB::beginTransaction();
//
//        try {
//            $task = new Task();
//            $task->title = $input['title'];
//            $task->body = $request->input('body');
//            $task->due_date = $request->input('due_date');
//            $task->user_id = $user->id;
//            $task->save();
//            DB::commit();
//            $this->sendResponse($task->id, 'Task Created');
//            // all good
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//            DB::rollback();
//            // something went wrong
//            $this->sendError('','','');
//        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
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
