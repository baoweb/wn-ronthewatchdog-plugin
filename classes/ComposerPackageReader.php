<?php

namespace Baoweb\RonWatchdog\Classes;

class ComposerPackageReader
{
    protected $composerData;

    public function __construct()
    {
        $composerFile = base_path('composer.json');

        $this->composerData = file_exists($composerFile)
            ? json_decode(file_get_contents($composerFile), true)
            : [];
    }

    public function getPackageList(): array
    {
        $packages = [];

        foreach ($this->composerData['require'] ?? [] as $name => $version) {
            $packages[] = [
                'name' => $name,
                'version' => $version,
                'is_dev' => false,
            ];
        }

        foreach ($this->composerData['require-dev'] ?? [] as $name => $version) {
            $packages[] = [
                'name' => $name,
                'version' => $version,
                'is_dev' => true,
            ];
        }

        return $packages;
    }

    public function getRequireKeys(): array
    {
        return array_keys($this->composerData['require'] ?? []);
    }
}
