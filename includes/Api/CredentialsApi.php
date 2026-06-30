<?php
namespace AllI1D\Transmission\Api;

use AllI1D\Transmission\Models\TransmissionClient;
use AllI1D\Helpers\Crypto;

class CredentialsApi {

    private $route_namespace;

    private $current_namespace = 'credentials';

    public function __construct(string $route_namespace) {
        $this->route_namespace = $route_namespace;
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function get_namespace(): string {
        return $this->route_namespace.'/'.$this->current_namespace;
    }

    public function check_permissions() :bool {
        return current_user_can('alli1d_admin');
    }

    public function get_routes():array {
        return [
            'credentials' => rest_url($this->get_namespace()),
        ];
    }

    public function register_routes() {
        register_rest_route(
            $this->route_namespace,
            $this->current_namespace,
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'set_credentials' ],
                'permission_callback' => [ $this, 'check_permissions' ],
                'args'                => [
                    'transmission_url'   => [
                        'required'          => true,
                        'type'              => 'string',
                        'sanitize_callback' => 'esc_url_raw',
                        'validate_callback' => static function ( $value ) {
                            $scheme = wp_parse_url( $value, PHP_URL_SCHEME );
                            return in_array( $scheme, [ 'http', 'https' ], true );
                        },
                    ],
                    'transmission_login' => [
                        'required'          => true,
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => static fn( $v ) => is_string( $v ) && strlen( $v ) >= 1 && strlen( $v ) <= 255,
                    ],
                    'transmission_pwd'   => [
                        'required'          => true,
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => static fn( $v ) => is_string( $v ) && strlen( $v ) >= 1 && strlen( $v ) <= 255,
                    ],
                ],
            ]
        );
    }

    public function set_credentials($request) {
        $transmission_url   = $request->get_param('transmission_url');
        $transmission_login = $request->get_param('transmission_login');
        $transmission_pwd   = $request->get_param('transmission_pwd');
        try {
            new TransmissionClient( $transmission_url, $transmission_login, $transmission_pwd );
        } catch ( \InvalidArgumentException $e ) {
            return new \WP_Error( 'invalid_url', $e->getMessage(), [ 'status' => 400 ] );
        }
        update_option('alli1d_transmission_pwd', Crypto::encrypt( $transmission_pwd ));
        update_option('alli1d_transmission_login', $transmission_login);
        update_option('alli1d_transmission_url', $transmission_url);
        return new \WP_REST_Response(['status' => 'success'], 200);
    }
}