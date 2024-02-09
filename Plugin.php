<?php namespace Baoweb\RonWatchdog;

use Baoweb\RonWatchdog\Console\GenerateUrlString;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function register()
    {
        $this->registerConsoleCommand('ron.generate', GenerateUrlString::class);
    }

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
}
