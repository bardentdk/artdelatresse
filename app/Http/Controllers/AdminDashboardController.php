<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Availability;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. Chiffre d'affaires total encaissé (Acomptes ou totaux payés via Stripe)
        $totalRevenue = Order::whereIn('status', ['confirmed', 'completed'])
            ->sum('deposit_paid');

        // 2. Rendez-vous du jour
        $todayAppointments = Order::with(['user', 'service', 'availability'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereHas('availability', function ($query) use ($today) {
                $query->where('date', $today);
            })
            ->get()
            ->sortBy(function ($order) {
                return $order->availability->start_time;
            })->values();

        // 3. Prochains rendez-vous (à partir de demain)
        $upcomingAppointments = Order::with(['user', 'service', 'availability'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereHas('availability', function ($query) use ($today) {
                $query->where('date', '>', $today);
            })
            ->take(5)
            ->get()
            ->sortBy(function ($order) {
                return $order->availability->date . ' ' . $order->availability->start_time;
            })->values();

        // 4. Nombre total de réservations confirmées
        $totalBookings = Order::whereIn('status', ['confirmed', 'completed'])->count();

        return Inertia::render('Admin/Dashboard', [
            'totalRevenue' => $totalRevenue,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'totalBookings' => $totalBookings,
        ]);
    }
}