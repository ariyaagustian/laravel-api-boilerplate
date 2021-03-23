<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'role'])->group( function () {
    Route::middleware(['scope:Superadmin,Manager,User'])->post('logout', [AuthController::class, 'logout']);

    Route::middleware(['scope:Superadmin,Manager,User'])->get('roles', [RoleController::class, 'index']);
    Route::middleware(['scope:Superadmin,Manager'])->get('roles/{id}', [RoleController::class, 'show']);
    Route::middleware(['scope:Superadmin'])->post('roles', [RoleController::class, 'store']);
    Route::middleware(['scope:Superadmin'])->put('roles/{id}', [RoleController::class, 'update']);
    Route::middleware(['scope:Superadmin'])->delete('roles/{id}', [RoleController::class, 'destroy']);
});
