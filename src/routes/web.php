<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\KanbanListController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')
    ->prefix('kanban')
    ->name('kanban.')
    ->group(function () {
        Route::get('/', [KanbanController::class, 'index'])->name('index');
});

Route::middleware('auth')
    ->prefix('boards')
    ->name('boards.')
    ->group(function () {
        Route::post('/store', [BoardController::class, 'store'])->name('store');
        Route::put('/{board}', [BoardController::class, 'update'])->name('update');
        Route::delete('/{board}', [BoardController::class, 'destroy'])->name('delete');
        Route::get('/{board}', [BoardController::class, 'show'])->name('show');
});

Route::middleware('auth')
    ->prefix('lists')
    ->name('lists.')
    ->group(function () {
        Route::post('/store', [KanbanListController::class, 'store'])->name('store');
        Route::put('/{kanbanList}', [KanbanListController::class, 'update'])->name('update');
        Route::post('/change-positions', [KanbanListController::class, 'changePositions'])->name('change-positions');
        Route::delete('/{kanbanList}', [KanbanListController::class, 'destroy'])->name('delete');
});

Route::middleware('auth')
    ->prefix('cards')
    ->name('cards.')
    ->group(function () {
        Route::post('/store', [CardController::class, 'store'])->name('store');
        Route::post('/change-positions', [CardController::class, 'changePositions'])->name('change-positions');
        Route::put('/{card}', [CardController::class, 'update'])->name('update');
        Route::delete('/{card}', [CardController::class, 'destroy'])->name('delete');
});

require __DIR__.'/auth.php';
