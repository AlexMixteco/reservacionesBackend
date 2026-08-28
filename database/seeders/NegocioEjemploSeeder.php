<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\Personal;
use App\Models\Servicio;
use App\Models\HorarioAtencion;
use Illuminate\Database\Seeder;

class NegocioEjemploSeeder extends Seeder
{
    public function run(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Dosse Barbería',
            'tipo_negocio' => 'barbería',
            'telefono_whatsapp' => '5215512345678',
            'direccion' => 'Av. López Mateos Sur 1234, Gdl.',
        ]);

        $corte = Servicio::create([
            'negocio_id' => $negocio->id,
            'nombre' => 'Corte de cabello',
            'duracion_minutos' => 45,
            'precio' => 250,
        ]);

        Servicio::create([
            'negocio_id' => $negocio->id,
            'nombre' => 'Barba',
            'duracion_minutos' => 30,

            'precio' => 150,
        ]);

        Servicio::create([
            'negocio_id' => $negocio->id,
            'nombre' => 'Corte + Barba',
            'duracion_minutos' => 60,
            'precio' => 350,
        ]);

        Personal::create([
            'negocio_id' => $negocio->id,
            'nombre' => 'Carlos',
        ]);

        // Lunes a sábado, 10:00 a 20:00
        foreach ([1, 2, 3, 4, 5, 6] as $dia) {
            HorarioAtencion::create([
                'negocio_id' => $negocio->id,
                'dia_semana' => $dia,
                'hora_inicio' => '10:00',
                'hora_fin' => '20:00',
            ]);
        }

        $this->command->info("Negocio de ejemplo creado con id: {$negocio->id}");
    }
}
