<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $table = 'sessions';
    protected $primaryKey = 'id_sessions';

    protected $fillable = [
        'date_heure', 'type_environnement', 'conditions_meteo',
        'type_drone', 'duree_max', 'login', 'id_apprentis'
    ];

    public function formateur()
    {
        return $this->belongsTo(Formateurs::class, 'login', 'login');
    }

    public function apprenti()
    {
        return $this->belongsTo(Apprentis::class, 'id_apprentis', 'id_apprentis');
    }

    public function objectifs()
    {
        return $this->belongsToMany(
            Objectifs::class,
            'valider',
            'id_sessions',
            'id_objectifs'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}