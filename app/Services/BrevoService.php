<?php

namespace App\Services;

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected TransactionalEmailsApi $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $this->apiInstance = new TransactionalEmailsApi(new Client(), $config);
    }

    public function sendConfirmationEmail($order)
    {
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'Confirmation de votre rendez-vous - L\'Art de la Tresse',
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'L\'Art de la Tresse'),
                'email' => env('MAIL_FROM_ADDRESS')
            ],
            'to' => [
                ['name' => $order->user->name, 'email' => $order->user->email]
            ],
            'htmlContent' => '
                <div style="font-family: Arial, sans-serif; color: #2d241e; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fef6ea;">
                    <h1 style="color: #c08257;">Rendez-vous confirmé !</h1>
                    <p>Bonjour ' . $order->user->name . ',</p>
                    <p>Votre réservation pour la prestation <strong>' . $order->service->name . '</strong> a bien été validée.</p>
                    <p><strong>Date :</strong> ' . \Carbon\Carbon::parse($order->availability->date)->format('d/m/Y') . '<br>
                    <strong>Heure :</strong> ' . \Carbon\Carbon::parse($order->availability->start_time)->format('H:i') . '</p>
                    <p>Montant réglé : ' . number_format($order->deposit_paid, 2, ',', ' ') . ' €</p>
                    <p>Reste à payer sur place : ' . number_format($order->total_price - $order->deposit_paid, 2, ',', ' ') . ' €</p>
                    <p>À très bientôt,<br>L\'Art de la Tresse</p>
                </div>
            '
        ]);

        try {
            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            Log::error('Erreur Brevo (Email Client) : ' . $e->getMessage());
        }
    }

    public function sendNotificationToAdmin($order)
    {
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => '🎉 Nouvelle réservation : ' . $order->service->name,
            'sender' => [
                'name' => 'Système de Réservation',
                'email' => env('MAIL_FROM_ADDRESS')
            ],
            'to' => [
                ['name' => 'Admin Tresseuse', 'email' => env('MAIL_FROM_ADDRESS')]
            ],
            'htmlContent' => '
                <div style="font-family: Arial, sans-serif; color: #2d241e; max-width: 600px; margin: 0 auto; padding: 20px;">
                    <h2>Nouvelle réservation !</h2>
                    <p><strong>Cliente :</strong> ' . $order->user->name . ' (' . $order->user->phone . ')</p>
                    <p><strong>Prestation :</strong> ' . $order->service->name . '</p>
                    <p><strong>Créneau :</strong> Le ' . \Carbon\Carbon::parse($order->availability->date)->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($order->availability->start_time)->format('H:i') . '</p>
                    <p><strong>Montant encaissé via Stripe :</strong> ' . number_format($order->deposit_paid, 2, ',', ' ') . ' €</p>
                </div>
            '
        ]);

        try {
            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            Log::error('Erreur Brevo (Email Admin) : ' . $e->getMessage());
        }
    }

    // NOUVELLE MÉTHODE : Le rappel de planning
    public function sendPlanningReminderEmail($adminUser)
    {
        // On génère l'URL absolue vers la page de l'agenda
        $planningUrl = route('admin.planning.index');

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => '🗓️ Il est temps de mettre à jour votre planning !',
            'sender' => [
                'name' => 'L\'Art de la Tresse - Assistant',
                'email' => env('MAIL_FROM_ADDRESS')
            ],
            'to' => [
                ['name' => $adminUser->name, 'email' => $adminUser->email]
            ],
            'htmlContent' => '
                <div style="font-family: Arial, sans-serif; color: #2d241e; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fef6ea; border-radius: 10px;">
                    <h2 style="color: #c08257; margin-bottom: 20px;">Mise à jour de vos disponibilités</h2>
                    <p>Bonjour ' . $adminUser->name . ',</p>
                    <p>Ceci est votre rappel bi-mensuel automatique.</p>
                    <p>Afin que vos clientes puissent continuer à réserver, pensez à générer vos nouveaux créneaux pour les deux prochaines semaines.</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $planningUrl . '" style="background-color: #2d241e; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                            Mettre à jour mon agenda
                        </a>
                    </div>
                    <p>Bonne journée et bonnes tresses !<br>Votre système de gestion.</p>
                </div>
            '
        ]);

        try {
            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            Log::error('Erreur Brevo (Rappel Planning) : ' . $e->getMessage());
        }
    }
}