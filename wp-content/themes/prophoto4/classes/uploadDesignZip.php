<?php

class ppUploadDesignZip extends ppUploadZip {


	protected $uploadDestinationDir = 'images';


	protected function fileRenamer() {
		return ppUtil::func( 'return "import_design_' . $this->file['name'] . '.zip";' );
	}


	protected function moveUploadedFile() {
		if ( @file_exists( pp::fileInfo()->imagesFolderPath . '/' . $this->file['name'] ) ) {
			@unlink( pp::fileInfo()->imagesFolderPath . '/' . $this->file['name'] );
		}
		parent::moveUploadedFile();
	}

}

