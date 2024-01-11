<?php

declare(strict_types=1);

namespace App\ApiResource;

use App\Metric\RamMetricInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class RamMetrics.
 *
 * @description This class is responsible for getting RAM usage information
 */
final class RamMetric implements RamMetricInterface
{
    /**
     * @return array<string, float> returns an array with RAM usage information
     */
    public function getMemoryUsage(): array
    {
        return $this->parseMemoryInfo();
    }

    /**
     * @return array<string, float> returns parsed memory information
     */
    private function parseMemoryInfo(): array
    {
        $process = new Process(['free']);
        try {
            $process->mustRun();
            $output = $process->getOutput();

            // Parse the output
            preg_match('/^Mem:\s+(\d+)\s+(\d+)\s+(\d+)\s+\d+\s+\d+\s+(\d+)/m', $output, $matches);
            $totalMb = $matches[1] / 1024;
            $usedMb = ($matches[1] - $matches[4]) / 1024;  // Used memory is total minus available
            $freeMb = $matches[3] / 1024;
            $availableMb = $matches[4] / 1024;

            // Calculate used percentage directly
            $usedPercentage = $usedMb / $totalMb * 100;

            return [
                'total' => round($totalMb, 2),
                'used' => round($usedMb, 2),
                'free' => round($freeMb, 2),
                'available' => round($availableMb, 2),
                'percentage' => round($usedPercentage, 2),
            ];
        } catch (ProcessFailedException) {
            // Handle the exception as needed
            throw new \RuntimeException('An error occurred while running the "free" command.');
        }
    }
}
