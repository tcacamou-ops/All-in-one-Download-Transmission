<?php
namespace AllI1D\Transmission\Filters;
use AllI1D\Transmission\Models\TransmissionClient;

class Download {

    public function __construct() {
    }

    public static function process_torrent($item) {
        try {
            $client = new TransmissionClient(
                get_option('alli1d_transmission_url', ''),
                get_option('alli1d_transmission_login', ''),
                get_option('alli1d_transmission_pwd', '')
            );
            $retour = $client->addTorrent($item['path'], $item['dest_directory']);
            $item['downloaded'] = true;
        }
        catch (\Exception $e) {
            $item['downloaded'] = false;
            return $item;
        }
        return $item;
    }
}
