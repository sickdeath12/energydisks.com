jQuery(document).ready(function($){

	for ( var filename in remoteFiles ) {
		
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			_filename: filename,
			timeout: 60000,
			data: {
				action: 'pp',
				download_remote_file: filename,
				remote_file_hash: remoteFiles[filename]
			}
		});
	}

});
