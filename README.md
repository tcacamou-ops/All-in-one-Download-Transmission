# All-in-one-Download-Transmission

An add-on for the [All-in-one Download](https://github.com/tcacamou-ops/All-in-one-Download) WordPress plugin that allows you to automatically send torrents to a [Transmission](https://transmissionbt.com/) client.

## Requirements

- WordPress
- [All-in-one Download](https://github.com/tcacamou-ops/All-in-one-Download) plugin
- A running Transmission instance accessible via RPC
- PHP 7.4+

## Installation

1. Upload the plugin folder to `wp-content/plugins/`.
2. Activate the plugin from the WordPress admin panel.
3. Go to **All-in-one Download > Transmission** and enter your Transmission credentials.

## Configuration

In the settings page, provide:

- **Transmission URL** — the RPC endpoint of your Transmission instance (e.g. `http://localhost:9091/transmission/rpc`)
- **Username** — your Transmission username
- **Password** — your Transmission password

The settings page also displays the current connection status and the number of active torrents.

## How it works

Once configured, any torrent processed by All-in-one Download is automatically forwarded to your Transmission client via the `alli1d_process_torrent` filter hook.

## License

Proprietary