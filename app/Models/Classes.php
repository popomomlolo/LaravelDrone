<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    public $timestamps = false;
    protected $table = 'classes';
    protected $primaryKey = 'id_classe';

    protected $fillable = ['libelle_classe'];

    public function apprentis()
    {
        return $this->hasMany(Apprentis::class, 'id_classe', 'id_classe');
    }
}
