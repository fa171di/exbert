<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\Specialization;
use Exception;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $expert = Expert::with('user')->find($id);
        if ($expert){
            return response()->json([
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
}
