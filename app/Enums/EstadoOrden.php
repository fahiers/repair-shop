<?php

namespace App\Enums;

enum EstadoOrden: string
{
    case Pendiente = 'pendiente';
    case Diagnostico = 'diagnostico';
    case EnReparacion = 'en_reparacion';
    case EsperaRepuesto = 'espera_repuesto';
    case Listo = 'listo';
    case Entregado = 'entregado';
    case Cancelado = 'cancelado';

    /**
     * Obtiene la etiqueta legible del estado.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Diagnostico => 'Diagnóstico',
            self::EnReparacion => 'En reparación',
            self::EsperaRepuesto => 'En espera de repuesto',
            self::Listo => 'Listo',
            self::Entregado => 'Entregado',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * Obtiene todos los estados disponibles con sus etiquetas.
     *
     * @return array<string, string>
     */
    public static function disponibles(): array
    {
        return array_reduce(
            self::cases(),
            function (array $carry, self $estado) {
                $carry[$estado->value] = $estado->etiqueta();

                return $carry;
            },
            []
        );
    }

    /**
     * Verifica si el estado es un estado cerrado (entregado o cancelado).
     */
    public function esCerrado(): bool
    {
        return in_array($this, [self::Entregado, self::Cancelado], true);
    }
}
