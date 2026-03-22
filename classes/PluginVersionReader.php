<?php

namespace Baoweb\RonWatchdog\Classes;

use System\Classes\PluginManager;
use System\Models\PluginVersion;

class PluginVersionReader
{

    /**
     * @return array
     */
    public function getPluginList(): array
    {
        $pluginManager = PluginManager::instance();

        $installedPlugins = $pluginManager->getAllPlugins();

        $plugins = [];

        $pluginVersions = PluginVersion::all();

        $composerRequires = $this->getComposerRequireKeys();

        foreach ($installedPlugins as $key => $plugin) {
            list($namespace, $pluginName) = explode('.', $key);

            $pluginRecord = $pluginVersions->where('code', $key)->first();

            $plugins[] = [
                'fullName' => $key,
                'name' => $pluginName,
                'namespace' => $namespace,
                'version' => $pluginRecord ? $pluginRecord->version : false,
                'is_disabled' => (bool) ($pluginRecord ? $pluginRecord->is_disabled : false),
                'is_frozen' => (bool) ($pluginRecord ? $pluginRecord->is_frozen : false),
                'is_custom' => !$this->isComposerInstalled($namespace, $pluginName, $composerRequires),
            ];
        }

        return $plugins;
    }

    protected function getComposerRequireKeys(): array
    {
        $composerFile = base_path('composer.json');
        if (!file_exists($composerFile)) {
            return [];
        }

        $composer = json_decode(file_get_contents($composerFile), true);

        return array_keys($composer['require'] ?? []);
    }

    protected function isComposerInstalled(string $namespace, string $pluginName, array $composerRequires): bool
    {
        $namespace = strtolower($namespace);
        $pluginName = strtolower($pluginName);

        foreach ($composerRequires as $package) {
            $package = strtolower($package);
            if (str_contains($package, $namespace) && str_contains($package, $pluginName)) {
                return true;
            }
        }

        return false;
    }
}
