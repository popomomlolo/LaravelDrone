<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant un objectif.
 *
 * Un objectif est une compétence à valider lors d'une session de vol
 * (ex: "Cerceaux", "Atterrissage"). Il peut être automatique (évalué
 * par le système) ou manuel (évalué par le formateur).
 *
 * La relation avec les sessions passe par la table pivot `validations`
 * qui stocke le résultat (réussi/échoué) et les quantités.
 *
 * @package App\Models
 *
 * @property int    $id_objectif      Identifiant unique (clé primaire)
 * @property string $libelle_objectif Intitulé de l'objectif (ex: "Cerceaux")
 * @property bool   $est_automatique  true = évaluation automatique, false = manuelle
 */
class Objectifs extends Model
{
    /** @brief Nom de la table en base de données. */
    protected $table = 'objectifs';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_objectif';

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    /**
     * @brief Champs autorisés à l'assignation en masse.
     * @var array<string>
     */
    protected $fillable = ['libelle_objectif', 'est_automatique'];


// ════════════════════════════RELATIONS══════════════════════════════════

    /**
     *
     * Chaque objectif peut être évalué dans plusieurs sessions.
     * Le pivot contient :
     * - `reussi` (bool) : si l'objectif a été validé
     * - `quantite_a_atteindre` (int) : seuil à atteindre
     * - `quantite_realisee` (int) : quantité effectivement réalisée
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sessions()
    {
        return $this->belongsToMany(
            SessionsDrone::class,
            'validations',
            'id_objectif',
            'id_session'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}
