<?php

namespace App\Http\Controllers\Api\Play\Minecraft;

use App\Http\Controllers\Controller;
use App\Models\Play\Minecraft\Document;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{
    public function get(int $user) : JsonResponse
    {
        return response()->json(Document::query()->where('user_id', $user)->get());
    }

    public function create() {
        //
    }

    public function edit() {
        //
    }

    public function delete() {
        //
    }
}
