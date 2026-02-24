<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    // Vue publique / catalogue
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        return Inertia::render('Public/Catalog', [
            'services' => $services
        ]);
    }

    // Vue Admin : Liste des services
    public function adminIndex()
    {
        $services = Service::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Services/Index', [
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:15',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        Service::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'deposit_amount' => $validated['deposit_amount'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => $validated['is_active'] ?? true,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Prestation ajoutée avec succès.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:15',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $service->image_path;

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'deposit_amount' => $validated['deposit_amount'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => $validated['is_active'] ?? true,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Prestation mise à jour avec succès.');
    }

    public function destroy(Service $service)
    {
        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
            Storage::disk('public')->delete($service->image_path);
        }
        
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Prestation supprimée avec succès.');
    }
}