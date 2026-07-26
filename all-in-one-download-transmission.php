<?php
/**
 * Plugin Name: All-in-one Download Transmission
 * Plugin URI: https://github.com/tcacamou-ops/All-in-one-Download-Transmission
 * Description: Add-on for All-in-one Download that allows you to send torrents to a Transmission client.
 * Version: 0.0.9
 * Author: tcacamou
 * Author URI: https://github.com/tcacamou-ops
 * Text Domain: all-in-one-download-transmission
 * Domain Path: /languages
 * Requires PHP: 8.2
 * License: Proprietary
 */

namespace AllI1D\Transmission;

use AllI1D\Transmission\Components\Credentials;
use AllI1D\Transmission\Filters\Download;
use AllI1D\Transmission\Filters\Status;
use AllI1D\Helpers\Crypto;
use honemo\updater\Updater;

// Security: prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

// Define the absolute path constant for the plugin.
if (!defined('AllI1D_TRANSMISSION_DIR')) {   
    define('AllI1D_TRANSMISSION_DIR', plugin_dir_path(__FILE__));
}

// Define the plugin URL constant.
if (!defined('AllI1D_TRANSMISSION_URL')) {
    define('AllI1D_TRANSMISSION_URL', plugin_dir_url(__FILE__));
}

// Include the Composer autoloader.
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

class Plugin {
    public function __construct() {
        $this->initialize_admin();
        $this->initialize_api();
        $this->initialize_filters();
    }

    private function initialize_admin() {
        new Admin();
        $updater = new Updater(
            __FILE__,                                      // Main plugin file.
            'https://github.com/tcacamou-ops/All-in-one-Download-Transmission'  // Repository URL.
        );

        $updater->init();
    }

    private function initialize_api() {
        Api::get_instance();
    }

    private function initialize_filters() {
        add_filter( 'alli1d_process_torrent', [Download::class, 'process_torrent'] );
        add_filter( 'alli1d_process_status', [Status::class, 'process_status'] );
        add_filter( 'alli1d_provider_settings_modals', [$this, 'register_modal'] );
        add_action( 'admin_init', [$this, 'migrate_credentials_encryption'] );
    }

    public function migrate_credentials_encryption(): void {
        $migrated_key = 'alli1d_transmission_credentials_encrypted_v1';
        if ( get_option( $migrated_key ) ) {
            return;
        }
        $pwd = get_option( 'alli1d_transmission_pwd', '' );
        if ( '' !== $pwd && 0 !== strpos( $pwd, 'enc:' ) ) {
            update_option( 'alli1d_transmission_pwd', Crypto::encrypt( $pwd ) );
        }
        update_option( $migrated_key, true );
    }

    public function register_modal( array $modals ): array {
        $credentials = new Credentials();
        $modals['transmission'] = [
            'title' => __( 'Transmission Settings', 'all-in-one-download-transmission' ),
            'html'  => $credentials->get_html(),
        ];
        return $modals;
    }
}

// Initialize the plugin.
new Plugin();