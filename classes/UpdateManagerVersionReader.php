<?php namespace Baoweb\RonWatchdog\Classes;

use System\Classes\UpdateManager;

class UpdateManagerVersionReader implements VersionReaderInterface
{
    public function getVersionNumber(): array
    {

        $build = UpdateManager::instance()->setBuildNumberManually();

        return [
            'build' => $build['build'],
            'modified' => $build['modified'],
        ];
    }
}
