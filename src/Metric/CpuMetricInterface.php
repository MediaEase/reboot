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

interface CpuMetricInterface
{
    /**
     * @return array<string, string> returns an array with CPU information
     */
    public function getCpuInfo(): array;
}
