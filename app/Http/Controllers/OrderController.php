<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Availability;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Services\BrevoService;

class OrderController extends Controller
{
    public function clientIndex()
    {
        $orders = Order::where('user_id', auth()->id())->with(['service', 'availability'])->orderBy('created_at', 'desc')->get();
        return Inertia::render('Client/Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function adminIndex()
    {
        $orders = Order::with(['user', 'service', 'availability'])->latest()->get();
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    // Affiche la page de choix du créneau et de paiement
    // public function create(Service $service)
    // {
    //     if (!$service->is_active) {
    //         return redirect()->route('home')->with('error', 'Cette prestation n\'est plus disponible.');
    //     }

    //     // On récupère les créneaux futurs non réservés
    //     $availabilities = Availability::where('is_booked', false)
    //         ->where('date', '>=', now()->toDateString())
    //         ->orderBy('date')
    //         ->orderBy('start_time')
    //         ->get()
    //         ->groupBy(function($date) {
    //             return \Carbon\Carbon::parse($date->date)->format('Y-m-d');
    //         });

    //     return Inertia::render('Public/Book', [
    //         'service' => $service,
    //         'availabilities' => $availabilities,
    //     ]);
    // }
    // 1. Affiche la page de choix du créneau et de paiement (Avec la correction SQLite '0')
    public function create(Service $service)
    {
        if (!$service->is_active) {
            return redirect()->route('home')->with('error', 'Cette prestation n\'est plus disponible.');
        }

        $availabilities = Availability::where('is_booked', 0)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($slot) {
                return \Carbon\Carbon::parse($slot->date)->format('Y-m-d');
            });

        return Inertia::render('Public/Book', [
            'service' => $service,
            'availabilities' => $availabilities,
        ]);
    }

    // 2. Traite le choix, crée la commande en "pending" et redirige vers Stripe
    public function checkout(Request $request)
    {
        
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'availability_id' => 'required|exists:availabilities,id',
            'payment_type' => 'required|in:deposit,full',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $availability = Availability::findOrFail($validated['availability_id']);

        if ($availability->is_booked) {
            return back()->with('error', 'Ce créneau vient juste d\'être réservé par une autre personne.');
        }

        $amountToPay = $validated['payment_type'] === 'deposit' 
            ? $service->price / 2 
            : $service->price;

        $order = Order::create([
            'reference' => 'CMD-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'availability_id' => $availability->id,
            'total_price' => $service->price,
            'deposit_paid' => $amountToPay, 
            'status' => 'pending',
        ]);

        $availability->update(['is_booked' => true]);

        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => auth()->user()->email,
            
            // --- C'EST ICI QUE L'ON AJOUTE LES INFOS POUR LE WEBHOOK ---
            'client_reference_id' => $order->id, 
            'metadata' => [                      
                'order_id' => $order->id,
            ],
            // -----------------------------------------------------------

            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $service->name . ($validated['payment_type'] === 'deposit' ? ' (Acompte 50%)' : ' (Paiement Intégral)'),
                        'description' => 'Rendez-vous le ' . \Carbon\Carbon::parse($availability->date)->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($availability->start_time)->format('H:i'),
                    ],
                    'unit_amount' => intval($amountToPay * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('orders.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
            'cancel_url' => route('orders.cancel') . '?order_id=' . $order->id,
        ]);

        return Inertia::location($checkout_session->url);
    }

    // 3. Retour du client après le paiement réussi sur Stripe
    public function success(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        // Sécurité pour s'assurer que le client ne regarde pas la commande d'un autre
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // On a ENLEVÉ le changement de statut ($order->update) et l'envoi d'emails.
        // C'est désormais le rôle exclusif du StripeWebhookController.
        
        return redirect()->route('client.orders.index')->with('success', 'Votre paiement a bien été reçu ! Vous recevrez un email de confirmation d\'ici quelques instants.');
    }

    public function cancel(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // On libère le créneau et on annule la commande
        $order->availability->update(['is_booked' => false]);
        $order->update(['status' => 'cancelled']);

        return redirect()->route('home')->with('error', 'Le paiement a été annulé. Votre créneau a été libéré.');
    }

    // Nouvelle méthode pour télécharger la facture PDF
    public function downloadInvoice(Order $order)
    {
        // Sécurité
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('facture_' . $order->reference . '.pdf');
    }
}