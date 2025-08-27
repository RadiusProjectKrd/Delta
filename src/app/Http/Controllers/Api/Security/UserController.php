<?php

namespace App\Http\Controllers\Api\Security;

use App\Models\Security\Objects;
use App\Models\Security\UnderSecurity;
use App\Models\Security\UserObjects;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function get(Request $request) {
        if (Auth::check()) {
            $user = $request->user();
            try {
                $under_security = UnderSecurity::with('objects')->where('user_id', '=', $user->id)->firstOrFail();
                return response()->json(['success' => true, 'data' => $under_security]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['success' => false, 'message' => 'Вы не зарегестрированы в Radius Security, свяжитесь с администрацией'], 403);
            }
        } else {
            return response()->json(['success' => false], 403);
        }
    }
}
