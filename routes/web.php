<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/create-chat', [ChatController::class, 'getCreateNewChat'])->name('get_createNewChat');
    Route::post('/create-chat', [ChatController::class, 'postCreateNewChat'])->name('post_createNewChat');

    Route::get('/chat/{id}', [ChatController::class, 'getChat'])->name('getChat');

    Route::post('/chat/{id}/all', [ChatController::class, 'postAllMessages'])->name('postAllMessages');
    Route::post('/chat/{id}/new', [ChatController::class, 'postGetNewMessages'])->name('postGetNewMessages');
    Route::post('/chat/{id}/message', [ChatController::class, 'postSendMessage'])->name('postSendMessage');
    Route::get('/chat/{chatId}/{messageId}/{fileId}', [ChatController::class, 'downloadFile'])->name('downloadFile');
    Route::post('/chat/{chatId}/delete', [ChatController::class, 'postDeleteChat'])->name('postDeleteChat');
});

require __DIR__.'/auth.php';
