<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meteo extends Model
{
    protected $table = 'conditions_meteo';
    protected $primaryKey = 'id_meteo';
    public $timestamps = false;

    protected $fillable = [
        'jour',
        'ciel',
        'vent_x',
        'vent_y',
        'vent_z',
        'vent_norme',
    ];

    protected $casts = [
        'jour' => 'boolean',
        'ciel' => 'integer',
    ];

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'id_meteo', 'id_meteo');
    }
}
