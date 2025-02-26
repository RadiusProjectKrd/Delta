<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'message' => 'Validation Error'
            ], 400);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::query()->create($input);
        $success['token'] = $user->createToken('access')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $success['token']
        ]);
    }

    public function login(Request $request) {
        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('access')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $success['token']
            ]);
        }
        else{
            return response()->json([
                'success' => 'false',
                'message' => 'Validation Error'
            ], 400);
        }
    }
}
