<?php

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

// トークンリフレッシュ
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});

Route::namespace('Auth')->group(function () {
    Route::post('/register', 'RegisterController@register')->name('register');
    Route::post('/login', 'LoginController@login')->name('login');
});

Route::get('/user', fn() => Auth::user())->name('user');
Route::get('/photos', 'PhotoController@index')->name('photos.index');
Route::get('/photos/{id}', 'PhotoController@show')->name('photos.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::resource('photos', 'PhotoController', ['only' => ['store', 'destroy']]);
    Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photos.comment');
    Route::put('/photos/{id}/like', 'PhotoController@like')->name('photos.like');
    Route::delete('/photos/{id}/like', 'PhotoController@unlike')->name('photos.unlike');
});
