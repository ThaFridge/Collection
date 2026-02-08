<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameSearchController;
use App\Http\Controllers\LegoSetController;
use App\Http\Controllers\LegoSearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\MagazineController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// API search
Route::get('/api/games/search', [GameSearchController::class, 'search'])->name('api.games.search');
Route::get('/api/lego/search', [LegoSearchController::class, 'search'])->name('api.lego.search');
Route::get('/api/games/check-duplicate', [GameController::class, 'checkDuplicate'])->name('api.games.check-duplicate');
Route::get('/api/lego/check-duplicate', [LegoSetController::class, 'checkDuplicate'])->name('api.lego.check-duplicate');

// Games
Route::get('/games/wishlist', [GameController::class, 'wishlist'])->name('games.wishlist');
Route::post('/games/{game}/platforms', [GameController::class, 'addPlatform'])->name('games.platforms.store');
Route::patch('/platforms/{platform}', [GameController::class, 'updatePlatform'])->name('platforms.update');
Route::delete('/platforms/{platform}', [GameController::class, 'destroyPlatform'])->name('platforms.destroy');
Route::patch('/platforms/{platform}/toggle-status', [GameController::class, 'togglePlatformStatus'])->name('platforms.toggle-status');
Route::post('/games/{game}/refresh-achievements', [GameController::class, 'refreshAchievements'])->name('games.refresh-achievements');
Route::post('/games/{game}/toggle-tag', [TagController::class, 'toggleGameTag'])->name('games.toggle-tag');
Route::resource('games', GameController::class);

// LEGO
Route::get('/lego/wishlist', [LegoSetController::class, 'wishlist'])->name('lego.wishlist');
Route::patch('/lego/{lego_set}/toggle-status', [LegoSetController::class, 'toggleStatus'])->name('lego.toggle-status');
Route::patch('/lego/{lego_set}/build-status', [LegoSetController::class, 'updateBuildStatus'])->name('lego.build-status');
Route::post('/lego/{lego_set}/toggle-tag', [TagController::class, 'toggleLegoTag'])->name('lego.toggle-tag');
Route::resource('lego', LegoSetController::class)->parameters(['lego' => 'lego_set']);

// Magazines
Route::resource('magazines', MagazineController::class);

// Tags
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

// Import/Export
Route::get('/export', [ExportController::class, 'index'])->name('export.index');
Route::get('/export/games/csv', [ExportController::class, 'exportGamesCsv'])->name('export.games.csv');
Route::get('/export/lego/csv', [ExportController::class, 'exportLegoCsv'])->name('export.lego.csv');
Route::get('/export/json', [ExportController::class, 'exportJson'])->name('export.json');
Route::post('/import/json', [ExportController::class, 'importJson'])->name('import.json');

// Admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::patch('/admin/providers/{provider}', [AdminController::class, 'updateProvider'])->name('admin.providers.update');
Route::post('/admin/providers/{provider}/test', [AdminController::class, 'testProvider'])->name('admin.providers.test');
