<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\FilmesController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\AtoresController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/filmes');
});

Route::get('/filmes/random',  [FilmesController::class, 'getRandom']);

Route::get('/filmes/search', [FilmesController::class, 'search']);

Route::get('filmes/genero/{genero}', [FilmesController::class, 'genero']);

Route::middleware('guest')->group(function () {

    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::resource('/filmes', FilmesController::class)
    ->except(['show']);

Route::get('/filmes/{filmes}/filmes', [FilmesController::class, 'saibaMais'])
->name('filmes.saiba_mais');


Route::middleware(['auth', 'admin'])->group(function ()
{
    Route::get('/filmes/create', [FilmesController::class, 'create'])->name('filmes.create');

    Route::post('/filmes/createApi', [FilmesController::class, 'apiStore'])->name('filmes.createApi');

    Route::get('/filmes/{filme}/editar',[FilmesController::class, 'edit'])->name('filmes.edit');


    Route::post('/diretores', [DiretoresController::class, 'store'])->name('diretores.store');

    Route::post('/atores', [AtoresController::class, 'store'])->name('atores.store');
});



Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');


    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    Route::get('/users/editar', [PerfilController::class, 'editar'])->name('editar');

    Route::put('/users/atualizar', [PerfilController::class, 'atualizar'])->name('users.atualizar');

    Route::post('/filmes/{filmes}/comentar', [FilmesController::class, 'comentar'])
    ->name('filmes.comentar');

});


Route::get('/filmes/search', [FilmesController::class, 'search'])->name('filmes.search');
