<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-channel', fn () => view('test-channel'));

Route::get('/test-broadcast', function () {
    event(new \App\Events\TestBroadcastEvent(
        message: 'Test broadcast fired at by me'.now()->toDateTimeString(),
        source: 'web-route',
    ));

    return response()->json(['status' => 'broadcast sent']);
});
