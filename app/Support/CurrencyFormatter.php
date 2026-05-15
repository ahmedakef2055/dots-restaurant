<?php

namespace App\Support;

final class CurrencyFormatter
{
    public static function format(float|int|string|null $amount): string
    {
        $numericAmount = (float) ($amount ?? 0);
        $sign = $numericAmount < 0 ? '-' : '';
        $formatted = number_format(abs($numericAmount), 2);

        if (app()->getLocale() === 'ar') {
            return $sign . $formatted . ' ج.م.';
        }

        return $sign . 'EGP ' . $formatted;
    }
}
