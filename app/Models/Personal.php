<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personal extends Model
{
    use HasUuids;

    protected $table = 'personal';

    protected $fillable = [
        'negocio_id',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class);
    }

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class);
    }

        public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'personal_servicio');
    }

    public function horariosAtencion()
    {
        return $this->hasMany(HorarioAtencion::class);
    }
}
