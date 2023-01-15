<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortManagerController;
use App\Models\Host;

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

Route::middleware('api.token')->get('hosts', function () {
    return Host::paginate(10);
});
