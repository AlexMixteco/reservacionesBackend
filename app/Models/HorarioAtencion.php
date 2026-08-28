<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioAtencion extends Model
{
    use HasUuids;

    protected $table = 'horarios_atencion';

    protected $fillable = [
        'negocio_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class);
    }
}
