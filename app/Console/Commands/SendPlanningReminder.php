<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BrevoService;

class SendPlanningReminder extends Command
{
    /**
     * Le nom de la commande (à utiliser dans le terminal).
     *
     * @var string
     */
    protected $signature = 'app:send-planning-reminder';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Envoie un email de rappel à la tresseuse pour la mise à jour de son agenda toutes les 2 semaines.';

    /**
     * Exécution de la commande.
     */
    public function handle(BrevoService $brevoService)
    {
        // Récupérer tous les administrateurs (normalement il n'y a que la tresseuse)
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('Aucun administrateur trouvé. Aucun email envoyé.');
            return;
        }

        foreach ($admins as $admin) {
            $brevoService->sendPlanningReminderEmail($admin);
            $this->info("Email de rappel envoyé à {$admin->email}.");
        }

        $this->info('Tâche terminée avec succès.');
    }
}