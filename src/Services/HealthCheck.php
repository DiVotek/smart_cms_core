<?php

namespace SmartCms\Core\Services;

use Illuminate\Support\Facades\Process;

class HealthCheck
{
    public function getDiskUsagePercentage(): int
    {
        $process = Process::fromShellCommandline('df -P '.($this->filesystemName ?: '.'));

        $process->run();

        $output = $process->getOutput();
        dd($output);
        // return (int) Regex::match('/(\d*)%/', $output)->group(1);
    }
}
