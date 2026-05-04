<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $table      = 'sessions_drone';
    protected $primaryKey = 'id_session';
    public $timestamps    = false;

    // date_heure a une valeur par défaut CURRENT_TIMESTAMP dans la migration
    // Peut être omis lors de la création pour utiliser la date/heure actuelle
    protected $fillable = [
        'date_heure',
        'type_environnement',
        'type_drone',
        'duree_max',
        'id_meteo',
        'id_formateur',
        'id_apprenti',
    ];

    protected $casts = [
        'type_environnement' => 'boolean',
        'date_heure'         => 'datetime',
    ];

    public function meteo()
    {
        return $this->belongsTo(Meteo::class, 'id_meteo', 'id_meteo');
    }

    public function formateur()
    {
        return $this->belongsTo(Formateurs::class, 'id_formateur', 'id_formateur');
    }

    public function apprenti()
    {
        return $this->belongsTo(Apprentis::class, 'id_apprenti', 'id_apprenti');
    }

    public function objectifs()
    {
        return $this->belongsToMany(
            Objectifs::class,
            'validations',
            'id_session',
            'id_objectif'
        )->withPivot('reussi', 'quantite_a_atteindre', 'quantite_realisee');
    }
}
