<?php


namespace Baoweb\RonWatchdog\Classes;


use System\Models\Parameter;

class DatabaseVersionReader implements VersionReaderInterface
{

    public function getVersionNumber()
    {
        $buildNumber = Parameter::select('value')
            ->where('namespace', 'system')
            ->where('group', 'core')
            ->where('item', 'build')
            ->pluck('value')
            ->first();

        return (int) $buildNumber;
    }
}
