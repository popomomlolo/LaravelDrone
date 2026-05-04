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
            ['login' => 'sdurand', 'mot_de_passe' => Hash::make('password123'), 'nom' => 'Durand',  'prenom' => 'Sophie'],
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

        // ── 5. METEO ─────────────────────────────────────────────────
        // Crée des entrées météo variées réutilisées par les sessions
        // condition_meteo : true = favorable, false = défavorable
        // vent_x, vent_y, vent_z : composantes du vecteur vent
        // vent_norme : intensité en m/s
        $meteos = [
            // 1 — Ensoleillé, vent faible du nord
            ['condition_meteo' => true,  'vent_x' => 0.0,  'vent_y' => 2.0,  'vent_z' => 0.0, 'vent_norme' => 2.0],
            // 2 — Nuageux, vent modéré
            ['condition_meteo' => true,  'vent_x' => 1.5,  'vent_y' => 1.5,  'vent_z' => 0.0, 'vent_norme' => 4.2],
            // 3 — Venteux, conditions difficiles
            ['condition_meteo' => false, 'vent_x' => 5.0,  'vent_y' => 3.0,  'vent_z' => 0.5, 'vent_norme' => 8.5],
            // 4 — Intérieur (N/A) — pas de vent
            ['condition_meteo' => true,  'vent_x' => 0.0,  'vent_y' => 0.0,  'vent_z' => 0.0, 'vent_norme' => 0.0],
            // 5 — Ensoleillé, vent nul
            ['condition_meteo' => true,  'vent_x' => 0.0,  'vent_y' => 0.5,  'vent_z' => 0.0, 'vent_norme' => 0.5],
            // 6 — Mauvais temps, vent fort
            ['condition_meteo' => false, 'vent_x' => 7.0,  'vent_y' => 4.0,  'vent_z' => 1.0, 'vent_norme' => 11.2],
        ];

        DB::table('meteo')->insert($meteos);

        // ── 6 & 7. SESSIONS + VALIDER ────────────────────────────────
        $drones  = ['DJI Mini 3', 'DJI Air 3', 'Parrot Anafi'];
        $logins  = ['jmartin', 'sdurand'];
        $durees  = [20, 30, 45, 60];

        // id_meteo entre 1 et 6 selon la session (cycle)
        // type_environnement : true = extérieur, false = intérieur
        $environnements = [true, false]; // 1 = extérieur, 2 = intérieur

        $quantitesMax = [1 => 3, 2 => 2, 3 => 4, 4 => 1, 5 => 5];

        $planSessions = [
            // ── Classe 1 — BTS 1ère année (débutants) ──
            1  => [[false,false,true, false,false],[true, false,true, false,false],[true, true, true, false,true ]],
            2  => [[false,false,false,false,false],[true, false,true, false,false]],
            3  => [[true, false,false,true, false],[true, true, false,true, false],[true, true, true, true, false]],
            4  => [[true, true, true, false,false],[true, true, true, true, false],[true, true, true, true, true ]],
            5  => [[false,false,false,false,false],[true, false,true, false,false],[true, true, true, false,false],[true, true, true, true, false]],
            6  => [[true, true, true, true, false],[true, true, true, true, true ]],
            7  => [[true, false,false,false,false],[true, true, false,false,true ],[true, true, true, false,true ]],
            8  => [[false,false,false,true, false],[true, false,false,true, false],[true, true, false,true, false]],
            9  => [[true, true, false,false,false],[true, true, true, false,true ],[true, true, true, true, true ]],
            10 => [[true, false,false,true, false],[true, true, false,true, true ]],

            // ── Classe 2 — BTS 2ème année (intermédiaires) ──
            11 => [[true, false,true, true, false],[true, true, true, true, false],[true, true, true, true, true ]],
            12 => [[true, true, true, true, false],[true, true, true, true, true ]],
            13 => [[false,true, false,false,false],[true, true, false,false,true ],[true, true, true, false,true ],[true, true, true, true, true ]],
            14 => [[true, true, true, false,false],[true, true, true, true, false]],
            15 => [[true, false,false,true, false],[true, true, false,true, true ],[true, true, true, true, true ]],
            16 => [[false,false,false,false,false],[false,true, false,true, false],[true, true, false,true, false],[true, true, true, true, false]],
            17 => [[true, true, true, true, false],[true, true, true, true, true ]],
            18 => [[true, false,true, true, false],[true, true, true, true, true ]],
            19 => [[false,true, true, false,false],[true, true, true, false,true ],[true, true, true, true, true ]],
            20 => [[true, true, true, true, true ]],

            // ── Classe 3 — Licence Pro (avancés) ──
            21 => [[true, true, true, true, false],[true, true, true, true, true ]],
            22 => [[true, true, true, true, true ]],
            23 => [[true, false,true, true, false],[true, true, true, true, true ]],
            24 => [[true, true, true, true, false],[true, true, true, true, true ]],
            25 => [[true, true, true, false,true ],[true, true, true, true, true ]],
            26 => [[true, true, true, true, true ]],
            27 => [[true, false,true, true, false],[true, true, true, true, false],[true, true, true, true, true ]],
            28 => [[true, true, true, true, false],[true, true, true, true, true ]],
            29 => [[true, true, true, true, true ]],
            30 => [[true, true, false,true, true ],[true, true, true, true, true ]],
        ];

        $sessionId = 1;
        $sessions  = [];
        $valider   = [];
        $dateBase  = strtotime('2025-01-05');

        foreach ($planSessions as $apprentiId => $toutesLesSessions) {
            $dateOffset = 0;

            foreach ($toutesLesSessions as $numSession => $objResultats) {
                $dateOffset += ($numSession === 0 ? 0 : rand(14, 28));
                $date  = date('Y-m-d', $dateBase + ($apprentiId * 3 + $dateOffset) * 86400);
                $heure = str_pad(rand(8, 16), 2, '0', STR_PAD_LEFT) . ':00:00';

                // id_meteo entre 1 et 6 en cycle
                $idMeteo = (($sessionId - 1) % 6) + 1;

                // type_environnement : true si id_meteo != 4 (intérieur = meteo 4)
                $typeEnv = ($idMeteo !== 4);

                $sessions[] = [
                    'date_heure'         => $date . ' ' . $heure,
                    'type_environnement' => $typeEnv,
                    'type_drone'         => $drones[$sessionId % 3],
                    'duree_max'          => $durees[$sessionId % 4],
                    'id_meteo'           => $idMeteo,
                    'login'              => $logins[$sessionId % 2],
                    'id_apprentis'       => $apprentiId,
                ];

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