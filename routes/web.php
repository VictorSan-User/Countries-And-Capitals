<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'startGame'])->name('star_game');
Route::post('/', [MainController::class, 'prepareGame'])->name('prepare_game');

//no jogo
Route::get('/game', [MainController::class, 'game'])->name('game');