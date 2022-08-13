<?php

use Baoweb\RonWatchdog\Classes\ComposerVersionReader;
use Baoweb\RonWatchdog\Classes\DatabaseVersionReader;
use Baoweb\RonWatchdog\Classes\FileUpdateChecker;
use Illuminate\Http\Request;

Route::get('ron-the-watchdog', function(Request $request) {

    $whitelistedIp = Config::get('baoweb.ronwatchdog::whitelisted_ip', false);

    // check if the request is coming from the whitelisted IP
    if($request->ip() != $whitelistedIp) {
        return  \Illuminate\Support\Facades\Response::make(View::make('cms::404'), 404);
    }

    $useComposerForCoreVersion = Config::get('baoweb.ronwatchdog::use_composer', false);

    if($useComposerForCoreVersion) {
        $buildNumber = (new ComposerVersionReader())->getVersionNumber();
        $platform =  (new ComposerVersionReader())->getPlatform();
    } else {
        $buildNumber = (new DatabaseVersionReader())->getVersionNumber();
        $platform = (new DatabaseVersionReader())->getPlatform();
    }

    $fileChecker = new FileUpdateChecker();

    $indexHash = $fileChecker->getIndexHash();

    $htaccessHash = $fileChecker->getHtaccessHash();



    return [
        'platform' => $platform,
        'version' => $buildNumber,
        'hashes' => [
            'index' => $indexHash,
            'htaccess' => $htaccessHash,
        ],
        'algorithm'=> $fileChecker->getAlgorithm()
    ];
});
