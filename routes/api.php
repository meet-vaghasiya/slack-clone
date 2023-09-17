<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('register/verify', [AuthController::class, 'verify']);

//todo -> replace all below router to AUTH router, and also create group add prefix later

//workspace 
Route::post('workspaces', [WorkspaceController::class, 'store']);

//member
Route::post('workspaces/{workspace}/members', [MemberController::class, 'store']);
