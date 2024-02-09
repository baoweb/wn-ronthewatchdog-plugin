<?php

$hash = config('baoweb.ronwatchdog::custom_url');

Route::get('ron/' . $hash, '\Baoweb\RonWatchdog\Controllers\MainApiController@index');
