<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negocio extends Model
{
    use HasUuids;

    protected $table = 'negocios';

    protected $fillable = [
        'nombre',
        'tipo_negocio',
        'telefono_whatsapp',
        'direccion',
        'color_marca',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class);
    }

    public function horariosAtencion(): HasMany
    {
        return $this->hasMany(HorarioAtencion::class);
    }

    public function bloqueos(): HasMany
    {
        return $this->hasMany(Bloqueo::class);
    }

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class);
    }

    public function usuariosAdmin(): HasMany
    {
        return $this->hasMany(UsuarioAdmin::class);
    }
}
