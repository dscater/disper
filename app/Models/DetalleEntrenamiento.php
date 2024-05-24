<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEntrenamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        "entrenamiento_id",
        "lugar_id",
        "ocupados",
        "fecha",
        "hora",
    ];

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamiento_id');
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'lugar_id');
    }
}
