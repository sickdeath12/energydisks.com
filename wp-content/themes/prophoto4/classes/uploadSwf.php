<?php


class ppUploadSwf extends ppUploadImg {


	protected $acceptableTypes = array( 'swf' => 'application/x-shockwave-flash' );


	protected function wrongFileTypeMsg() {
		return ppString::id( 'only_swf_file_allowed' );
	}
}

