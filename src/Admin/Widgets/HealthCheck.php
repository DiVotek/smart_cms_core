<?php

namespace SmartCms\Core\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HealthCheck extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            ...$this->getDiskSpaceUsage(),
            ...$this->getCpuLoad(),
            ...$this->getMemoryUsage(),
        ];
    }

    protected function getDiskSpaceUsage(): array
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        return [
            Stat::make(_actions('disk_usage'), round($used / 1_073_741_824, 2).' / '.round($total / 1_073_741_824, 2).' GB')
                ->chart([$used, $used, $used])
                ->color($free > 1_000_000 ? 'success' : 'danger')
                ->description(_actions('used_space')),
        ];
    }

    protected function getCpuLoad(): array
    {
        $load = sys_getloadavg();
        $icon = $load[0] > 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make(_actions('cpu_load'), round($load[0], 2).' %')
                ->descriptionIcon($icon)
                ->chart([$load[0], $load[1], $load[2]])
                ->color($load[0] > 80 ? 'danger' : 'success')
                ->description(_actions('15_minute_average')),
        ];
    }

    protected function getMemoryUsage(): array
    {
        $mem = shell_exec('free -m');
        preg_match('/Mem:\s+(\d+)\s+(\d+)/', $mem, $matches);

        $total = $matches[1] ?? 0;
        $used = $matches[2] ?? 0;
        $percent = $total > 0 ? round(($used / $total) * 100, 2) : 0;

        return [
            Stat::make(_actions('memory_usage'), "{$used} MB / {$total} MB")
                ->description("$percent% used")
                ->chart([$percent, $percent, $percent])
                ->color($percent > 80 ? 'danger' : 'success'),
        ];
    }
}
