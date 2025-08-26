<?php

namespace App\Http\Controllers\Api\Play\Minecraft;

use App\Http\Controllers\Controller;
use App\Models\Play\Minecraft\Player;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function get() {
        if (Auth::check()) {
            $user = Auth::guard('api')->user();
            try {
                $player = Player::query()->where('user_id', '=', $user->id)->firstOrFail();
                return response()->json(['success' => true, 'data' => $user]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['success' => false, 'message' => 'Вы не зарегестрированы в Zetta, свяжитесь с администрацией'], 403);
            }
        } else {
            return response()->json(['success' => false], 403);
        }
    }
}
