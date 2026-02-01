<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameSearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LegoSetController;

Route::get('/', function () {
    return redirect()->route('games.index');
});

// Game API search
Route::get('/api/games/search', [GameSearchController::class, 'search'])->name('api.games.search');

// Games
Route::get('/games/wishlist', [GameController::class, 'wishlist'])->name('games.wishlist');
Route::patch('/games/{game}/toggle-status', [GameController::class, 'toggleStatus'])->name('games.toggle-status');
Route::resource('games', GameController::class);

// LEGO
Route::get('/lego/wishlist', [LegoSetController::class, 'wishlist'])->name('lego.wishlist');
Route::patch('/lego/{lego_set}/toggle-status', [LegoSetController::class, 'toggleStatus'])->name('lego.toggle-status');
Route::patch('/lego/{lego_set}/build-status', [LegoSetController::class, 'updateBuildStatus'])->name('lego.build-status');
Route::resource('lego', LegoSetController::class)->parameters(['lego' => 'lego_set']);

// Admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::patch('/admin/providers/{provider}', [AdminController::class, 'updateProvider'])->name('admin.providers.update');
Route::post('/admin/providers/{provider}/test', [AdminController::class, 'testProvider'])->name('admin.providers.test');
