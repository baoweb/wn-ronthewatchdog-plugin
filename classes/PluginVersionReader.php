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

        $installedPlugins = $pluginManager->getPlugins();

        $plugins = [];

        $pluginVersions = PluginVersion::all();

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
            ];
        }

        return $plugins;
    }
}
