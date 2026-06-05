<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant une session de vol de drone.
 *
 * Une session est le coeur du système : elle relie un apprenti,
 * un formateur, des conditions météo et une liste d'objectifs évalués
 * (via la table pivot `validations`).
 *
 * @package App\Models
 *
 * @property int             $id_session          Identifiant unique (clé primaire)
 * @property \Carbon\Carbon  $date_heure          Date et heure de la session (défaut : CURRENT_TIMESTAMP)
 * @property bool            $type_environnement  true = extérieur, false = intérieur
 * @property bool            $type_drone          true = drone classique, false = drone assisté
 * @property int             $duree_max           Durée maximale de la session en minutes
 * @property int             $id_meteo            Clé étrangère vers conditions_meteo
 * @property int             $id_formateur        Clé étrangère vers formateurs
 * @property int             $id_apprenti         Clé étrangère vers apprentis
 */
class SessionsDrone extends Model
{
    /** @brief Nom de la table en base de données. */
    protected $table = 'sessions_drone';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_session';

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    /**
     * `date_heure` peut être omis à la création : la BDD utilise alors
     * `CURRENT_TIMESTAMP` comme valeur par défaut.
     *
     */
    protected $fillable = [
        'date_heure',
        'type_environnement',
        'type_drone',
        'duree_max',
        'id_meteo',
        'id_formateur',
        'id_apprenti',
    ];

    /**
     * @brief Conversion automatique des types.
     *
     * - `type_environnement` est casté en booléen.
     * - `date_heure` est casté en instance pour faciliter le formatage.
     *
     */
    protected $casts = [
        'type_environnement' => 'boolean',
        'date_heure'         => 'datetime',
    ];

// ════════════════════════════RELATIONS══════════════════════════════════

    /**
     * @brief Relation vers les conditions météo de la session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meteo()
    {
        return $this->belongsTo(ConditionsMeteo::class, 'id_meteo', 'id_meteo');
    }

    /**
     * @brief Relation vers le formateur ayant animé la session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formateur()
    {
        return $this->belongsTo(Formateurs::class, 'id_formateur', 'id_formateur');
    }

    /**
     * @brief Relation vers l'apprenti ayant effectué la session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apprenti()
    {
        return $this->belongsTo(Apprentis::class, 'id_apprenti', 'id_apprenti');
    }

    /**
     * @brief Relation Many-to-Many vers les objectifs via la table pivot `validations`.
     *
     * Le pivot contient :
     * - `reussi` (bool) : si l'objectif a été validé durant cette session
     * - `quantite_a_atteindre` (int) : seuil de réussite
     * - `quantite_realisee` (int) : valeur effectivement atteinte
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function objectifs()
    {
        return $this->belongsToMany(
            Objectifs::class,
            'validations',
            'id_session',
            'id_objectif'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}

