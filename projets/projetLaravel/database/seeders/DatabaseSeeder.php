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
            ['libelle_objectifs' => 'passage dans un cerceau',   'est_automatique' => true],
            ['libelle_objectifs' => 'atterrissage sur une plateforme',    'est_automatique' => true],
            ['libelle_objectifs' => 'positionnement au-dessus d\'un bâtiment',   'est_automatique' => true],
            ['libelle_objectifs' => 'maintien d\'altitude', 'est_automatique' => false],
            ['libelle_objectifs' => 'tours autour d\'un obstacle',   'est_automatique' => false],
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
}