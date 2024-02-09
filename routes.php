<?php
use Baoweb\RonWatchdog\Controllers\MainApiController;

$hash = config('baoweb.ronwatchdog::custom_url');

Route::get('ron/' . $hash, [MainApiController::class, 'index']);
