<?php

use App\Models\Host;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HostController;
use App\Http\Controllers\PortManagerController;

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

Route::prefix('tunnel')->name('api.tunnel.')->group(function () {
    Route::post('/handler/{server}', [PortManagerController::class, 'handler'])->name('handler');
});

Route::middleware('api.token')->get('hosts', [HostController::class, 'index']);
Route::middleware('api.token')->get('hosts/{host}', [HostController::class, 'show']);
