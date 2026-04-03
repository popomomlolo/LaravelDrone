<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    public $timestamps = false;
    protected $table = 'classes';
    protected $primaryKey = 'id_classes';

    protected $fillable = ['libelle_classes'];

    public function apprentis()
    {
        return $this->hasMany(Apprentis::class, 'id_classes', 'id_classes');
    }
}
