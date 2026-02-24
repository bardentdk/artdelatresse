<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\OrderController;
use Inertia\Inertia;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StripeWebhookController;

// ==== ROUTES PUBLIQUES ====
Route::get('/', [ServiceController::class, 'index'])->name('home');

// ==== WEBHOOK STRIPE ====
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

// ==== AUTHENTIFICATION ====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==== ROUTES CLIENT (Nécessite connexion) ====
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Client/Dashboard');
    })->name('client.dashboard');

    // Profil
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil/info', [App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::put('/profil/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update.password');


    Route::get('/commande/{order}/facture', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/mes-commandes', [OrderController::class, 'clientIndex'])->name('client.orders.index');
    
    // Processus de réservation
    Route::post('/reserver/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/reserver/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/reserver/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Le {service} dynamique doit toujours être en dernier !
    Route::get('/reserver/{service}', [OrderController::class, 'create'])->name('orders.create');
});

// ==== ROUTES ADMIN (Tresseuse) ====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion du catalogue
    Route::get('/services', [ServiceController::class, 'adminIndex'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Gestion du planning
    Route::get('/planning', [AvailabilityController::class, 'index'])->name('planning.index');
    Route::post('/planning', [AvailabilityController::class, 'store'])->name('planning.store');
    Route::delete('/planning/{availability}', [AvailabilityController::class, 'destroy'])->name('planning.destroy');

    // Gestion des commandes globales
    Route::get('/commandes', [OrderController::class, 'adminIndex'])->name('orders.index');
});