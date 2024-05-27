<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        "asignacion_id",
        "lugar_id",
        "detalle_entrenamiento_id",
        "lat",
        "lng",
        "fecha",
        "hora",
        "requerido",
        "total_personal",
        "restante",
        "fecha_registro"
    ];

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id');
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'lugar_id');
    }

    public function detalle_entrenamiento()
    {
        return $this->belongsTo(DetalleEntrenamiento::class, 'detalle_entrenamiento_id');
    }

    public function asignacion_personals()
    {
        return $this->hasMany(AsignacionPersonal::class, 'asignacion_detalle_id');
    }
}
