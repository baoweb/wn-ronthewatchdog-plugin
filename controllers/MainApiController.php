<?php namespace Baoweb\RonWatchdog\Controllers;

use Baoweb\RonWatchdog\Classes\ComposerPackageReader;
use Baoweb\RonWatchdog\Classes\PluginVersionReader;
use Illuminate\Http\Request;
use Baoweb\RonWatchdog\Classes\FileUpdateChecker;
use Baoweb\RonWatchdog\Classes\UpdateManagerVersionReader;
use Winter\Storm\Support\Facades\Config;

/**
 * Main Api Controller Backend Controller
 */
class MainApiController
{

    protected $output = [];

    public function index(Request $request)
    {
        if ($denied = $this->checkIpWhitelist($request)) {
            return $denied;
        }

        // getting the version
        $build = (new UpdateManagerVersionReader())->getVersionNumber();

        $this->output['cms'] = 'winter-cms';
        $this->output['version'] = $build['build'];
        $this->output['modified'] = $build['modified'];

        if (config('baoweb.ronwatchdog::send_file_hashes', true)) {
            $this->getHashes();
        }

        if (config('baoweb.ronwatchdog::send_plugin_info', true)) {
            $this->getPluginInfo();
        }

        if (config('baoweb.ronwatchdog::send_package_info', true)) {
            $this->getPackageInfo();
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

    public function audit(Request $request)
    {
        if ($denied = $this->checkIpWhitelist($request)) {
            return $denied;
        }

        $lockFile = base_path('composer.lock');

        if (!file_exists($lockFile)) {
            return response()->json(['error' => 'composer.lock not found'], 404);
        }

        return response()->json(
            json_decode(file_get_contents($lockFile), true)
        );
    }

    protected function checkIpWhitelist(Request $request)
    {
        $whitelistedIp = Config::get('baoweb.ronwatchdog::whitelisted_ip', false);

        if ($request->ip() != $whitelistedIp) {
            return \Illuminate\Support\Facades\Response::make(\View::make('cms::404'), 404);
        }

        return null;
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

    protected function getPackageInfo(): void
    {
        $packageReader = new ComposerPackageReader();

        $this->output['packages'] = $packageReader->getPackageList();
    }
}
