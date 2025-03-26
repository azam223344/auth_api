<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
public function signup(Request $request){
    $validateUser= Validator::make(
        $request->all(),
        [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',          
                'regex:/[a-z]/',  
                'regex:/[A-Z]/',  
                'regex:/[0-9]/',  
                'regex:/[@$!%*?&]/' 
            ],
        ]);
        if($validateUser->fails()){
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors()->all(),

                ], 422
            );
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'user' => $user
        ], 200);

}
public function login(Request $request){
    $validateUser= Validator::make(
        $request->all(),
        [
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',          
                'regex:/[a-z]/',  
                'regex:/[A-Z]/',  
                'regex:/[0-9]/',  
                'regex:/[@$!%*?&]/' 
            ],
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken('Api Token')->plainTextToken,
                'token_type' => 'Bearer',
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 422);
        }
}
public function logout(Request $request){
    $user = $request->user();
    $user->Tokens()->delete();
    return response()->json([
        'status' => true,
        'user' => $user,
        'message' => 'User Logged Out Successfully',
    ], 200);
}
}

