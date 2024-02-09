<?php

use Baoweb\RonWatchdog\Classes\ComposerVersionReader;
use Baoweb\RonWatchdog\Classes\DatabaseVersionReader;
use Baoweb\RonWatchdog\Classes\FileUpdateChecker;
use Baoweb\RonWatchdog\Controllers\MainApiController;
use Illuminate\Http\Request;

$hash = config('baoweb.ronwatchdog::custom_url');

Route::get('ron/' . $hash, [MainApiController::class, 'index']);
