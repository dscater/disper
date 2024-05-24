<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $fillable = [
        "cod",
        "nro",
        "fecha_registro",
        "status"
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    // relaciones
    public function asignacion_detalles()
    {
        return $this->hasMany(AsignacionDetalle::class, 'asignacion_id');
    }


    // funciones
    public static function getNuevoCodigo()
    {
        $ultimo = Asignacion::get()->last();
        $nro = 1;
        if ($ultimo) {
            $nro = (int)$ultimo->nro + 1;
        }
        $cod = "A." . $nro;
        return [$cod, $nro];
    }
}
