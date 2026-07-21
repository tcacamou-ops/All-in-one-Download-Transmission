=== All-in-one Download Transmission ===
Contributors: tcacamou
Tags: download, torrent, transmission, all-in-one-download
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.2
Stable tag: 0.0.6
License: Proprietary

Add-on for All-in-one Download that allows you to automatically send torrents to a Transmission client.

== Description ==

All-in-one Download Transmission is an add-on for the [All-in-one Download](https://github.com/tcacamou-ops/All-in-one-Download) WordPress plugin. Once configured, any torrent processed by All-in-one Download is automatically forwarded to your Transmission client via RPC.

Features:

* Automatic torrent forwarding to Transmission
* Configurable RPC endpoint, username, and password
* Connection status indicator in the settings page
* Active torrent count display

== Requirements ==

* WordPress 6.0 or higher
* [All-in-one Download](https://github.com/tcacamou-ops/All-in-one-Download) plugin
* A running Transmission instance accessible via RPC
* PHP 8.2 or higher

== Installation ==

1. Upload the `all-in-one-download-transmission` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **All-in-one Download > Transmission** and enter your Transmission credentials.

== Configuration ==

In the settings page, provide:

* **Transmission URL** — the RPC endpoint of your Transmission instance (e.g. `http://localhost:9091/transmission/rpc`)
* **Username** — your Transmission username (leave empty if authentication is disabled)
* **Password** — your Transmission password (leave empty if authentication is disabled)

== Frequently Asked Questions ==

= Does this plugin work without All-in-one Download? =

No. This plugin is an add-on and requires the All-in-one Download plugin to be installed and activated.

= Which versions of Transmission are supported? =

Any version of Transmission that exposes an RPC interface (2.x and 3.x are supported).

= Can I use a remote Transmission instance? =

Yes, as long as the RPC endpoint is accessible from your WordPress server.

== Changelog ==
= 0.0.5 =
* Security: validate REST API args at registration level
* Security: encrypt Transmission password at rest with AES-256-CBC
* Security: block SSRF via Transmission URL validation
* Security: various hardening fixes
* Feat: expose credentials as modal on Status page

= 0.0.4 =
* Feat Add the status feature

= 0.0.3 =
* Fix : composer issues

= 0.0.2 =
* Initial public release.

= 0.0.1 =
* First development version.

== Upgrade Notice ==

= 0.0.2 =
Initial release. No upgrade path from previous versions.
