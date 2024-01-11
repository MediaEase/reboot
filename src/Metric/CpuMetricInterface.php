<?php

declare(strict_types=1);

namespace App\Metric;

interface CpuMetricInterface
{
    /**
     * @return array<string, string> returns an array with CPU information
     */
    public function getCpuInfo(): array;
}
