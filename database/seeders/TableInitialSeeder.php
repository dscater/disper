<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TableInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "usuario" => "admin",
            "password" => Hash::make("admin"),
            "nombre" => "admin",
            "paterno" => "admin",
            "materno" => "",
            "ci" => "0",
            "ci_exp" => "LP",
            "dir" => "",
            "email" => "",
            "fono" => "",
            "tipo" => "ADMINISTRADOR",
            "foto" => "",
        ]);

        Configuracion::create([
            "nombre_sistema" => "DISPER",
            "alias" => "DP",
            "razon_social" => "DISPER S.A.",
            "ciudad" => "LA PAZ",
            "dir" => "LOS OLIVOS",
            "fono" => "777777",
            "correo" => "DISPER@GMAIL.COM",
            "web" => "DISPER.COM",
            "actividad" => "ACTIVIDAD",
            "logo" => "logo.webp",
        ]);
    }
}
