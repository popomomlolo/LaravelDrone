<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Formateurs extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['login', 'mot_de_passe', 'nom', 'prenom'];
    protected $primaryKey = 'login';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}