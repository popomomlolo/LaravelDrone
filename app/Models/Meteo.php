<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meteo extends Model
{
    protected $table = 'meteo';
    protected $primaryKey = 'id_meteo';
    public $timestamps = true;

    protected $fillable = [
        'condition_meteo',
        'vent_x',
        'vent_y',
        'vent_z',
        'vent_norme',
    ];

    protected $casts = [
        'condition_meteo' => 'boolean',
    ];

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'id_meteo', 'id_meteo');
    }
}
