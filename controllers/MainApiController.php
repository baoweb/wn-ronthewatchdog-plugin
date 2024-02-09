<?php namespace Baoweb\RonWatchdog\Controllers;

use Baoweb\RonWatchdog\Classes\ComposerVersionReader;
use Baoweb\RonWatchdog\Classes\DatabaseVersionReader;
use Baoweb\RonWatchdog\Classes\PluginVersionReader;
use Illuminate\Http\Request;
use Baoweb\RonWatchdog\Classes\FileUpdateChecker;
use Baoweb\RonWatchdog\Classes\UpdateManagerVersionReader;
use October\Rain\Parse\Yaml;
use Winter\Storm\Support\Facades\Config;

/**
 * Main Api Controller Backend Controller
 */
class MainApiController
{

    protected $output = [];

    public function index(Request $request)
    {
        $whitelistedIp = Config::get('baoweb.ronwatchdog::whitelisted_ip', false);

        // check if the request is coming from the whitelisted IP
        if($request->ip() != $whitelistedIp) {
            return  \Illuminate\Support\Facades\Response::make(View::make('cms::404'), 404);
        }

        // getting the version
        $useComposerForCoreVersion = Config::get('baoweb.ronwatchdog::use_composer', false);

        if($useComposerForCoreVersion) {
            $buildNumber = (new ComposerVersionReader())->getVersionNumber();
        } else {
            $buildNumber = (new DatabaseVersionReader())->getVersionNumber();
        }

        $this->output['cms'] = 'october-cms';
        $this->output['version'] = $buildNumber;

        if (config('baoweb.ronwatchdog::send_file_hashes', true)) {
            $this->getHashes();
        }

        if (config('baoweb.ronwatchdog::send_plugin_info', true)) {
            $this->getPluginInfo();
        }

        $versionFile = (new \Winter\Storm\Parse\Yaml())->parseFile(__DIR__ . '/../updates/version.yaml');
        end($versionFile);

        $this->output['meta'] = [
            'generated_by' => [
                'plugin' => 'ron-the-watchdog',
                'version' => key($versionFile)
            ]
        ];

        return $this->output;
    }

    protected function getHashes(): void
    {
        $fileChecker = new FileUpdateChecker();

        $indexHash = $fileChecker->getIndexHash();

        $htaccessHash = $fileChecker->getHtaccessHash();

        $this->output['hashes'] = [
            'index' => $indexHash,
            'htaccess' => $htaccessHash,
        ];
        $this->output['algorithm'] = $fileChecker->getAlgorithm();
    }

    protected function getPluginInfo(): void
    {
        $pluginsReader = new PluginVersionReader();

        $plugins = $pluginsReader->getPluginList();

        $this->output['plugins'] = $plugins;
    }
}
