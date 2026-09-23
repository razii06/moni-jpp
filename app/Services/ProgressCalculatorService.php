<?php

namespace App\Services;

class ProgressCalculatorService
{
    /**
     * Mengkalkulasi akumulasi progres (RAB + PBJ + Pekerjaan)
     */
    public static function calculate(float $rab = 0, float $pbj = 0, float $progressPekerjaan = 0): float
    {
        $total = $rab + $pbj + $progressPekerjaan;
        return max(0, min(100, round($total, 2)));
    }
}