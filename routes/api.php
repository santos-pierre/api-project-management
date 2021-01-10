<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\Github\GithubController;
use App\Http\Controllers\FindUserByEmailController;
use App\Http\Controllers\UpdateUserProfileController;
use Illuminate\Support\Facades\Cookie;

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

// Auth Route

//Github Provider
Route::middleware('web')->group(function () {
    Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);
});

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('register', RegisterUserController::class)->name('register');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
Route::get('user/exist/email', FindUserByEmailController::class);
//End Auth Route

//Access Cookies
Route::get('/cookies/{cookie_name}', function ($cookie_name) {
    $cookie = Cookie::get($cookie_name);
    if ($cookie) {
        return response($cookie, 200);
    } else {
        return response('cookie not found', 404);
    }
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response(new UserResource($request->user()));
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'currentUser']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects/{project}/tasks', TaskController::class);
    Route::post('/user/profile', [UpdateUserProfileController::class, 'updateUserInfo'])->name('users.update');
    Route::get('/github/commits/{owner}/{repo}', [GithubController::class, 'commits'])->name('github.commits');
});
