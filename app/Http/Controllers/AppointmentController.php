<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Expert;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use http\Env\Response;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function appointment_store(Request $request)
    {
        $user = auth()->user();
        $role = $user->roles;
        $userId = $user->id;
            $request->validate([
                'appointment_for' => 'required',
                'appointment_with' => 'required',
                'appointment_date' => 'required',
                'available_time' => 'required',
                'available_slot' => 'required',
            ]);
            try {
                    $expert = Expert::with('user')->find($request->appointment_with);
                    $usr = User::find($request->appointment_for);
                    $date = $request->appointment_date;
                    $newDate = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
                    $appointment = new Appointment();
                    $appointment->appointment_for = $request->appointment_for;
                    $appointment->appointment_with = $request->appointment_with;
                    $appointment->appointment_date = $newDate;
                    $appointment->available_time = $request->available_time;
                    $appointment->available_slot  = $request->available_slot;
                    $appointment->save();
                    return response()->json([
                        'success' => true,
                        'appointment' => $appointment,
                        'for' => $usr,
                        'with' =>$expert
                    ],200);

            } catch (Exception $e) {
                return response()->json(['error' => 'Something went wrong!!! ' . $e->getMessage()]);
            }
    }

    public function usr_appoints(){
        $user = auth()->user();
        $appointments = Appointment::with('expert','timeSlot')->where('appointment_for',$user->id)
            ->where('is_deleted',0)->get();
        return response()->json([
           'Appointments' => $appointments,
        ]);
    }

    public function exp_appoints(){
        $user = auth()->user();
        $expert = Expert::where('user_id',$user->id)->first();
        $appointments = Appointment::with('user','timeSlot')->where('appointment_with',$expert->id)
            ->where('is_deleted',0)->get();
        return response()->json([
           'Appointments' => $appointments,
        ]);
    }

    public function exp_upcoming_appoints()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $today = Carbon::today()->format('Y/m/d');
        $expert = Expert::where('user_id',$user_id)->first();
        $ex_id = $expert->id;
        $Upcoming_appointment = Appointment::with('user','timeSlot')
            ->where('appointment_with', $ex_id)
            ->whereDate('appointment_date', '>', $today)
            ->where('status', 0)
            ->orderBy('id', 'DESC')->get();
        return response()->json([
            'Upcoming_Appointments' => $Upcoming_appointment,
        ]);
    }

    public function usr_upcoming_appoints(){
        $user = auth()->user();
        $user_id = $user->id;
        $today = Carbon::today()->format('Y/m/d');
        $Upcoming_appointment = Appointment::with('expert','timeSlot')
            ->where('appointment_for', $user_id)
            ->whereDate('appointment_date', '>', $today)
            ->where('status', 0)->get();
        return response()->json([
            'Upcoming_Appointments' => $Upcoming_appointment,
        ]);
    }

    public function cancel_appoint($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->status = 2;
            $appointment->save();
            return response()->json([
                'message' => 'Appointment Canceled successfuly . . '
            ], 200);
        } else {
            return response()->json([
                'Error' => 'Appointment Not Found ..!'
            ],406);
        }
    }

    public function confirm_appoint($id){
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->status = 1;
            $appointment->save();
            return response()->json([
                'message' => 'Appointment Confirmed successfuly . . '
            ],200);
        }else{
            return response()->json([
                'Error' => 'Appointment Not Found ..!'
            ],406);
        }

    }

    public function today_appoints(){
        $user = auth()->user();
        $userID = $user->id;
        $usr = Sentinel::findById($userID);
        $role = $usr->roles[0]->slug;
        $today = Carbon::today()->format('Y/m/d');
        if ($role == 'user'){
            $today_appointments = Appointment::with('expert','timeSlot')
                ->where('appointment_for', $userID)
                ->whereDate('appointment_date', '=', $today)
                ->where('status', 0)->get();
        }elseif ($role == 'expert'){
            $exprt = Expert::where('user_id',$userID)->first();
            $expertID = $exprt->id;
            $today_appointments = Appointment::with('user','timeSlot')
                ->where('appointment_with', $expertID)
                ->whereDate('appointment_date', '=', $today)
                ->where('status', 0)->get();
        }
        return response()->json([
           'today_appointments' => $today_appointments,
        ]);

    }

}
