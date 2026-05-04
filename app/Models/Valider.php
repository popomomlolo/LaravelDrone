<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valider extends Model
{
    protected $table = 'validations';
    protected $primaryKey = ['id_session', 'id_objectif'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_session',
        'id_objectif',
        'reussi',
        'quantite_a_atteindre',
        'quantite_realisee'
    ];

    public function session()
    {
        return $this->belongsTo(Sessions::class, 'id_session', 'id_session');
    }

    public function objectif()
    {
        return $this->belongsTo(Objectifs::class, 'id_objectif', 'id_objectif');
    }
}
