<?php

use Illuminate\Support\Facades\Route;

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
    return view('landing.index');
});


Route::get('/dashboard', "OpaVerify\OpaVerifyController@dashboard")->name('dashboard');


Route::group(
    ['as' => 'user.', 'namespace' => 'user', 'middleware' => ['auth']],
    function () {
        // auth route goes here

    }
);

Route::group(
    ['as' => 'moderator.', 'prefix' => 'moderator', 'namespace' => 'Moderator', 'middleware' => ['auth', 'moderator']],
    function () {
        // Moderator routwe here 
    }
);

Route::group(
    ['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'moderator', 'admin']],
    function () {
        // Admin Route Here

    }
);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
