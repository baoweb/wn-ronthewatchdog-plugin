<?php
use Baoweb\RonWatchdog\Controllers\MainApiController;

$hash = config('baoweb.ronwatchdog::custom_url');

Route::get('ron/' . $hash, [MainApiController::class, 'index']);
Route::get('ron/' . $hash . '/audit', [MainApiController::class, 'audit']);
