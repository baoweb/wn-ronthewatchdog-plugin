<?php


namespace Baoweb\RonWatchdog\Classes;


use October\Rain\Support\Facades\File;

class ComposerVersionReader implements VersionReaderInterface
{

    public function getVersionNumber()
    {
        $path = base_path() . '/vendor/composer/installed.json';

        $installerPackages = json_decode(File::get($path), true);

        $installerPackages = collect($installerPackages);

        $octoberRainPackage = $installerPackages->where('name', 'october/rain')->first();

        $semanticVersions = explode('.', $octoberRainPackage['version_normalized']);

        return (int) $semanticVersions[2];
    }
}
