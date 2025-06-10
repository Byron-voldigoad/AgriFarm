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
            ],
            [
                'nom' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'motDePasse' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Marie Claire',
                'email' => 'marie.claire@example.com',
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
            ],
            [
                'nom' => 'Parcelle Est',
                'surface' => 8.50,
                'localisation' => '4.123456, 11.654321',
                'etat' => 'En culture',
                'agriculteur_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Parcelle Ouest',
                'surface' => 6.75,
                'localisation' => '3.987654, 10.123456',
                'etat' => 'En jachère',
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
                'photo' => 'images/cultures/ble_hiver.jpg',
                'type_culture' => 'Céréales',
                'conditions_climatiques' => 'Climat tempéré avec des précipitations modérées.',
                'cout_estime' => 1500.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Maïs',
                'description' => 'Maïs cultivé sur la parcelle Sud.',
                'photo' => 'images/cultures/mais.jpg',
                'type_culture' => 'Céréales',
                'conditions_climatiques' => 'Climat chaud avec un bon ensoleillement.',
                'cout_estime' => 2000.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Orge de printemps',
                'description' => 'Orge de printemps cultivé sur la parcelle Sud.',
                'photo' => 'images/cultures/orge_printemps.jpg',
                'type_culture' => 'Céréales',
                'conditions_climatiques' => 'Climat tempéré avec des précipitations modérées.',
                'cout_estime' => 1800.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Soja',
                'description' => 'Soja cultivé sur la parcelle Nord.',
                'photo' => 'images/cultures/soja.jpg',
                'type_culture' => 'Légumineuses',
                'conditions_climatiques' => 'Climat chaud et humide.',
                'cout_estime' => 2200.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Colza',
                'description' => 'Colza cultivé sur la parcelle Sud.',
                'photo' => 'images/cultures/colza.jpg',
                'type_culture' => 'Oléagineux',
                'conditions_climatiques' => 'Climat tempéré avec des précipitations modérées.',
                'cout_estime' => 1700.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Cacao',
                'description' => 'Cacao cultivé dans la région du Centre.',
                'photo' => 'images/cultures/cacao.jpg',
                'type_culture' => 'Arbres fruitiers',
                'conditions_climatiques' => 'Climat tropical humide.',
                'cout_estime' => 2500.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Café',
                'description' => 'Café cultivé dans la région de l\'Ouest.',
                'photo' => 'images/cultures/cafe.jpg',
                'type_culture' => 'Arbres fruitiers',
                'conditions_climatiques' => 'Climat tropical avec des précipitations modérées.',
                'cout_estime' => 2300.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Banane Plantain',
                'description' => 'Banane Plantain cultivée dans la région du Littoral.',
                'photo' => 'images/cultures/banane_plantain.jpg',
                'type_culture' => 'Fruits',
                'conditions_climatiques' => 'Climat chaud et humide.',
                'cout_estime' => 2100.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Manioc',
                'description' => 'Manioc cultivé dans la région de l\'Est.',
                'photo' => 'images/cultures/manioc.jpg',
                'type_culture' => 'Tubercules',
                'conditions_climatiques' => 'Climat tropical humide.',
                'cout_estime' => 1900.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Arachide',
                'description' => 'Arachide cultivée dans la région du Nord.',
                'photo' => 'images/cultures/arachide.jpg',
                'type_culture' => 'Légumineuses',
                'conditions_climatiques' => 'Climat chaud et sec.',
                'cout_estime' => 1700.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Mil',
                'description' => 'Mil cultivé dans la région de l\'Extrême-Nord.',
                'photo' => 'images/cultures/mil.jpg',
                'type_culture' => 'Céréales',
                'conditions_climatiques' => 'Climat aride.',
                'cout_estime' => 1600.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Sorgho',
                'description' => 'Sorgho cultivé dans la région de l\'Adamaoua.',
                'photo' => 'images/cultures/sorgho.jpg',
                'type_culture' => 'Céréales',
                'conditions_climatiques' => 'Climat semi-aride.',
                'cout_estime' => 1800.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Patate Douce',
                'description' => 'Patate Douce cultivée dans la région du Sud.',
                'photo' => 'images/cultures/patate_douce.jpg',
                'type_culture' => 'Tubercules',
                'conditions_climatiques' => 'Climat tropical humide.',
                'cout_estime' => 2000.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Igname',
                'description' => 'Igname cultivée dans la région du Centre.',
                'photo' => 'images/cultures/igname.jpg',
                'type_culture' => 'Tubercules',
                'conditions_climatiques' => 'Climat tropical humide.',
                'cout_estime' => 2200.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Tomate',
                'description' => 'Tomate cultivée dans la région de l\'Ouest.',
                'photo' => 'images/cultures/tomate.jpg',
                'type_culture' => 'Légumes',
                'conditions_climatiques' => 'Climat tempéré avec un bon ensoleillement.',
                'cout_estime' => 2400.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Ananas',
                'description' => 'Ananas cultivé dans la région du Littoral.',
                'photo' => 'images/cultures/ananas.jpg',
                'type_culture' => 'Fruits',
                'conditions_climatiques' => 'Climat chaud et humide.',
                'cout_estime' => 2600.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Papaye',
                'description' => 'Papaye cultivée dans la région du Sud.',
                'photo' => 'images/cultures/papaye.jpg',
                'type_culture' => 'Fruits',
                'conditions_climatiques' => 'Climat tropical humide.',
                'cout_estime' => 2500.00,
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

        // 8. Analyses et Recommandations
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