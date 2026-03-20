<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apprenti extends Model
{
    protected $table = 'apprentis';
    public $timestamps = false; // Si pas de created_at/updated_at
    protected $fillable = ['nom', 'prenom', 'id_classes'];
    protected $primaryKey = 'id_apprentis';
    public $incrementing = true;
    protected $keyType = 'int';
}
