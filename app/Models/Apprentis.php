<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apprentis extends Model
{
    protected $table = 'apprentis';
    protected $primaryKey = 'id_apprenti';

    protected $fillable = ['nom', 'prenom', 'id_classe'];

    public $timestamps = false;

    public function classe()
    {
        return $this->belongsTo(Classes::class, 'id_classe', 'id_classe');
    }

    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'id_apprenti', 'id_apprenti');
    }
}
