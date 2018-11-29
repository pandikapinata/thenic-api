<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Task;
use App\Activity;
use App\User;
use Carbon\Carbon;
use App\Http\Resources\Task as TaskResource;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->id;
        //Get all task
        $tasks = Task::with('activity')->where('user_id', $user)->orderBy('date_task', 'desc')->get();
        // return $tasks;
        // Return a collection of $task with pagination
        return TaskResource::collection($tasks);
    }

    public function indexLatest(Request $request, $date)
    {
        $user = $request->user()->id;
        //Get all task
        $tasks = Task::with('activity')->where('user_id', $user)->where('date_task','>',$date)->orderBy('date_task', 'desc')->get();
        // return $tasks;
        // Return a collection of $task with pagination
        return TaskResource::collection($tasks);
    }

    public function tasksDay(Request $request)
    {
        $user = $request->user()->id;
        //Get all task
        $now = Carbon::now('Asia/Makassar')->format('Y-m-d');
        $tasks = Task::with('activity')->where('user_id', $user)->whereRaw("DATE(date_task) = '$now'")->orderBy('date_task', 'desc')->get();
        // return $tasks;
        // Return a collection of $task with pagination
        return TaskResource::collection($tasks);
    }

    public function totalVolumeperDay(Request $request)
    {
        $now = Carbon::now('Asia/Makassar')->format('Y-m-d');
        // return $now;
        $userId = $request->user()->id;
        // return $userId;
        //Total Volume per Day
        // DB::table('texts')->whereRaw("strftime('%Y-%m', created_at) = '2010-1'")->get();
        $volume = Task::where('user_id', $userId)->whereRaw("DATE(date_task) = '$now'")->sum('volume');
        // return $tasks;
        $sets = Task::where('user_id', $userId)->whereRaw("DATE(date_task) = '$now'")->sum('sets');
        return response()->json([
            'volume' => $volume,
            'sets' => $sets
        ]);
    }
 
    public function show($id)
    {
        //Get the task
        $task = Task::findOrfail($id);
 
        // Return a single task
        return new TaskResource($task);
    }
 
    
    public function store(Request $request)  {
 
        $task = $request->isMethod('put') ? Task::findOrFail($request->task_id) : new Task;
        
        $volumeTask = ($request->input('sets'))*($request->input('reps'));
        $idAct = $request->input('activity_id');
        // $nameTask = Activity::where('id', $idAct)->first(['name']);

        $task->id = $request->input('task_id');
        $task->user_id = $request->user()->id;
        $task->activity_id = $request->input('activity_id');
        // $task->name = $nameTask['name'];
        $task->note = $request->input('note');
        $task->sets = $request->input('sets');
        $task->repetition = $request->input('reps');
        // if($task->activity_id == 1){
        //     $task->task_icon = 'http://192.168.43.74:8000/images/pull-up.png';
        // }elseif ($task->activity_id == 2) {
        //     $task->task_icon = 'http://192.168.43.74:8000/images/pull-up.png';
        // }elseif ($task->activity_id == 3) {
        //     $task->task_icon = 'http://192.168.43.74:8000/images/bars.png';
        // }elseif ($task->activity_id == 4) {
        //     $task->task_icon = 'http://192.168.43.74:8000/images/push-up.png';
        // }elseif ($task->activity_id == 5) {
        //     $task->task_icon = 'http://192.168.43.74:8000/images/rings.png';
        // }
        
        $task->volume = $volumeTask;
        $task->date_task = $request->input('date_task');
 
        if($task->save()) {
            return new TaskResource($task);
        } 
        
    }

    public function destroy($id)
    {
        //Get the task
        $task = Task::findOrfail($id);
 
        if($task->delete()) {
            return new TaskResource($task);
        } 
 
    }
 
}
