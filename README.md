# Ron the watchdog

This plugin is still work in progress. Please use with care.

## Instalation guide

- Clone the repository into your plugin directory under baoweb/ronwatchdog

- Whitelist IP address you will be making your requests either in the .env file:

```RONTHEWATCHDOG_WHITELISTED_IP=127.0.0.1```

or by creating new config file `config/baoweb/ronwatchdog/config.php` where you can override any of these default settings:

```php
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
```
