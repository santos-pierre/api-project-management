<?php

use App\Models\User;
use App\Models\Project;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/project', function () {
    return ProjectResource::collection(Project::all());
});

Route::get('/user', function () {
    return UserResource::collection(User::all());
});

Route::get('/tasks', function () {
    return TaskResource::collection(Task::all());
});

Route::get('/task', function () {
    return new TaskResource(Task::find(1));
});

Route::post('/user/exist/email', 'FindUserByEmailController');
Route::post('/register', 'RegisterUserController');
