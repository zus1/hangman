<?php

use App\Constant\RouteName;
use App\Http\Controllers\Hangman\Create;
use App\Http\Controllers\Hangman\Play;
use Illuminate\Support\Facades\Route;

Route::middleware('api-auth')->group(function () {
    Route::post('/hangmans', Create::class)->name(RouteName::HANGMAN_CREATE);
    Route::post('/tik-tak-toes', \App\Http\Controllers\Tik\Create::class)->name(RouteName::TIK_CREATE);
});

Route::put('/hangmans/{hangman}/play', Play::class)
    ->name(RouteName::HANGMAN_PLAY)
    ->where('hangman', '[0-9]+')
    ->middleware('lower-case');
Route::put('tik-tak-toes/{tik}/play', \App\Http\Controllers\Tik\Play::class)
    ->name(RouteName::TIK_PLAY)
    ->where('tik', '[0-9]+');
Route::get('/words/random', \App\Http\Controllers\Word\Random::class);

Route::get('/languages', \App\Http\Controllers\Language\Retrieve::class)->name(RouteName::LANGUAGE);
Route::post('auth/resend', \App\Http\Controllers\Auth\Resend::class)->name(RouteName::AUTH_EMAIL_RESEND);
