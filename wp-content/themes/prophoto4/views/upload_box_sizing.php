<ul class="uploaded-file-data sizing-box">

	<li class="title">
		Recommended Image Size:
	</li>

	<li>
		<?php echo $upload->sizingExplanation(); ?>
	</li>

	<?php if ( $upload->recommendedWidth() ) { ?>
		<li>
			<b>Width:</b> <span class="recommended-width"><?php echo $upload->recommendedWidth() ?></span> pixels
		</li>
	<?php } ?>

	<?php if ( $upload->recommendedHeight() ) { ?>
		<li>
			<b>Height:</b> <span class="recommended-height"><?php echo $upload->recommendedHeight() ?></span> pixels
		</li>
	<?php } ?>

	<li class="sizing-msgs">
		<span class="size-correct">
			Image size correct.
		</span>
		<span class="size-incorrect">
			Current image does not meet recommendations.
		</span>
	</li>

</ul>
