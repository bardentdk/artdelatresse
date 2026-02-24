<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AvailabilityController extends Controller
{
    // Vue Admin : Gérer le planning
    public function index()
    {
        $availabilities = Availability::orderBy('date')->orderBy('start_time')->get();
        return Inertia::render('Admin/Planning/Index', [
            'availabilities' => $availabilities
        ]);
    }

    // Sauvegarder des créneaux générés (Toutes les 2 semaines)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.date' => 'required|date',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
        ]);

        foreach ($validated['slots'] as $slot) {
            Availability::updateOrCreate(
                [
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                ],
                [
                    'end_time' => $slot['end_time'],
                    'is_booked' => false,
                ]
            );
        }

        return redirect()->route('admin.planning.index')->with('success', 'Planning mis à jour.');
    }

    public function destroy(Availability $availability)
    {
        if (!$availability->is_booked) {
            $availability->delete();
        }
        return back()->with('success', 'Créneau supprimé.');
    }
}