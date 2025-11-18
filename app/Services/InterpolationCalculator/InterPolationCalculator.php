<?php

namespace App\Services\InterpolationCalculator;

readonly class InterPolationCalculator
{
    public function __construct(
        private int $upperDipInmm,
        private int $lowerDipInmm,
        private int $upperVolLtr,
        private int $lowerVolLtr,
    )
    {}

    public function calculateInterpolationAverage(): float
    {
        $dipDiff = $this->upperDipInmm - $this->lowerDipInmm;
        $volDiff = $this->upperVolLtr - $this->lowerVolLtr;
        return round(($dipDiff / $volDiff) * 100, 2);
    }

    public function calculateNumberOfInterpolationGaps(): float|int
    {
        $volDiff = $this->upperVolLtr - $this->lowerVolLtr;
        return ($volDiff / 100) - 1;
    }
}
