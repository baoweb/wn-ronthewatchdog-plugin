# Ron the Watchdog

Ron is a lightweight monitoring endpoint for Winter CMS. It exposes a single, IP-restricted
HTTP endpoint that a central watchdog service can poll to learn the state of an installation:

- the **CMS build number** and whether core files have been modified,
- **integrity hashes** of critical entry files (`index.php`, `.htaccess`),
- the full **plugin inventory** (versions, enabled/frozen state, and whether each plugin was
  installed via Composer or dropped in manually),
- the project's **Composer requirements**, and
- on demand, the complete **`composer.lock`** for dependency auditing.

> **This plugin is still work in progress. Please use with care.**

## Requirements

- Winter CMS (Winter Storm `^1.2`)
- PHP `>= 8.0.2`

## Installation

Install via Composer from the Baoweb private (Satis) repository:

```bash
composer require baoweb/wn-ronthewatchdog-plugin
```

This installs the plugin into `plugins/baoweb/ronwatchdog`. (Make sure the Baoweb Satis
repository is registered under `repositories` in your project's `composer.json` first.)

## Configuration

All settings have sensible, secure-by-default values. You can override them with environment
variables, or by creating a config file at `config/baoweb/ronwatchdog/config.php`.

### Environment variables

| Variable                      | Default           | Description                                                  |
| ----------------------------- | ----------------- | ------------------------------------------------------------ |
| `RONTHEWATCHDOG_WHITELISTED_IP` | `false`         | The single IP address allowed to call the endpoint. Until set, **every** request is rejected with a 404. |
| `RONTHEWATCHDOG_CUSTOM_URL`     | `ron-the-watchdog` | The obscured URL segment the endpoint is mounted under (see [Endpoints](#endpoints)). |

```dotenv
RONTHEWATCHDOG_WHITELISTED_IP=127.0.0.1
RONTHEWATCHDOG_CUSTOM_URL=some-hard-to-guess-string
```

### Config file

Create `config/baoweb/ronwatchdog/config.php` to override any of the defaults:

```php
return [

    /**
     * The single IP address allowed to make requests to the endpoint.
     * Any request from a different IP receives a 404 response.
     * Defaults to env('RONTHEWATCHDOG_WHITELISTED_IP', false) — i.e. locked down
     * until you configure it.
     */
    'whitelisted_ip' => env('RONTHEWATCHDOG_WHITELISTED_IP', false),

    /**
     * The obscured URL segment the endpoint is mounted under, e.g.
     * /ron/<custom_url>. Use `php artisan ron:generate` to produce a random one.
     */
    'custom_url' => env('RONTHEWATCHDOG_CUSTOM_URL', 'ron-the-watchdog'),

    /**
     * The hashing algorithm used for the file integrity hashes.
     * Currently only 'md5' is implemented; any other value yields 'false'.
     */
    'hash_algorithm' => 'md5',

    /**
     * Include the index.php / .htaccess integrity hashes in the output.
     */
    'send_file_hashes' => true,

    /**
     * Include the plugin inventory in the output.
     */
    'send_plugin_info' => true,

    /**
     * Include the Composer requirements in the output.
     */
    'send_package_info' => true,
];
```

| Option             | Default            | Description                                                                 |
| ------------------ | ------------------ | --------------------------------------------------------------------------- |
| `whitelisted_ip`   | `false`            | Only this IP may call the endpoint; all others get a 404.                   |
| `custom_url`       | `ron-the-watchdog` | URL segment the endpoint is served under.                                  |
| `hash_algorithm`   | `md5`              | Algorithm for file hashes. Only `md5` is implemented; other values → `false`. |
| `send_file_hashes` | `true`             | Toggle the `hashes` / `algorithm` block in the main response.              |
| `send_plugin_info` | `true`             | Toggle the `plugins` block in the main response.                           |
| `send_package_info`| `true`             | Toggle the `packages` block in the main response.                          |

## Securing the endpoint

Access is gated by two independent mechanisms:

1. **IP whitelist** — only requests from `whitelisted_ip` are served; everyone else receives a
   standard `cms::404` response, so the endpoint is indistinguishable from a missing page.
2. **Obscured URL** — the endpoint lives under a configurable, hard-to-guess segment
   (`custom_url`).

Generate a strong random URL segment with the bundled console command and put the result in
`RONTHEWATCHDOG_CUSTOM_URL`:

```bash
php artisan ron:generate
```

## Endpoints

With the defaults, the endpoint is mounted at `/ron/ron-the-watchdog`. Replacing
`ron-the-watchdog` with your configured `custom_url`:

### `GET /ron/{custom_url}` — status report

Returns a JSON snapshot of the installation. The optional blocks are included only when their
corresponding `send_*` option is enabled.

```jsonc
{
  "cms": "winter-cms",
  "version": 475,            // CMS build number
  "modified": false,         // true if core files differ from the reference build

  // present when send_file_hashes = true
  "hashes": {
    "index": "d41d8cd98f00b204e9800998ecf8427e",
    "htaccess": "098f6bcd4621d373cade4e832627b4f6"
  },
  "algorithm": "md5",

  // present when send_plugin_info = true
  "plugins": [
    {
      "fullName": "Baoweb.RonWatchdog",
      "name": "RonWatchdog",
      "namespace": "Baoweb",
      "version": "2.0.0",
      "is_disabled": false,
      "is_frozen": false,
      "is_custom": false       // true = not found among Composer requirements (manually installed)
    }
  ],

  // present when send_package_info = true
  "packages": [
    { "name": "winter/storm", "version": "^1.2", "is_dev": false }
  ],

  "meta": {
    "generated_by": {
      "plugin": "ron-the-watchdog",
      "version": "2.0.0"       // taken from this plugin's version.yaml
    }
  }
}
```

### `GET /ron/{custom_url}/audit` — dependency audit

Returns the raw contents of the project's `composer.lock` as JSON, for exact dependency-version
auditing. If no `composer.lock` is present, responds with `404` and
`{ "error": "composer.lock not found" }`.

## Console commands

| Command              | Description                                                              |
| -------------------- | ------------------------------------------------------------------------ |
| `php artisan ron:generate` | Print a random 20–30 character string suitable for `RONTHEWATCHDOG_CUSTOM_URL`. |

## How detection works

- **Version / modified flag** come from Winter's `UpdateManager` build-number detection.
- **File hashes** are computed over `index.php` and `.htaccess` in the project root.
- **Plugin inventory** is read from Winter's `PluginManager` and the `PluginVersion` records.
  A plugin is reported as `is_custom` when its vendor/name is not found among the project's
  Composer `require` entries.
- **Packages** are read from the project's root `composer.json` (`require` and `require-dev`).

## License

MIT © Baoweb. See [LICENCE](LICENCE).
