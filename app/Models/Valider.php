<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valider extends Model
{
    protected $table = 'valider';
    protected $primaryKey = ['id_sessions', 'id_objectifs'];
    public $incrementing = false;

    protected $fillable = [
        'id_sessions', 'id_objectifs',
        'reussi', 'quantite_a_atteindre', 'quantite_realisee'
    ];

    public function session()
    {
        return $this->belongsTo(Sessions::class, 'id_sessions', 'id_sessions');
    }

    public function objectif()
    {
        return $this->belongsTo(Objectifs::class, 'id_objectifs', 'id_objectifs');
    }
}
