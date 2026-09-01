<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservacion extends Model
{
    use HasUuids;

    protected $table = 'reservaciones';

    protected $fillable = [
        'negocio_id',
        'servicio_id',
        'personal_id',
        'fecha',
        'hora',
        'numero_personas',
        'nombre_cliente',
        'telefono_cliente',
        'email_cliente',
        'estado',
        'token_gestion',
        'notas_admin',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Reservacion $reservacion) {
            if (empty($reservacion->token_gestion)) {
                $reservacion->token_gestion = Str::random(40);
            }
            if (empty($reservacion->estado)) {
                $reservacion->estado = 'pendiente';
            }
        });
    }

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    // Calculado a partir de hora + servicio.duracion_minutos — no se guarda en BD
    public function getHoraFinAttribute(): ?string
    {
        if (!$this->hora || !$this->servicio) {
            return null;
        }

        return \Carbon\Carbon::parse($this->hora)
            ->addMinutes($this->servicio->duracion_minutos)
            ->format('H:i');
    }
}
