<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;
use App\Services\BrevoService;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, BrevoService $brevoService)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // On écoute spécifiquement l'événement "Paiement réussi"
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            // On récupère l'ID de la commande
            $orderId = $session->client_reference_id ?? $session->metadata->order_id;

            if ($orderId) {
                $order = Order::with(['user', 'service', 'availability'])->find($orderId);

                if ($order && $order->status === 'pending') {
                    // 1. On valide la commande
                    $order->update(['status' => 'confirmed']);

                    // 2. On envoie les emails via Brevo en arrière-plan
                    $brevoService->sendConfirmationEmail($order);
                    $brevoService->sendNotificationToAdmin($order);
                    
                    Log::info("Commande {$order->reference} validée via Webhook Stripe.");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}