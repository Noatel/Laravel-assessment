<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');

Route::softDeletes('users', App\Http\Controllers\UserController::class);
Route::group(['prefix' => 'user', 'as' => 'users.', 'middleware' => ['auth']], function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\UserController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('update');
    Route::get('/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('show');
    Route::get('/{id}/destroy', [App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    Route::post('/upload/photo', [App\Http\Controllers\UserController::class, 'updatePhoto'])->name('updatePhoto');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
