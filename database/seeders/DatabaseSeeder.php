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
        // id_objectif : 1=Cerceaux, 2=Atterrissage, 3=Positionnement,
        //               4=Maintien d'Altitude, 5=Tours
        DB::table('objectifs')->insert([
            ['libelle_objectif' => 'Cerceaux',            'est_automatique' => true],
            ['libelle_objectif' => 'Atterrissage',        'est_automatique' => true],
            ['libelle_objectif' => 'Positionnement',      'est_automatique' => true],
            ['libelle_objectif' => "Maintien d'Altitude", 'est_automatique' => true],
            ['libelle_objectif' => 'Tours',               'est_automatique' => false],
        ]);

        // ── 5. METEO ─────────────────────────────────────────────────
        DB::table('conditions_meteo')->insert([
            ['jour' => true,  'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 2.0, 'vent_z' => 0.0, 'vent_norme' => 2.0],
            ['jour' => true,  'ciel' => 1, 'vent_x' => 1.5, 'vent_y' => 1.5, 'vent_z' => 0.0, 'vent_norme' => 4.2],
            ['jour' => true,  'ciel' => 3, 'vent_x' => 5.0, 'vent_y' => 3.0, 'vent_z' => 0.5, 'vent_norme' => 8.5],
            ['jour' => true,  'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 0.0, 'vent_z' => 0.0, 'vent_norme' => 0.0],
            ['jour' => false, 'ciel' => 0, 'vent_x' => 0.0, 'vent_y' => 0.5, 'vent_z' => 0.0, 'vent_norme' => 0.5],
        ]);

        // ── 6 & 7. SESSIONS + VALIDATIONS ───────────────────────────
        //
        // Le plan de sessions définit, pour chaque session d'un apprenti,
        // quels objectifs sont TENTÉS et leur résultat (true=réussi, false=échoué).
        //
        // Un objectif ABSENT du tableau de la session = NON TENTÉ.
        // Cela permet d'avoir les 3 cas dans le graphique :
        //   🟢 Réussi   — objectif tenté et validé
        //   🔴 Échoué   — objectif tenté mais raté
        //   ⬜ Non tenté — objectif jamais présent dans les sessions de l'apprenti
        //
        // Structure : id_apprenti => [ session1 => [id_objectif => reussi, ...], ... ]
        //
        // Exemples de "non tentés" voulus :
        //   - Apprentis 1,2,8,16 : "Tours" (5) jamais tenté
        //   - Apprentis 2,8      : "Maintien d'Altitude" (4) jamais tenté
        //   - Apprentis 3,5      : "Tours" (5) jamais tenté
        //   - Apprentis 13,16    : débutants qui n'ont pas encore tous les objectifs
        // ─────────────────────────────────────────────────────────────
        $planSessions = [

            // ══ Classe 1 — BTS 1ère année (débutants) ══════════════

            // Apprenti 1 — Jean Dupont : ne tente jamais "Tours" (5)
            1 => [
                [1 => false, 2 => false, 3 => true],                    // session 1 : objectifs 1,2,3 seulement
                [1 => true,  2 => false, 3 => true,  4 => false],       // session 2 : objectifs 1,2,3,4
                [1 => true,  2 => true,  3 => true,  4 => false],       // session 3 : objectifs 1,2,3,4
            ],

            // Apprenti 2 — Emma Martin : ne tente jamais "Maintien d'Altitude" (4) ni "Tours" (5)
            2 => [
                [1 => false, 2 => false, 3 => false],                   // session 1 : objectifs 1,2,3
                [1 => true,  2 => false, 3 => true],                    // session 2 : objectifs 1,2,3
            ],

            // Apprenti 3 — Théo Leroy : ne tente jamais "Tours" (5)
            3 => [
                [1 => true,  2 => false, 3 => false, 4 => true],
                [1 => true,  2 => true,  3 => false, 4 => true],
                [1 => true,  2 => true,  3 => true,  4 => true],
            ],

            // Apprenti 4 — Inès Fontaine : tente tous les objectifs
            4 => [
                [1 => true,  2 => true,  3 => true,  4 => false, 5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 5 — Maxime Garnier : ne tente jamais "Tours" (5)
            5 => [
                [1 => false, 2 => false, 3 => false, 4 => false],
                [1 => true,  2 => false, 3 => true,  4 => false],
                [1 => true,  2 => true,  3 => true,  4 => false],
                [1 => true,  2 => true,  3 => true,  4 => true],
            ],

            // Apprenti 6 — Lucie Chevalier : tente tous les objectifs
            6 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 7 — Antoine Morin : ne tente jamais "Positionnement" (3)
            7 => [
                [1 => true,  2 => false, 3 => false],                   // 3 tenté et échoué ici
                [1 => true,  2 => true,  4 => false, 5 => true],        // 3 absent = non tenté cette session
                [1 => true,  2 => true,  4 => false, 5 => true],        // 3 toujours absent
            ],

            // Apprenti 8 — Manon Girard : ne tente jamais "Maintien d'Altitude" (4) ni "Tours" (5)
            8 => [
                [1 => false, 2 => false, 3 => false],
                [1 => true,  2 => false, 3 => false],
                [1 => true,  2 => true,  3 => false],
            ],

            // Apprenti 9 — Baptiste Rousseau : tente tous les objectifs
            9 => [
                [1 => true,  2 => true,  3 => false, 4 => false, 5 => false],
                [1 => true,  2 => true,  3 => true,  4 => false, 5 => true],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 10 — Camille Faure : ne tente jamais "Tours" (5)
            10 => [
                [1 => true,  2 => false, 3 => false, 4 => true],
                [1 => true,  2 => true,  3 => false, 4 => true],
            ],

            // ══ Classe 2 — BTS 2ème année (intermédiaires) ══════════

            // Apprenti 11 — Lucas Bernard : tente tous les objectifs
            11 => [
                [1 => true,  2 => false, 3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 12 — Chloé Petit : tente tous les objectifs
            12 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 13 — Romain Legrand : ne tente jamais "Cerceaux" (1)
            13 => [
                [2 => false, 3 => false, 4 => false, 5 => false],       // 1 absent
                [2 => true,  3 => false, 4 => false, 5 => true],        // 1 absent
                [2 => true,  3 => true,  4 => false, 5 => true],        // 1 absent
                [2 => true,  3 => true,  4 => true,  5 => true],        // 1 absent
            ],

            // Apprenti 14 — Jade Marchand : ne tente jamais "Tours" (5)
            14 => [
                [1 => true,  2 => true,  3 => true,  4 => false],
                [1 => true,  2 => true,  3 => true,  4 => true],
            ],

            // Apprenti 15 — Quentin Lemaire : tente tous les objectifs
            15 => [
                [1 => true,  2 => false, 3 => false, 4 => true,  5 => false],
                [1 => true,  2 => true,  3 => false, 4 => true,  5 => true],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 16 — Océane Dupuis : ne tente jamais "Cerceaux" (1) ni "Tours" (5)
            16 => [
                [2 => false, 3 => false, 4 => false],
                [2 => false, 3 => false, 4 => true],
                [2 => true,  3 => false, 4 => true],
                [2 => true,  3 => true,  4 => true],
            ],

            // Apprenti 17 — Florian Renard : tente tous les objectifs
            17 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 18 — Pauline Blanc : ne tente jamais "Atterrissage" (2)
            18 => [
                [1 => true,  3 => false, 4 => true,  5 => false],       // 2 absent
                [1 => true,  3 => true,  4 => true,  5 => true],        // 2 absent
            ],

            // Apprenti 19 — Alexis Guerin : tente tous les objectifs
            19 => [
                [1 => false, 2 => true,  3 => true,  4 => false, 5 => false],
                [1 => true,  2 => true,  3 => true,  4 => false, 5 => true],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 20 — Sarah Millet : tente tous les objectifs (déjà avancée)
            20 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // ══ Classe 3 — Licence Pro (avancés) ════════════════════

            // Apprenti 21 — Hugo Robert : tente tous les objectifs
            21 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 22 — Léa Moreau : tente tous les objectifs
            22 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 23 — Nicolas Simon : ne tente jamais "Atterrissage" (2)
            23 => [
                [1 => true,  3 => false, 4 => true,  5 => false],       // 2 absent
                [1 => true,  3 => true,  4 => true,  5 => true],        // 2 absent
            ],

            // Apprenti 24 — Anaïs Laurent : tente tous les objectifs
            24 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 25 — Valentin Michel : tente tous les objectifs
            25 => [
                [1 => true,  2 => true,  3 => true,  4 => false, 5 => true],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 26 — Charlotte Lefebvre : tente tous les objectifs
            26 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 27 — Julien Lefevre : ne tente jamais "Atterrissage" (2)
            27 => [
                [1 => true,  3 => false, 4 => true,  5 => false],       // 2 absent
                [1 => true,  3 => true,  4 => true,  5 => false],       // 2 absent
                [1 => true,  3 => true,  4 => true,  5 => true],        // 2 absent
            ],

            // Apprenti 28 — Marine Roux : tente tous les objectifs
            28 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => false],
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 29 — Kevin David : tente tous les objectifs
            29 => [
                [1 => true,  2 => true,  3 => true,  4 => true,  5 => true],
            ],

            // Apprenti 30 — Elise Bertrand : ne tente jamais "Positionnement" (3)
            30 => [
                [1 => true,  2 => true,  4 => true,  5 => false],       // 3 absent
                [1 => true,  2 => true,  4 => true,  5 => true],        // 3 absent
            ],
        ];

        // ── Quantités max par objectif ───────────────────────────────
        $quantitesMax = [1 => 3, 2 => 2, 3 => 4, 4 => 1, 5 => 5];
        $formateurs   = [1, 2];
        $durees       = [20, 30, 45, 60];
        $dateBase     = strtotime('2025-01-05');

        $sessionId   = 1;
        $sessions    = [];
        $validations = [];

        foreach ($planSessions as $apprentiId => $toutesLesSessions) {
            $dateOffset = 0;

            foreach ($toutesLesSessions as $numSession => $objResultats) {
                $dateOffset += ($numSession === 0 ? 0 : rand(14, 28));
                $date  = date('Y-m-d', $dateBase + ($apprentiId * 3 + $dateOffset) * 86400);
                $heure = str_pad(rand(8, 16), 2, '0', STR_PAD_LEFT) . ':00:00';

                $idMeteo = (($sessionId - 1) % 5) + 1;
                $typeEnv = ($idMeteo !== 4);

                $sessions[] = [
                    'date_heure'         => $date . ' ' . $heure,
                    'type_environnement' => $typeEnv,
                    'type_drone'         => ($sessionId % 2 === 0),
                    'duree_max'          => $durees[$sessionId % 4],
                    'id_meteo'           => $idMeteo,
                    'id_formateur'       => $formateurs[$sessionId % 2],
                    'id_apprenti'        => $apprentiId,
                ];

                // Seuls les objectifs PRÉSENTS dans le tableau sont insérés
                // → les objectifs absents = non tentés (aucune ligne dans validations)
                foreach ($objResultats as $objId => $reussi) {
                    $qa = $quantitesMax[$objId];
                    $qr = $reussi ? $qa : rand(0, $qa - 1);

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