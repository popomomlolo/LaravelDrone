<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @brief Modèle Eloquent représentant un formateur, utilisé comme entité d'authentification.
 *
 * Le formateur est l'utilisateur authentifié de l'application.
 * Il utilise le champ `login` comme identifiant d'authentification
 * à la place du champ `email` par défaut de Laravel.
 *
 * @package App\Models
 *
 * @property int    $id_formateur Identifiant unique (clé primaire)
 * @property string $login        Identifiant de connexion (unique)
 * @property string $mot_de_passe Mot de passe hashé (caché dans les sérialisations)
 * @property string $nom          Nom de famille du formateur
 * @property string $prenom       Prénom du formateur
 */
class Formateurs extends Authenticatable
{
    use HasFactory, Notifiable;

    /** @brief Nom de la table en base de données. */
    protected $table = 'formateurs';

    /** @brief Clé primaire de la table. */
    protected $primaryKey = 'id_formateur';

    /** @brief La clé primaire est auto-incrémentée. */
    public $incrementing = true;

    /** @brief Type de la clé primaire. */
    protected $keyType = 'int';

    /**
     * @brief Champs autorisés à l'assignation en masse.
     * @var array<string>
     */
    protected $fillable = ['login', 'mot_de_passe', 'nom', 'prenom'];

    /**
     * @brief Champs masqués lors de la sérialisation (JSON, array).
     * @var array<string>
     */
    protected $hidden = ['mot_de_passe'];

    /** @brief Désactive les colonnes created_at / updated_at. */
    public $timestamps = false;

    // ══════════════════════════════════════════════════════════════
    // AUTH CUSTOM — Laravel utilise login au lieu de email

    /**
     * @brief Retourne la valeur de l'identifiant d'authentification.
     *
     * Surcharge Laravel pour utiliser `login` au lieu de `id`.
     *
     * @return mixed Valeur du champ `login`
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute('login');
    }

    /**
     * @brief Retourne le nom du champ utilisé comme identifiant d'authentification.
     *
     * @return string Nom du champ : `'login'`
     */
    public function getAuthIdentifierName()
    {
        return 'login';
    }

    /**
     * @brief Retourne le mot de passe hashé pour l'authentification.
     *
     * @return string Mot de passe hashé
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

// ════════════════════════════RELATIONS══════════════════════════════════

    /**
     * @brief Relation vers les sessions animées par ce formateur.
     *
     * Un formateur peut animer plusieurs sessions de vol.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(SessionsDrone::class, 'id_formateur', 'id_formateur');
    }
}
