<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Play\Minecraft\PackageController;
use App\Http\Controllers\Api\Play\Minecraft\ProductionController;
use App\Http\Controllers\Api\Play\Minecraft\TelegramController as MinecraftTelegram;
use App\Http\Controllers\Api\Security\TelegramController as SecurityTelegram;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return response()->json(['success' => true]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);

Route::post('/bot/minecraft', [MinecraftTelegram::class, 'handler']);
Route::post('/bot/security', [SecurityTelegram::class, 'handler']);

Route::get('/production/get/{name}', [ProductionController::class, 'get'])->middleware(['auth:sanctum']);
//Route::get('/production/create/{name}/{type}/{ver}', [ProductionController::class, 'create']);

Route::get('/package/download/{prod}', [PackageController::class, 'latest'])->middleware(['auth:sanctum']);
Route::get('/package/download/{prod}/{build}', [PackageController::class, 'download'])->middleware(['auth:sanctum']);
//Route::get('/package/create/{prod}/{ver}', [PackageController::class, 'create']);

Route::any('/{path?}', function () {
    return response()->json(['success' => false, 'error' => 'Not found'], 404);
})->where('path', '(.*)');
