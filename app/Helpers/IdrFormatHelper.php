<?php

if (! function_exists('format_idr_jt_mobile')) {
    /**
     * Format IDR for narrow screens: "Rp 285 Jt" when ≥ 1 juta; below that, full "Rp x.xxx".
     */
    function format_idr_jt_mobile(float|int|string|null $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }
        $amount = (float) $amount;
        if ($amount <= 0) {
            return '';
        }
        if ($amount < 1_000_000) {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
        $jt = $amount / 1_000_000;
        if (abs($jt - round($jt)) < 0.000_01) {
            return 'Rp ' . number_format((int) round($jt), 0, ',', '.') . ' Jt';
        }

        return 'Rp ' . number_format(round($jt, 1), 1, ',', '.') . ' Jt';
    }
}
