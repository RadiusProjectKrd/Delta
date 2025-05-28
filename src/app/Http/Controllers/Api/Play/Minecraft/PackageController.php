<?php

namespace App\Http\Controllers\Api\Play\Minecraft;

use App\Http\Controllers\Controller;
use App\Models\Play\Minecraft\Package;
use App\Models\Play\Minecraft\Production;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackageController extends Controller
{
    /*public function create($prod, $build) : JsonResponse {
        //
    }*/

    public function download(string $prod, string $build) : JsonResponse|StreamedResponse {
        try {
            $pkg = Package::query()->where('prod', '=', $prod)->where('build', '=', $build)->firstOrFail();
            return Storage::download('package/' . $prod . '-' . $build . '.zip');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Файл не найден'
            ], 404);
        }
    }

    public function latest(string $prod) {
        try {
            $pkg = Package::query()->where('prod', '=', $prod)->orderByDesc('build')->firstOrFail();
            $prod = Production::query()->where('id', '=', $pkg->prod)->first();
            return redirect(url('/storage/package/'.$prod->name.'-'.$pkg->build.'.zip'));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Проект не найден'
            ], 404);
        }
    }
}
