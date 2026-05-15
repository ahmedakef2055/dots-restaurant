<?php

namespace App\Services;

use App\Models\Unit;
use InvalidArgumentException;

class UnitConversionService
{
    public function convert(float $quantity, Unit|string $from, Unit|string $to): float
    {
        $fromUnit = $from instanceof Unit ? $from : $this->resolveUnitByCode($from);
        $toUnit = $to instanceof Unit ? $to : $this->resolveUnitByCode($to);

        if ($fromUnit->family !== $toUnit->family) {
            throw new InvalidArgumentException('Cannot convert between different unit families.');
        }

        $baseQuantity = $quantity * (float) $fromUnit->base_factor;

        if ((float) $toUnit->base_factor <= 0) {
            throw new InvalidArgumentException('Target unit base factor must be greater than zero.');
        }

        return $baseQuantity / (float) $toUnit->base_factor;
    }

    public function resolveUnitByCode(string $code): Unit
    {
        $normalized = mb_strtolower(trim($code));

        return Unit::query()
            ->whereRaw('LOWER(code) = ?', [$normalized])
            ->firstOrFail();
    }
}
