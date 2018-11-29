<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;
use App\User;
use App\Http\Resources\Activity as ActivityResource;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        //Get all task
        $activities = Activity::all();
 
        // Return a collection of $task with pagination
        return ActivityResource::collection($activities);
    }
 
    public function show($id)
    {
        //Get the task
        $activities = Activity::findOrfail($id);
 
        // Return a single task
        return new ActivityResource($activities);
    }

    public function store(Request $request)  {

        // $url = 'http://192.168.43.74:8000/images/';
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $act = $request->isMethod('put') ? Activity::findOrFail($request->activity_id) : new Activity;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = str_slug($request->input('name')).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            // $imagePath = $destinationPath. "/".  $name;
            $image->move($destinationPath, $name);
            $act->task_icon = $name;
        }
        $act->name = $request->input('name');
        $act->save();
        // if($act->save()) {
        //      return new ActivityResource($act);
        // } 
        $users = User::get(['name','fcm_token']);
        $titleNotif = 'Notification';
        
        
        // return $token_user[0]->fcm_token;
        foreach ($users as $key => $value) {
            $message = 'Hai '.$value->name.' '.$act->name.' baru saja ditambahkan, segera buat aktivitas barumu';
            $this->notification($value->fcm_token, $titleNotif, $message );
        }
        return response()->json([
            'success' => 'Data tersimpan dan pesan sudah terkirim'
            ]);
    }

    public function notification($token, $title, $message)
    {
        $key = 'AIzaSyBq-CsZ-hcU55zIVm9_2wwSP4ZqSVV3P5E';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token=$token;

        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default'
        ];
        
        // $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            // 'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key='.$key,
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }

}
