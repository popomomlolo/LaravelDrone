<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objectifs extends Model
{
    protected $table = 'objectifs';
    protected $primaryKey = 'id_objectifs';

    protected $fillable = ['libelle_objectifs', 'est_automatique'];

    public function sessions()
    {
        return $this->belongsToMany(
            Sessions::class,
            'valider',
            'id_objectifs',
            'id_sessions'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}
