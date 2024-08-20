<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;
use Phpml\ModelManager;
use Phpml\Clustering\KMeans;

class MLEntrenamientoController extends Controller
{

    public function entrenamientoModelo()
    {
        // Obtener todos los lugares
        $lugares = Lugar::all();

        // Crear la información necesaria con la capacidad del lugar
        $data = [];
        $nombresLugares = [];
        foreach ($lugares as $key_lugar => $item) {
            $data[] = [
                "capacidad" => $item->capacidad,
            ];
            // Agregar $key_lugar para diferenciar el lugar en caso de zonas con más de una latitud y longitud
            $nombresLugares[] = $item->nombre . $key_lugar;
        }

        // Convertir los datos en un array para las características (X)
        $samples = [];
        foreach ($data as $item) {
            $samples[] = [$item['capacidad']];
        }

        // Usar KMeans para clustering
        $totalClusters = count($nombresLugares); // O puedes definir un número fijo de clusters
        $kmeans = new KMeans($totalClusters);

        // Entrenar el modelo
        $clusters = $kmeans->cluster($samples);

        // Guardar el modelo entrenado para su uso posterior
        $modelManager = new ModelManager();
        $modelManager->saveToFile($kmeans, public_path("modelos/modelo_entrenado.phpml"));

        return true;
    }
}
