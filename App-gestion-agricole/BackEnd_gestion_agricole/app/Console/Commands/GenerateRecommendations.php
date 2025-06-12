<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parcelle;
use App\Models\Recommandation;
use Illuminate\Support\Facades\Log;

class GenerateRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ia:generate-recommendations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère des recommandations pour les parcelles en utilisant un service IA (simulé pour l\instant).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Début de la génération des recommandations...');

        $parcelles = Parcelle::all();

        if ($parcelles->isEmpty()) {
            $this->warn('Aucune parcelle trouvée dans la base de données.');
            return 0;
        }

        foreach ($parcelles as $parcelle) {
            $this->line("Génération d'une recommandation pour la parcelle : {$parcelle->nom}");

            // Étape 1: Préparer le prompt pour l'IA
            $prompt = "Voici les informations sur une parcelle agricole nommée '{$parcelle->nom}'. ";
            $prompt .= "Surface: {$parcelle->surface} hectares. ";
            $prompt .= "État actuel: {$parcelle->etat}. ";

            // Étape 2: Simuler l'appel à l'IA et générer une réponse adaptée
            if ($parcelle->etat === 'En jachère') {
                $prompt .= "Suggère une culture adaptée pour une mise en culture future.";
                $titre = "Suggestion pour '{$parcelle->nom}'";
                $contenu = "Pour la parcelle '{$parcelle->nom}' qui est en jachère, une culture de légumineuses comme le trèfle ou la luzerne serait idéale pour enrichir le sol en azote avant une culture plus exigeante.";
                $type = 'CULTURE';
                $priorite = 'moyenne';
            } else { // 'En culture' ou autres états
                $prompt .= "Suggère une action d'entretien ou de surveillance pour cette parcelle.";
                $titre = "Surveillance pour '{$parcelle->nom}'";
                $contenu = "La parcelle '{$parcelle->nom}' étant en culture, il est conseillé de surveiller l'apparition de maladies ou de ravageurs. Une inspection visuelle hebdomadaire est recommandée.";
                $type = 'ENTRETIEN';
                $priorite = 'haute';
            }

            $this->info("Prompt généré : " . $prompt); // Pour voir le prompt qui serait envoyé à l'IA

            // Étape 3: Sauvegarder la recommandation dans la base de données
            try {
                Recommandation::create([
                    'parcelle_id' => $parcelle->id,
                    'titre' => $titre,
                    'contenu' => $contenu,
                    'type' => $type,
                    'priorite' => $priorite,
                    'status' => 'proposée',
                ]);
                $this->info("Recommandation créée avec succès pour la parcelle '{$parcelle->nom}'.");
            } catch (\Exception $e) {
                $this->error("Erreur lors de la création de la recommandation pour la parcelle '{$parcelle->nom}': " . $e->getMessage());
                Log::error('Erreur de recommandation IA: ' . $e->getMessage());
            }
        }

        $this->info('Toutes les recommandations ont été générées.');
        return 0;
    }
}
