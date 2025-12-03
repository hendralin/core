<?php

if (!function_exists('numberToWords')) {
    function numberToWords($number) {
        $words = [
            0 => 'nol',
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan',
            10 => 'sepuluh',
            11 => 'sebelas',
            12 => 'dua belas',
            13 => 'tiga belas',
            14 => 'empat belas',
            15 => 'lima belas',
            16 => 'enam belas',
            17 => 'tujuh belas',
            18 => 'delapan belas',
            19 => 'sembilan belas',
            20 => 'dua puluh',
            30 => 'tiga puluh',
            40 => 'empat puluh',
            50 => 'lima puluh',
            60 => 'enam puluh',
            70 => 'tujuh puluh',
            80 => 'delapan puluh',
            90 => 'sembilan puluh'
        ];

        $result = '';

        if ($number == 0) {
            return '';
        }

        // Handle numbers less than 20
        if ($number < 20) {
            $result = $words[$number];
        }
        // Handle numbers less than 100
        elseif ($number < 100) {
            $tens = floor($number / 10) * 10;
            $units = $number % 10;
            $result = $words[$tens];
            if ($units > 0) {
                $result .= ' ' . $words[$units];
            }
        }
        // Handle numbers less than 1000 (hundreds)
        elseif ($number < 1000) {
            $hundreds = floor($number / 100);
            $remainder = $number % 100;

            if ($hundreds == 1) {
                $result = 'seratus';
            } else {
                $result = $words[$hundreds] . ' ratus';
            }

            if ($remainder > 0) {
                $result .= ' ' . numberToWords($remainder);
            }
        }
        // Handle numbers less than 1 million (thousands)
        elseif ($number < 1000000) {
            $thousands = floor($number / 1000);
            $remainder = $number % 1000;

            if ($thousands == 1) {
                $result = 'seribu';
            } elseif ($thousands < 100) {
                $result = numberToWords($thousands) . ' ribu';
            } else {
                $result = numberToWords($thousands) . ' ribu';
            }

            if ($remainder > 0) {
                $result .= ' ' . numberToWords($remainder);
            }
        }
        // Handle numbers less than 1 billion (millions)
        elseif ($number < 1000000000) {
            $millions = floor($number / 1000000);
            $remainder = $number % 1000000;

            $result = numberToWords($millions) . ' juta';

            if ($remainder > 0) {
                $result .= ' ' . numberToWords($remainder);
            }
        }
        // Handle larger numbers
        elseif ($number < 1000000000000) {
            $billions = floor($number / 1000000000);
            $remainder = $number % 1000000000;

            $result = numberToWords($billions) . ' miliar';

            if ($remainder > 0) {
                $result .= ' ' . numberToWords($remainder);
            }
        } else {
            return 'Angka terlalu besar';
        }

        return trim($result);
    }
}

if (!function_exists('terbilang')) {
    function terbilang($amount) {
        if ($amount <= 0) {
            return 'nol rupiah';
        }

        // Round to nearest rupiah (no decimals for rupiah)
        $rupiah = floor($amount);

        // Convert to words
        $words = numberToWords($rupiah);

        // Handle special cases for Indonesian currency
        $words = preg_replace('/\bsatu ribu\b/', 'seribu', $words);
        $words = preg_replace('/\bsatu ratus\b/', 'seratus', $words);

        // Capitalize first letter
        $result = ucfirst($words) . ' rupiah';

        return $result;
    }
}
