<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Expert;
use App\Models\User;
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

}
