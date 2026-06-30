<?php
namespace AllI1D\Transmission\Filters;
use AllI1D\Transmission\Models\TransmissionClient;
use AllI1D\Helpers\Crypto;

class Download {

    public function __construct() {
    }

    public static function process_torrent($item) {
        try {
            $client = new TransmissionClient(
                get_option('alli1d_transmission_url', ''),
                get_option('alli1d_transmission_login', ''),
                Crypto::decrypt( get_option('alli1d_transmission_pwd', '') )
            );
            $retour = $client->addTorrent($item['path'], $item['dest_directory']);
            $item['downloaded'] = true;
        }
        catch (\Exception $e) {
            $item['downloaded'] = false;
            error_log('[AllI1D Transmission] Error processing torrent: ' . $e->getMessage());
            return $item;
        }
        return $item;
    }
}
