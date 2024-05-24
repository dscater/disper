<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        "cod",
        "nro",
        "total",
        "status",
        "fecha_registro",
        "status"
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function detalle_entrenamientos()
    {
        return $this->hasMany(DetalleEntrenamiento::class, 'entrenamiento_id');
    }

    public static function getNuevoCodigo()
    {
        $ultimo = Entrenamiento::get()->last();
        $nro = 1;
        if ($ultimo) {
            $nro = (int)$ultimo->nro + 1;
        }
        $cod = "E." . $nro;
        return [$cod, $nro];
    }
}
