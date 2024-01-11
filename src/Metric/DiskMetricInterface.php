<?php

declare(strict_types=1);

namespace App\Metric;

interface DiskMetricInterface
{
    /**
     * @return array{ total: int, used: int, free: int, percentage: float } returns an array with disk usage information
     */
    public function getDiskUsage(): array;
}
