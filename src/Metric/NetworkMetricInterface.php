<?php

declare(strict_types=1);

namespace App\Metric;

interface NetworkMetricInterface
{
    /**
     * @return array{ total: int, used: int, free: int, percentage: float } returns an array with network usage information
     */
    public function getNetworkUsage(): array;
}
