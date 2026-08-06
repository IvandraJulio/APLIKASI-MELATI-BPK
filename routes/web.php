<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Rute Otentikasi untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,2');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Rute khusus untuk peran Pengguna (Pelapor)
    Route::middleware('role:pengguna')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengguna'])->name('dashboard');
        Route::get('/dashboard/tiket', [DashboardController::class, 'tiketSaya'])->name('dashboard.tiket');
        Route::get('/dashboard/detail', [DashboardController::class, 'tiketDetail'])->name('dashboard.detail');
        Route::get('/dashboard/faq', [DashboardController::class, 'faq'])->name('dashboard.faq');
    });

    // Rute khusus untuk peran Kasubbag
    Route::middleware('role:kasubbag')->group(function () {
        Route::get('/kasubbag', [DashboardController::class, 'kasubbag'])->name('kasubbag');
    });

    // Rute khusus untuk peran Solver
    Route::middleware('role:solver')->group(function () {
        Route::get('/solver', [DashboardController::class, 'solver'])->name('solver');
    });

    // Rute khusus untuk peran Operator
    Route::middleware('role:operator')->group(function () {
        Route::get('/operator', [DashboardController::class, 'operator'])->name('operator');
        Route::get('/operator/tiket', [DashboardController::class, 'operatorTiket'])->name('operator.tiket');
        Route::get('/operator/analitik', [DashboardController::class, 'operatorAnalitik'])->name('operator.analitik');
        Route::get('/operator/faq', [DashboardController::class, 'operatorFaq'])->name('operator.faq');
        Route::post('/api/operator/faq', [DashboardController::class, 'storeFaqApi']);
        Route::post('/api/operator/faq/{id}', [DashboardController::class, 'updateFaqApi']);
        Route::delete('/api/operator/faq/{id}', [DashboardController::class, 'deleteFaqApi']);
        Route::get('/api/operator/dataset/export', [DashboardController::class, 'exportDatasetApi']);
    });

    // Rute API AJAX umum (pengguna yang sudah terotentikasi)
    Route::get('/api/tickets', [DashboardController::class, 'getTicketsApi']);
    Route::post('/api/tickets', [DashboardController::class, 'createTicketApi']);
    Route::post('/api/tickets/{id}/actions', [DashboardController::class, 'updateTicketActionApi']);
    Route::post('/api/tickets/{id}/comments', [DashboardController::class, 'addCommentApi']);
    Route::post('/api/chat-recommend', [DashboardController::class, 'chatRecommendApi']);
    Route::get('/api/solvers/busy-status', [DashboardController::class, 'getSolversBusyStatusApi']);

    // Rute API Notifikasi
    Route::get('/api/notifications', [DashboardController::class, 'getNotificationsApi']);
    Route::post('/api/notifications/read', [DashboardController::class, 'markNotificationsReadApi']);
});
