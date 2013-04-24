<?php

class WpHttpRequest {


	protected $http;


	public function __construct( WP_Http $http ) {
		$this->http = $http;
	}


	public function get( $URL, $getParams = array() ) {
		$wpResponseArray = $this->http->get( $URL, $getParams );
		return new WpHttpResponse( $wpResponseArray );
	}


	public function post( $URL, $postParams = array() ) {
		$wpResponseArray = $this->http->post( $URL, $postParams );
		return new WpHttpResponse( $wpResponseArray );
	}


	public function authenticatedPost( $url, $body, $user, $pass, $contentType = null ) {
		$params = array(
			'method'  => 'POST',
			'body'    => $body,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $user . ':' . $pass )
			)
		);
		if ( $contentType ) {
			$params['headers']['Content-type'] = $contentType;
		}
		$wpResponseArray = $this->http->request( $url, $params );
		return new WpHttpResponse( $wpResponseArray );
	}

}

