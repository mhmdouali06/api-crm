<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware("auth:api")->group(function(){
    Route::get("/me",[AuthController::class,'me']);
    Route::apiResource("roles",RoleController::class);
    Route::apiResource("permissions",PermissionController::class);
    Route::apiResource("users",UserController::class); 
});
Route::post("login",[AuthController::class,'login']);

