<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

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

Route::resources([
    'users' => UserManagementController::class,
    'places' => PlaceController::class,
    'dashboard'=>DashboardController::class
]);

//Historique
Route::get('/history', [PlaceController::class, 'history'])->name('places.history');

//route custom pour delete
Route::get('/remove/{id}/', [UserManagementController::class, 'remove'])->name('users.remove');
Route::get('/downgrade/{id}/', [PlaceController::class, 'downgrade'])->name('places.downgrade');
Route::get('/erase/{id}/', [PlaceController::class, 'erase'])->name('places.erase');
Route::get('/delete/{id}/', [UserManagementController::class, 'delete'])->name('users.delete');

//route attribution place
Route::get('/reserve', [UserManagementController::class, 'reserve'])->name('users.reserve');

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
