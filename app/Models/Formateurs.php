<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Formateurs extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'formateurs';
    protected $primaryKey = 'login';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['login', 'mot_de_passe', 'nom', 'prenom'];
    protected $hidden = ['mot_de_passe'];
    public $timestamps = false;
    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'login', 'login');
    }
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
