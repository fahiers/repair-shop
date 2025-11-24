<?php

namespace App\Enums;

enum EstadoOrden: string
{
    case Pendiente = 'pendiente';
    case Diagnostico = 'diagnostico';
    case EnReparacion = 'en_reparacion';
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

    /**
     * Obtiene las clases CSS de Tailwind para el color del estado.
     */
    public function clasesColor(): string
    {
        return match ($this) {
            self::Pendiente => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
            self::Diagnostico => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            self::EnReparacion => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
            self::Listo => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            self::Entregado => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::Cancelado => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
        };
    }
}
