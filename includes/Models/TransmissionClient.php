<?php
namespace AllI1D\Transmission\Models;

class TransmissionClient {
    private $url;
    private $username;
    private $password;

    public function __construct($url, $username, $password) {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    public function addTorrent($torrentPath, $destination) {
        if (!file_exists($torrentPath)) {
            throw new \Exception("Torrent file not found.");
        }
        $torrentData = base64_encode(file_get_contents($torrentPath));

        // 1. Get session id
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['method' => 'session-get']));
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        preg_match('/X-Transmission-Session-Id: ([^\s]+)/', $response, $matches);
        $sessionId = $matches[1] ?? '';
        if (!$sessionId) {
            curl_close($ch);
            throw new \Exception("Could not get Transmission session id.");
        }

        // 2. Add torrent
        $payload = [
            'method' => 'torrent-add',
            'arguments' => [
                'metainfo' => $torrentData,
                'download-dir' => $destination
            ]
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Transmission-Session-Id: ' . $sessionId
        ]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
	
	public function listTorrents() {
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['method' => 'session-get']));
		curl_setopt($ch, CURLOPT_HEADER, true);
		$response = curl_exec($ch);
		preg_match('/X-Transmission-Session-Id: ([^\s]+)/', $response, $matches);
		$sessionId = $matches[1] ?? '';
		if (!$sessionId) {
			curl_close($ch);
			throw new \Exception("Could not get Transmission session id.");
		}

		$payload = [
			'method' => 'torrent-get',
			'arguments' => [
				'fields' => [
					'id', 'name', 'status', 'totalSize', 'percentDone', 'rateDownload', 'rateUpload'
				]
			]
		];
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'X-Transmission-Session-Id: ' . $sessionId
		]);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($result, true);
		if (isset($data['arguments']['torrents'])) {
			return $data['arguments']['torrents'];
		}
		return [];
	}
}