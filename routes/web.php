<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketAssignmentController;
use App\Http\Controllers\TicketRepliesController;
use App\Http\Controllers\Tickets\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

/* Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard'); */

Route::redirect('/dashboard', '/tickets')->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::post('/ticket/{ticket}/replies', [TicketRepliesController::class, 'store'])->name('tickets.replies.store');

    Route::patch('/tickets/{ticket}/assign', TicketAssignmentController::class)->name('tickets.assign');
});

require __DIR__.'/auth.php';
