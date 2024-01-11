<?php

declare(strict_types=1);

namespace App\ApiResource;

use App\Metric\NetworkMetricInterface;

final class NetworkMetric implements NetworkMetricInterface
{
    /**
     * @return array<string, float> returns an array with network usage information
     */
    public function getNetworkUsage(): array
    {
        $interface = 'wlp2s0';
        $rxFile = sprintf('/sys/class/net/%s/statistics/rx_bytes', $interface);
        $txFile = sprintf('/sys/class/net/%s/statistics/tx_bytes', $interface);
        $rxBytesPrev = file_exists($rxFile) ? file_get_contents($rxFile) : 0;
        $txBytesPrev = file_exists($txFile) ? file_get_contents($txFile) : 0;
        sleep(1);

        $rxBytesCurr = file_exists($rxFile) ? file_get_contents($rxFile) : 0;
        $txBytesCurr = file_exists($txFile) ? file_get_contents($txFile) : 0;

        // Calculate speeds in bytes per second
        $rxSpeed = $rxBytesCurr - $rxBytesPrev;
        $txSpeed = $txBytesCurr - $txBytesPrev;

        // Convert speeds to a more readable format, e.g., kilobytes per second
        $rxSpeedKbps = $rxSpeed / 1024;
        $txSpeedKbps = $txSpeed / 1024;

        return [
            'interface' => $interface,
            'downloadSpeed' => $rxSpeedKbps,
            'uploadSpeed' => $txSpeedKbps,
        ];
    }
}
