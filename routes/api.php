<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // --------------- Register and Login ----------------//
    Route::post('register', 'AuthenticationController@register')->name('register');
    Route::post('login', 'AuthenticationController@login')->name('login');
    
    // ------------------ Get Data ----------------------//
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-user', 'AuthenticationController@userInfo')->name('get-user');
        Route::post('logout', 'AuthenticationController@logOut')->name('logout');
        Route::get('time-entries', 'TimeEntryController@index')->name('time-entries.index');
        Route::post('time-entries', 'TimeEntryController@store')->name('time-entries.store');
        Route::put('time-entries/{id}', 'TimeEntryController@update')->name('time-entries.update');
        Route::delete('time-entries/{id}', 'TimeEntryController@destroy')->name('time-entries.destroy');
    
        // Admin-only Routes
        Route::middleware('admin')->group(function () {
            Route::get('/admin/reports/time', [AdminController::class, 'timeReport']);
            Route::get('/admin/reports/time/{year}/{month?}', [AdminController::class, 'timeReport'])
                ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);
            Route::get('/admin/reports/time/{year}', [AdminController::class, 'timeReport'])
                ->where('year', '[0-9]{4}');
            
            // Get specific user's time entries
            Route::get('/admin/users/{userId}/time-entries', [AdminController::class, 'getUserTimeEntries'])
                ->where('userId', '[0-9]+');
        });
    });
});