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
            ['libelle_classe' => 'BTS Drone 1ère année'],
            ['libelle_classe' => 'BTS Drone 2ème année'],
            ['libelle_classe' => 'Licence Pro Drone'],
        ]);

        // ── 2. FORMATEURS ───────────────────────────────────────────
        DB::table('formateurs')->insert([
            ['login' => 'jmartin', 'mot_de_passe' => Hash::make('password123'), 'nom' => 'Martin', 'prenom' => 'Jacques'],
            ['login' => 'sdurand', 'mot_de_passe' => Hash::make('password123'), 'nom' => 'Durand',  'prenom' => 'Sophie'],
        ]);

        // ── 3. APPRENTIS (10 par classe) ────────────────────────────
        DB::table('apprentis')->insert([
            // Classe 1 — BTS Drone 1ère année
            ['nom' => 'Dupont',    'prenom' => 'Jean',      'id_classe' => 1],
            ['nom' => 'Martin',    'prenom' => 'Emma',      'id_classe' => 1],
            ['nom' => 'Leroy',     'prenom' => 'Théo',      'id_classe' => 1],
            ['nom' => 'Fontaine',  'prenom' => 'Inès',      'id_classe' => 1],
            ['nom' => 'Garnier',   'prenom' => 'Maxime',    'id_classe' => 1],
            ['nom' => 'Chevalier', 'prenom' => 'Lucie',     'id_classe' => 1],
            ['nom' => 'Morin',     'prenom' => 'Antoine',   'id_classe' => 1],
            ['nom' => 'Girard',    'prenom' => 'Manon',     'id_classe' => 1],
            ['nom' => 'Rousseau',  'prenom' => 'Baptiste',  'id_classe' => 1],
            ['nom' => 'Faure',     'prenom' => 'Camille',   'id_classe' => 1],

            // Classe 2 — BTS Drone 2ème année
            ['nom' => 'Bernard',   'prenom' => 'Lucas',     'id_classe' => 2],
            ['nom' => 'Petit',     'prenom' => 'Chloé',     'id_classe' => 2],
            ['nom' => 'Legrand',   'prenom' => 'Romain',    'id_classe' => 2],
            ['nom' => 'Marchand',  'prenom' => 'Jade',      'id_classe' => 2],
            ['nom' => 'Lemaire',   'prenom' => 'Quentin',   'id_classe' => 2],
            ['nom' => 'Dupuis',    'prenom' => 'Océane',    'id_classe' => 2],
            ['nom' => 'Renard',    'prenom' => 'Florian',   'id_classe' => 2],
            ['nom' => 'Blanc',     'prenom' => 'Pauline',   'id_classe' => 2],
            ['nom' => 'Guerin',    'prenom' => 'Alexis',    'id_classe' => 2],
            ['nom' => 'Millet',    'prenom' => 'Sarah',     'id_classe' => 2],

            // Classe 3 — Licence Pro Drone
            ['nom' => 'Robert',    'prenom' => 'Hugo',      'id_classe' => 3],
            ['nom' => 'Moreau',    'prenom' => 'Léa',       'id_classe' => 3],
            ['nom' => 'Simon',     'prenom' => 'Nicolas',   'id_classe' => 3],
            ['nom' => 'Laurent',   'prenom' => 'Anaïs',     'id_classe' => 3],
            ['nom' => 'Michel',    'prenom' => 'Valentin',  'id_classe' => 3],
            ['nom' => 'Lefebvre',  'prenom' => 'Charlotte', 'id_classe' => 3],
            ['nom' => 'Lefevre',   'prenom' => 'Julien',    'id_classe' => 3],
            ['nom' => 'Roux',      'prenom' => 'Marine',    'id_classe' => 3],
            ['nom' => 'David',     'prenom' => 'Kevin',     'id_classe' => 3],
            ['nom' => 'Bertrand',  'prenom' => 'Elise',     'id_classe' => 3],
        ]);

        // ── 4. OBJECTIFS ─────────────────────────────────────────────
        DB::table('objectifs')->insert([
            ['libelle_objectif' => 'Cerceaux',            'est_automatique' => true],
            ['libelle_objectif' => 'Atterrissage',        'est_automatique' => true],
            ['libelle_objectif' => 'Positionnement',      'est_automatique' => true],
            ['libelle_objectif' => "Maintien d'Altitude", 'est_automatique' => true],
            ['libelle_objectif' => 'Tours',               'est_automatique' => false],
        ]);

        // ── 5. METEO ─────────────────────────────────────────────────
        // jour : true = jour, false = nuit
        // ciel : 0=dégagé, 1=nuageux, 2=couvert, 3=pluvieux, 4=orageux
        // vent_x, vent_y, vent_z : composantes du vecteur vent (direction)
        // vent_norme : intensité du vent en m/s
        DB::table('conditions_meteo')->insert([
            // 1 — Jour ensoleillé, vent faible du nord
            ['jour' => true,  'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 2.0, 'vent_z' => 0.0, 'vent_norme' => 2.0],
            // 2 — Jour nuageux, vent modéré
            ['jour' => true,  'ciel' => 1, 'vent_x' => 1.5, 'vent_y' => 1.5, 'vent_z' => 0.0, 'vent_norme' => 4.2],
            // 3 — Jour pluvieux, conditions difficiles
            ['jour' => true,  'ciel' => 3, 'vent_x' => 5.0, 'vent_y' => 3.0, 'vent_z' => 0.5, 'vent_norme' => 8.5],
            // 4 — Intérieur, pas de vent (jour)
            ['jour' => true,  'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 0.0, 'vent_z' => 0.0, 'vent_norme' => 0.0],
            // 5 — Nuit dégagée, vent faible
            ['jour' => false, 'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 0.5, 'vent_z' => 0.0, 'vent_norme' => 0.5],
            // 6 — Jour orageux, vent fort
            ['jour' => true,  'ciel' => 4, 'vent_x' => 7.0, 'vent_y' => 4.0, 'vent_z' => 1.0, 'vent_norme' => 11.2],
        ]);

        // ── 6 & 7. SESSIONS + VALIDER ────────────────────────────────
        // type_drone : true = drone pro, false = drone débutant (selon migration boolean)
        $formateurs  = [1, 2]; // id_formateur (auto-incrémentés)
        $durees      = [20, 30, 45, 60];
        $quantitesMax = [1 => 3, 2 => 2, 3 => 4, 4 => 1, 5 => 5];

        $planSessions = [
            // ── Classe 1 — BTS 1ère année (débutants) ──
            1  => [[false, false, true, false, false], [true, false, true, false, false], [true, true, true, false, true]],
            2  => [[false, false, false, false, false], [true, false, true, false, false]],
            3  => [[true, false, false, true, false], [true, true, false, true, false], [true, true, true, true, false]],
            4  => [[true, true, true, false, false], [true, true, true, true, false], [true, true, true, true, true]],
            5  => [[false, false, false, false, false], [true, false, true, false, false], [true, true, true, false, false], [true, true, true, true, false]],
            6  => [[true, true, true, true, false], [true, true, true, true, true]],
            7  => [[true, false, false, false, false], [true, true, false, false, true], [true, true, true, false, true]],
            8  => [[false, false, false, true, false], [true, false, false, true, false], [true, true, false, true, false]],
            9  => [[true, true, false, false, false], [true, true, true, false, true], [true, true, true, true, true]],
            10 => [[true, false, false, true, false], [true, true, false, true, true]],

            // ── Classe 2 — BTS 2ème année (intermédiaires) ──
            11 => [[true, false, true, true, false], [true, true, true, true, false], [true, true, true, true, true]],
            12 => [[true, true, true, true, false], [true, true, true, true, true]],
            13 => [[false, true, false, false, false], [true, true, false, false, true], [true, true, true, false, true], [true, true, true, true, true]],
            14 => [[true, true, true, false, false], [true, true, true, true, false]],
            15 => [[true, false, false, true, false], [true, true, false, true, true], [true, true, true, true, true]],
            16 => [[false, false, false, false, false], [false, true, false, true, false], [true, true, false, true, false], [true, true, true, true, false]],
            17 => [[true, true, true, true, false], [true, true, true, true, true]],
            18 => [[true, false, true, true, false], [true, true, true, true, true]],
            19 => [[false, true, true, false, false], [true, true, true, false, true], [true, true, true, true, true]],
            20 => [[true, true, true, true, true]],

            // ── Classe 3 — Licence Pro (avancés) ──
            21 => [[true, true, true, true, false], [true, true, true, true, true]],
            22 => [[true, true, true, true, true]],
            23 => [[true, false, true, true, false], [true, true, true, true, true]],
            24 => [[true, true, true, true, false], [true, true, true, true, true]],
            25 => [[true, true, true, false, true], [true, true, true, true, true]],
            26 => [[true, true, true, true, true]],
            27 => [[true, false, true, true, false], [true, true, true, true, false], [true, true, true, true, true]],
            28 => [[true, true, true, true, false], [true, true, true, true, true]],
            29 => [[true, true, true, true, true]],
            30 => [[true, true, false, true, true], [true, true, true, true, true]],
        ];

        $sessionId = 1;
        $sessions  = [];
        $validations = [];
        $dateBase  = strtotime('2025-01-05');

        // NOTE: date_heure est spécifiée ici pour avoir des données de test variées
        // En production, on peut omettre ce champ pour utiliser CURRENT_TIMESTAMP automatiquement
        foreach ($planSessions as $apprentiId => $toutesLesSessions) {
            $dateOffset = 0;

            foreach ($toutesLesSessions as $numSession => $objResultats) {
                $dateOffset += ($numSession === 0 ? 0 : rand(14, 28));
                $date  = date('Y-m-d', $dateBase + ($apprentiId * 3 + $dateOffset) * 86400);
                $heure = str_pad(rand(8, 16), 2, '0', STR_PAD_LEFT) . ':00:00';

                // id_meteo entre 1 et 6 en cycle
                $idMeteo = (($sessionId - 1) % 6) + 1;

                // true = extérieur (météo 1,2,3,5,6), false = intérieur (météo 4)
                $typeEnv = ($idMeteo !== 4);

                $sessions[] = [
                    'date_heure'         => $date . ' ' . $heure,
                    'type_environnement' => $typeEnv,
                    'type_drone'         => ($sessionId % 2 === 0), // true/false alternés
                    'duree_max'          => $durees[$sessionId % 4],
                    'id_meteo'           => $idMeteo,
                    'id_formateur'       => $formateurs[$sessionId % 2],
                    'id_apprenti'        => $apprentiId,
                ];

                foreach ($objResultats as $objIndex => $reussi) {
                    $objId = $objIndex + 1;
                    $qa    = $quantitesMax[$objId];
                    $qr    = $reussi ? $qa : rand(0, $qa - 1);

                    $validations[] = [
                        'id_session'           => $sessionId,
                        'id_objectif'          => $objId,
                        'reussi'               => $reussi,
                        'quantite_a_atteindre' => $qa,
                        'quantite_realisee'    => $qr,
                    ];
                }

                $sessionId++;
            }
        }

        DB::table('sessions_drone')->insert($sessions);
        DB::table('validations')->insert($validations);
    }
}
