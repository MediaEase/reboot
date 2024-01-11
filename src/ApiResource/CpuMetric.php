<?php

declare(strict_types=1);

namespace App\ApiResource;

use App\Metric\CpuMetricInterface;

final class CpuMetric implements CpuMetricInterface
{
    /**
     * @return array<string, string> returns an array with CPU usage information
     */
    public function getCpuInfo(): array
    {
        $cpuInfo = file_get_contents('/proc/cpuinfo');
        if ($cpuInfo === false) {
            return $this->getDefaultCpuInfo();
        }

        $cpuModel = $this->extractCpuModel($cpuInfo);
        $numberOfCores = substr_count($cpuInfo, 'processor');

        return [
            'model' => sprintf('%s (%d cores)', $cpuModel, $numberOfCores),
            'percentage' => $this->getCurrentCpuUsage(),
            'loadAverage' => $this->getLoadAverage(),
        ];
    }

    /**
     * @return array<string, string> returns the default CPU information when actual data is not available
     */
    private function getDefaultCpuInfo(): array
    {
        return [
            'model' => 'Unknown CPU Model',
            'percentage' => 0.0,
            'loadAverage' => ['1min' => 0.0, '5min' => 0.0, '15min' => 0.0],
        ];
    }

    private function extractCpuModel(string $cpuInfo): string
    {
        if (preg_match("/model name\s+:\s+(.*)/m", $cpuInfo, $matches)) {
            return trim($matches[1]);
        }

        return 'Unknown CPU Model';
    }

    /**
     * @return array<string, float> returns an array with load average information
     */
    private function getLoadAverage(): array
    {
        $loadAvg = file_get_contents('/proc/loadavg');
        if ($loadAvg !== false) {
            $parts = explode(' ', $loadAvg);

            return [
                '1min' => (float) $parts[0],
                '5min' => (float) $parts[1],
                '15min' => (float) $parts[2],
            ];
        }

        return ['1min' => 0.0, '5min' => 0.0, '15min' => 0.0];
    }

    /**
     * @return float returns the current CPU usage percentage
     */
    private function getCurrentCpuUsage(): float
    {
        $statData1 = $this->parseProcStat();
        sleep(1);  // Wait for 1 second to get a better sample of CPU usage
        $statData2 = $this->parseProcStat();

        if ($statData1 && $statData2) {
            // Calculate the differences in various CPU times
            $diffUser = $statData2['user'] - $statData1['user'];
            $diffNice = $statData2['nice'] - $statData1['nice'];
            $diffSystem = $statData2['system'] - $statData1['system'];
            $diffIdle = $statData2['idle'] - $statData1['idle'];
            $diffIowait = $statData2['iowait'] - $statData1['iowait'];

            // Calculate total difference in jiffies
            $diffTotal = $diffUser + $diffNice + $diffSystem + $diffIdle + $diffIowait;

            // Calculate the percentage of CPU used
            return ($diffTotal - $diffIdle) / $diffTotal * 100;
        }

        return 0.0;
    }

    /**
     * @return array<string, float>|null returns an array with CPU usage information
     */
    private function parseProcStat(): ?array
    {
        $content = file_get_contents('/proc/stat');
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (preg_match('/^cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $line, $matches)) {
                return [
                    'user' => (float) $matches[1],
                    'nice' => (float) $matches[2],
                    'system' => (float) $matches[3],
                    'idle' => (float) $matches[4],
                    'iowait' => (float) $matches[5],
                ];
            }
        }

        return null;
    }
}
