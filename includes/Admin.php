<?php

namespace AllI1D\Transmission;
use AllI1D\Transmission\Pages\Settings;
use AllI1D\Transmission\Api;
use AllI1D\Components\ToastMessage;

class Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu'], 99);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

    }

    public function register_admin_menu() {
        add_submenu_page(
            'all-in-one-download',
            __('Transmission settings', 'all-in-one-download-transmission'),
            __('Transmission', 'all-in-one-download-transmission'),
            'alli1d_admin',
            'all-in-one-download-transmission',
            [$this, 'settings_page'],
            99,
        );
    }

    public function admin_enqueue_scripts() {
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

    public function settings_page() {
        $toastMessage = new ToastMessage();
        $settings = new Settings();
        $settings->render();
        $toastMessage->render();
    }
}