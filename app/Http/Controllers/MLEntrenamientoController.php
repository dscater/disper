<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;
use Phpml\Clustering\KMeans;
use Phpml\ModelManager;
use Phpml\Estimator;

class MLEntrenamientoController extends Controller
{

    public function entrenamientoModelo()
    {
        $lugares = Lugar::all();
        // crear la información necesaria con la capacidad del lugar
        // deacuerdo a este se asignara una cierta cantidad de personal al lugar
        $data = [];
        $lugares = [];
        foreach ($lugares as $key_lugar => $item) {
            $data[] = [
                "capacidad" => $item->capacidad,
            ];
            // agregamos $key_lugar para diferenciar el lugar de la zona ya que en algunas zonas
            // existen mas de una latitud y longitud en el mapa 
            $lugares[] = $item->nombre . $key_lugar;
        }

        // Convertimos los datos en un array para las características (X)
        $samples = [];
        foreach ($data as $item) {
            $samples[] = [$item['Capacidad']];
        }

        // Creamos y entrenamos el modelo
        $total_lugares = count($lugares);
        $clusterer = new Estimator($total_lugares);
        // pasamos la capacidad y lugares respectivamente
        $clusterer->train($samples, $lugares);

        // Guardamos el modelo entrenado para su uso posterior
        $modelManager = new ModelManager();
        $modelManager->saveToFile($clusterer, public_path("modelos/modelo_entrenado.phpml"));

        return true;
    }
}
