<?php

namespace App\Http\Controllers;

use App\Models\DetalleEntrenamiento;
use Illuminate\Http\Request;

use Phpml\Clustering\KMeans;
use Phpml\ModelManager;
use Phpml\Estimator;

class MLASignacionPersonalController extends Controller
{
    public function predecirAsignacion()
    {
        // cargar los datos de la base de datos
        $resultado = [];
        $lugares = Lugar::all();
        foreach ($lugares as $item) {
            $ocupados = DetalleEntrenamiento::where("lugar_id", $item->id)->sum("ocupados");
            $modelManager = new ModelManager();
            $clusterer = $modelManager->restoreFromFile(public_path("modelos/modelo_entrenado.phpml"));
            // Realizamos la predicción por cada lugar usando el modelo entrenado
            // segun la cantidad de ocupados indicar cuanto personal se necesitara
            $cluster = $clusterer->predict([$ocupados]);
            // armar el resultado por cada lugar
            $resultado[] = [
                "lugar_id" => $item->id,
                "requerido" => $cluster
            ];
        }
        return $resultado;
    }
}
