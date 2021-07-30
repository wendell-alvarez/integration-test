<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeysController;
use App\Http\Controllers\MailerLiteController;

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

Route::get('/', [ApiKeysController::class, 'index']);
Route::post('validateApiKey',  [ApiKeysController::class, 'validateApiKey']);
Route::get('/logout', [ApiKeysController::class, 'logout']);

Route::get('/list', [MailerLiteController::class, 'index'])->name('list');
Route::post('/add_subs', [MailerLiteController::class, 'store'])->name('add_subs');
Route::post('/get_subscribers', [MailerLiteController::class, 'getSubscribers'])->name('get_subscribers');
Route::get('/subscribe', [MailerLiteController::class, 'subscribe'])->name('subscribe');
Route::get('/subdelete/{id}', [MailerLiteController::class, 'destroy'])->name('subs.delete');
Route::get('/subscriber/{id}', [MailerLiteController::class, 'show'])->name('subscriber.view');
Route::post('/update_sub', [MailerLiteController::class, 'update'])->name('update_sub');