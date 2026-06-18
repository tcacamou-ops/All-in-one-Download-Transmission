<?php
namespace AllI1D\Transmission\Filters;
use AllI1D\Transmission\Models\TransmissionClient;

class Status {

    public function __construct() {
    }

    public static function process_status($status) {
        $retour = [];
        $url = get_option('alli1d_transmission_url', '');
        $login = get_option('alli1d_transmission_login', '');
        $pwd = get_option('alli1d_transmission_pwd', '');
        // If any of the credentials are missing, return an error status.
        if (empty($url) || empty($login) || empty($pwd)) {
            $retour = ['error' => 'Transmission credentials not set'];
            $status['transmission'] = $retour;
            return $status;
        }
        try {
            $client = new TransmissionClient(
                $url,
                $login,
                $pwd
            );
            $torentsList = $client->listTorrents();
            $retour = ['status' => 'connected', 'active_torrents' => count($torentsList)];
        }
        catch (\Exception $e) {
            $retour = ['error' => $e->getMessage()];
        }
        $retour['settings_url'] = admin_url('admin.php?page=all-in-one-download-transmission');
        $status['transmission'] = $retour;
        return $status;
    }
}
