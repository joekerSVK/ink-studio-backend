<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\BookingController;
Route::get('/ping',       [PublicController::class,'ping']);
Route::get('/artists',        [PublicController::class,'artists']);
Route::get('/services',       [PublicController::class,'services']);
Route::get('/portfolio',      [PublicController::class,'portfolio']);             // ?artist_slug=

Route::get('/availability',   [BookingController::class,'availability']);         // ?artist_id=&date=YYYY-MM-DD&service_id=
Route::post('/bookings',      [BookingController::class,'store']);                // vytvor rezerváciu
Route::get('/bookings/{id}',  [BookingController::class,'show']);                 // detail
Route::post('/bookings/{id}/cancel', [BookingController::class,'cancel']);        // zrušenie
Route::post('/contact', [\App\Http\Controllers\PublicController::class, 'contact']);

