<?php

namespace SmartCms\Core\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use PDO;

class VersionCheck extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            ...$this->getPhpVersion(),
            ...$this->getNodeVersion(),
            ...$this->getDatabaseCheck(),
            ...$this->getErrorsCheck(),
        ];
    }

    protected function getPhpVersion(): array
    {
        return [
            Stat::make(_actions('php_version'), phpversion())
                ->description(_actions('php_version_description')),
        ];
    }

    protected function getNodeVersion(): array
    {
        $node = trim(shell_exec('node -v'));
        $npm = trim(shell_exec('npm -v'));

        return [
            Stat::make(_actions('node_version'), $node)
                ->description(_actions('node_version_description')),
        ];
    }

    protected function getErrorsCheck(): array
    {
        $errors = config('app.debug', false);
        $errorsText = $errors ? 'Enabled' : 'Disabled';

        return [
            Stat::make(_actions('errors_check'), $errorsText)->color($errors ? 'danger' : 'success')->description(_actions('is_errors_enabled')),
        ];
    }

    protected function getDatabaseCheck(): array
    {
        $database = DB::connection()->getPdo();
        $driver = DB::connection()->getDriverName();
        $serverVersion = $database->getAttribute(PDO::ATTR_SERVER_VERSION);
        $versionParts = explode('-', $serverVersion);
        $databaseName = isset($versionParts[1]) ? $versionParts[1] : ucfirst($driver);
        $databaseVersion = $versionParts[0];

        return [
            Stat::make(_actions('database_version'), $databaseVersion)
                ->description(_actions('current').' '.$databaseName.' '._actions('version')),
        ];
    }
}
