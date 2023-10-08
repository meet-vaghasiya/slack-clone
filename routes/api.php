<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\FuncCall;

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

Route::post('/signin', [AuthController::class, 'signin']);
Route::post('register/verify', [AuthController::class, 'verify']);
Route::post('is-email-exist', [AuthController::class, 'isValid']);

Route::get('/accept-invitation/{token}', [MemberController::class, 'acceptInvitation']);

//todo -> replace all below router to AUTH router, and also create group add prefix later
Route::post('/test', [TestController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::group(['prefix' => 'workspaces'], function () {
        Route::controller(WorkspaceController::class)->group(function () {
            Route::post('/', 'store');
        });

        Route::post('/{workspace}/members', [MemberController::class, 'store']);
        Route::get('/{workspace}/members', [MemberController::class, 'index']);

        Route::post('/{workspace_id}/invites', [MemberController::class, 'invites']);
    });
});

//workspace 

//member
