<?php

return [

    /**
     * Determiner whether system version number will be read from
     * the installed json file in composer or from the database.
     */
    'use_composer' => true,

    /**
     * Allowed IP address to make the requests from.
     * Defaults to 127.0.0.1
     */
    'whitelisted_ip' => env('RONTHEWATCHDOG_WHITELISTED_IP', false),

    /**
     * Allowed IP address to make the requests from.
     * Defaults to 127.0.0.1
     */
    'custom_url' => env('RONTHEWATCHDOG_CUSTOM_URL', 'ron-the-watchdog'),

    /**
     * The hashing algorithm used for file hashes.
     */
    'hash_algorithm' => 'md5',

    /**
     * Include file hashes in the output
     */
    'send_file_hashes' => true,

    /**
     * Include plugin information including plugin version in the output
     */
    'send_plugin_info' => true,
];
