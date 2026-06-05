<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant un apprenti.
 *
 * Un apprenti appartient à une classe et peut avoir plusieurs sessions de vol.
 *
 * @package App\Models
 *
 * @property int    $id_apprenti Identifiant unique de l'apprenti (clé primaire)
 * @property string $nom         Nom de famille de l'apprenti
 * @property string $prenom      Prénom de l'apprenti
 * @property int    $id_classe   Identifiant de la classe de rattachement (clé étrangère)
 */
class Apprentis extends Model
{
    /** @brief Nom de la table en base de données. */
    protected $table = 'apprentis';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_apprenti';

    /**
     * @brief Champs autorisés à l'assignation en masse.
     * @var array<string>
     */
    protected $fillable = ['nom', 'prenom', 'id_classe'];

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    // ════════════════════════════RELATIONS══════════════════════════════════

    /**
     * @brief Relation vers la classe de l'apprenti.
     *
     * Un apprenti appartient à une seule classe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(Classes::class, 'id_classe', 'id_classe');
    }

    /**
     * @brief Relation vers les sessions de vol de l'apprenti.
     *
     * Un apprenti peut avoir plusieurs sessions de vol.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(SessionsDrone::class, 'id_apprenti', 'id_apprenti');
    }
}