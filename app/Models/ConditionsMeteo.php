<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant les conditions météorologiques d'une session.
 *
 * Stocke les informations météo au moment d'une session de vol :
 * période (jour/nuit), type de ciel et vecteur vent (direction + intensité).
 *
 * @package App\Models
 *
 * @property int   $id_meteo    Identifiant unique (clé primaire)
 * @property bool  $jour        true = jour, false = nuit
 * @property int   $ciel        Code du type de ciel :
 *                               0 = Dégagé, 1 = Nuageux, 2 = Couvert, 3 = Pluvieux
 * @property float $vent_x      Composante X du vecteur direction du vent
 * @property float $vent_y      Composante Y du vecteur direction du vent
 * @property float $vent_z      Composante Z du vecteur direction du vent
 * @property float $vent_norme  Intensité du vent
 */
class ConditionsMeteo extends Model
{
    /** @brief Nom de la table en base de données. */
    protected $table = 'conditions_meteo';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_meteo';

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    /**
     * @brief Champs autorisés à l'assignation en masse.
     * @var array<string>
     */
    protected $fillable = [
        'jour',
        'ciel',
        'vent_x',
        'vent_y',
        'vent_z',
        'vent_norme',
    ];

    /**
     * @brief Conversion automatique des types.
     *
     * - `jour` est casté en booléen (true = jour, false = nuit).
     * - `ciel` est casté en entier (0-3).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jour' => 'boolean',
        'ciel' => 'integer',
    ];

// ════════════════════════════RELATIONS══════════════════════════════════

    /**
     * @brief Relation vers les sessions utilisant ces conditions météo.
     *
     * Des conditions météo peuvent être associées à plusieurs sessions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(SessionsDrone::class, 'id_meteo', 'id_meteo');
    }
}
    