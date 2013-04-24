<?php

class ppUploadFavicon extends ppUploadImg {


	protected $acceptableTypes = array(
		'ico'  => 'image/x-icon',
		'ico2' => 'image/vnd.microsoft.icon',
	);


	protected function wrongFileTypeMsg() {
		return 'Filetype must be <code>.ico</code>. Read a <a href="' . pp::tut()->favicon .'">tutorial here</a>.';
	}
}

