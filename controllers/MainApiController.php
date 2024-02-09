<?php namespace Baoweb\RonWatchdog\Controllers;

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
        $whitelistedIp = Config::get('baoweb.ronwatchdog::whitelisted_ip', false);

        // check if the request is coming from the whitelisted IP
        if($request->ip() != $whitelistedIp) {
            return  \Illuminate\Support\Facades\Response::make(View::make('cms::404'), 404);
        }

        // getting the version
        $build = (new UpdateManagerVersionReader())->getVersionNumber();

        $this->output['version'] = $build['build'];
        $this->output['modified'] = $build['modified'];


        if (config('baoweb.ronwatchdog::send_file_hashes', true)) {
            $this->getHashes();
        }

        if (config('baoweb.ronwatchdog::send_plugin_info', true)) {
            $this->getPlginInfo();
        }

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

    protected function getPlginInfo(): void
    {
        $pluginsReader = new PluginVersionReader();

        $plugins = $pluginsReader->getPluginList();

        $this->output['plugins'] = $plugins;
    }
}
