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
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\AtoresController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmesRestController;
use App\Http\Controllers\AtoresRestController;
use App\Http\Controllers\DiretoresRestController;
use App\Http\Controllers\UsersRestController;




Route::get('/', function () {
    return redirect('/filmes');
});

    //  FILMES

    Route::get('/filmes/random',  [FilmesController::class, 'getRandom']);

    Route::get('/filmes/search', [FilmesController::class, 'search']);

    Route::get('filmes/genero/{genero}', [FilmesController::class, 'genero']);

    //  END FILMES

    Route::middleware('guest')->group(function () {

    // ROTAS REST DE USUÁRIO

    Route::get('/api/users', [UsersRestController::class, 'index']);
    Route::get('/api/users/{id}', [UsersRestController::class, 'show']);
    Route::post('/api/users', [UsersRestController::class, 'store']);
    Route::put('/api/users/{id}', [UsersRestController::class, 'update']);
    Route::delete('/api/users/{id}', [UsersRestController::class, 'destroy']);

    // FINAL ROTAS REST DE USÁRIOS

    // ROTAS REST DE DIRETORES

    Route::get('/api/diretores', [DiretoresRestController::class, 'index']);
    Route::get('/api/diretores/{id}', [DiretoresRestController::class, 'show']);
    Route::post('/api/diretores', [DiretoresRestController::class, 'store']);
    Route::put('/api/diretores/{id}', [DiretoresRestController::class, 'update']);
    Route::delete('/api/diretores/{id}', [DiretoresRestController::class, 'destroy']);

    // FINAL ROTAS REST DE DIRETORES

    // ROTAS REST DE ATORES

    Route::get('/api/atores', [AtoresRestController::class, 'index']);
    Route::get('/api/atores/{id}', [AtoresRestController::class, 'show']);
    Route::post('/api/atores', [AtoresRestController::class, 'store']);
    Route::put('/api/atores/{id}', [AtoresRestController::class, 'update']);
    Route::delete('/api/atores/{id}', [AtoresRestController::class, 'destroy']);

    // FINAL ROTAS REST DE ATORES

    // ROTAS REST DE FILMES

    Route::get('/api/filmes', [FilmesRestController::class, 'index']);
    Route::get('/api/filmes/{id}', [FilmesRestController::class, 'show']);
    Route::post('/api/filmes', [FilmesRestController::class, 'store']);
    Route::put('/api/filmes/{id}', [FilmesRestController::class, 'update']);
    Route::delete('/api/filmes/{id}', [FilmesRestController::class, 'destroy']);

    // FINAL ROTA REST DE FILMES

    // LOGIN

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

    // END LOGIN
});

    // FILMES

Route::resource('/filmes', FilmesController::class)
    ->except(['show']);

Route::get('/filmes/{filmes}/filmes', [FilmesController::class, 'saibaMais'])
->name('filmes.saiba_mais');

Route::get('/filmes/search', [FilmesController::class, 'search'])->name('filmes.search');

    // END FILMES


Route::middleware(['auth', 'admin'])->group(function ()
{
    //  ROTAS DE FILME:
    Route::get('/filmes/create', [FilmesController::class, 'create'])->name('filmes.create');
    Route::post('/filmes/createApi', [FilmesController::class, 'apiStore'])->name('filmes.createApi');
    Route::get('/filmes/{filme}/editar',[FilmesController::class, 'edit'])->name('filmes.edit');
    Route::delete('/filmes/{filme}', [FilmesController::class, 'destroy'])->name('filmes.destroy');

    // ROTAS DE DIRETOR:
    Route::post('/diretores_create', [DiretoresController::class, 'store'])->name('diretores.store');
    Route::get('/diretores', [DiretoresController::class, 'list'])->name('diretores.list');
    Route::put('/diretores/{id}', [DiretoresController::class, 'update'])->name('diretores.update');
    Route::delete('/diretores/{id}', [DiretoresController::class, 'destroy'])->name('diretores.destroy');

    // ROTAS DE ATOR:
    Route::post('/atores', [AtoresController::class, 'store'])->name('atores.store');
    Route::get('/atores', [AtoresController::class, 'list'])->name('atores.list');
    Route::get('/atores/{id}/edit', [AtoresController::class, 'edit'])->name('atores.edit');
    Route::put('/atores/{id}', [AtoresController::class, 'update'])->name('atores.update');
    Route::delete('/atores/{id}', [AtoresController::class, 'destroy'])->name('atores.destroy');

});



Route::middleware('auth')->group(function () {

    // AUTENTICAÇÃO

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

    // END AUTENTICAÇÃO

    // USERS

    Route::get('/users/editar', [UsersController::class, 'editar'])->name('users.editar');

    Route::put('/users/atualizar', [UsersController::class, 'atualizar'])->name('users.atualizar');

    Route::get('/users/list', [UsersController::class, 'list'])->name('users.list');

    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

    // END USERS

    // AVALIAÇÃO

    Route::post('/filmes/{filmes}/comentar', [FilmesController::class, 'comentar'])
    ->name('filmes.comentar');

    // END AVALIAÇÃO

});


