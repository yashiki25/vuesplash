<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::namespace('Auth')->group(function () {
    Route::post('/register', 'RegisterController@register')->name('register');
    Route::post('/login', 'LoginController@login')->name('login');
});

Route::get('/user', fn() => Auth::user())->name('user');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::resource('photos', 'PhotoController', ['only' => ['store', 'destroy']]);
});
