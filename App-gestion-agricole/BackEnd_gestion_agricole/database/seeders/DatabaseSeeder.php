<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 0. Rôles de base
        DB::table('roles')->truncate();
        $roles = [
            ['nom' => 'Administrateur', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Agriculteur', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'OuvrierAgricole', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles')->insert($roles);

        // 1. Nettoyage des tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('utilisateurs')->truncate();
        DB::table('cultures')->truncate();
        DB::table('parcelles')->truncate();
        DB::table('finances')->truncate();
        DB::table('rendements')->truncate();
        DB::table('taches')->truncate();
        DB::table('capteur_io_ts')->truncate();
        DB::table('analyses')->truncate();
        DB::table('recommandations')->truncate();
        DB::table('notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 2. Utilisateurs
        DB::table('utilisateurs')->insert([
            [
                'nom' => 'Admin Test',
                'email' => 'admin@test.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Agriculteur Test',
                'email' => 'agriculteur@test.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Ouvrier Test',
                'email' => 'ouvrier@test.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Pierre Martin',
                'email' => 'pierre.martin@example.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Sophie Lambert',
                'email' => 'sophie.lambert@example.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Lier les utilisateurs à leurs rôles dans la table pivot
        $utilisateurs = DB::table('utilisateurs')->get();
        $roles = DB::table('roles')->pluck('id', 'nom');
        $pivot = [];
        foreach ($utilisateurs as $u) {
            if ($u->email === 'admin@test.com') {
                $pivot[] = [
                    'utilisateur_id' => $u->id,
                    'role_id' => $roles['Administrateur'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            } elseif ($u->email === 'agriculteur@test.com' || $u->email === 'pierre.martin@example.com') {
                $pivot[] = [
                    'utilisateur_id' => $u->id,
                    'role_id' => $roles['Agriculteur'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            } elseif ($u->email === 'ouvrier@test.com' || $u->email === 'sophie.lambert@example.com') {
                $pivot[] = [
                    'utilisateur_id' => $u->id,
                    'role_id' => $roles['OuvrierAgricole'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('role_utilisateur')->insert($pivot);

        // 3. Parcelles
        DB::table('parcelles')->insert([
            [
                'nom' => 'Parcelle Nord',
                'surface' => 5.20,
                'localisation' => '3.831812, 11.497601',
                'etat' => 'En culture',
                'agriculteur_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Parcelle Sud',
                'surface' => 3.75,
                'localisation' => '4.057571, 9.711953',
                'etat' => 'En jachère',
                'agriculteur_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Grand Champ',
                'surface' => 12.50,
                'localisation' => '7.377644, 13.515451',
                'etat' => 'En culture',
                'agriculteur_id' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 4. Cultures
        $now = now();
        $currentYear = $now->year;
        
        DB::table('cultures')->insert([
            [
                'nom' => 'Blé d\'hiver',
                'description' => 'Blé d\'hiver cultivé sur la parcelle Nord.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Maïs',
                'description' => 'Maïs cultivé sur la parcelle Sud.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Orge de printemps',
                'description' => 'Orge de printemps cultivé sur la parcelle Sud.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Soja',
                'description' => 'Soja cultivé sur la parcelle Nord.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Colza',
                'description' => 'Colza cultivé sur la parcelle Sud.',
                'created_at' => now(),
                'updated_at' => now()
            ]
            
        ]);
    
        // 5. Finances - Données mensuelles de 2024 à maintenant
        $finances = [];
        $startDate = Carbon::create(2024, 1, 1);
        
        while ($startDate <= $now) {
            $finances[] = [
                'dépenseTotale' => rand(800, 1500),
                'revenu' => rand(2000, 5000),
                'agriculteur_id' => $startDate->month % 2 == 0 ? 2 : 4,
                'created_at' => $startDate->copy(),
                'updated_at' => $startDate->copy()
            ];
            $startDate->addMonth();
        }
        DB::table('finances')->insert($finances);
    
        // 6. Rendements - Récoltes passées et actuelles
        DB::table('rendements')->insert([
            [
                'quantité' => 3200.50,
                'couts' => 1500.00,
                'date' => '2024-06-20', // Récolte 2024
                'culture_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 2500.75,
                'couts' => 1200.00,
                'date' => '2024-09-15', // Récolte 2024
                'culture_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 1800.00,
                'couts' => 900.00,
                'date' => $currentYear.'-07-30', // Récolte récente
                'culture_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 2000.50,
                'couts' => 1000.00,
                'date' => $currentYear.'-05-15', // Récolte récente
                'culture_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 2800.25,
                'couts' => 1350.50,
                'date' => $currentYear.'-06-15', // Récolte récente
                'culture_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 1500,
                'couts' => 800,
                'date' => '2025-02-15',
                'culture_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 1800,
                'couts' => 900,
                'date' => '2025-03-20',
                'culture_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 1200,
                'couts' => 700,
                'date' => '2025-04-10',
                'culture_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 2000,
                'couts' => 1100,
                'date' => '2025-05-15',
                'culture_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quantité' => 2500,
                'couts' => 1300,
                'date' => '2025-06-20',
                'culture_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 7. Tâches
        $taches = [
            ['Labour hivernal', '2024-11-05', '2024-11-10', 'Terminée', 3],
            ['Semis printanier', $currentYear.'-03-15', $currentYear.'-03-20', 'Terminée', 3],
            ['Désherbage', $currentYear.'-05-10', null, 'En cours', 5],
            ['Irrigation', $currentYear.'-06-01', null, 'En cours', 5],
            ['Irrigation', '2025-04-10', '2025-04-12', 'Terminée', 3],
            ['Récolte de blé', '2025-06-25', null, 'En cours', 3],
            ['Semis du blé', '2025-04-15', '2025-04-17', 'Terminée', 5],
            ['Désherbage', '2025-04-20', null, 'En cours', 5]
        ];

        foreach ($taches as $tache) {
            DB::table('taches')->insert([
                'description' => $tache[0],
                'dateDebut' => $tache[1],
                'dateFin' => $tache[2],
                'status' => $tache[3],
                'ouvrier_id' => $tache[4],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 8. Capteurs IoT
        for ($i = 1; $i <= 30; $i++) { // 30 jours de données
            DB::table('capteur_io_ts')->insert([
                'type' => 'Humidité',
                'valeur' => rand(40, 70).'%',
                'dateMesure' => $now->copy()->subDays(30 - $i),
                'parcelle_id' => rand(1, 3),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::table('capteur_io_ts')->insert([
                'type' => 'Température',
                'valeur' => rand(15, 30).'°C',
                'dateMesure' => $now->copy()->subDays(30 - $i),
                'parcelle_id' => rand(1, 3),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 9. Analyses et Recommandations
        $analyses = [
            ['2025-04-15', 'Analyse du sol pour la parcelle 1', 1],
            ['2025-03-10', 'Analyse du pH du sol', 2]
        ];

        foreach ($analyses as $analyse) {
            $analyseId = DB::table('analyses')->insertGetId([
                'dateAnalyse' => $analyse[0],
                'description' => $analyse[1],
                'ia_id' => $analyse[2],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('recommandations')->insert([
                'contenu' => 'Utiliser un engrais riche en azote pour la parcelle '.$analyse[2],
                'dateGeneration' => $analyse[0],
                'analyse_id' => $analyseId,
                'agriculteur_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 10. Notifications
        DB::table('notifications')->insert([
            [
                'message' => 'La tâche d\'irrigation est terminée',
                'dateNotification' => '2025-04-12',
                'ouvrier_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'message' => 'Récolte prévue le 30 juin',
                'dateNotification' => '2025-06-25',
                'ouvrier_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}