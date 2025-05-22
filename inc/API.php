<?php
/**
 * API class for registering WordPress REST API endpoints.
 *
 * This file contains the API class which is responsible for
 * registering and handling custom REST API routes in WordPress.
 *
 * @package PostSummarizer
 */

namespace ViridianSG;

/**
 * A singleton class that registers WordPress REST API endpoints.
 */
class API {
	/**
	 * The instance of the class.
	 *
	 * @var API
	 */
	private static $instance = null;

	/**
	 * The constructor.
	 */
	private function __construct() {
	}

	/**
	 * Initialize the API by registering the necessary WordPress hooks.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Get the instance of the class.
	 *
	 * @return API
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {
		// Register the /summarize_post endpoint.
		register_rest_route(
			'vsg/v1',
			'/summarize_post',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'summarize_post' ),
				'permission_callback' => '__return_true',
			)
		);
	}
	/**
	 * Callback function for the /summarize endpoint.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response The response object.
	 */
	public function summarize_post( $request ) {
		$body = $request->get_json_params();
		$text = isset( $body['text'] ) ? sanitize_text_field( $body['text'] ) : '';

		if ( empty( $text ) ) {
			return new \WP_Error( 'no_text', 'No text provided', array( 'status' => 400 ) );
		}

		$summarizer = new PostSummarizer();
		$result     = $summarizer->summarize( $text );

		if ( is_wp_error( $result ) ) {
			return new \WP_Error( 'summarization_failed', 'Summarization failed', array( 'status' => 500 ) );
		}

		return rest_ensure_response( array( 'summary' => $result ) );
	}
}
