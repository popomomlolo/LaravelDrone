<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'formateurs';
    protected $primaryKey = 'id_formateur'; // ← renommé depuis login
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = ['id_formateur', 'mot_de_passe', 'nom', 'prenom'];
    protected $hidden   = ['mot_de_passe'];

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    protected function casts(): array
    {
        return [
            'mot_de_passe' => 'hashed',
        ];
    }
}