<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre",
        "paterno",
        "materno",
        "ci",
        "ci_exp",
        "estado_civil",
        "genero",
        "dir",
        "correo",
        "cel",
        "nombre_contacto",
        "cel_contacto",
        "foto",
        "estado",
        "fecha_registro",
    ];

    protected $appends = ["url_foto", "full_ci", "full_name", "iniciales_nombre", "fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . ($this->paterno != NULL && $this->paterno != '' ? ' ' . $this->paterno : '') . ($this->materno != NULL && $this->materno != '' ? ' ' . $this->materno : '');
    }

    public function getFullCiAttribute()
    {
        return $this->ci . ' ' . $this->ci_exp;
    }
    public function getUrlFotoAttribute()
    {
        if ($this->foto) {
            return asset("imgs/personals/" . $this->foto);
        }
        return null;
    }

    public function getInicialesNombreAttribute()
    {
        $iniciales = substr($this->nombre, 0, 1) . substr($this->paterno, 0, 1);
        return $iniciales;
    }
}
