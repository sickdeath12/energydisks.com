<ul class="uploaded-file-data sc">

	<li class="title">
		<b>Current Image Stats:</b>
	</li>

	<li class="width">
		<b>Width:</b> <span class="uploaded-img-width"><?php echo $upload->img()->width; ?></span> pixels
	</li>

	<li class="height">
		<b>Height:</b> <span class="uploaded-img-height"><?php echo $upload->img()->height ?></span> pixels
	</li>

	<li class="size">
		<b>Size:</b> <span class="uploaded-file-size"><?php echo $upload->filesize(); ?></span> kb
	</li>

	<?php if ( $upload->img()->width > $upload->maxImgDisplayWidth() ) { ?>

		<li class="not-fullsize-msg">
			Not shown <a href="<?php echo $upload->img()->url ?>">fullsize</a>
		</li>

	<?php } ?>

</ul>