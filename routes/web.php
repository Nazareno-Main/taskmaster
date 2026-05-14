<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TaskMaster
|--------------------------------------------------------------------------
| Routes are grouped by:
|   1. Guest-only routes (login, register) — redirect to dashboard if logged in
|   2. Authenticated routes (tasks CRUD) — redirect to login if not logged in
|--------------------------------------------------------------------------
*/

// ── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('tasks.index'));

// ── Guest routes (unauthenticated users only) ────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

// ── Authenticated routes (must be logged in) ─────────────────────────────────
Route::middleware('auth')->group(function () {
    // Task CRUD — resourceful routes map to TaskController methods:
    //   GET    /tasks           → index()    (dashboard)
    //   GET    /tasks/create    → create()   (add form)
    //   POST   /tasks           → store()    (save new task)
    //   GET    /tasks/{task}/edit  → edit()  (edit form)
    //   PUT    /tasks/{task}    → update()   (save edits)
    //   DELETE /tasks/{task}    → destroy()  (delete)
    Route::resource('tasks', TaskController::class);

    // Quick status toggle (used by the React component via fetch)
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
