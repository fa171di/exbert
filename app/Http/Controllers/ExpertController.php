<?php

namespace App\Http\Controllers;

use App\DoctorAvailableSlot;
use App\Models\Expert;
use App\Models\ExpertAvailableDay;
use App\Models\ExpertAvailableSlot;
use App\Models\ExpertAvailableTime;
use App\Models\Specialization;
use Exception;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExpertController extends Controller
{
    public function index($id){
        $specialization = Specialization::find($id);
        if ($specialization) {
            $experts = Expert::with('user')->where('specialization_id', $specialization->id)
                ->where('is_deleted', 0)->get();
            return response()->json([
                'specialization' => $specialization,
                'experts' => $experts,
            ],200);
        }else{
            return response()->json([
               'message' => 'Specialization Not Found..!',
            ],404);
        }
    }

    public function show($id){
        $user = auth()->user();
        $expert = Expert::with('user')->find($id);
        if ($expert){
            $expert_available_day = '';
            $expert_available_time = '';
            $dayArray = collect();
            $expert_available_day = ExpertAvailableDay::where('expert_id', $expert->id)->first()->toArray();
            if ($expert_available_day['sun'] == 0) {
                $dayArray->push(0);
            }
            if ($expert_available_day['mon'] == 0) {
                $dayArray->push(1);
            }
            if ($expert_available_day['tue'] == 0) {
                $dayArray->push(2);
            }
            if ($expert_available_day['wen'] == 0) {
                $dayArray->push(3);
            }
            if ($expert_available_day['thu'] == 0) {
                $dayArray->push(4);
            }
            if ($expert_available_day['fri'] == 0) {
                $dayArray->push(5);
            }
            if ($expert_available_day['sat'] == 0) {
                $dayArray->push(6);
            }
            $expert_available_time = ExpertAvailableTime::where('expert_id', $expert->id)->where('is_deleted', 0)->get();

            return response()->json([
                'available_days' => $expert_available_day,
                'available_time' => $expert_available_time,
                'days' => $dayArray,
                'expert' => $expert,
            ],200);
        }else{
            return response()->json([
                'message' => 'Expert Not Found..!',
            ],404);
        }
    }

    public function search(Request $request):JsonResponse{
        $query = Expert::query()->join('users','experts.user_id','=','users.id')
            ->join('specializations','experts.specialization_id','=','specializations.id')
            ->select('experts.id','title','degree',
            'experience','first_name','last_name','name','description')->orderBy('id');
        $columns = ['title','degree','experience','first_name','last_name','name','description'];
        foreach ($columns as $column){
            $query->orWhere($column,'LIKE','%' . $request->val . '%');
        }
        if ($query->count()){
            $expert = $query->get();
            return response()->json([
                'result' => $expert,
            ],200);
        }else {
            return response()->json([
                'result' => 'No Results..!',
            ], 200);
        }
    }

    public function slots(Request $request){
        $timeId = $request->time_id;
        $expertId  = $request->expert_id;
        $date  = $request->dates;
        $dates = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');

        $appointment_slot = ExpertAvailableSlot::with(['appointment' => function ($re) use ($dates) {
            $re->where('appointment_date', $dates);
        }])
            ->where('expert_available_time_id', $timeId)->get();
        $slots[]=null;
        $i=0;
        foreach ($appointment_slot as $slot){
            if ($slot->appointment) {
                $slots[$i] = $slot;
                $i++;
            }
        }
        return response()->json([
            'appointment_slot' => $slots,
            'slots'=>$appointment_slot,
            'date' => $dates,
            'expertId' => $expertId
        ]);
    }
}
