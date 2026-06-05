<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant la table pivot entre sessions et objectifs.
 *
 * La table `validations` lie une session de vol à un objectif 
 * et stocke le résultat de l'évaluation (réussi/échoué) ainsi que
 * les quantités attendues et réalisées.
 *
 * La clé primaire est composite : (id_session, id_objectif).
 *
 * @package App\Models
 *
 * @property int  $id_session           Clé étrangère vers sessions_drone (partie de la PK composite)
 * @property int  $id_objectif          Clé étrangère vers objectifs (partie de la PK composite)
 * @property bool $reussi               true = objectif validé, false = objectif échoué
 * @property int  $quantite_a_atteindre Seuil quantitatif à atteindre pour valider l'objectif
 * @property int  $quantite_realisee    Quantité effectivement réalisée par l'apprenti
 */
class Validations extends Model
{
    /** @brief Nom de la table en base de données. */
    protected $table = 'validations';

    /**
     * @brief Clé primaire composite (id_session + id_objectif).
     *
     * Laravel ne gère pas nativement les clés composites sur les modèles,
     * mais la contrainte est gérée au niveau de la migration.
     *
     * @var array<string>
     */
    protected $primaryKey = ['id_session', 'id_objectif'];

    /** @brief La clé primaire n'est pas auto-incrémentée. */
    public $incrementing = false;

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    /**
     * @brief Champs autorisés à l'assignation en masse.
     * @var array<string>
     */
    protected $fillable = [
        'id_session',
        'id_objectif',
        'reussi',
        'quantite_a_atteindre',
        'quantite_realisee',
    ];

    // ══════════════════════════════════════════════════════════════
    // RELATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * @brief Relation vers la session de vol associée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session()
    {
        return $this->belongsTo(SessionsDrone::class, 'id_session', 'id_session');
    }

    /**
     * @brief Relation vers l'objectif associé.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objectif()
    {
        return $this->belongsTo(Objectifs::class, 'id_objectif', 'id_objectif');
    }
}