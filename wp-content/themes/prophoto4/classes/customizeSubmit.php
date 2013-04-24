<?php

class ppCustomizeSubmit {


	protected $post;
	protected $processedArray = array();
	protected static $fieldPrefix = 'p_';


	function __construct( $post ) {
		$this->post = $this->validatePost( $post );
		if ( !empty( $this->post ) ) {
			add_action( 'pp_minify_generated_js', 'ppStaticFile::minifyJsInit' );
			foreach ( $this->post as $key => $val ) {
				if ( !NrUtil::isIn( self::$fieldPrefix, $key ) ) {
					continue;
				} else {
					if ( strlen( $key ) > 64 && pp::site()->isDev ) {
						new ppIssue( "\$_POST variable length for '$key' greater than 64, can cause problems with Suhosin" );
					}
					$this->processedArray[ preg_replace( "/^" . self::$fieldPrefix . '/', '', $key ) ] = $val;
				}
			}
			$this->handleMastheadOrder();
			$this->handleGaVerifySum();
		}
	}


	public function processedArray() {
		return $this->processedArray;
	}


	public function fieldPrefix() {
		return self::$fieldPrefix;
	}


	protected function validatePost( $post ) {

		if ( !is_array( $post ) ) {
			new ppIssue( 'ppCustomizeSubmit requires array as input param' );
			return array();

		} else if ( get_option( 'blog_charset' ) != 'UTF-8' ) {
			new ppIssue( 'ppCustomizeSubmit only proccesses $_POST data from customize form' );
			return array();

		} else if ( count( $post ) >= 200 ) {
			new ppIssue( 'ppCustomizeSubmit $_POST count should be less than 200 for Suhosin' );
		}

		return $post;
	}


	protected function handleMastheadOrder() {

		if ( isset( $this->post['masthead_order_reordered'] ) && $this->post['masthead_order_reordered'] == 'true' ) {

			if ( !$this->post['masthead_order_string'] ) {
				new ppIssue( 'Masthead order string not set in ppCustomizeSubmit::handleMastheadOrder()' );
			}

			parse_str( $this->post['masthead_order_string'] );

			if ( is_array( $ppMastheadOrder ) ) {

				$newOrder = array();
				foreach( $ppMastheadOrder as $i => $imgNameEnd ) {
					$newOrder['masthead_image' . ( $i + 1 ) ] = ppImg::id( 'masthead_' . $imgNameEnd )->filename;
					$this->processedArray['masthead_image' . ( $i + 1 ) . '_linkurl' ] = ppOpt::id( 'masthead_' . $imgNameEnd . '_linkurl' );
				}

				ppImg::updateMultiple( $newOrder );

			} else {
				new ppIssue( 'Masthead order string did not parse into array in ppCustomizeSubmit::handleMastheadOrder()' );
			}

		}
	}


	protected function handleGaVerifySum() {
		if ( array_key_exists( 'link_removal_txn_id', $this->processedArray ) && $this->processedArray['link_removal_txn_id'] != ppOpt::id( 'link_removal_txn_id' ) ) {
			if ( !ppUid::exists() ) {
				ppUid::set();
			}
			$txnID = trim( $this->processedArray['link_removal_txn_id'] );
			$verifyResponse = wp_remote_get( $verifyUrl = PROPHOTO_SITE_URL . '?' . http_build_query( array(
				'requestHandler' => 'LinkRemoval::verify',
				'txnId' => $txnID,
				'uid'   => ppUid::get(),
				'url'   => trailingslashit( preg_replace( '/http(s)?:\/\/(www.)?/', '', pp::site()->url ) ),
			) ) );
			if ( isset( $_GET['debug_verify'] ) && pp::browser()->isTech ) {
				NrDump::it( $verifyResponse );
				if ( md5( $_GET['debug_verify'] ) == 'ad6e9f720f422f406294645dc005053d' ) {
					NrDump::it( $verifyUrl );
				 	ppAdmin::warn( 'link_removal_tech_troubleshoot', $txnID, md5( ppUid::get() ) );
				}
			}
			if ( 'Verified' == wp_remote_retrieve_body( $verifyResponse ) ) {
				$this->processedArray['link_removal_verified_hash'] = md5( ppUid::get() );
			} else {
				$this->processedArray['link_removal_verified_hash'] = '';
				$this->processedArray['link_removal_txn_id'] = 'verification failed, try again';
				ppAdmin::warn( 'link_removal_txn_id_not_found' );
			}
		}
	}
}
