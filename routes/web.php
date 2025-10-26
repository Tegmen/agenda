<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [EntryController::class,'upcoming'])->name('home');
    Route::get('/woche', [EntryController::class,'weekly'])->name('weekly');

    Route::get('/eintrag/neu', [EntryController::class,'create'])->can('create','App\Models\Entry');
    Route::post('/eintrag', [EntryController::class,'store']);

    Route::get('/eintrag/{entry}/bearbeiten', [EntryController::class,'edit'])->can('update','entry');
    Route::put('/eintrag/{entry}', [EntryController::class,'update']);

    Route::delete('/eintrag/{entry}', [EntryController::class,'destroy'])->can('delete','entry');

    Route::middleware('can:admin-only')->prefix('admin')->group(function () {
        Route::get('/users', [AdminUserController::class,'index']);
        Route::get('/users/create', [AdminUserController::class,'create']);
        Route::post('/users', [AdminUserController::class,'store']);
        Route::get('/users/{user}/edit', [AdminUserController::class,'edit']);
        Route::put('/users/{user}', [AdminUserController::class,'update']);
        Route::post('/users/{user}/disable', [AdminUserController::class,'disable']);

        Route::get('/groups', [AdminGroupController::class,'index']);
        Route::post('/groups', [AdminGroupController::class,'store']);
        Route::put('/groups/{group}', [AdminGroupController::class,'update']);

        Route::get('/entries', [EntryController::class,'adminIndex']);
    });
});
