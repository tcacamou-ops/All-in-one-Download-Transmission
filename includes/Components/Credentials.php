<?php
namespace AllI1D\Transmission\Components;

use AllI1D\Transmission\Models\TransmissionClient;
use AllI1D\Helpers\Crypto;

class Credentials {
    public function get_html(): string {
        ob_start();
        $this->render();
        return ob_get_clean() ?: '';
    }

    public function render() {
        echo '<label for="transmission_url">' . __('Transmission url', 'all-in-one-download') . '</label>';
        echo '<input type="text" id="transmission_url" name="transmission_url" placeholder="Your transmission url" required  value="' . esc_attr( get_option( 'alli1d_transmission_url', '' ) ) . '" />';
        echo '<br /><br />';
        echo '<label for="transmission_login">' . __('Transmission username', 'all-in-one-download') . '</label>';
        echo '<input type="text" id="transmission_login" name="transmission_login" placeholder="Your login here" required  value="' . esc_attr( get_option( 'alli1d_transmission_login', '' ) ) . '" />';
        echo '<br /><br />';
        echo '<label for="transmission_pwd">' . __('Transmission password', 'all-in-one-download') . '</label>';
        echo '<input type="password" id="transmission_pwd" name="transmission_pwd" placeholder="' . esc_attr( __( 'Transmission password', 'all-in-one-download' ) ) . '" required value="' . esc_attr( Crypto::decrypt( get_option( 'alli1d_transmission_pwd', '' ) ) ) . '" />';
        echo '<br /><br />';
        echo '<button type="button" id="submit-transmission-credentials">' . __('Save', 'all-in-one-download') . '</button>';
        echo '<div id="url-message" style="margin-top: 10px;"></div>';
        try {
            $transmission_url = get_option('alli1d_transmission_url', 'http://localhost:9091/transmission/rpc');
            $username = get_option('alli1d_transmission_login', '');
            $password = Crypto::decrypt( get_option('alli1d_transmission_pwd', '') );

            $client = new TransmissionClient($transmission_url, $username, $password);
            $torrents = $client->listTorrents();
            echo '<div class="transmission-credentials-status"><span class="dashicons dashicons-yes"></span> ' . __('Connected', 'all-in-one-download') . ' with '.count($torrents).' active torrents</div>';
        } catch (\Exception $e) {
            echo '<div class="transmission-credentials-status"><span class="dashicons dashicons-no"></span> ' . __('Not connected', 'all-in-one-download') . '</div>';
        }

    }
}