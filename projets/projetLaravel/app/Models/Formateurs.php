<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formateurs extends Model
{
    protected $table = 'formateurs';
    protected $primaryKey = 'login';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['login', 'mot_de_passe', 'nom', 'prenom'];
    protected $hidden = ['mot_de_passe'];

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'login', 'login');
    }
}
