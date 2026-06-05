<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @brief Modèle Eloquent représentant une classe d'apprentis.
 *
 * Une classe regroupe plusieurs apprentis.
 *
 * @package App\Models
 *
 * @property int    $id_classe      Identifiant unique de la classe (clé primaire)
 * @property string $libelle_classe Intitulé de la classe (ex: "BTS Drone 1ère année")
 */
class Classes extends Model
{
    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    /** @brief Nom de la table en base de données. */
    protected $table = 'classes';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_classe';

    /**
     *
     * @var array<string>
     */
    protected $fillable = ['libelle_classe'];

    // ════════════════════════════RELATIONS══════════════════════════════════

    /**
     * @brief Relation vers les apprentis de la classe.
     *
     * Une classe peut contenir plusieurs apprentis.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apprentis()
    {
        return $this->hasMany(Apprentis::class, 'id_classe', 'id_classe');
    }
}