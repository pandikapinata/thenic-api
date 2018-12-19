<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Task;
use App\Activity;
use App\User;
use Carbon\Carbon;
use App\Http\Resources\Task as TaskResource;
use App\Http\Resources\TaskSync as TaskSynced;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function loadDataAfterLogin(Request $request)
    {
        $user = $request->user()->id;
        //Get all task
        $tasks = Task::with('activity')->where('user_id', $user)->where('status_active', 1)->orderBy('date_task', 'desc')->get();
        // return $tasks;
        // Return a collection of $task with pagination
        return TaskResource::collection($tasks);
    }

    public function tasksDay(Request $request)
    {
        $user = $request->user()->id;
        //Get all task
        $now = Carbon::now('Asia/Makassar')->format('Y-m-d');
        $tasks = Task::with('activity')->where('user_id', $user)->where('status_active', 1)->whereRaw("DATE(date_task) = '$now'")->orderBy('date_task', 'desc')->get();
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
        $volume = Task::where('user_id', $userId)->where('status_active', 1)->whereRaw("DATE(date_task) = '$now'")->sum('volume');
        // return $tasks;
        $sets = Task::where('user_id', $userId)->where('status_active', 1)->whereRaw("DATE(date_task) = '$now'")->sum('sets');
        return response()->json([
            'volume' => $volume,
            'sets' => $sets
        ]);
    }

    public function store(Request $request)  {

        $task = new Task;
        $task->user_id = $request->user()->id;
        $task->activity_id = $request->input('activity_id');
        $task->note = $request->input('note');
        $task->sets = $request->input('sets');
        $task->repetition = $request->input('reps');
        $task->volume = $request->input('volume');
        $task->date_task = $request->input('date_task');
        $task->save();
   
        $taskObject[] = new TaskSynced($task);
        return response()->json([
            'data' => $taskObject
        ]);
    }

    public function syncTask(Request $request){

        $array = $request->input('data');
        foreach($array as $row)
        {
            try
            {
                $task = Task::findOrfail($row["taskId"]);
            }
            // catch(Exception $e) catch any exception
            catch(ModelNotFoundException $e)
            {
                $task = new Task;
                $task->user_id = $request->user()->id;
                $idSQL = $row["id"];
                $task->activity_id = $row["activityId"];
                $task->note = $row["taskNote"];
                $task->sets = $row["taskSets"];
                $task->repetition = $row["taskReps"];
                $task->volume = $row["taskVolume"];
                $task->date_task = $row["taskDate"];
                $task->save();
            }
  
            $task->user_id = $request->user()->id;
            $task->activity_id = $row["activityId"];
            $task->note = $row["taskNote"];
            $task->sets = $row["taskSets"];
            $task->repetition = $row["taskReps"];
            $task->volume = $row["taskVolume"];
            $task->date_task = $row["taskDate"];
            $idSQL = $row["id"];
            // return $idSQL;
            $task->save();
            $taskObject[] = [
                'id' => $idSQL,
                'taskId' => $task->id,
                'activityId' => $task->activity_id,
                'taskName' => $task->activity->name,
                'taskNote' => $task->note,
                'taskSets' => $task->sets,
                'taskReps' => $task->repetition,
                'taskVolume' => $task->volume,
                'taskDate' => $task->date_task,
                'taskIcon' => "http://192.168.43.74:8000/images/".$task->activity->task_icon
                
            ];
        }
        return response()->json([
            'data' => $taskObject
        ]);
    }

    public function updateTask(Request $request)  {

        $idTask = $request->input('taskId');
        $task = Task::findOrfail($idTask);
        $task->user_id = $request->user()->id;
        $task->activity_id = $request->input('activity_id');
        $task->note = $request->input('note');
        $task->sets = $request->input('sets');
        $task->repetition = $request->input('reps');
        $task->volume = $request->input('volume');
        $task->date_task = $request->input('date_task');
        $idSQL = $request->input('idSQL');
        $task->save();
   
        $taskObject[] = new TaskSynced($task);
        return response()->json([
            'data' => $taskObject
        ]);
    }

    public function bulkUpdate(Request $request){

        $array = $request->input('data');
        foreach($array as $row)
        {
            try
            {
                $task = Task::findOrfail($row["taskId"]);
            }
            // catch(Exception $e) catch any exception
            catch(ModelNotFoundException $e)
            {
                return "GA ADA";
            }
  
            return "ADA";
            $taskObject[] = [
                'taskId' => $task->id,
                'activityId' => $task->activity_id,
                'taskName' => $task->activity->name,
                'taskNote' => $task->note,
                'taskSets' => $task->sets,
                'taskReps' => $task->repetition,
                'taskVolume' => $task->volume,
                'taskDate' => $task->date_task,
                'taskIcon' => "http://192.168.43.74:8000/images/".$task->activity->task_icon,
                'id' => $idSQL
            ];
        }
        return response()->json([
            'data' => $taskObject
        ]);
    }

    public function softDeleteTask(Request $request)
    {
        $id = $request->input('taskId');
        // return $id;
        $task = Task::findOrfail($id);
        $task->status_active = $request->input('statusDelete');
        $task->save();

        return response()->json([
            'success' => 'Your data has been updated'
        ]);
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
 
}
