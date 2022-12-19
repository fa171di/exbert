<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
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
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $accessToken=  $user->createToken('Personal Access Token')->accessToken;
        $user->remember_token = $accessToken;
        return response()->json([
            "user" => $user,
            "token" => $accessToken
        ],200);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['name'] =  $user->first_name;
            $token =  $user->createToken('MyApp')-> accessToken;

            return response()->json([
                "user" => $user,
                "token" => $token
            ],200);
//            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return response()->json(['error' => 'Email Or Password Is Not Correct, pleas try again...!'], 401);
        }
    }
}
