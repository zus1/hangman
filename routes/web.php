<?php

use App\Constant\RouteName;
use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Controllers\Hangman\Start::class)->name(RouteName::HANGMAN_START);
Route::get('/tic-tac-toe', \App\Http\Controllers\Tik\Start::class)->name(RouteName::TIK_START);

Route::prefix('auth')->group(function () {
    Route::get('/register', \App\Http\Controllers\Auth\RegisterForm::class)->name(RouteName::AUTH_REGISTER_FORM);
    Route::post('/register', \App\Http\Controllers\Auth\Register::class)->name(RouteName::AUTH_REGISTER);
    Route::get('/login', \App\Http\Controllers\Auth\LoginForm::class)->name(RouteName::AUTH_LOGIN_FORM);
    Route::post('/login', \App\Http\Controllers\Auth\Login::class)->name(RouteName::AUTH_LOGIN);
    Route::get('/logout', \App\Http\Controllers\Auth\Logout::class)->name(RouteName::AUTH_LOGOUT);
    Route::get('verify', \App\Http\Controllers\Auth\Verify::class)->name(RouteName::AUTH_VERIFY);
    Route::get('/reset-password-send-from', \App\Http\Controllers\Auth\PasswordReset\SendForm::class)
        ->name(RouteName::AUTH_RESET_PASSWORD_SEND_FORM);
    Route::post('/reset-password-send', \App\Http\Controllers\Auth\PasswordReset\Send::class)
        ->name(RouteName::AUTH_RESET_PASSWORD_SEND);
    Route::get('/rest-password-form', \App\Http\Controllers\Auth\PasswordReset\ResetForm::class)
        ->name(RouteName::AUTH_RESET_PASSWORD_FORM);
    Route::post('/reset-password', \App\Http\Controllers\Auth\PasswordReset\Reset::class)
        ->name(RouteName::AUTH_RESET_PASSWORD);
});

Route::get('me/games', \App\Http\Controllers\Me\Games::class)->name(RouteName::ME_GAMES)->middleware('auth');
