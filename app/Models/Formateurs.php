<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Formateurs extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'formateurs';
    protected $primaryKey = 'id_formateur';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_formateur', 'login', 'mot_de_passe', 'nom', 'prenom'];
    protected $hidden = ['mot_de_passe'];
    public $timestamps = false;

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'id_formateur', 'id_formateur');
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute('login');
    }

    public function getAuthIdentifierName()
    {
        return 'login';
    }

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
