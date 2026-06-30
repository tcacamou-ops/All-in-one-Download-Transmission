<?php

namespace AllI1D\Transmission;
use AllI1D\Transmission\Api;

class Admin {
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    }

    public function admin_enqueue_scripts( string $hook ): void {
        if ( 'downloads_page_all-in-one-download-status' !== $hook ) {
            return;
        }
        wp_enqueue_script(
            'allI1d-transmission-admin',
            AllI1D_TRANSMISSION_URL . 'assets/js/components/credentials.js',
            ['jquery'],
            '1.0.0'
        );
        $api = Api::get_instance();
        wp_localize_script(
            'allI1d-transmission-admin',
            'allI1d_transmission',
            [
                'api' => $api->get_data(),
            ]
        );
    }
}
