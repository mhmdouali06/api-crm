<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
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
    Route::post("update_role/{role}",[RoleController::class,'update']);
    Route::apiResource("permissions",PermissionController::class);
    Route::apiResource("users",UserController::class); 
    Route::post("update_user/{user}",[UserController::class,'update']);
    Route::apiResource("suppliers",SupplierController::class);
    Route::post("update_supplier/{supplier}",[SupplierController::class,'update']);
    Route::apiResource("categories",CategoryController::class);
    Route::post("update_category/{category}",[CategoryController::class,'update']);

    Route::post("logout",[AuthController::class,'logout']);
});
Route::post("login",[AuthController::class,'login']);

