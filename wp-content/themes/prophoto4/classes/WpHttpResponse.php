<?php


class WpHttpResponse {


	protected $headers;
	protected $cookies;
	protected $body;
	protected $code;
	protected $msg;


	public function __construct( $input ) {
		if ( is_array( $input ) ) {
			if ( isset( $input['headers'] ) ) {
				$this->headers = $input['headers'];
			}
			if ( isset( $input['cookies'] ) ) {
				$this->cookies = $input['cookies'];
			}
			if ( isset( $input['body'] ) ) {
				$this->body = $input['body'];
			}
			if ( isset( $input['response'] ) ) {
				if ( isset( $input['response']['code'] ) ) {
					$this->code = $input['response']['code'];
				}
				if ( isset( $input['response']['message'] ) ) {
					$this->msg = $input['response']['message'];
				}
			}
		} else if ( $input instanceof WP_Error ) {
			$this->code = 'error';
			$this->msg  = reset( array_keys( $input->errors ) ) . ': ' . reset( reset( $input->errors ) );
		} else {
			trigger_error( 'Invalid input to WpHttpResponse::__construct()', E_USER_NOTICE );
		}
	}


	public function headers() {
		return $this->headers;
	}


	public function cookies() {
		return $this->cookies;
	}


	public function body() {
		return $this->body;
	}


	public function jsonDecodedBody() {
		if ( $object = json_decode( strval( $this->body() ) ) ) {
			return $object;
		} else {
			return false;
		}
	}


	public function unserializedBody() {
		return @unserialize( strval( $this->body() ) );
	}


	public function code() {
		return $this->code;
	}


	public function msg() {
		return $this->msg;
	}


}

