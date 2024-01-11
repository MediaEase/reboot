<?php

declare(strict_types=1);

namespace App\ApiResource;

use App\Metric\DiskMetricInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class DiskMetric implements DiskMetricInterface
{
    /**
     * @return array<string, string> returns an array with disk usage information
     */
    public function getDiskUsage(string $path = '/', ?string $exclude = null): array
    {
        if (str_contains($path, 'rclone')) {
            return $command = ['rclone', 'size', $path];
        }

        $command = ['df', '-h', $path, '-x', 'fuse.rclone'];
        $process = new Process($command);
        try {
            $process->mustRun();
            $output = $process->getOutput();
            $lines = explode("\n", trim($output));
            $lastLine = end($lines);

            return $this->parseDiskUsage($lastLine);
        } catch (ProcessFailedException) {
            return $this->getDefaultDiskUsage();
        }
    }

    /**
     * @param string $output output from disk usage command
     *
     * @return array<string, string> returns parsed disk usage information
     */
    private function parseDiskUsage(string $output): array
    {
        $diskInfo = preg_split('/\s+/', $output);

        return [
            'name' => $diskInfo[0],
            'total' => $diskInfo[1],
            'used' => $diskInfo[2],
            'free' => $diskInfo[3],
            'percentage' => rtrim($diskInfo[4], '%'),
            'mount' => $diskInfo[5] ?? '/',
        ];
    }

    /**
     * @return array<string, string> returns the default disk usage information when actual data is not available
     */
    private function getDefaultDiskUsage(): array
    {
        return [
            'diskName' => 'Unknown',
            'total' => '0',
            'used' => '0',
            'free' => '0',
            'percentage' => '0',
            'mount' => 'Unknown',
        ];
    }
}
