<?php

namespace ViridianSG;

/**
 * Class PostSummarizer
 *
 * @package ViridianSG
 */
class PostSummarizer {

	const API_URL = 'https://api.openai.com/v1/chat/completions';
	/**
	 * The API key for authenticating requests to the OpenAI API.
	 *
	 * @var string
	 */
	private $api_key;
	/**
	 * The model to use for the OpenAI API.
	 *
	 * @var string
	 */
	private $model;

	/**
	 * The temperature setting for the OpenAI API.
	 *
	 * @var float
	 */
	private $temperature;

	/**
	 * The maximum number of tokens for the OpenAI API response.
	 *
	 * @var int
	 */
	private $max_tokens;

	/**
	 * Constructor for the PostSummarizer class.
	 *
	 * @param string $model The model to use for the OpenAI API. Default is 'gpt-3.5-turbo'.
	 * @param float  $temperature The temperature setting for the OpenAI API. Default is 0.7.
	 * @param int    $max_tokens The maximum number of tokens for the OpenAI API response. Default is 300.
	 * throws \Exception If the API key is not defined in the configuration.
	 */
	public function __construct( $model = 'gpt-3.5-turbo', $temperature = 0.7, $max_tokens = 300 ) {
		$this->api_key     = $this->get_api_key();
		$this->model       = $model;
		$this->temperature = $temperature;
		$this->max_tokens  = $max_tokens;
	}
	/**
	 * Summarize the given text using OpenAI's GPT-3.5 Turbo model.
	 *
	 * @param string $text The text to summarize.
	 * @return string The summarized text.
	 */
	public function summarize( $text ) {

		$data = array(
			'model'       => $this->model,
			'messages'    => array(
				array(
					'role'    => 'user',
					'content' => "Summarize the following text:\n\n" . $text,
				),
			),
			'temperature' => $this->temperature,
			'max_tokens'  => $this->max_tokens,
		);

		$api_response = $this->make_request( $data );
		if ( is_wp_error( $api_response ) ) {
			return $api_response;
		}
		return $api_response['choices'][0]['message']['content'];
	}
	/**
	 * Make a request to the OpenAI API.
	 *
	 * @param string $data The data to send in the request body.
	 * @return string The response from the API.
	 */
	private function make_request( $data ) {
		$response = wp_remote_post(
			static::API_URL,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => json_encode( $data ),
				'timeout' => 30,
			)
		);

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		error_log( 'Response: ' . print_r( $response_body, true ) );

		if ( ! empty( $response_body['error'] ) ) {
			return new \WP_Error( 'ai_api_error', $response_body['error']['message'], array( 'status' => 500 ) );
		}

		if ( empty( $response_body['choices'][0]['message']['content'] ) ) {
			return new \WP_Error( 'ai_api_error', 'Invalid AI API response', array( 'status' => 500 ) );
		}
		return $response_body;
	}

	/**
	 * Retrieve the API key for authenticating requests.
	 *
	 * @return string The API key if defined
	 * @throws \Exception If the API key is not defined in the configuration.
	 */
	private function get_api_key() {
		if ( defined( 'VIRIDIANSG_API_KEY' ) ) {
			return VIRIDIANSG_API_KEY;
		}
		throw new \Exception( 'API key not defined. Please set your OpenAPI API key as the value of the VIRIDIANSG_API_KEY constant in wp-config.php.' );
	}
}
