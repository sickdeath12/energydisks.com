<?php

class ppPostImg extends NrPostImg {


	protected function newImgTag( $src, $args = null ) {
		return new ppImgTag( $src, $args );
	}


	protected function filterURL( $url ) {
		return ppPathfixer::fix( $url );
	}


	protected function error( $msg ) {
		new ppIssue( $msg );
	}


	protected function pathFromUrl( $url ) {
		return ppUtil::pathFromUrl( $url );
	}
}
