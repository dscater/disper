<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionPersonal extends Model
{
    use HasFactory;

    protected $fillable  = [
        "asignacion_detalle_id",
        "personal_id",
    ];

    public function asignacion_detalle()
    {
        return $this->belongsTo(AsignacionDetalle::class, 'asignacion_detalle_id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }
}
