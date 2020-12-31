<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Github\GithubController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UpdateUserProfileController;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
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
Route::middleware('web')->group(function () {
    Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response(new UserResource($request->user()));
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects/{project}/tasks', TaskController::class);
    Route::post('/user/profile', [UpdateUserProfileController::class, 'updateUserInfo'])->name('users.update');
    Route::get('/github/commits', [GithubController::class, 'commits'])->name('github.commits');
});
