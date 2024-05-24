<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Metric;

interface NetworkMetricInterface
{
    /**
     * @return array{ total: int, used: int, free: int, percentage: float } returns an array with network usage information
     */
    public function getNetworkUsage(): array;
}
