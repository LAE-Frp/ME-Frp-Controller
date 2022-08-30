<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\UserController;

Route::view('/login', 'login')->name('login');
Route::post('/login', [IndexController::class, 'login']);


// Auth group
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [IndexController::class, 'index'])->name('index');


    Route::resource('users', UserController::class);
    Route::resource('servers', ServerController::class);
    Route::resource('hosts', HostController::class);
    Route::resource('work-orders', WorkOrderController::class);
    Route::resource('work-orders.replies', ReplyController::class);
    // Route::resource('products', ProductController::class);
    // // Route::resource('configurable-options', ConfigurableOptionController::class);
    // Route::resource(
    //     'configurable-option-groups',
    //     ConfigurableOptionGroupController::class
    // );
    // Route::resource('configurable-option-groups.options', ConfigurableOptionController::class);




    Route::get('/logout', [IndexController::class, 'logout'])->name('logout');
});
