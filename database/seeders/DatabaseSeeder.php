<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. CLASSES ──────────────────────────────────────────────
        DB::table('classes')->insert([
            ['libelle_classes' => 'BTS Drone 1ère année'],
            ['libelle_classes' => 'BTS Drone 2ème année'],
            ['libelle_classes' => 'Licence Pro Drone'],
        ]);

        // ── 2. FORMATEURS ───────────────────────────────────────────
        DB::table('formateurs')->insert([
            ['login' => 'jmartin', 'mot_de_passe' => Hash::make('password123'), 'nom' => 'Martin', 'prenom' => 'Jacques'],
            ['login' => 'sdurand', 'mot_de_passe' => Hash::make('password123'), 'nom' => 'Durand', 'prenom' => 'Sophie'],
        ]);

        // ── 3. APPRENTIS (10 par classe) ────────────────────────────
        DB::table('apprentis')->insert([
            // Classe 1 — BTS Drone 1ère année
            ['nom' => 'Dupont',    'prenom' => 'Jean',      'id_classes' => 1],
            ['nom' => 'Martin',    'prenom' => 'Emma',      'id_classes' => 1],
            ['nom' => 'Leroy',     'prenom' => 'Théo',      'id_classes' => 1],
            ['nom' => 'Fontaine',  'prenom' => 'Inès',      'id_classes' => 1],
            ['nom' => 'Garnier',   'prenom' => 'Maxime',    'id_classes' => 1],
            ['nom' => 'Chevalier', 'prenom' => 'Lucie',     'id_classes' => 1],
            ['nom' => 'Morin',     'prenom' => 'Antoine',   'id_classes' => 1],
            ['nom' => 'Girard',    'prenom' => 'Manon',     'id_classes' => 1],
            ['nom' => 'Rousseau',  'prenom' => 'Baptiste',  'id_classes' => 1],
            ['nom' => 'Faure',     'prenom' => 'Camille',   'id_classes' => 1],

            // Classe 2 — BTS Drone 2ème année
            ['nom' => 'Bernard',   'prenom' => 'Lucas',     'id_classes' => 2],
            ['nom' => 'Petit',     'prenom' => 'Chloé',     'id_classes' => 2],
            ['nom' => 'Legrand',   'prenom' => 'Romain',    'id_classes' => 2],
            ['nom' => 'Marchand',  'prenom' => 'Jade',      'id_classes' => 2],
            ['nom' => 'Lemaire',   'prenom' => 'Quentin',   'id_classes' => 2],
            ['nom' => 'Dupuis',    'prenom' => 'Océane',    'id_classes' => 2],
            ['nom' => 'Renard',    'prenom' => 'Florian',   'id_classes' => 2],
            ['nom' => 'Blanc',     'prenom' => 'Pauline',   'id_classes' => 2],
            ['nom' => 'Guerin',    'prenom' => 'Alexis',    'id_classes' => 2],
            ['nom' => 'Millet',    'prenom' => 'Sarah',     'id_classes' => 2],

            // Classe 3 — Licence Pro Drone
            ['nom' => 'Robert',    'prenom' => 'Hugo',      'id_classes' => 3],
            ['nom' => 'Moreau',    'prenom' => 'Léa',       'id_classes' => 3],
            ['nom' => 'Simon',     'prenom' => 'Nicolas',   'id_classes' => 3],
            ['nom' => 'Laurent',   'prenom' => 'Anaïs',     'id_classes' => 3],
            ['nom' => 'Michel',    'prenom' => 'Valentin',  'id_classes' => 3],
            ['nom' => 'Lefebvre',  'prenom' => 'Charlotte', 'id_classes' => 3],
            ['nom' => 'Lefevre',   'prenom' => 'Julien',    'id_classes' => 3],
            ['nom' => 'Roux',      'prenom' => 'Marine',    'id_classes' => 3],
            ['nom' => 'David',     'prenom' => 'Kevin',     'id_classes' => 3],
            ['nom' => 'Bertrand',  'prenom' => 'Elise',     'id_classes' => 3],
        ]);

        // ── 4. OBJECTIFS ─────────────────────────────────────────────
        DB::table('objectifs')->insert([
            ['libelle_objectifs' => 'Cerceaux',            'est_automatique' => true],
            ['libelle_objectifs' => 'Atterrissage',        'est_automatique' => true],
            ['libelle_objectifs' => 'Positionnement',      'est_automatique' => true],
            ['libelle_objectifs' => "Maintien d'Altitude", 'est_automatique' => true],
            ['libelle_objectifs' => 'Tours',               'est_automatique' => false],
        ]);

        // ── 5 & 6. SESSIONS + VALIDER ────────────────────────────────
        // Chaque apprenti fait entre 1 et 4 sessions
        // Les résultats s'améliorent au fil des sessions

        $drones  = ['DJI Mini 3', 'DJI Air 3', 'Parrot Anafi'];
        $envs    = ['Extérieur', 'Intérieur'];
        $meteos  = ['Ensoleillé', 'Nuageux', 'Venteux', 'N/A'];
        $durees  = [20, 30, 45, 60];
        $logins  = ['jmartin', 'sdurand'];

        // Quantités à atteindre par objectif (id_objectif => quantite)
        $quantitesMax = [1 => 3, 2 => 2, 3 => 4, 4 => 1, 5 => 5];

        // Résultats par apprenti et par passage :
        // Plus on avance dans les sessions, meilleurs sont les résultats
        // Format : id_apprenti => [ [session1_obj1..5], [session2_obj1..5], ... ]
        $planSessions = [
            // ── Classe 1 — BTS 1ère année (débutants) ──
            1  => [
                [false, false, true,  false, false],
                [true,  false, true,  false, false],
                [true,  true,  true,  false, true ],
            ],
            2  => [
                [false, false, false, false, false],
                [true,  false, true,  false, false],
            ],
            3  => [
                [true,  false, false, true,  false],
                [true,  true,  false, true,  false],
                [true,  true,  true,  true,  false],
            ],
            4  => [
                [true,  true,  true,  false, false],
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            5  => [
                [false, false, false, false, false],
                [true,  false, true,  false, false],
                [true,  true,  true,  false, false],
                [true,  true,  true,  true,  false],
            ],
            6  => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            7  => [
                [true,  false, false, false, false],
                [true,  true,  false, false, true ],
                [true,  true,  true,  false, true ],
            ],
            8  => [
                [false, false, false, true,  false],
                [true,  false, false, true,  false],
                [true,  true,  false, true,  false],
            ],
            9  => [
                [true,  true,  false, false, false],
                [true,  true,  true,  false, true ],
                [true,  true,  true,  true,  true ],
            ],
            10 => [
                [true,  false, false, true,  false],
                [true,  true,  false, true,  true ],
            ],

            // ── Classe 2 — BTS 2ème année (intermédiaires) ──
            11 => [
                [true,  false, true,  true,  false],
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            12 => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            13 => [
                [false, true,  false, false, false],
                [true,  true,  false, false, true ],
                [true,  true,  true,  false, true ],
                [true,  true,  true,  true,  true ],
            ],
            14 => [
                [true,  true,  true,  false, false],
                [true,  true,  true,  true,  false],
            ],
            15 => [
                [true,  false, false, true,  false],
                [true,  true,  false, true,  true ],
                [true,  true,  true,  true,  true ],
            ],
            16 => [
                [false, false, false, false, false],
                [false, true,  false, true,  false],
                [true,  true,  false, true,  false],
                [true,  true,  true,  true,  false],
            ],
            17 => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            18 => [
                [true,  false, true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            19 => [
                [false, true,  true,  false, false],
                [true,  true,  true,  false, true ],
                [true,  true,  true,  true,  true ],
            ],
            20 => [
                [true,  true,  true,  true,  true ],
            ],

            // ── Classe 3 — Licence Pro (avancés) ──
            21 => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            22 => [
                [true,  true,  true,  true,  true ],
            ],
            23 => [
                [true,  false, true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            24 => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            25 => [
                [true,  true,  true,  false, true ],
                [true,  true,  true,  true,  true ],
            ],
            26 => [
                [true,  true,  true,  true,  true ],
            ],
            27 => [
                [true,  false, true,  true,  false],
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            28 => [
                [true,  true,  true,  true,  false],
                [true,  true,  true,  true,  true ],
            ],
            29 => [
                [true,  true,  true,  true,  true ],
            ],
            30 => [
                [true,  true,  false, true,  true ],
                [true,  true,  true,  true,  true ],
            ],
        ];

        $sessionId = 1;
        $sessions  = [];
        $valider   = [];

        // Dates de base par apprenti pour garder une progression chronologique
        $dateBase = strtotime('2025-01-05');

        foreach ($planSessions as $apprentiId => $toutesLesSessions) {
            $dateOffset = 0;

            foreach ($toutesLesSessions as $numSession => $objResultats) {
                // Écart de 2 à 4 semaines entre chaque session
                $dateOffset += ($numSession === 0 ? 0 : rand(14, 28));
                $date = date('Y-m-d', $dateBase + ($apprentiId * 3 + $dateOffset) * 86400);
                $heure = str_pad(rand(8, 16), 2, '0', STR_PAD_LEFT) . ':00:00';

                $sessions[] = [
                    'date_heure'         => $date . ' ' . $heure,
                    'type_environnement' => $envs[$sessionId % 2],
                    'type_drone'         => $drones[$sessionId % 3],
                    'conditions_meteo'   => $meteos[$sessionId % 4],
                    'duree_max'          => $durees[$sessionId % 4],
                    'login'              => $logins[$sessionId % 2],
                    'id_apprentis'       => $apprentiId,
                ];

                // Objectifs de cette session
                foreach ($objResultats as $objIndex => $reussi) {
                    $objId = $objIndex + 1;
                    $qa    = $quantitesMax[$objId];
                    $qr    = $reussi ? $qa : rand(0, $qa - 1);

                    $valider[] = [
                        'id_sessions'          => $sessionId,
                        'id_objectifs'         => $objId,
                        'reussi'               => $reussi,
                        'quantite_a_atteindre' => $qa,
                        'quantite_realisee'    => $qr,
                    ];
                }

                $sessionId++;
            }
        }

        DB::table('sessions_drone')->insert($sessions);
        DB::table('valider')->insert($valider);
    }
}

/*

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. CLASSES ──────────────────────────────────────────────
        DB::table('classes')->insert([
            ['libelle_classes' => 'BTS Drone 1ère année'],
            ['libelle_classes' => 'BTS Drone 2ème année'],
            ['libelle_classes' => 'Licence Pro Drone'],
        ]);

        // ── 2. FORMATEURS ───────────────────────────────────────────
        DB::table('formateurs')->insert([
            [
                'login'        => 'jmartin',
                'mot_de_passe' => Hash::make('password123'),
                'nom'          => 'Martin',
                'prenom'       => 'Jacques',
            ],
            [
                'login'        => 'sdurand',
                'mot_de_passe' => Hash::make('password123'),
                'nom'          => 'Durand',
                'prenom'       => 'Sophie',
            ],
        ]);

        // ── 3. APPRENTIS ────────────────────────────────────────────
        DB::table('apprentis')->insert([
            ['nom' => 'Dupont',  'prenom' => 'Jean',  'id_classes' => 1],
            ['nom' => 'Martin',  'prenom' => 'Emma',  'id_classes' => 1],
            ['nom' => 'Bernard', 'prenom' => 'Lucas', 'id_classes' => 2],
            ['nom' => 'Petit',   'prenom' => 'Chloé', 'id_classes' => 2],
            ['nom' => 'Robert',  'prenom' => 'Hugo',  'id_classes' => 3],
            ['nom' => 'Moreau',  'prenom' => 'Léa',   'id_classes' => 3],
        ]);

        // ── 4. OBJECTIFS ─────────────────────────────────────────────
        DB::table('objectifs')->insert([
            ['libelle_objectifs' => 'Cerceaux',   'est_automatique' => true],
            ['libelle_objectifs' => 'Atterrissage',    'est_automatique' => true],
            ['libelle_objectifs' => 'Positionnement',   'est_automatique' => true],
            ['libelle_objectifs' => 'Maintien d\'Altitude', 'est_automatique' => true],
            ['libelle_objectifs' => 'Tours',   'est_automatique' => false],
        ]);

        // ── 5. SESSIONS ──────────────────────────────────────────────
        DB::table('sessions_drone')->insert([
            // Apprenti 1 — Jean Dupont — session 1
            [
                'date_heure'         => '2025-01-10 09:00:00',
                'type_environnement' => 'Extérieur',
                'conditions_meteo'   => 'Ensoleillé',
                'type_drone'         => 'DJI Mini 3',
                'duree_max'          => 30,
                'login'              => 'jmartin',
                'id_apprentis'       => 1,
            ],
            // Apprenti 1 — Jean Dupont — session 2
            [
                'date_heure'         => '2025-02-14 14:00:00',
                'type_environnement' => 'Extérieur',
                'conditions_meteo'   => 'Nuageux',
                'type_drone'         => 'DJI Mini 3',
                'duree_max'          => 30,
                'login'              => 'jmartin',
                'id_apprentis'       => 1,
            ],
            // Apprenti 2 — Emma Martin
            [
                'date_heure'         => '2025-01-15 10:30:00',
                'type_environnement' => 'Intérieur',
                'conditions_meteo'   => 'N/A',
                'type_drone'         => 'Parrot Anafi',
                'duree_max'          => 20,
                'login'              => 'sdurand',
                'id_apprentis'       => 2,
            ],
            // Apprenti 3 — Lucas Bernard
            [
                'date_heure'         => '2025-03-05 08:00:00',
                'type_environnement' => 'Extérieur',
                'conditions_meteo'   => 'Venteux',
                'type_drone'         => 'DJI Air 3',
                'duree_max'          => 45,
                'login'              => 'jmartin',
                'id_apprentis'       => 3,
            ],
            // Apprenti 4 — Chloé Petit
            [
                'date_heure'         => '2025-03-12 11:00:00',
                'type_environnement' => 'Extérieur',
                'conditions_meteo'   => 'Ensoleillé',
                'type_drone'         => 'DJI Mini 3',
                'duree_max'          => 30,
                'login'              => 'sdurand',
                'id_apprentis'       => 4,
            ],
            // Apprenti 5 — Hugo Robert
            [
                'date_heure'         => '2025-04-02 09:30:00',
                'type_environnement' => 'Extérieur',
                'conditions_meteo'   => 'Ensoleillé',
                'type_drone'         => 'DJI Air 3',
                'duree_max'          => 60,
                'login'              => 'sdurand',
                'id_apprentis'       => 5,
            ],
            // Apprenti 6 — Léa Moreau
            [
                'date_heure'         => '2025-04-10 14:30:00',
                'type_environnement' => 'Intérieur',
                'conditions_meteo'   => 'N/A',
                'type_drone'         => 'Parrot Anafi',
                'duree_max'          => 20,
                'login'              => 'jmartin',
                'id_apprentis'       => 6,
            ],
        ]);

        // ── 6. VALIDER ───────────────────────────────────────────────
        DB::table('valider')->insert([

            // Session 1 — Jean Dupont (réussi ✓)
            ['id_sessions' => 1, 'id_objectifs' => 1, 'reussi' => true,  'quantite_a_atteindre' => 3, 'quantite_realisee' => 3],
            ['id_sessions' => 1, 'id_objectifs' => 2, 'reussi' => true,  'quantite_a_atteindre' => 2, 'quantite_realisee' => 2],
            ['id_sessions' => 1, 'id_objectifs' => 3, 'reussi' => true,  'quantite_a_atteindre' => 4, 'quantite_realisee' => 4],

            // Session 2 — Jean Dupont 2ème passage (réussi ✓)
            ['id_sessions' => 2, 'id_objectifs' => 4, 'reussi' => true,  'quantite_a_atteindre' => 1, 'quantite_realisee' => 1],
            ['id_sessions' => 2, 'id_objectifs' => 5, 'reussi' => true,  'quantite_a_atteindre' => 1, 'quantite_realisee' => 1],

            // Session 3 — Emma Martin (échoué ✗)
            ['id_sessions' => 3, 'id_objectifs' => 1, 'reussi' => true,  'quantite_a_atteindre' => 3, 'quantite_realisee' => 3],
            ['id_sessions' => 3, 'id_objectifs' => 2, 'reussi' => false, 'quantite_a_atteindre' => 2, 'quantite_realisee' => 1],
            ['id_sessions' => 3, 'id_objectifs' => 3, 'reussi' => false, 'quantite_a_atteindre' => 4, 'quantite_realisee' => 2],

            // Session 4 — Lucas Bernard (échoué ✗)
            ['id_sessions' => 4, 'id_objectifs' => 1, 'reussi' => true,  'quantite_a_atteindre' => 3, 'quantite_realisee' => 3],
            ['id_sessions' => 4, 'id_objectifs' => 2, 'reussi' => false, 'quantite_a_atteindre' => 2, 'quantite_realisee' => 0],

            // Session 5 — Chloé Petit (réussi ✓)
            ['id_sessions' => 5, 'id_objectifs' => 1, 'reussi' => true,  'quantite_a_atteindre' => 3, 'quantite_realisee' => 3],
            ['id_sessions' => 5, 'id_objectifs' => 2, 'reussi' => true,  'quantite_a_atteindre' => 2, 'quantite_realisee' => 2],
            ['id_sessions' => 5, 'id_objectifs' => 3, 'reussi' => true,  'quantite_a_atteindre' => 4, 'quantite_realisee' => 4],
            ['id_sessions' => 5, 'id_objectifs' => 4, 'reussi' => true,  'quantite_a_atteindre' => 1, 'quantite_realisee' => 1],

            // Session 6 — Hugo Robert (réussi ✓)
            ['id_sessions' => 6, 'id_objectifs' => 1, 'reussi' => true,  'quantite_a_atteindre' => 3, 'quantite_realisee' => 3],
            ['id_sessions' => 6, 'id_objectifs' => 2, 'reussi' => true,  'quantite_a_atteindre' => 2, 'quantite_realisee' => 2],
            ['id_sessions' => 6, 'id_objectifs' => 5, 'reussi' => true,  'quantite_a_atteindre' => 1, 'quantite_realisee' => 1],

            // Session 7 — Léa Moreau (échoué ✗)
            ['id_sessions' => 7, 'id_objectifs' => 1, 'reussi' => false, 'quantite_a_atteindre' => 3, 'quantite_realisee' => 1],
            ['id_sessions' => 7, 'id_objectifs' => 2, 'reussi' => false, 'quantite_a_atteindre' => 2, 'quantite_realisee' => 0],
        ]);
    }
}*/