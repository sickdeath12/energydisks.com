<?php if ( $videoURL = ppAdmin::optionVideoURL( $id ) ) { ?>

	<a href="<?php echo $videoURL ?>" title="Watch video" class="video help-icon">
		Watch video
	</a>

<?php } ?>

<?php if ( !NrUtil::endsWith( $id, '_note' ) || $id == 'splitter_note' ) { ?>

	<a href="<?php echo ppAdmin::optionTutorialURL( $id ); ?>" title="Read tutorial" class="tutorial help-icon">
		Read tutorial
	</a>

<?php } ?>


<?php if ( ppAdmin::optionBlurb( $id ) && !NrUtil::endsWith( $id, '_note' ) ) { ?>

	<a title="More info" class="blurb help-icon">
		More info
	</a>

<?php } else {

	if ( pp::site()->isDev && !NrUtil::endsWith( $id, '_note' ) ) {
		new ppIssue( "No blurb for \$id '$id'" );
	}

}?>
