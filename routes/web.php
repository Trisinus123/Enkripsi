<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncryptionController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/form', [EncryptionController::class, 'showForm'])->name('form');
Route::post('/encrypt', [EncryptionController::class, 'encrypt'])->name('encrypt');
Route::get('/decrypt/{id}', [EncryptionController::class, 'decrypt'])->name('decrypt');
Route::get('/download-encrypted/{id}', [EncryptionController::class, 'downloadEncrypted'])->name('downloadEncrypted');