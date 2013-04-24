<?php

class ppImgTag extends NrImgTag {


	public static function createFromHtml( $tag ) {
		return parent::createFromHtml( $tag, 'ppImgTag' );
	}


	protected function filterSrc( $src ) {
		return ppPathfixer::fix( $src );
	}


	protected static function error( $msg ) {
		new ppIssue( $msg );
	}

}

