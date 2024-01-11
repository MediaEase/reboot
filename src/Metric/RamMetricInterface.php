<?php

declare(strict_types=1);

namespace App\Metric;

interface RamMetricInterface
{
    /**
     * @return array<string, string> returns an array with RAM usage information
     */
    public function getMemoryUsage(): array;
}
