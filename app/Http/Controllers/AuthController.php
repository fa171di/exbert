<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\ExpertAvailableDay;
use App\Models\ExpertAvailableSlot;
use App\Models\ExpertAvailableTime;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function usr_register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phoneNumber'=>'required',
            'address'=>'required',
        ]);
        $input = $request->all();
        if ($request->profile_photo != null) {
            $file = $request->profile_photo;
            $extention = $file->getClientOriginalExtension();
            $imageName = time() . '.' . $extention;
            $file->move('assets/images/users', $imageName);
            $input['profile_photo'] = $imageName;
        }
        $user = Sentinel::registerAndActivate($input);
        $role = Sentinel::findRoleBySlug('user');
        $role->users()->attach($user);
        $usr = User::find($user->id);
        $accessToken=  $usr->createToken('Personal Access Token')->accessToken;
        $usr->remember_token = $accessToken;
        return response()->json([
            "user" => $user,
            "token" => $accessToken
        ],200);
    }

    public function exp_register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phoneNumber'=>'required',
            'address'=>'required',
            'title' => 'required',
            'fees' => 'required',
            'degree' => 'required',
            'experience' => 'required',
            'slot_time' => 'required',
            'mon' => 'required_without_all:tue,wen,thu,fri,sat,sun',
            'tue' => 'required_without_all:mon,wen,thu,fri,sat,sun',
            'wen' => 'required_without_all:mon,tue,thu,fri,sat,sun',
            'thu' => 'required_without_all:mon,wen,tue,fri,sat,sun',
            'fri' => 'required_without_all:wen,tue,mon,thu,sat,sun',
            'sat' => 'required_without_all:wen,tue,mon,thu,fri,sun',
            'sun' => 'required_without_all:wen,tue,mon,thu,fri,sat',
            'TimeSlot.*.from' => 'required',
            'TimeSlot.*.to' => 'required',
            'specialization' => 'required'
        ]);
        $slot_time = $request->slot_time;
        $input = $request->all();
        if ($request->profile_photo != null) {
            $file = $request->profile_photo;
            $extention = $file->getClientOriginalExtension();
            $imageName = time() . '.' . $extention;
            $file->move('assets/images/users', $imageName);
            $input['profile_photo'] = $imageName;
        }
        $user = Sentinel::registerAndActivate($input);
        $role = Sentinel::findRoleBySlug('expert');
        $role->users()->attach($user);
        $usr = User::find($user->id);
        $accessToken=  $usr->createToken('Personal Access Token')->accessToken;
        $usr->remember_token = $accessToken;
        $expert = new Expert();
        $expert->user_id = $user->id;
        $expert->title = $request->title;
        $expert->degree = $request->degree;
        $expert->experience = $request->experience;
        $expert->fees = $request->fees;
        $expert->slot_time = $request->slot_time;
        $expert->specialization_id = $request->specialization;
        $expert->save();
        // Expert Available day record add
        $availableDay = new ExpertAvailableDay();
        $availableDay->expert_id = $user->id;
        if ($availableDay->mon = $request->mon !== Null) {
            $availableDay->mon = $request->mon;
        }
        if ($availableDay->tue = $request->tue !== Null) {
            $availableDay->tue = $request->tue;
        }
        if ($availableDay->wen = $request->wen !== Null) {
            $availableDay->wen = $request->wen;
        }
        if ($availableDay->thu = $request->thu !== Null) {
            $availableDay->thu = $request->thu;
        }
        if ($availableDay->fri = $request->fri !== Null) {
            $availableDay->fri = $request->fri;
        }
        if ($availableDay->sat = $request->sat !== Null) {
            $availableDay->sat = $request->sat;
        }
        if ($availableDay->sun = $request->sun !== Null) {
            $availableDay->sun = $request->sun;
        }
        $availableDay->save();
            $availableTime = new ExpertAvailableTime();
            $availableTime->expert_id = $user->id;
            $availableTime->from = $request->from;
            $availableTime->to = $request->to;
            $availableTime->save();
            $start_datetime = Carbon::parse($request->from)->format('H:i:s');
            $end_datetime = Carbon::parse($request->to)->format('H:i:s');
            $start_datetime_carbon = Carbon::parse($request->from);
            $end_datetime_carbon = Carbon::parse($request->to);
            $totalDuration = $end_datetime_carbon->diffInMinutes($start_datetime_carbon);
            $totalSlots = $totalDuration / $slot_time;
            for ($a = 0; $a <= $totalSlots; $a++) {
                $slot_time_start_min = $a * $slot_time;
                $slot_time_end_min = $slot_time_start_min + $slot_time;
                $slot_time_start = Carbon::parse($start_datetime)->addMinute($slot_time_start_min)->format('H:i:s');
                $slot_time_end = Carbon::parse($start_datetime)->addMinute($slot_time_end_min)->format('H:i:s');
                if ($slot_time_end <= $end_datetime) {
                    // add time slot here
                    $time = $slot_time_start . '<=' . $slot_time_end . '<br>';
                    $availableSlot = new ExpertAvailableSlot();
                    $availableSlot->expert_id = $user->id;
                    $availableSlot->expert_available_time_id = $availableTime->id;
                    $availableSlot->from = $slot_time_start;
                    $availableSlot->to = $slot_time_end;
                    $availableSlot->save();
                }
            }
            $availableSlots = ExpertAvailableSlot::where('expert_id',$user->id)->get();
        return response()->json([
            'user' => $user,
            'expert' => $expert,
            'aviailable_slots' => $availableSlots,
            'token' => $accessToken
        ],200);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = Sentinel::authenticate($validatedData);

        if($user){
            $usr = User::find($user->id);
            $token =  $usr->createToken('MyApp')-> accessToken;
            if ($user->roles[0]->slug == 'user') {
                return response()->json([
                    'user' => $user,
                    'role' => 'user',
                    'token' => $token
                ], 200);
            }elseif ($user->roles[0]->slug == 'expert'){
                $expert = Expert::where('user_id',$user->id)->first();
                return response()->json([
                    'user' => $user,
                    'expert' => $expert,
                    'role' => 'expert',
                    'token' => $token
                ], 200);
            }
        }else{
            return response()->json(['error' => 'Email Or Password Is Not Correct, pleas try again...!'], 401);
        }
    }

    public function index(){
        $users = User::with('expert')->get();
        return response()->json([
            'users' => $users
        ]);
    }

    public function logout(){
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['success' =>'logout_success'],200);
        }else{
            return response()->json(['error' =>'api.something_went_wrong'], 500);
        }
    }
}
