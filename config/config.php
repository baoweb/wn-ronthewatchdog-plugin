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
     * The hashing algorithm used for file hashes.
     */
    'hash_algorithm' => 'md5',
];
