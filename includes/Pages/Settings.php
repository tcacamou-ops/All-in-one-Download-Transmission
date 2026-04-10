<?php
namespace AllI1D\Transmission\Pages;

use AllI1D\Transmission\Components\Credentials;

class Settings {
    public function render() {
        $credentials = new Credentials();
        echo '<div class="wrap">';
        echo '<h1>' . __('Transmission Settings', 'all-in-one-download-transmission') . '</h1>';
        $credentials->render();
        
        echo '</div>';
        
    }
}