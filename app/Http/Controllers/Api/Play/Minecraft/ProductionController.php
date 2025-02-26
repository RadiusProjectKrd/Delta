<?php

namespace App\Http\Controllers\Api\Play\Minecraft;

use App\Http\Controllers\Controller;
use App\Models\Play\Minecraft\Production;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductionController extends Controller
{
    /*public function create(string $prod, string $type, string $ver) : JsonResponse {
        //
    }*/

    public function get(int $id) : JsonResponse {
        try {
            $prod = Production::query()->where('id', '=', $id)->firstOrFail();
            return response()->json([
                'success' => true,
                'production' => $prod
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Проект не найден'
            ], 404);
        }
    }
}
