<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    public function getLugarUnico()
    {
        $lugares = Lugar::distinct()->pluck("nombre");

        return response()->JSON($lugares);
    }

    public function getLugars()
    {
    }
}
