<?php

use App\Http\Controllers\AcceptAnswerController;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\VoteAnswerController;
use App\Http\Controllers\VoteQuestionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [QuestionsController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('questions', QuestionsController::class)->except('show');

//Route::post('/answers/{question}/answers', [AnswersController::class, 'store'])->name('answers.store');

Route::resource('questions.answers', AnswersController::class)->except(['create', 'show']);

Route::get('/questions/{slug}', [QuestionsController::class, 'show'])->name('questions.show');

Route::post('/answers/{answer}/accept', AcceptAnswerController::class)->name('answer.accept');

Route::post('/questions/{question}/favorites', [FavoritesController::class, 'store'])->name('questions.favorite');
Route::delete('/questions/{question}/favorites', [FavoritesController::class, 'destroy'])->name('questions.unfavorite');
Route::post('/questions/{question}/vote', VoteQuestionController::class);

Route::post('/answers/{answer}/vote', VoteAnswerController::class);
