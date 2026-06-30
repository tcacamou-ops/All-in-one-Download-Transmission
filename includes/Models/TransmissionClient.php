<?php
namespace AllI1D\Transmission\Models;

class TransmissionClient {
    private $url;
    private $username;
    private $password;

    public function __construct( string $url, string $username, string $password ) {
        $this->url      = self::validate_url( $url );
        $this->username = $username;
        $this->password = $password;
    }

    private static function validate_url( string $url ): string {
        $parsed = wp_parse_url( $url );

        if ( ! isset( $parsed['scheme'] ) || ! in_array( $parsed['scheme'], [ 'http', 'https' ], true ) ) {
            throw new \InvalidArgumentException(
                'Transmission URL must use http or https scheme.'
            );
        }

        $host = $parsed['host'] ?? '';

        if ( 'transmission' === $host ) {
            return $url;
        }

        if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
            $blocked_ranges = [
                '/^127\./',
                '/^10\./',
                '/^192\.168\./',
                '/^172\.(1[6-9]|2\d|3[01])\./',
                '/^169\.254\./',
                '/^::1$/',
                '/^fc00:/i',
            ];
            foreach ( $blocked_ranges as $pattern ) {
                if ( preg_match( $pattern, $host ) ) {
                    throw new \InvalidArgumentException(
                        'Transmission URL must not point to an internal IP address.'
                    );
                }
            }
        }

        return $url;
    }

    private function request(array $payload, string $sessionId = '', int $retryCount = 0): array {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HEADER, true);

        $headers = ['Content-Type: application/json'];
        if ($sessionId) {
            $headers[] = 'X-Transmission-Session-Id: ' . $sessionId;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response   = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $curlError  = curl_error($ch);

        if ($response === false) {
            throw new \Exception("cURL error: {$curlError}");
        }

        // Transmission returns 409 with a new session ID on first request or when session expires.
        if ($httpCode === 409) {
            if ($retryCount >= 1) {
                throw new \Exception("409 Conflict after retry. Could not authenticate with Transmission.");
            }
            preg_match('/X-Transmission-Session-Id:\s*(\S+)/i', $response, $matches);
            $newSessionId = $matches[1] ?? '';
            if (!$newSessionId) {
                throw new \Exception("409 Conflict but no X-Transmission-Session-Id found in response.");
            }
            return $this->request($payload, $newSessionId, $retryCount + 1);
        }

        $body = substr($response, $headerSize);
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response (HTTP {$httpCode}): " . substr($body, 0, 500));
        }
        if (!isset($data['result']) || $data['result'] !== 'success') {
            throw new \Exception("Transmission request failed: " . ($data['result'] ?? 'unknown') . ". Body: " . substr($body, 0, 500));
        }

        return $data;
    }

    public function addTorrent(string $torrentPath, string $destination): array {
        if (!file_exists($torrentPath)) {
            throw new \Exception("Torrent file not found: {$torrentPath}");
        }
        $torrentData = base64_encode(file_get_contents($torrentPath));

        return $this->request([
            'method' => 'torrent-add',
            'arguments' => [
                'metainfo'     => $torrentData,
                'download-dir' => $destination,
            ]
        ]);
    }

    public function listTorrents(): array {
        $data = $this->request([
            'method' => 'torrent-get',
            'arguments' => [
                'fields' => ['id', 'name', 'status', 'totalSize', 'percentDone', 'rateDownload', 'rateUpload']
            ]
        ]);

        return $data['arguments']['torrents'] ?? [];
    }
}