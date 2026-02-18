<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tickets\TicketAssigneeController;
use App\Http\Controllers\Tickets\TicketController;
use App\Http\Controllers\Tickets\TicketReplyController;
use App\Http\Controllers\Tickets\TicketStatusController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tickets')->name('home');

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

    Route::post('/tickets/{ticket}/replies', [TicketReplyController::class, 'store'])->name('tickets.replies.store');

    Route::patch('/tickets/{ticket}/assignee', [TicketAssigneeController::class, 'update'])->name('tickets.assignee.update');

    Route::patch('/tickets/{ticket}/status', [TicketStatusController::class, 'update'])->name('tickets.status.update');
});

require __DIR__.'/auth.php';
