<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response(new UserResource($request->user()));
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', 'ProjectController');
    Route::apiResource('projects/{project}/tasks', 'TaskController');
    Route::post('/user/profile', 'UpdateUserProfileController');
});
