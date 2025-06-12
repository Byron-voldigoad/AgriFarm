<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parcelle;
use App\Models\Analyse;
use App\Models\Recommandation;
use App\Models\Meteo;
use Carbon\Carbon;

class GenerateRecommandations extends Command
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
    protected $description = 'Génère automatiquement des recommandations basées sur les données des capteurs et analyses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer toutes les parcelles avec leurs capteurs récents
        $parcelles = Parcelle::with(['analyses', 'capteurs' => function($query) {
            $query->latest();
        }])->get();
        
        $recommandations = [];
        
        foreach ($parcelles as $parcelle) {
            // Analyser les données récentes
            $capteurs = $parcelle->capteurs;
            $analyses = $parcelle->analyses;
            
            // Analyser les données des capteurs
            $temperature = $capteurs->where('type', 'temperature')->first();
            $humidite = $capteurs->where('type', 'humidite')->first();
            $pluie = $capteurs->where('type', 'pluie')->first();

            // Analyser l'humidité
            if ($humidite && $humidite->valeur < 30) {
                $recommandations[] = [
                    'titre' => 'Arrosage nécessaire',
                    'contenu' => 'L\'humidité du sol est faible. Il est recommandé d\'arroser la parcelle pour maintenir un niveau d\'humidité optimal.',
                    'type' => 'ENTRETIEN',
                    'priorite' => 'haute'
                ];
            }
            
            // Analyser la température
            if ($temperature && $temperature->valeur > 35) {
                $recommandations[] = [
                    'titre' => 'Attention à la chaleur',
                    'contenu' => 'Les températures élevées peuvent affecter les cultures. Il est recommandé de surveiller l\'état des plantes et d\'adapter les horaires d\'arrosage.',
                    'type' => 'ENTRETIEN',
                    'priorite' => 'haute'
                ];
            }
            
            // Analyser la pluie
            if ($pluie && $pluie->valeur > 0) {
                $recommandations[] = [
                    'titre' => 'Prévision de pluie',
                    'contenu' => 'Il est prévu de la pluie. Il est recommandé de surveiller l\'état des plantes et d\'adapter l\'arrosage.',
                    'type' => 'INFORMATION',
                    'priorite' => 'moyenne'
                ];
            }
        }
        
        // Enregistrer les recommandations
        foreach ($recommandations as $recommandation) {
            Recommandation::create($recommandation);
        }
        
        return 0;
    }
}
