<?php


namespace Baoweb\RonWatchdog\Classes;


use October\Rain\Support\Facades\File;
use Winter\Storm\Support\Collection;

class ComposerVersionReader implements VersionReaderInterface
{

    public function getVersionNumber()
    {
        $installerPackages = $this->getPackages();

        $octoberRainPackage = $installerPackages->where('name', 'october/rain')->first();

        if(!$octoberRainPackage) {
            $octoberRainPackage = $installerPackages->where('name', 'winter/storm')->first();
        }

        return $octoberRainPackage['version_normalized'];
    }

    public function getPlatform()
    {
        $installerPackages = $this->getPackages();

        if($installerPackages->where('name', 'october/rain')->first()) {
            return 'october';
        }


        if($installerPackages->where('name', 'winter/storm')->first()) {
            return 'winter';
        }

        return false;
    }

    protected function getPackages()
    {
        $path = base_path() . '/vendor/composer/installed.json';

        $installerPackages = json_decode(File::get($path, false), true);

        if(is_array($installerPackages['packages'])) {
            return collect($installerPackages['packages']);
        }

        return collect($installerPackages);
    }
}
