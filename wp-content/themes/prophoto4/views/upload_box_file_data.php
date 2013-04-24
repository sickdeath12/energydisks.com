<ul class="uploaded-file-data">

	<li>
		<b>Current File Stats:</b>
	<li>

	<li>
		<b>File type</b>: <span class="uploaded-file-ext"><?php echo NrUtil::fileExt( $upload->file()->filename ); ?></span>
	</li>

	<li>
		<b>Size:</b> <span class='uploaded-file-size'><?php echo $upload->filesize() ?></span> kb
	</li>

</ul>
