<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apprentis extends Model
{
    protected $table = 'apprentis';
    protected $primaryKey = 'id_apprentis';

    protected $fillable = ['nom', 'prenom', 'id_classes'];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'id_classes', 'id_classes');
    }

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'id_apprentis', 'id_apprentis');
    }
}
