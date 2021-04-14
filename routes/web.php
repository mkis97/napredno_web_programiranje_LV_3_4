<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectsController;
use Illuminate\Support\Facades\View;

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
    return view('login');
});

Route::get('/register', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if ($loggedId) {
        return redirect('/projects');
    }
    return view('register');
});

Route::get('/login', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if ($loggedId) {
        return redirect('/projects');
    }
    return view('login');
});

Route::post('/register', [UserController::class, 'register']);

Route::post('/login', [UserController::class, 'login']);

Route::get('/logout', [UserController::class, 'logout']);

Route::post('/create-project', [ProjectsController::class, 'create']);

Route::get('/projects', [ProjectsController::class, 'show']);

Route::get('/create-project', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if (!$loggedId) {
        return view('login');
    }
    return view('create-project');
});

Route::get('/assign/{projectId}', [ProjectsController::class, 'assignees']);

Route::post('/assign', [ProjectsController::class, 'assign']);

Route::get('/edit-project/{projectId}', [ProjectsController::class, 'editForm']);

Route::post('/edit-project/{projectId}', [ProjectsController::class, 'edit']);