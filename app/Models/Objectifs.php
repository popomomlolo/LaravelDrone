<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objectifs extends Model
{
    protected $table = 'objectifs';
    protected $primaryKey = 'id_objectif';
    public $timestamps = false;

    protected $fillable = ['libelle_objectif', 'est_automatique'];

    public function sessions()
    {
        return $this->belongsToMany(
            Sessions::class,
            'validations',
            'id_objectif',
            'id_session'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}
