<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;

class Apprenti extends Model
{
    protected $table = 'apprentis';
    public $timestamps = false; // Si pas de created_at/updated_at
    protected $fillable = ['nom', 'prenom', 'id_classe'];
    protected $primaryKey = 'id_apprenti';
    public $incrementing = true;
    protected $keyType = 'int';

    public function classe()
    {
        return $this->belongsTo(Classes::class, 'id_classe', 'id_classe');
    }
}
